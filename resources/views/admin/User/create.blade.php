@extends('templates.app')

@section('content')
    <div class="container my-5">

        <div class="w-75 d-block mx-auto my-5 p-4">
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb bg-white rounded shadow-sm p-2">
                    <li class="breadcrumb-item">Pengguna</li>
                    <li class="breadcrumb-item">Data</li>
                    <li class="breadcrumb-item active" aria-current="page">Tambah</li>
                </ol>
            </nav>

                <form method="POST" class="card shadow-sm " action="{{ route('admin.users.store') }}">
                    <h5 class="card-title text-center mb-4">Buat Data Staff</h5>
                    @csrf
                    <div class="mb-3 mx-3">
                        <label for="name" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                            name="name" value="{{ old('name') }}">
                        @error('name')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="mb-3 mx-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="text" class="form-control @error('email') is-invalid @enderror" id="email"
                            name="email" value="{{ old('email') }}">
                        @error('email')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="mb-3 mx-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="text" class="form-control @error('password') is-invalid @enderror" id="password"
                            name="password">
                        @error('password')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary mx-3 mb-3">Buat</button>
                </form>

        </div>
    </div>
@endsection
