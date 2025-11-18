<?php

namespace App\Http\Controllers;

use App\Models\cr;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Exports\UserExport;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function register(Request $request)
    {
        // Request $request digunakan untuk menangkap data yang dikirim dari FE atau untuk mengambil data dari request/input
        // dd(): debugging, untuk mengecek data sebelum diproses
        // dd($request->all());
        // validasi data
        $request->validate(
            [
                // 'name_input' => 'validasi'
                'first_name' => 'required|min:3',
                'last_name' => 'required|min:3',
                // email:dns memastikan email valid
                'email' => 'required|email:dns|unique:users',
                'password' => 'required'
            ],
            [
                // custom pesan
                // format: 'name_input.validasi' => 'pesan error'
                'first_name.required' => 'Nama depan wajib diisi',
                'first_name.min' => 'Nama depan diisi minimal 3 karakter',
                'last_name.required' => 'Nama belakang wajib diisi',
                'last_name.min' => 'Nama belakang diisi minimal 3 karakter',
                'email.required' => 'Email wajib diisi',
                'email.email' => 'Email diisi dengan data valid',
                'password.required' => 'Password wajib diisi'
            ]
        );

        //  eloquent (fungsi model) tambah data baru ; create ([])
        $createData = User::create([
            // 'column' => $request->name_input
            'name' => $request->first_name . " " . $request->last_name,
            'email' => $request->email,
            // enkiprsi data: merubah menjadi karakter acak, tidak ada yang bisa tau isi datanya : Hash::make()
            'password' => Hash::make($request->password),
            // role diisi langsung sebagai user agar tidak bisa menjadi admin/staff bagi pendaftar akun
            'role' => 'user'
        ]);

        if ($createData) {
            // redirect() perpindahan halaman, route() nama route yang akan dipanggil
            // with() mengirim data session, biasanya untuk notif
            return redirect()->route('login')->with('success', 'Berhasil membuat  akun. Silahkan login');
        } else {
            return redirect()->back()->with('error', 'Gagal! silahkan coba lagi.');
        }
    }

    public function loginAuth(Request $request)
    {
        $request->validate(
            [
                'email' => 'required',
                'password' => 'required'
            ],
            [
                'email.required' => 'Email wajib diisi',
                'password.required' => 'Password wajib diisi'
            ]
        );
        // menyimpan data yang akan diverifikasi
        $data = $request->only(['email', 'password']);
        // Auth::attempt() -> verifikasi kecocokan email-pw atau username,e-pw
        if (Auth::attempt($data)) {
            // setelah berhasil login, dicek lagi terkait rolenya untuk menentukan perpindahan halaman
            if (Auth::user()->role == 'admin') {
                return redirect()->route('admin.dashboard')->with(
                    'success',
                    'Berhasil login!'
                );
            } elseif (Auth::user()->role == 'staff') {
                return redirect()->route('staff.promos.index')->with('login', 'Berhasil login!');
            } else {
                return redirect()->route('home')->with('success', 'Login berhasil dilakukan!');
            }
        } else {
            return redirect()->back()->with('error', 'Gagal! pastikan email dan password sesuai');
        }
    }

    public function logout()
    {
        // Auth::logout() itu buat hapus sesi login
        Auth::logout();
        return redirect()->route('home')->with('logout', 'Anda sudah logout! Silahkan login kembali untuk akses lengkap');
    }

    public function index()
    {
        // return view('admin.user.index');
        $user = User::whereIn('role', ['admin', 'staff'])->get();
        return view('admin.user.index', compact('user'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.user.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(
            [
                'name' => 'required|min:3',
                'email' => 'required|unique:users',
                'password' => 'required'
            ],
            [
                'name.required' => 'Nama wajib diisi',
                'name.min' => 'Nama diisi minimal 3 karakter',
                'email.required' => 'Email wajib diisi',
                'email.email' => 'Email diisi dengan data valid',
                'password.required' => 'Password wajib diisi'
            ]
        );
        $createData = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'staff'
        ]);

        if ($createData) {
            return redirect()->route('admin.users.index')->with('success', 'Berhasil tambah data!');
        } else {
            return redirect()->back()->with('error', 'Gagal! silahkan coba lagi.');
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $users = User::find($id);
        return view('admin.user.edit', compact('users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email:dns'
        ], [
            'name.required' => 'Nama lengkap wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Email diisi dengan data valid'
        ]);

        $data = [
            'name'  => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $updateData = User::where('id', $id)->update($data);

        if ($updateData) {
            return redirect()->route('admin.users.index')->with('success', 'Berhasil mengubah data!');
        } else {
            return redirect()->back()->with('error', 'Gagal! silahkan coba lagi');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        User::where('id', $id)->delete();
        return redirect()->route('admin.users.index')->with('success', 'Berhasil menghapus data!');
    }
    public function trash()
    {
        $users = User::onlyTrashed()->get();
        return view('admin.user.trash', compact('users'));
    }

    public function restore($id)
    {
        $user = User::onlyTrashed()->find($id);
        $user->restore();

        return redirect()->route('admin.users.index')
            ->with('success', 'Berhasil mengembalikan data user!');
    }

    public function deletePermanent($id)
    {
        $user = User::onlyTrashed()->find($id);
        $user->forceDelete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Berhasil menghapus data user selamanya!');
    }
    public function exportExcel()
    {
        return Excel::download(new UserExport, 'users.xlsx');
    }
    public function dataForDatatables()
    {
        $users = User::whereIn('role', ['admin', 'staff'])->get();

        return DataTables::of($users)
            ->addIndexColumn()
            ->addColumn('role', function ($data) {
                if ($data->role == 'admin') {
                    return '<span class="badge badge-primary">' . $data->role . '</span>';
                } else {
                    return '<span class="badge badge-success">' . $data->role . '</span>';
                }
            })
            ->addColumn('buttons', function ($data) {
                $btnEdit = '<a href="' . route('admin.users.edit', $data->id) . '" class="btn btn-info mx-2">Edit</a>';

                $btnDelete = '<form action="' . route('admin.users.delete', $data->id) . '" method="post" style="display:inline-block;margin-left:6px;">'
                    . csrf_field()
                    . method_field('DELETE')
                    . '<button type="submit" class="btn btn-danger">Hapus</button>'
                    . '</form>';

                return '<div class="d-flex justify-content-center">' . $btnEdit . $btnDelete . '</div>';
            })
            ->rawColumns(['role', 'buttons'])
            ->make(true);
    }
}
