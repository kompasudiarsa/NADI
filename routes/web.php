<?php

use App\Http\Controllers\LayananController;
use App\Http\Controllers\PublicQueueApiController;
use App\Http\Controllers\WaktuTungguController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LaboratoryController;
use App\Http\Controllers\RadiologyController;
use App\Http\Controllers\ReservationController;
// Route::get(
//     '/cek-hasil-laboratorium',
//     [LaboratoryController::class, 'index']
// )->name('laboratory.index');

// Route::post(
//     '/cek-hasil-laboratorium',
//     [LaboratoryController::class, 'check']
// )->name('laboratory.check');
use App\Http\Controllers\VideoEdukasiController;

Route::get(
    '/video-edukasi',
    [VideoEdukasiController::class, 'index']
)->name('video-edukasi.index');
Route::get(
    '/layanan/cek-reservasi',
    [ReservationController::class, 'index']
)->name('reservation.index');

Route::get('/cek-hasil-radiologi', [RadiologyController::class, 'index'])
    ->name('radiology.index');

Route::get('/layanan/radiologi/{id}', [RadiologyController::class, 'detail'])
    ->name('radiology.detail');

Route::get('/', [PublicQueueApiController::class, 'home'])->name('queue.home');
// Route::post('/cek-antrean', [PublicQueueApiController::class, 'check'])->name('queue.check');
// Route::post('/cek-antrean/refresh', [PublicQueueApiController::class, 'refresh'])
//     ->name('queue.refresh');
Route::post('/cek-antrean', [WaktuTungguController::class, 'check'])->name('queue.check');
Route::post('/cek-antrean/refresh', [WaktuTungguController::class, 'refresh'])
    ->name('queue.refresh');

Route::get(
    '/cek-hasil-laboratorium',
    [LayananController::class, 'laboratory']
)->name('laboratory.index');

Route::get(
    '/layanan/laboratorium/{noOrder}/{lab}/detail',
    [LaboratoryController::class, 'detailhasil']
)->name('laboratory.detail');
// Route::post(
//     '/cek-hasil-laboratorium',
//     [LaboratoryController::class, 'check']
// )->name('laboratory.check');
Route::post(
    '/masuk',
    [WaktuTungguController::class, 'masuk']
)->name('layanan.masuk');

Route::get(
    '/menu',
    [WaktuTungguController::class, 'menu']
)->name('layanan.menu');

Route::post(
    '/keluar',
    [WaktuTungguController::class, 'keluar']
)->name('layanan.keluar');

Route::get(
    '/cek-waktu-tunggu',
    [WaktuTungguController::class, 'check']
)->name('queue.check');

Route::post(
    '/cek-waktu-tunggu/refresh',
    [WaktuTungguController::class, 'refresh']
)->name('queue.refresh');
Route::post('/logout', [
    WaktuTungguController::class,
    'logout'
])->name('layanan.logout');