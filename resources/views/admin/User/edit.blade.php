@extends('templates.app')

@section('content')
    <div class="container my-5">

        <div class="w-75 d-block mx-auto my-5 p-4">
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb bg-white rounded shadow-sm p-2">
                    <li class="breadcrumb-item">Pengguna</li>
                    <li class="breadcrumb-item">Data</li>
                    <li class="breadcrumb-item active" aria-current="page">Edit</li>
                </ol>
            </nav>

            <form method="POST" class="card shadow-sm " action="{{ route('admin.users.update', $users->id) }}">
                <h5 class="text-center my-3">Ubah Data Staff</h5>
                @csrf
                @method('PUT')
                <div class="mb-3 mx-3">
                    <label for="name" class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                        name="name" value="{{ $users['name'] }}">
                    @error('name')
                        <small class="text-danger">*{{ $message }}</small>
                    @enderror
                </div>
                <div class="mb-3 mx-3">
                    <label for="name" class="form-label">Email</label>
                    <input type="text" class="form-control @error('email') is-invalid @enderror" id="Email"
                        name="email" value="{{ $users['email'] }}">
                    @error('email')
                        <small class="text-danger">*{{ $message }}</small>
                    @enderror
                </div>
                <div class="mb-3 mx-3">
                    <label for="name" class="form-label @error('password') is-invalid @enderror">Password</label>
                    <input type="text" class="form-control" id="password" name="password">
                    @error('password')
                        <small class="text-danger">*{{ $message }}</small>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary mx-3 mb-3">Ubah Data</button>
            </form>
        </div>
    </div>
@endsection
