<?php

namespace App\Http\Controllers;

use App\Models\Cinema;
use Illuminate\Http\Request;
use App\Exports\CinemaExport;
use App\Models\Schedule;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class CinemaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cinemas = Cinema::all();
        return view('admin.cinema.index', compact('cinemas'));
        //compact -> argumen pada fungsi akan sama dengan nama variabel yang akan dikirim ke blade
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.cinema.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //validasi
        $request->validate([
            'name' => 'required|min:3',
            'location' => 'required|min:10',
        ], [
            'name.required' => 'Nama Bioskop harus diisi',
            'name.min' => 'Nama Wajib diisi minimal 3 huruf',
            'location.required' => 'Lokasi Bioskop harus diisi',
            'location.min' => 'Lokasi Wajib diisi minimal 10 Huruf',
        ]);
        $createCinema = Cinema::create([
            'name' => $request->name,
            'location' => $request->location,
        ]);
        if ($createCinema) {
            return redirect()->route('admin.cinemas.index')->with('success', 'Berhasil membuat data Bioskop!');
        } else {
            return redirect()->back()->with('failed', 'Gagal membuat data Bioskop');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Cinema $cinema)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $cinema = cinema::find($id);
        return view('admin.cinema.edit', compact('cinema'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //
        $request->validate([
            'name' => 'required|min:3',
            'location' => 'required|min:10',
        ], [
            'name.required' => 'Nama Bioskop harus diisi',
            'name.min' => 'Nama Wajib diisi minimal 3 huruf',
            'location.required' => 'lokasi Bioskop harus diisi',
            'location.min' => 'lokasi wajib diisi minimal 10 huruf'
        ]);

        $updateCinema = Cinema::where('id', $id)->update([
            'name' => $request->name,
            'location' => $request->location
        ]);

        if ($updateCinema) {
            return redirect()->route('admin.cinemas.index')->with('success', 'Berhasil mengubah data bioskop!');
        } else {
            return redirect()->back()->with('failed', 'Gagal mengubah data bioskop!');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $deleteData = Cinema::where('id', $id)->delete();
        if ($deleteData) {
            return redirect()->route('admin.cinemas.index')->with('succes', 'Berhasil menghapus data bioskop!');
        } else {
            return redirect()->back()->with('filed', 'Gagal mengahapus data bioskop!');
        }
    }
    public function trash()
    {
        $cinemas = Cinema::onlyTrashed()->get();
        return view('admin.cinema.trash', compact('cinemas'));
    }

    public function restore($id)
    {
        $cinema = Cinema::onlyTrashed()->findOrFail($id);
        $cinema->restore();

        return redirect()->route('admin.cinemas.index')->with('success', 'Berhasil mengembalikan data bioskop!');
    }

    public function deletePermanent($id)
    {
        $cinema = Cinema::onlyTrashed()->findOrFail($id);
        $cinema->forceDelete();

        return redirect()->route('admin.cinemas.index')->with('success', 'Berhasil menghapus data bioskop selamanya!');
    }


    public function exportExcel()
    {
        return Excel::download(new CinemaExport, 'cinemas.xlsx');
    }
    public function dataForDatatables()
    {
        $cinemas = Cinema::query();
        return DataTables::of($cinemas)
            ->addIndexColumn()
            ->addColumn('buttons', function ($data) {
                $btnEdit = '<a href="' . route('admin.cinemas.edit', $data->id) . '" class="btn btn-info">Edit</a>';

                $btnDelete = '<form action="' . route('admin.cinemas.delete', $data->id) . '" method="post" style="display:inline-block;margin-left:6px;">'
                    . csrf_field()
                    . method_field('DELETE')
                    . '<button type="submit" class="btn btn-danger">Hapus</button>'
                    . '</form>';

                return '<div class="d-flex justify-content-center">' . $btnEdit . $btnDelete . '</div>';
            })
            ->rawColumns(['buttons'])
            ->make(true);
    }
    public function listCinema()
    {
        $cinemas = Cinema::all();
        return view('schedule.cinemas', compact('cinemas'));
    }
    public function cinemaSchedules($cinema_id)
    {
        // whereHas ('namarelasi', function($q){...}: argumen 1 (nama relasi) wajib, argumen 2 (func utk filter pada relasi)optional)
        $schedules = Schedule::where('cinema_id', $cinema_id)->with('movie')->whereHas('movie', function ($q) {
            $q->where('actived', 1);
        })->get();
        return view('schedule.cinema-schedule', compact('schedules'));
    }
}
