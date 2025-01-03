<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;

use App\Http\Controllers\BerandaProdiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HeaderController;
use App\Http\Controllers\KKController;

Route::get('/', function(){
    return view('page/login/index');
});

Route::post('/select-role', [LoginController::class, 'handleRoleSelection']);

Route::get('/login', function () {
    return view('page/login/index'); // This returns the login form view
});

Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::post('/logout', [LoginController::class, 'clearRoleSession'])->name('clearRoleSession');

// ----------------------------------------------------  Return View  ----------------------------------------------------
// ----------- Beranda View -----------  
Route::get('/dashboard/PIC P-KNOW', function () {
    return view('Backbone/BerandaUtama');
});



Route::get('/beranda_utama', function () {
    return view('Backbone.BerandaUtama');
})->name('beranda_utama');

Route::get('/kelola_kk/PIC P-KNOW', [KKController::class, 'getTempDataKK'])->name('kelola_kk');
Route::get('/kelola_kk', [KKController::class, 'getTempDataKK'])->name('kelola_kk');
Route::get('/kelola_kk/PIC P-KNOW', [KKController::class, 'getTempDataKK'])->name('kelola_kk.getTempDataKK');
Route::get('/kelola_kk/PIC P-KNOW/tambah', [KKController::class, 'create'])->name('kelola_kk.create');
Route::post('/kelola_kk/PIC P-KNOW/tambah', [KKController::class, 'store'])->name('kelola_kk.store');
Route::get('/get-list-karyawan', [KKController::class, 'getListKaryawan'])->name('kelola_kk.getKaryawan');
Route::delete('/kelola_kk/deleteKK/{id}', [KKController::class, 'deleteKelompokKeahlian'])->name('kelola_kk.delete');

Route::get('/beranda_pengguna', function () {
    return view('Backbone/BerandaPengguna');
});


Route::get('/daftar_pustaka', function () {
    return view('page/DaftarPustaka/index');
});


// Route Dashboard
Route::get('/dashboard/PIC P-KNOW', function () {
    return view('Backbone/BerandaUtama');
});

// Tenaga Pendidik
Route::get('/dashboard/Tenaga Pendidik', function () {
    return view('Backbone/BerandaTenagaPendidik');
});



Route::get('/pickk', function () {
    return view('page/master-pic-kk/index');
});

Route::get('/tenaga_kependidikan', function () {
    return view('page/master-tenaga-kependidikan/index');
});

Route::get('/header', [HeaderController::class, 'index']);
Route::get('/logout', [HeaderController::class, 'logout']);
Route::get('/notifications', [HeaderController::class, 'notifications']);
