
@extends('templates.app')
@section('content')
    <div class="container my-5">
        <div class="d-flex justify-content-end">
            <a href="{{ route('admin.cinemas.index') }}" class="btn btn-secondary">Kembali</a>
        </div>

        @if (Session::get('success'))
            <div class="alert alert-success mt-3">{{ Session::get('success') }}</div>
        @endif
        <h3 class="my-3">DATA SAMPAH : Bioskop</h3>
        <table class="table table-bordered">
            <tr>
                <th>#</th>
                <th>Nama Bioskop</th>
                <th>Lokasi</th>
                <th>Aksi</th>
            </tr>
            @foreach ($cinemas as $key => $cinema)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $cinema->name }}</td>
                    <td>{{ $cinema->location }}</td>
                    <td class="d-flex align-items-center">
                        <form action="{{ route('admin.cinemas.restore', $cinema->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success btn-sm">Kembalikan</button>
                        </form>

                        <form action="{{ route('admin.cinemas.delete_permanent', $cinema->id) }}" method="POST"
                            class="ms-2">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Hapus Selamanya</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
@endsection
