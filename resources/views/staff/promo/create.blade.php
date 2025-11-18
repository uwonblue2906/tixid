@extends('templates.app')

@section('content')
<div class="container align-items-center w-75 d-block mx-auto my-5 p-4 align-items-center">
    <form action="{{ route('staff.promos.store') }}"  method="POST" class="card p-4 my-4">
        @csrf
        @if (Session::get('error'))
                <div class="alert alert-danger">
                    {{ Session::get('error') }}
                </div>
            @endif
        <h5 class="text-center my-3">Buat Data Promo</h5>

        <div class="mb-3">
            <label for="promo_code" class="form-label">Kode Promo</label>
            <input type="text" class="form-control @error('promo_code') is-invalid @enderror"
                   id="promo_code" name="promo_code">
            @error('promo_code')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="mb-3">
            <label for="type" class="form-label">Tipe Promo</label>
            <select name="type" id="type" class="form-control @error('type') is-invalid @enderror">
                <option value="percent">(%)</option>
                <option value="rupiah">Rupiah (Rp)</option>
            </select>
            @error('type')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="mb-3">
            <label for="discount" class="form-label">Jumlah Potongan</label>
            <input type="number" class="form-control @error('discount') is-invalid @enderror"
                   id="discount" name="discount">
            <small class="form-text text-muted">Isi sesuai tipe yang dipilih (% atau Rupiah)</small>
            @error('discount')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary w-100">Simpan</button>
    </form>
</div>
@endsection
