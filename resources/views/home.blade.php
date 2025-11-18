@extends('templates.app')

{{-- mengisi yield --}}
@section('content')
    @if (Session::get('success'))
        {{-- Auth::user() : mengambil data pengguna yang login --}}
        {{-- format : Auth::user()->coloum_di_fillable --}}
        <div class="alert alert-success w-100">{{ Session::get('success') }} <b>Selamat Datang, {{ Auth::user()->name }}</b>
        </div>
    @endif

    @if (Session::get('logout'))
        <div class="alert alert-warning w-100">{{ Session::get('logout') }}</div>
    @endif

    <div class="dropdown">
        <button class="btn btn-light dropdown-toggle w-100 d-flex align-items-center" type="button" id="dropdownMenuButton"
            data-mdb-dropdown-init data-mdb-ripple-init aria-expanded="false"><i class="fa-solid fa-location-dot me-2"></i>
            Bogor
        </button>
        <ul class="dropdown-menu w-100" aria-labelledby="dropdownMenuButton">
            <li><a class="dropdown-item" href="#">Action</a></li>
            <li><a class="dropdown-item" href="#">Another action</a></li>
            <li><a class="dropdown-item" href="#">Something else here</a></li>
        </ul>
    </div>

    <div id="carouselExampleIndicators" class="carousel slide" data-mdb-ride="carousel" data-mdb-carousel-init>
        <div class="carousel-indicators">
            <button type="button" data-mdb-target="#carouselExampleIndicators" data-mdb-slide-to="0" class="active"
                aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-mdb-target="#carouselExampleIndicators" data-mdb-slide-to="1"
                aria-label="Slide 2"></button>
            <button type="button" data-mdb-target="#carouselExampleIndicators" data-mdb-slide-to="2"
                aria-label="Slide 3"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="https://asset.tix.id/microsite_v2/440af9f9-90b2-467c-845a-e0170c838361.webp" class="d-block w-100"
                    style="height: 500px" alt="Wild Landscape" />
            </div>
            <div class="carousel-item">
                <img src="https://i.pinimg.com/1200x/84/80/00/84800065e1d5329f808776e7f5a96b8d.jpg" class="d-block w-100"
                    style="height: 500px" alt="Camera" />
            </div>
            <div class="carousel-item">
                <img src="https://i.pinimg.com/1200x/48/4e/78/484e78205cf886aedf3469b6d3c2c9eb.jpg" class="d-block w-100"
                    style="height: 500px" alt="Exotic Fruits" />
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-mdb-target="#carouselExampleIndicators"
            data-mdb-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-mdb-target="#carouselExampleIndicators"
            data-mdb-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>

    <div class="d-flex justify-content-between container mt-4">
        <div class="d-flex align-items-center gap-2 mt-4">
            <i class="fa-solid fa-clapperboard"></i>
            <h5 class="mt-2"> Sedang Tayang</h5>
        </div>
        <div>
            <a href="{{ route('home.movies.all') }}" class="btn btn-warning rounded-pill mt-4">Semua <i
                    class="fa-solid fa-angle-right"></i></a>
        </div>
    </div>

    <div class="d-flex gap-2 container">
        <button type="button" class="btn btn-outline-primary rounded-pill" data-mdb-ripple-init
            data-mdb-ripple-color="dark">Semua Film</button>
        <button type="button" class="btn btn-outline-secondary rounded-pill" data-mdb-ripple-init
            data-mdb-ripple-color="dark">XXI</button>
        <button type="button" class="btn btn-outline-secondary rounded-pill" data-mdb-ripple-init
            data-mdb-ripple-color="dark">Cinepolis</button>
        <button type="button" class="btn btn-outline-secondary rounded-pill" data-mdb-ripple-init
            data-mdb-ripple-color="dark">Imax</button>
    </div>

    <div class="d-flex justify-content-center gap-2 my-3">
        @foreach ($movie as $item)
            <div class="card" style="width: 13rem;">
                <img src="{{ asset('storage/' . $item['poster']) }}" class="card-img-top" alt="Sunset Over the Sea"
                    style="object-fit: cover; height: 350px" />
                <div class="card-body" style="padding: 0 !important">
                    <p class="card-text text-center bg-primary py-2"><a href="{{ route('schedules.detail', $item['id']) }}"
                            class="text-warning"><b>Beli Tiket</b></a></p>
                </div>
            </div>
        @endforeach
    </div>


    <footer class="bg-body-tertiary text-center text-lg-start mt-4">
        {{-- Copyright --}}
        <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.05);">
            © 2020 Copyright:
            <a class="text-body" href="https://mdbootstrap.com/">MDBootstrap.com</a>
        </div>
        {{-- Copyright --}}
    </footer>
@endsection
