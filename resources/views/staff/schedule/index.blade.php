@extends('templates.app')

@section('content')
    <div class="container my-5">
        <div class="d-flex justify-content-end">
            {{--karna modal isi tidak akan dirubah,  munculkan dengan boostrap target--}}
            <a href="{{ route('staff.schedules.export') }}" class="btn btn-secondary me-2">Export (.xlsx)</a>
            <a href="{{ route('staff.schedules.trash') }}" class="btn btn-secondary me-2">Data Sampah</a>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalAdd">Tambah Data</button>
        </div>

        @if (Session::get('success'))
        <div class="alert alert-success">{{ Session::get('success')}}</div>
        @endif

        <h3 class="my-3">Data Jadwal Tayangan</h3>
        <table class="table table-bordered" id="scheduleTable">
            <thead>
            <tr class="text-center">
                <th>#</th>
                <th>Nama Bioskop</th>
                <th>Judul Film</th>
                <th>Harga</th>
                <th>Jam Tayang</th>
                <th>Aksi</th>
            </tr>
            </thead>
        </table>

        {{--Modal--}}
        <div class="modal fade" id="modalAdd" tabindex="-1" aria-labelledby="modalAdd" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="modalAddLabel">Tambah Data</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{route ('staff.schedules.store') }}">
                @csrf
            <div class="modal-body">
                <div class="mb-3">
                    <label for="cinema_id" class="cinema_id">Bioskop:</label>
                    <select name="cinema_id" id="cinema_id" class="form-select @error('cinema_id') is-invalid @enderror">
                    <option disabled hidden selected>Pilih Bioskop</option>
                    @foreach ($cinemas as $cinema)
                    {{--jumlah opsi  select sesuai data cinema--}}
                    {{--cinema_id menyimpan id jd value ['id'] tp munculkan ['namenya']--}}
                    <option value="{{ $cinema['id'] }}">{{ $cinema['name'] }}</option>
                    @endforeach
                    </select>
                    @error('cinema_id')
                    <small class="text-danger">{{$message}}</small>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="movie_id" class="col-form-label">film:</label>
                    <select name="movie_id" id="movie_id" class="form-select @error('cinema_id') is-invalid @enderror">
                    <option disabled hidden selected>Pilih Film</option>
                    @foreach ($movies as $movie)
                    <option value="{{ $movie['id'] }}">{{ $movie['title'] }}</option>
                    @endforeach
                    </select>
                    @error('movie_id')
                    <small class="text-danger">{{$message}}</small>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="price" class="form-label">Harga :</label>
                    <input type="number" name="price" id="price" class="form-control @error('price') is-invalid @enderror">
                    @error('price')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="hours" class="form-label">Jam Tayang :</label>
                    @if ($errors->has('hours.*'))
                    {{--ambil ket error pada item pertama --}}
                    <small class="text-danger">{{ $errors->first('hours.*') }}</small>
                    @endif
                    <input type="time" name="hours[]" id="hours" class="form-control @if ($errors->has('hours.*')) is-invalid @endif">
                    <div id="additionalInput"></div>
                    <span class="text-primary my-3" style="cursor: pointer" onclick="addInput()"> + Tambah Input Jam</span>
                </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Kirim</button>
            </div>
            </form>
            </div>
        </div>
        </div>
    </div>
@endsection

@push('script')
<script>
    $(function() {
        $('#scheduleTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('staff.schedules.datatables') }}",
            columns:[
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'cinema', name: 'cinema', orderable: true, searchable: true },
                { data: 'movie', name: 'movie', orderable: true, searchable: true },
                { data: 'price', name: 'price', orderable: true, searchable: true },
                { data: 'hours', name: 'hours', orderable: true, searchable: true },
                { data: 'buttons', name: 'buttons', orderable: false, searchable: false }
            ],
        })
    });
</script>

<script>
    function addInput() {
        let content = `<input type="time" name="hours[]" id="hours" class="form-control my-2">`;
        //ambil wadah
        let wrap=document.querySelector
        //simpan content, tp menggunakan += agar konten terus bertambah bukan mengubah
        ("#additionalInput");
        wrap.innerHTML += content;
    }
</script>
@if ($errors->any())
<script>
    //panggil modal
    let modalAdd = document.querySelector("#modalAdd");
    //munculkan modal lg, lewat js
    new bootstrap.Modal(modalAdd).show();
</script>
@endif
@endpush
