<?php

namespace App\Http\Controllers;

use App\Exports\MovieExport;
use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class MovieController extends Controller
{
    public function home(Request $request)
    // format pencarian data : where ('column', 'operator', 'value')
    // jika operator ==/= operator bisa TIDAK DITULIS
    // operator yang digunakan : < kurang dari | > lebih dari | <> tidak sama dengan
    // format mengurutkan data :
    {
        $movie = Movie::where('actived', 1)->orderBy('created_at', 'DESC')->limit(4)->get();

        return view('home', compact('movie'));
    }

    public function homeAllMovie(Request $request)
    {
        //ambil data dari input naem="search_movie"
        $title = $request->search_movie;
        //kalau search_movie engga kosong, cari data
        if ($title != "") {
            //operator LIKE : mencari data yang mirip / mengandung kata tertentu
            //% digunakan untuk mengaktifkan LIKE
            //% kata : mencari kata belakang
            //kata % : mencari kata depan
            //% kata % : mencari kata ddepan, tengah, dan belakang

            $movie = Movie::where('title', 'LIKE', '%' . $title . '%')->where(
                'actived',
                1
            )->orderBy('created_at', 'DESC')->get();
        } else {
            $movie = Movie::where('actived', 1)->orderBy('created_at', 'DESC')->get();
        }

        return view('movies', compact('movie'));
    }

    public function movieSchedules($movie_id, Request $request)
    {
        //Request $request : mengambil data dari form atau href="?"
        $sortPrice = $request['sort-price']; //kalau pake strip - jadi harus pake kurung kotak kalau engga pake panah
        if ($sortPrice) {
            //karena mau mengurutkan berdasakan price yang ada di schedules, maka sorting (ordeyBy) disimpan di relasi with schedules
            $movie = Movie::where('id', $movie_id)->with(['schedules' => function ($q) use ($sortPrice) {
                //$q : mewakilkan model Schedule // use buat manggil function yang ada di luar
                //'schedules' => function ($q) {...} : melakukan filter / menjalankan eloquent didalam relasi
                $q->orderBy('price', $sortPrice);
            }, 'schedules.cinema'])->first();
        } else {
            //mengambil relais didalam relasi
            //relasi cinema ada di schedule -> schedules.cinema (.)
            $movie = Movie::where('id', $movie_id)->with(['schedules', 'schedules.cinema'])->first();
            //first() : karena 1 data film, diambilnya satu
        }

        $sortAlfabet = $request['sort-alfabet'];
        if ($sortAlfabet == 'ASC') {
            //ambil collection, collection : hasil dari get, first, all
            //$movie->schedules mengacu ke data relasi schedules
            //sortBy : mengurutkan collection (ASC), ordeyBy : mengurutkan query eloquent
            $movie->schedules = $movie->schedules->sortBy(function ($schedule) {
                return $schedule->cinema->name; //mengurutkan berdasarkan name dari relasi cinema
            })->values();
        } elseif ($sortAlfabet == 'DESC') {
            //kalau sortAlfabet bukan ASC, Berarti DESC, gunakan sortByDesc (untuk mengurukN secara DESC)
            $movie->schedules = $movie->schedules->sortByDESC(function ($schedule) {
                return $schedule->cinema->name;
            })->values();
            //ambil ulang data
        }

        $searchCinema = $request['search-cinema'];
        if ($searchCinema) {
            //filter collection, ambil relasi schedules hanya yang cinema_id
            $movie->schedules = $movie->schedules->where('cinema_id', $searchCinema)->values();
        }

        $listCinema = Movie::where('id', $movie_id)->with(['schedules', 'schedules.cinema'])->first();

        return view('schedule.detail-film', compact('movie', 'listCinema'));
    }

    public function index()
    {
        $movies = Movie::all();

        return view('admin.movie.index', compact('movies'));
    }

    public function create()
    {
        return view('admin.movie.create');
    }

    public function store(Request $request)
    {

        $request->validate([
            'title' => 'required',
            'duration' => 'required',
            'genre' => 'required',
            'director' => 'required',
            'age_rating' => 'required|numeric',
            'poster' => 'required|mimes:jpeg,jpg,png,svg,webp',
            'description' => 'required|min:15',
        ], [
            'title.required' => 'Judul film wajib diisi!',
            'genre.required' => 'Genre film wajib diisi!',
            'director.required' => 'Director film wajib diisi!',
            'age_rating.required' => 'Minimal usia wajib diisi!',
            'age_rating.numeric' => 'Minimal usia wajib diisi dengan angka',
            'poster.required' => 'Poster film wajib diisi!',
            'poster.mimes' => 'File hanya boleh bertipe JPG/JPEG/PNG/SVG/WEBP',
            'description.required' => 'Sinopsis film wajib diisi',
        ]);

        $poster = $request->file('poster');
        // $poster->storeAs('movies', $poster->hashName());
        $namaPoster = Str::random(5) . '-poster.' . $poster->getClientOriginalExtension();
        $path = $poster->storeAs('movies', $namaPoster, 'public');

        $createMovies = Movie::create([
            'title' => $request->title,
            'genre' => $request->genre,
            'duration' => $request->duration,
            'description' => $request->description,
            'director' => $request->director,
            'age_rating' => $request->age_rating,
            'poster' => $path,
            'actived' => 1,
        ]);

        if ($createMovies) {
            return redirect()->route('admin.movies.index')->with('success', 'Data film berhasil ditambahkan');
        } else {
            return redirect()->back()->with('error', 'Gagal! silahkan coba lagi');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Movie $movie)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $movie = Movie::Find($id);

        return view('admin.movie.edit', compact('movie'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Movie $movie, $id)
    {
        $request->validate([
            'title' => 'required',
            'duration' => 'required',
            'genre' => 'required',
            'director' => 'required',
            'age_rating' => 'required|numeric',
            'poster' => 'mimes:jpeg,jpg,png,svg,webp',
            'description' => 'required|min:15',
        ], [
            'title.required' => 'Judul film wajib diisi!',
            'genre.required' => 'Genre film wajib diisi!',
            'director.required' => 'Director film wajib diisi!',
            'age_rating.required' => 'Minimal usia wajib diisi!',
            'age_rating.numeric' => 'Minimal usia wajib diisi dengan angka',
            'poster.mimes' => 'File hanya boleh bertipe JPG/JPEG/PNG/SVG/WEBP',
            'description.required' => 'Sinopsis film wajib diisi',
        ]);

        $movie = Movie::find($id);

        if ($request->file('poster')) {
            $fileSebelumnya = storage_path('app/public' . $movie['poster']);
            if (file_exists($fileSebelumnya)) {
                unlink($fileSebelumnya);
            }
            $poster = $request->file('poster');
            // $poster->storeAs('movies', $poster->hashName());
            $namaPoster = Str::random(5) . '-poster.' . $poster->getClientOriginalExtension();
            $path = $poster->storeAs('movies', $namaPoster, 'public');
        }

        $updateMovies = Movie::where('id', $id)->update([
            'title' => $request->title,
            'genre' => $request->genre,
            'duration' => $request->duration,
            'description' => $request->description,
            'director' => $request->director,
            'age_rating' => $request->age_rating,
            // ?? ternary : (if, jika ada ambil) ?? (else, jika tidak ada maka ambil yang di else)
            'poster' => $path ?? $movie['poster'],
            'actived' => 1,
        ]);

        if ($updateMovies) {
            return redirect()->route('admin.movies.index')->with('success', 'Data film diubah ditambahkan');
        } else {
            return redirect()->back()->with('error', 'Gagal! silahkan coba lagi');
        }
    }

    public function dataChart()
    {
        $movieActive = Movie::where('actived', 1)->count();
        $movieNonActive = Movie::where('actived', 0)->count();

        $labels = ['Film Aktif', 'Film Non-Aktif'];
        $data = [$movieActive, $movieNonActive];

        return response()->json([
            'labels' => $labels,
            'data' => $data
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $movie = Movie::findOrFail($id);
        //hapus gambar nya dari storage
        if ($movie->poster) {
            $posterPath = storage_path("app/public" . $movie['poster']);
            if (file_exists($posterPath)) {
                unlink($posterPath);
            }
        }

        $movie->delete();

        return redirect()->route('admin.movies.index')->with('success', 'Data film berhasil dihapus');
    }

    public function trash()
    {
        $movies = Movie::onlyTrashed()->get();
        return view('admin.movie.trash', compact('movies'));
    }

    public function restore($id)
    {
        $movie = Movie::onlyTrashed()->find($id);
        $movie->restore();

        return redirect()->route('admin.movies.index')->with('success', 'Berhasil mengembalikan data film!');
    }

    public function deletePermanent($id)
    {
        $movie = Movie::onlyTrashed()->find($id);
        $movie->forceDelete();

        return redirect()->route('admin.movies.index')->with('success', 'Berhasil menghapus data film selamanya!');
    }

    public function toggle($id)
    {
        $movie = Movie::findOrfail($id);
        $movie->actived = $movie->actived == 1 ? 0 : 1;
        $movie->save();

        return redirect()->route('admin.movies.index')->with('success', 'Status Film berhasil di ubah');
    }

    public function exportExcel()
    {
        $file_name = 'data-film.xlsx';

        return Excel::download(new MovieExport, $file_name);
    }
    public function dataForDatatables()
    {
        // siapkan query eloquent dari model movie
        $movies = Movie::query();
        return DataTables::of($movies)
            ->addIndexColumn() //memberikan nomor 1, 2 dst di column table
            ->addColumn('imgPoster', function ($data) {
                $urlImage = asset('storage') . "/" . $data['poster'];
                return '<img src ="' . $urlImage . '" width="150">';
            })
            ->addColumn('activeBadge', function ($data) {
                // membuat data activedBadge yg akan mengembalikan badge warna sesuai status
                if ($data->actived == 1) {
                    return '<span class="badge badge-success">Aktif</span>';
                } else {
                    return '<span class="badge badge-secondary">Non-Aktif</span>';
                }
            })
            ->addColumn('buttons', function ($data) {
                $btnDetail = '<button class="btn btn-secondary me-2" onclick=\'showModal(' . json_encode($data) . ')\'>Detail</button>';

                $btnEdit = ' <a href="' . route('admin.movies.edit', $data['id']) . '" class="btn btn-primary me-2">Edit</a>';
                $btnDelete = '<form class="me-2" action=" ' . route('admin.movies.delete', $data['id']) . '" method="post">' .
                    csrf_field() .
                    method_field('DELETE') .
                    '<button type="submit" class="btn btn-danger ">Hapus</button>
                        </form>';
                $btnNonAktif = '';
                if ($data->actived == 1) {
                    $btnNonAktif = '<form class="me-2" action=" ' . route('admin.movies.toggle', $data['id']) . '" method="post">' .
                        csrf_field() .
                        method_field('PATCH') .
                        '<button type="submit" class="btn btn-warning">Non-Aktif</button>
                        </form>';
                }
                return '<div class="d-flex justify-content-center">' . $btnDetail . $btnEdit . $btnDelete . $btnNonAktif . '</div>';
            })
            //rawColumns([]) MENDAFTARKAN COLUMN YG DIBUAT di addColumn
            ->rawColumns(['imgPoster', 'activeBadge', 'buttons'])
            ->make(true); //mengubah query menjadi JSON (format yg bisa dibaca datatables)
    }

    public function movieSchedule($movie_id) {}
}
