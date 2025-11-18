<?php

use App\Http\Controllers\CinemaController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\PromoController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Models\Cinema;

Route::get('/', [MovieController::class, 'home'])->name('home');
Route::get('/movies/all', [MovieController::class, 'homeALLMovie'])->name('home.movies.all');
Route::get('/schedules/{movie_id}', [MovieController::class, 'movieSchedules'])->name('schedules.detail');


// daftar bioskop
Route::get('/cinemas/list', [CinemaController::class, 'listCinema'])->name('cinemas.list');
Route::get('/cinemas/{cinema_id}/schedules', [CinemaController::class, 'cinemaSchedules'])->name('cinemas.schedules');


Route::middleware('isUser')->group(function () {
    //halaman pilih kursi
    Route::get(
        '/schedules/{scheduleId}/hours/{hourId}/show-seats',
        [TicketController::class, 'showSeats']
    )->name('schedules.show_seats');

    Route::prefix('/tickets')->name('tickets.')->group(function () {
        Route::get('/', [TicketController::class, 'index'])->name('index');
        Route::post('/', [TicketController::class, 'store'])->name('store');
        Route::get('/{ticketId}/order', [TicketController::class, 'ticketOrder'])->name('order');
        Route::post('/{ticketId}/barcode', [TicketController::class, 'createBarcode'])->name('barcode');
        Route::get('/{ticketId}/payment', [TicketController::class, 'paymentPage'])->name('payment');
        Route::patch('/{ticketId}/payment/proof', [TicketController::class, 'proofPayment'])->name('payment.proof');
        Route::get('/{ticketId}', [TicketController::class, 'show'])->name('show');
        Route::get('/{ticketId}/export/pdf', [TicketController::class, 'exportPdf'])->name('export.pdf');
    });
});


// Route::get('/schedules', function () {
//     return view('schedule.detail-film');
// })->name('schedules.detail');

Route::get('/auth', function () {
    return view('login');
})->name('login');

Route::get('/signup', function () {
    return view('signup');
})->name('signup');

// httpmethod
// 1. get digunakan untuk menampilkan halaman
// 2. post digunakan untuk menambahkan data baru
// 3. put digunakan untuk mengubah data
// 4. delete digunakan untuk menghapus data

Route::post('/signup', [UserController::class, 'register'])->name('signup.register');
Route::post('/login', [UserController::class, 'loginAuth'])->name('login.auth');
Route::get('/logout', [UserController::class, 'logout'])->name('logout');

