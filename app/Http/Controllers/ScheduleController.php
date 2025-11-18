<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Movie;
use App\Models\Cinema;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ScheduleExport;
use Yajra\DataTables\Facades\DataTables;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cinemas = Cinema::all();
        $movies = Movie::all();

        //with() : mengambil data detail dari relasi, tidak hanya idnya
        //isi di dlm with diambil dr nama fungsi relasi di model
        $schedules = Schedule::with(['cinema', 'movie'])->get();
        return view('staff.schedule.index', compact('cinemas', 'movies', 'schedules'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'cinema_id' => 'required',
            'movie_id' => 'required',
            'price' => 'required|numeric',
            //validasi item array(.) validasi index ke berapa pun (*)
            'hours.*' => 'required|date_format:H:i'
        ], [
            'cinema_id.required' => 'Bioskop harus dipilih',
            'movie_id.required' => 'Film harus dipilih',
            'price.required' => 'Harga harus diisi',
            'price.numeric' => 'Harga harus diisi dengan angka',
            'hours.*.required' => 'Jam tayang harus diisi dengan minimal satu data',
            'hours.*.date_format' => 'Harga harus diisi dengan jam:menit',
        ]);

        //pengecekan data berdasarkan cinbema_id dan movie_id lalu ambil hoursnya
        //value('hours') : hanya mngembil haours, gaperlu data lain
        $hours = Schedule::Where('cinema_id', $request->cinema_id)->where('movie_id', $request->movie_id)->value('hours');
        //jika data blm ada $hours akan NULL, agar tetp array gunakan terrary
        // jika $hours ada isinya ambil, klo NULL buat array kosong
        $hoursBefore = $hours ?? [];
        //gabungkan hours sblmnya dg yg baru ditambhkan
        $mergeHours = array_merge($hoursBefore, $request->hours);
        //hilangkan jam yg duplikat, hgunakan array ini utk db
        $newHours = array_unique($mergeHours);
        //updateOrCreate() : jika cinema_id & movie_id udh ada di schedule (UPDATE data price & hours) klo gd (CREATE semua)
        $createData = Schedule::updateOrCreate([
            //cari data
            'cinema_id' => $request->cinema_id,
            'movie_id' => $request->movie_id,
        ], [
            //update ini
            'price' => $request->price,
            'hours' => $newHours,
        ]);
        if ($createData) {
            return redirect()->route('staff.schedules.index')->with('success', 'Berhasil menambahkan data!');
        } else {
            return redirect()->back()->with('error', 'Gagal! coba lagi');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Schedule $schedule)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        //first() : mengambil satu data
        $schedule = Schedule::where('id', $id)->with(['cinema', 'movie'])->first();
        return view('staff.schedule.edit', compact('schedule'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'price' => 'required|numeric',
            'hours.*' => 'required|date_format:H:i',
        ], [
            'price.required' => 'Harga harus diisi',
            'price.numeric' => 'Harga harus diisi dengan angka',
            'hours.*.required' => 'Harga harus diisi minimal satu data',
            'hours.*.date_format' => 'Harga harus diisi dengan jam:menit',
        ]);
        $updateData = Schedule::where('id', $id)->update([
            'price' => $request->price,
            'hours' => array_unique($request->hours),
        ]);
        if ($updateData) {
            return redirect()->route('staff.schedules.index')->with('success', 'Berhasil mengubah data!');
        } else {
            return redirect()->back()->with('error', 'Gagal! coba lagi');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Schedule::where('id', $id)->delete();
        return redirect()->route('staff.schedules.index')->with('success', 'Berhasil menghapus data!');
    }

    public function trash()
    {
        //onlyTrashed() : mengambil data yang sudah dihapus, yg deleted_at di phpmyadminnya ada isi tanggal, hanya filter tetep digunakan get()/first() untuk mengambilnya
        $schedules = Schedule::onlyTrashed()->with(['cinema', 'movie'])->get();
        return view('staff.schedule.trash', compact('schedules'));
    }

    public function restore($id)
    {
        $schedule = Schedule::onlyTrashed()->find($id);
        $schedule->restore();
        return redirect()->route('staff.schedules.index')->with('success', 'Berhasil mengambil data!');
    }

    public function deletePermanent($id)
    {
        $schedule = Schedule::onlyTrashed()->find($id);
        $schedule->forceDelete();
        return redirect()->back()->with('success', 'Berhasil menghapus data selamanya!');
    }
    public function exportExcel()
    {
        $file_name = "schedule.xlsx";
        return Excel::download(new ScheduleExport, $file_name);
    }
    public function dataForDataTables()
    {
        $schedules = Schedule::with(['cinema', 'movie'])->get();

        return dataTables::of($schedules)
            ->addIndexColumn()
            ->addColumn('cinema', fn($s) => $s->cinema->name ?? '-')
            ->addColumn('movie', fn($s) => $s->movie->title ?? '-')
            ->addColumn('price', fn($s) => 'Rp ' . number_format($s->price, 0, ',', '.'))
            ->addColumn('hours', function ($s) {
                if (!$s->hours) return '-';
                $hours = is_array($s->hours) ? $s->hours : explode(',', $s->hours);
                $html = '<ul class="mb-0 text-center">';
                foreach ($hours as $hour) {
                    $html .= "<li>{$hour}</li>";
                }
                return $html . '</ul>';
            })
            ->addColumn('buttons', function ($s) {
                $btnEdit = '<a href="' . route('staff.schedules.edit', $s->id) . '" class="btn btn-primary btn-sm me-1">Edit</a>';
                $btnDelete = '<form action="' . route('staff.schedules.delete', $s->id) . '" method="POST" style="display:inline-block">'
                    . csrf_field() . method_field('DELETE') . '
                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                    </form>';
                return '<div class="d-flex justify-content-center align-items-center">' . $btnEdit . $btnDelete . '</div>';
            })
            ->rawColumns(['hours', 'buttons'])
            ->make(true);
    }
}
