@extends ('templates.app')

@section('content')
    <div class="container my-5">
        <div class="d-flex justify-content-end">
            <a href="{{ route('staff.promos.export') }}" class="btn btn-secondary me-2">
        Export (.xlsx)
    </a>
     <a href="{{ route('staff.promos.trash') }}" class="btn btn-secondary me-2">Data Sampah</a>
            <a href="{{ route('staff.promos.create') }}" class="btn btn-success">Tambah Promo</a>
        </div>

        @if (Session::get('success'))
            <div class="alert alert-success ">{{ Session::get('success') }}</div>
        @endif

        <h5 class="mb-3">Data Promo</h5>
        <table class="table table-bordered" id="promoTable">
            <thead>
                 <tr>
                <th>#</th>
                <th>Kode Promo</th>
                <th>Potongan</th>
                <th>Aksi</th>
            </tr>
            {{-- </thead>
            @foreach ($promos as $key => $item)
                <tr>
                    <th>{{ $key + 1 }}</th>
                    <td>{{ $item->promo_code }}</td>
                    <td>
                        @if ($item->type == 'percent')
                            {{ $item->discount }}%
                        @else
                            Rp. {{ number_format($item->discount, 0, ',', '.') }}
                        @endif
                    </td>

                    <td class="d-flex">
                        <a href="{{ route('staff.promos.edit', $item->id) }}" class="btn btn-primary me-2">Edit</a>
                        <form action="{{ route('staff.promos.delete', $item->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger me-2">Hapus</button>
                        </form>
                    </td>
                </tr>
            @endforeach --}}
        </table>
    </div>
@endsection
@push('script')
    <script>
    $(function(){
            $('#promoTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('staff.promos.datatables') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'promo_code', name: 'promo_code'},
                    { data: 'discount', name: 'discount'},
                    { data: 'buttons', name: 'buttons', orderable: false, searchable: false },
                ]
            });
        });
</script>
@endpush

