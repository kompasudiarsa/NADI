<?php

use App\Http\Controllers\LaboratoryController;
use App\Http\Controllers\LayananController;
use App\Http\Controllers\PublicQueueApiController;
use App\Http\Controllers\RadiologyController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\VideoEdukasiController;
use App\Http\Controllers\WaktuTungguController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Halaman Awal / Login Pasien
|--------------------------------------------------------------------------
*/
Route::get(
    '/',
    [PublicQueueApiController::class, 'home']
)->name('queue.home');

Route::post(
    '/masuk',
    [WaktuTungguController::class, 'masuk']
)->name('layanan.masuk');

Route::post(
    '/logout',
    [WaktuTungguController::class, 'logout']
)->name('layanan.logout');

Route::post(
    '/keluar',
    [WaktuTungguController::class, 'keluar']
)->name('layanan.keluar');

/*
|--------------------------------------------------------------------------
| Menu Utama NADI
|--------------------------------------------------------------------------
*/
Route::get(
    '/menu',
    [WaktuTungguController::class, 'menu']
)->name('layanan.menu');

/*
|--------------------------------------------------------------------------
| Auto Refresh Kontrol Berikutnya
|--------------------------------------------------------------------------
| Dipanggil oleh menu Blade setiap 30 detik dan saat tab kembali aktif.
*/
Route::get(
    '/menu/kontrol-berikutnya/refresh',
    [WaktuTungguController::class, 'refreshKontrolBerikutnya']
)->name('layanan.kontrol-berikutnya.refresh');

/*
|--------------------------------------------------------------------------
| Reservasi
|--------------------------------------------------------------------------
*/
Route::get(
    '/layanan/cek-reservasi',
    [ReservationController::class, 'index']
)->name('reservation.index');

/*
|--------------------------------------------------------------------------
| Laboratorium
|--------------------------------------------------------------------------
*/
Route::get(
    '/cek-hasil-laboratorium',
    [LayananController::class, 'laboratory']
)->name('laboratory.index');

Route::get(
    '/layanan/laboratorium/{noOrder}/{lab}/detail',
    [LaboratoryController::class, 'detailhasil']
)->name('laboratory.detail');

/*
|--------------------------------------------------------------------------
| Radiologi
|--------------------------------------------------------------------------
*/
Route::get(
    '/cek-hasil-radiologi',
    [RadiologyController::class, 'index']
)->name('radiology.index');

Route::get(
    '/layanan/radiologi/{id}',
    [RadiologyController::class, 'detail']
)->name('radiology.detail');

/*
|--------------------------------------------------------------------------
| Video Edukasi
|--------------------------------------------------------------------------
*/
Route::get(
    '/video-edukasi',
    [VideoEdukasiController::class, 'index']
)->name('video-edukasi.index');

/*
|--------------------------------------------------------------------------
| Waktu Tunggu
|--------------------------------------------------------------------------
| Nama route dibuat unik. Pada route lama queue.check dan queue.refresh
| masing-masing didefinisikan dua kali.
*/
Route::post(
    '/cek-antrean',
    [WaktuTungguController::class, 'check']
)->name('queue.check');

Route::post(
    '/cek-antrean/refresh',
    [WaktuTungguController::class, 'refresh']
)->name('queue.refresh');

Route::get(
    '/cek-waktu-tunggu',
    [WaktuTungguController::class, 'check']
)->name('waktu-tunggu.index');

Route::post(
    '/cek-waktu-tunggu/refresh',
    [WaktuTungguController::class, 'refresh']
)->name('waktu-tunggu.refresh');