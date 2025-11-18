@extends('templates.app')

@section('content')
    <div class="container mt-3">
        @if (Session::get('success'))
            <div class="alert alert-success">{{ Session::get('success') }}</div>
        @endif
        <div class="d-flex justify-content-end">
             <a href="{{ route('admin.users.export') }}" class="btn btn-secondary me-2">
        Export (.xlsx)
    </a>
    <a href="{{ route('admin.users.trash') }}" class="btn btn-secondary me-2">Data Sampah</a>
            <a href="{{ route('admin.users.create') }}" class="btn btn-success">Tambah Data</a>
        </div>
        <h5 class="mt-3">Data Pengguna (Staff & Admin)</h5>
        <table class="table table-bordered" id="userTable">
            <thead>
                  <tr>
                <th>#</th>
                <th>Nama Lengkap</th>
                <th>Email</th>
                <th>Role</th>
                <th>Aksi</th>
            </tr>
            </thead>
            {{-- $cinemas dari compact --}}
            {{-- foreach karena $cinemas pake ::all() datanya lebih dari satu dan berbentuk array --}}
           {{-- @foreach ($user as $key => $item)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->email }}</td>
                    <td>
                        @if ($item->role == 'admin')
                            <span class="badge badge-primary">{{ $item['role'] }}</span>
                        @else
                            <span class="badge badge-success">{{ $item['role'] }}</span>
                        @endif
                    </td>
                    <td class="d-flex justify-content-center">
                        <a href="{{ route('admin.users.edit', $item['id']) }}" class="btn btn-info mx-2">Edit</a>
                        <form action="{{ route('admin.users.delete', $item['id']) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
            @endforeach --}}
        </table>
    </div>
@endsection
@push('script')
<script>
$(function() {
    $('#userTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.users.datatables') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'name', name: 'name' },
            { data: 'email', name: 'email' },
            { data: 'role', name: 'role' },
            { data: 'buttons', name: 'buttons', orderable: false, searchable: false }
        ]
    });
});
</script>
@endpush

