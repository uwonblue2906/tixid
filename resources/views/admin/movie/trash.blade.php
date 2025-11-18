@extends('templates.app')
@section('content')
    <div class="container my-5">
        <div class="d-flex justify-content-end">
            <a href="{{ route('admin.movies.index') }}" class="btn btn-secondary">Kembali</a>
        </div>

        @if (Session::get('success'))
            <div class="alert alert-success mt-3">{{ Session::get('success') }}</div>
        @endif

        <h3 class="my-3">DATA SAMPAH : Film</h3>

        <table class="table table-bordered">
            <tr>
                <th>#</th>
                <th>Poster</th>
                <th>Judul</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
            @foreach ($movies as $key => $movie)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>
                        @if ($movie->poster)
                            <img src="{{ asset('storage/' . $movie->poster) }}" width="120" class="rounded shadow-sm">
                        @else
                            <span class="text-muted">Tidak ada gambar</span>
                        @endif
                    </td>
                    <td>{{ $movie->title }}</td>
                    <td>
                        @if ($movie->actived == 1)
                            <span class="badge badge-success">Aktif</span>
                        @else
                            <span class="badge badge-warning">Non-aktif</span>
                        @endif
                    </td>
                    <td class="d-flex align-items-center">
                        <form action="{{ route('admin.movies.restore', $movie->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success btn-sm">Kembalikan</button>
                        </form>

                        <form action="{{ route('admin.movies.delete_permanent', $movie->id) }}" method="POST"
                            class="ms-2">
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
