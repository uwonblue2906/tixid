@extends('templates.app')

@section('content')
    <div class="container my-5">
        <div class="d-flex justify-content-end">
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Kembali</a>
        </div>

        @if (Session::get('success'))
            <div class="alert alert-success mt-3">{{ Session::get('success') }}</div>
        @endif
        <h3 class="my-3">DATA SAMPAH : User / Staff</h3>
        <table class="table table-bordered align-middle">
            <tr class="text-center">
                <th>#</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Role</th>
                <th>Aksi</th>
            </tr>
            @foreach ($users as $key => $user)
                <tr class="text-center">
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @if ($user->role == 'admin')
                            <span class="badge badge-primary">{{ $user['role'] }}</span>
                        @else
                            <span class="badge badge-success">{{ $user['role'] }}</span>
                        @endif
                    </td>

                    <td class="d-flex justify-content-center gap-2">
                        <form action="{{ route('admin.users.restore', $user->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success btn-sm">Kembalikan</button>
                        </form>

                        <form action="{{ route('admin.users.delete_permanent', $user->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Hapus Selamanya</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
@endsection
