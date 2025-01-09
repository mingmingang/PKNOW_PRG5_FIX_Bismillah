<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;

use App\Http\Controllers\BerandaProdiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CaptchaController;
use App\Http\Controllers\HeaderController;
use App\Http\Controllers\KKController;
use App\Http\Controllers\PustakaController;
use App\Http\Controllers\PengajuanKKController;
use App\Http\Controllers\PersetujuanController;


Route::get('/', function(){
    return view('page/login/index');
});

Route::post('/select-role', [LoginController::class, 'handleRoleSelection']);

Route::get('/login', function () {
    return view('page/login/index'); // This returns the login form view
});

Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::post('/logout', [LoginController::class, 'clearRoleSession'])->name('clearRoleSession');

Route::get('/captcha', [CaptchaController::class, 'generate'])->name('captcha.generate');
Route::post('/captcha/validate', [CaptchaController::class, 'validateCaptcha'])->name('captcha.validate');

// ----------------------------------------------------  Return View  ----------------------------------------------------
// ----------- Beranda View -----------  
Route::get('/dashboard/PIC P-KNOW', function () {
    return view('Backbone/BerandaUtama');
});
Route::get('/dashboard/Program Studi', function () {
    return view('Backbone/BerandaProdi');
});

Route::get('/beranda_utama', function () {
    return view('Backbone.BerandaUtama');
})->name('beranda_utama');

Route::get('/kelola_kk/PIC P-KNOW', [KKController::class, 'getTempDataKK'])->name('kelola_kk');
Route::get('/kelola_kk/PIC P-KNOW/tambah', [KKController::class, 'create'])->name('kelola_kk.create');
Route::post('/kelola_kk/PIC P-KNOW/tambah', [KKController::class, 'store'])->name('kelola_kk.store');
Route::get('/kelola_kk/{role}/edit/{id}', [KKController::class, 'edit'])->name('kelola_kk.edit');
Route::post('/kelola_kk/{role}/update/{id}', [KKController::class, 'update'])->name('kelola_kk.update');
Route::get('/kelola_kk/{role}/detail/{id}', [KKController::class, 'detail'])->name('kelola_kk.detail');
Route::post('/kelola_kk/toggleStatus', [KKController::class, 'toggleStatus'])->name('kelola_kk.toggleStatus');
Route::get('/get-list-karyawan', [KKController::class, 'getListKaryawan'])->name('kelola_kk.getKaryawan');
Route::delete('/kelola_kk/deleteKK/{id}', [KKController::class, 'deleteKelompokKeahlian'])->name('kelola_kk.delete');
Route::get('/kelola_akk/{role}', [KKController::class, 'kelolaAKK'])->name('kelola_akk.akk');
Route::get('/kelola_akk/{role}/anggota/{id}', [KKController::class, 'tambahAnggota'])->name('kelola_anggota');


Route::get('/daftar_pustaka/{role}', [PustakaController::class, 'getTempDataPustaka'])->name('daftar_pustaka');
Route::post('/daftar_pustaka/tambah', [PustakaController::class, 'store'])->name('daftar_pustaka.store');
Route::get('/daftar_pustaka/{role}/tambah', [PustakaController::class, 'create'])->name('daftar_pustaka.create');
Route::get('/daftar_pustaka/{role}/edit/{id}', [PustakaController::class, 'edit'])->name('daftar_pustaka.edit');
Route::put('/daftar_pustaka/{id}', [PustakaController::class, 'update'])->name('daftar_pustaka.update');
Route::get('/daftar_pustaka/{role}/lihat/{id}', [PustakaController::class, 'lihat'])->name('daftar_pustaka.lihat');
Route::delete('/daftar_pustaka/deletePustaka/{id}', [PustakaController::class, 'deleteDaftarPustaka'])->name('daftar_pustaka.delete');
Route::put('/daftar_pustaka/setStatusPustaka/{id}', [PustakaController::class, 'setStatusPustaka'])->name('daftar_pustaka.setStatusPustaka');
Route::post('/daftar_pustaka/{role}/search', [PustakaController::class, 'search'])->name('daftar_pustaka.search');

Route::get('/pengajuan_kk/Tenaga Pendidik', [PengajuanKKController::class, 'getTempDataKK'])->name('pengajuan_KK');
Route::get('/pengajuan_kk', [PengajuanKKController::class, 'getTempDataKK'])->name('pengajuan_KK');
Route::get('/pengajuan_kk/{role}/gabung/{id}', [PengajuanKKController::class, 'gabung'])->name('pengajuan_kk.gabung');



Route::get('/pengajuan_kk/Tenaga Pendidik/tambah', [PengajuanKKController::class, 'create'])->name('pengajuan_kk.create');
// Route::post('/pengajuan_kk/Tenaga Pendidik/tambah', [PengajuanKKController::class, 'store'])->name('kelola_kk.store');
Route::post('/pengajuan_kk/tambah', [PengajuanKKController::class, 'store'])->name('pengajuan_kk.store');

Route::get('/pengajuan_kk/{role}/detailKK/{id}', [PengajuanKKController::class, 'detailKK'])->name('pengajuan_kk.detailKK');
Route::get('/persetujuan/Program Studi', [PersetujuanController::class, 'getTempDataKK'])->name('persetujuan');
Route::get('/persetujuan', [PersetujuanController::class, 'getTempDataKK'])->name('persetujuan');
Route::get('/persetujuan/{role}/detailPersetujuan/{id}', [PersetujuanController::class, 'detailPersetujuan'])->name('pengajuan_kk.detailPersetujuan');
Route::get('/persetujuan/anggota-aktif/{kke_id}', [PersetujuanController::class, 'getAnggotaAktif'])->name('anggota.aktif');

Route::post('/pengajuan_kk/setStatus', [PengajuanKKController::class, 'setStatus'])->name('persetujuan.setStatus');
Route::post('/persetujuan/update-status', [PersetujuanController::class, 'updateStatusAnggota'])->name('persetujuan.updateStatus');

Route::post('/get-detail-lampiran', [PersetujuanController::class, 'getDetailLampiran'])->name('persetujuan.getDetailLampiran');


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

Route::get('/dashboard/Mahasiswa', function () {
    return view('Backbone/BerandaMahasiswa');
});

Route::get('/pickk', function () {
    return view('page/master-pic-kk/index');
});

Route::get('/tenaga_kependidikan', function () {
    return view('page/master-tenaga-kependidikan/index');
});

Route::prefix('prodi')->group(function () {
    Route::get('kelola_pic', [KKController::class, 'kelolaPICIndex'])->name('kelola.pic');
    Route::get('edit-pic/{id}', [KKController::class, 'kelolaPICEdit'])->name('edit.pic');
    Route::put('edit-pic/{id}', [KKController::class, 'kelolaPICUpdate'])->name('update.pic');
});

Route::get('/header', [HeaderController::class, 'index']);
Route::get('/logout', [HeaderController::class, 'logout']);
Route::get('/notifications', [HeaderController::class, 'notifications']);