// halaman khusus admin
// middleware() : memanggil middleware yang akan digunakan
// group() : memngelompokkan route agar mengikuti sifat sebelumnya (sebelumnya = middleware)
Route::middleware('isAdmin')->prefix('/admin')->name('admin.')->group(function () {
    Route::get('/tickets/chart', [TicketController::class, 'chartData'])->name('tickets.chart');
    // admin dashboard disimpan di group middleware agar dapat menggunakan middleware tersebut
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    Route::prefix('/cinemas')->name('cinemas.')->group(function () {
        // ambil banyak data :index
        Route::get('/', [CinemaController::class, 'index'])->name('index');
        // resource create (function create controller) untuk memunculkan form tambah data
        Route::get('/create', [CinemaController::class, 'create'])->name('create');
        // resource store (function store controller) untuk proses form tambah
        Route::post('/store', [CinemaController::class, 'store'])->name('store');
        // {id} -> parameter placeholder, mengirim data ke controller. Digunakan ketika akan mengambil data spesifik
        Route::get('edit/{id}', [CinemaController::class, 'edit'])->name('edit');
        // put itu buat proses edit/update data
        Route::put('update/{id}', [CinemaController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [CinemaController::class, 'destroy'])->name('delete');
        Route::get('export', [CinemaController::class, 'exportExcel'])->name('export');
        Route::get('/trash', [CinemaController::class, 'trash'])->name('trash');
        Route::patch('/restore/{id}', [CinemaController::class, 'restore'])->name('restore');
        Route::delete('/delete-permanent/{id}', [CinemaController::class, 'deletePermanent'])->name('delete_permanent');
        Route::get('/datatables', [CinemaController::class, 'dataForDatatables'])->name('datatables');
    });
    Route::prefix('/users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/store', [UserController::class, 'store'])->name('store');
        Route::get('edit/{id}', [UserController::class, 'edit'])->name('edit');
        Route::put('update/{id}', [UserController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [UserController::class, 'destroy'])->name('delete');
        Route::get('/export', [UserController::class, 'exportExcel'])->name('export');
        Route::get('/trash', [UserController::class, 'trash'])->name('trash');
        Route::patch('/restore/{id}', [UserController::class, 'restore'])->name('restore');
        Route::delete('/delete-permanent/{id}', [UserController::class, 'deletePermanent'])->name('delete_permanent');
        Route::get('/datatables', [UserController::class, 'dataForDatatables'])->name('datatables');
    });
    // data film
    Route::prefix('/movies')->name('movies.')->group(function () {
        Route::get('/chart', [MovieController::class, 'dataChart'])->name('chart');

        Route::get('/', [MovieController::class, 'index'])->name('index');
        Route::get('/create', [MovieController::class, 'create'])->name('create');
        Route::post('/store', [MovieController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [MovieController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [MovieController::class, 'update'])->name('update');
        Route::delete('/{id}', [MovieController::class, 'destroy'])->name('delete');
        Route::put('/{id}/toggle', [MovieController::class, 'toggle'])->name('toggle');
        Route::get('/export', [MovieController::class, 'exportExcel'])->name('export');
        Route::get('/trash', [MovieController::class, 'trash'])->name('trash');
        Route::patch('/restore/{id}', [MovieController::class, 'restore'])->name('restore');
        Route::delete('/delete-permanent/{id}', [MovieController::class, 'deletePermanent'])->name('delete_permanent');
        Route::get('/datatables', [MovieController::class, 'dataForDatatables'])->name('datatables');
    });
});

// beranda
Route::get('/', [MovieController::class, 'home'])->name('home');
Route::get('/movies/active', [MovieController::class, 'homeMovies'])->name('home.movies.active');

// halaman khusus staff
// staff
Route::middleware('isStaff')->prefix('/staff')->name('staff.')->group(function () {
    // Route::get('/dashboard', function () {
    //     return view('staff.dashboard');
    // })->name('dashboard');

    Route::prefix('/promos')->name('promos.')->group(function () {
        Route::get('/', [PromoController::class, 'index'])->name('index');
        Route::get('/create', [PromoController::class, 'create'])->name('create');
        Route::post('/store', [PromoController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [PromoController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [PromoController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [PromoController::class, 'destroy'])->name('delete');
        Route::get('/export', [PromoController::class, 'exportExcel'])->name('export');
        Route::get('/trash', [PromoController::class, 'trash'])->name('trash');
        Route::patch('/restore/{id}', [PromoController::class, 'restore'])->name('restore');
        Route::delete('/delete-permanent/{id}', [PromoController::class, 'deletePermanent'])->name('delete_permanent');
        Route::get('/datatables', [PromoController::class, 'dataForDatatables'])->name('datatables');
    });

    //jadwal tayang
    Route::prefix('/schedules')->name('schedules.')->group(function () {
        Route::get('/', [ScheduleController::class, 'index'])->name('index');
        Route::post('/store', [ScheduleController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [ScheduleController::class, 'edit'])->name('edit');
        Route::patch('/update/{id}', [ScheduleController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [ScheduleController::class, 'destroy'])->name('delete');
        //recycle-bin
        Route::get('/trash', [ScheduleController::class, 'trash'])->name('trash');
        Route::patch('/restore/{id}', [ScheduleController::class, 'restore'])->name('restore');
        Route::delete('/delete-permanent/{id}', [ScheduleController::class, 'deletePermanent'])->name('delete_permanent');
        Route::get('/export', [ScheduleController::class, 'exportExcel'])->name('export');
        Route::get('/datatables', [ScheduleController::class, 'dataForDatatables'])->name('datatables');
    });
});
