
<?php

use Illuminate\Support\Facades\Route;

// ============================================================
// CONTROLLER NADI - LAYANAN PASIEN
// ============================================================
use App\Http\Controllers\LaboratoryController;
use App\Http\Controllers\LayananController;
use App\Http\Controllers\PublicQueueApiController;
use App\Http\Controllers\RadiologyController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\VideoEdukasiController;
use App\Http\Controllers\WaktuTungguController;
use App\Http\Controllers\SuratKontrolBPJSController;
use App\Http\Controllers\AturanMinumObatController;
use App\Http\Controllers\BpjsRujukanController;
// ============================================================
// CONTROLLER ULASAN NADI
// ============================================================
use App\Http\Controllers\NadiReviewController;
use App\Http\Controllers\Admin\NadiReviewAdminController;

// ============================================================
// CONTROLLER BANTUAN PASIEN DAN CUSTOMER SERVICE
// ============================================================
use App\Http\Controllers\NadiSupport\PatientChatController;
use App\Http\Controllers\NadiSupport\PatientFeedbackController;
use App\Http\Controllers\NadiSupport\CsAuthController;
use App\Http\Controllers\NadiSupport\CsChatController;
use App\Http\Controllers\NadiSupport\CsReviewController;
use App\Http\Controllers\NadiSupport\CsOperationsController;


/*
|--------------------------------------------------------------------------
| 1. AUTENTIKASI CUSTOMER SERVICE NADI
|--------------------------------------------------------------------------
| CS menggunakan guard nadi_cs.
| Login pasien NADI tetap menggunakan session('pasien').
|
| Tidak menggunakan controller NadiCsLoginController lagi.
|--------------------------------------------------------------------------
*/
Route::get('/bpjs/rujukan/check', [
    BpjsRujukanController::class,
    'check'
])
->middleware('throttle:10,1')
->name('bpjs.rujukan.check');

Route::prefix('cs')
    ->name('nadi.cs.')
    ->group(function () {

        Route::get('/login', [
            CsAuthController::class,
            'show'
        ])->name('login');

        Route::post('/login', [
            CsAuthController::class,
            'login'
        ])
        ->middleware('throttle:5,1')
        ->name('login.submit');

        Route::post('/logout', [
            CsAuthController::class,
            'logout'
        ])
        ->middleware('auth:nadi_cs')
        ->name('logout');
    });


/*
|--------------------------------------------------------------------------
| 2. DASHBOARD CUSTOMER SERVICE
|--------------------------------------------------------------------------
| Hanya petugas CS / Admin yang telah login.
|--------------------------------------------------------------------------
*/

Route::prefix('cs/nadi')
    ->name('cs.nadi.')
    ->middleware([
        'auth:nadi_cs',
        'nadi.cs.auth',
        'throttle:120,1'
    ])
    ->group(function () {

        // Dashboard utama
        Route::get('/', [
            CsChatController::class,
            'page'
        ])->name('dashboard');

        // ----------------------------------------------------
        // MANAJEMEN PERCAKAPAN
        // ----------------------------------------------------

        Route::get('/chats', [
            CsChatController::class,
            'index'
        ])->name('chats');

        Route::post('/chats/{session}/claim', [
            CsChatController::class,
            'claim'
        ])->name('claim');

        Route::get('/chats/{session}/messages', [
            CsChatController::class,
            'messages'
        ])->name('messages');

        Route::post('/chats/{session}/messages', [
            CsChatController::class,
            'send'
        ])->name('send');

        Route::post('/chats/{session}/close', [
            CsChatController::class,
            'close'
        ])->name('close');

        // ----------------------------------------------------
        // STATUS DAN TRANSFER CS
        // ----------------------------------------------------

        Route::post('/presence', [
            CsOperationsController::class,
            'presence'
        ])->name('presence');

        Route::get('/agents', [
            CsOperationsController::class,
            'agents'
        ])->name('agents');

        Route::post('/chats/{session}/transfer', [
            CsOperationsController::class,
            'transfer'
        ])->name('transfer');

        // ----------------------------------------------------
        // NOTIFIKASI CUSTOMER SERVICE
        // ----------------------------------------------------

        Route::get('/notifications', [
            CsOperationsController::class,
            'notifications'
        ])->name('notifications');

        Route::post('/notifications/{id}/read', [
            CsOperationsController::class,
            'read'
        ])->name('notifications.read');

        // ----------------------------------------------------
        // ULASAN PASIEN DAN BALASAN CS
        // ----------------------------------------------------

        Route::get('/reviews', [
            CsReviewController::class,
            'index'
        ])->name('reviews');

        Route::post('/reviews/{id}/reply', [
            CsReviewController::class,
            'reply'
        ])->name('review.reply');

        // ----------------------------------------------------
        // LAPORAN CUSTOMER SERVICE
        // ----------------------------------------------------

        Route::get('/reports', [
            CsOperationsController::class,
            'reports'
        ])->name('reports');
    });


/*
|--------------------------------------------------------------------------
| 3. BANTUAN PASIEN NADI
|--------------------------------------------------------------------------
| Autentikasi pasien berdasarkan session('pasien').
| Setiap chat mempunyai session/tiket tersendiri.
|--------------------------------------------------------------------------
*/

Route::prefix('nadi/bantuan')
    ->name('nadi.bantuan.')
    ->middleware('throttle:60,1')
    ->group(function () {

        Route::get('/', [
            PatientChatController::class,
            'page'
        ])->name('index');

        Route::get('/current', [
            PatientChatController::class,
            'current'
        ])->name('current');

        Route::post('/start', [
            PatientChatController::class,
            'start'
        ])->name('start');

        Route::get('/sessions/{session}/messages', [
            PatientChatController::class,
            'messages'
        ])->name('messages');

        Route::post('/sessions/{session}/messages', [
            PatientChatController::class,
            'send'
        ])->name('send');

        Route::post('/sessions/{session}/close', [
            PatientChatController::class,
            'close'
        ])->name('close');

        Route::post('/sessions/{session}/feedback', [
            PatientFeedbackController::class,
            'store'
        ])->name('feedback');
        Route::get('/history', [PatientChatController::class, 'history'])->name('history');
    });


/*
|--------------------------------------------------------------------------
| 4. ULASAN PELAYANAN NADI
|--------------------------------------------------------------------------
| Mempertahankan fitur ulasan yang sudah berjalan.
|--------------------------------------------------------------------------
*/

Route::prefix('nadi/ulasan')
    ->name('nadi.reviews.')
    ->group(function () {

        Route::get('/', [
            NadiReviewController::class,
            'index'
        ])->name('index');

        Route::post('/', [
            NadiReviewController::class,
            'store'
        ])
        ->middleware('throttle:5,1')
        ->name('store');

        Route::delete('/{review}', [
            NadiReviewController::class,
            'destroy'
        ])->name('destroy');
    });


/*
|--------------------------------------------------------------------------
| 5. ADMIN ULASAN NADI
|--------------------------------------------------------------------------
| Menggunakan autentikasi admin Laravel existing.
|--------------------------------------------------------------------------
*/

Route::prefix('admin/nadi-ulasan')
    ->name('admin.nadi-reviews.')
    ->middleware('auth')
    ->group(function () {

        Route::get('/', [
            NadiReviewAdminController::class,
            'index'
        ])->name('index');

        Route::patch('/{review}', [
            NadiReviewAdminController::class,
            'update'
        ])->name('update');
    });


/*
|--------------------------------------------------------------------------
| 6. HALAMAN UTAMA DAN AUTENTIKASI PASIEN
|--------------------------------------------------------------------------
*/

Route::get('/', [
    PublicQueueApiController::class,
    'home'
])->name('queue.home');

Route::post('/masuk', [
    WaktuTungguController::class,
    'masuk'
])->name('layanan.masuk');

Route::post('/logout', [
    WaktuTungguController::class,
    'logout'
])->name('layanan.logout');

Route::post('/keluar', [
    WaktuTungguController::class,
    'keluar'
])->name('layanan.keluar');

Route::get('/menu', [
    WaktuTungguController::class,
    'menu'
])->name('layanan.menu');

Route::get('/menu/kontrol-berikutnya/refresh', [
    WaktuTungguController::class,
    'refreshKontrolBerikutnya'
])->name('layanan.kontrol-berikutnya.refresh');


/*
|--------------------------------------------------------------------------
| 7. RESERVASI PASIEN
|--------------------------------------------------------------------------
*/

Route::get('/layanan/cek-reservasi', [
    ReservationController::class,
    'index'
])->name('reservation.index');


/*
|--------------------------------------------------------------------------
| 8. LABORATORIUM
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\LaboratoryPdfController;

Route::get('/laboratorium/pdf', [
    LaboratoryPdfController::class,
    'show'
])->name('laboratory.pdf.proxy');

Route::get('/cek-hasil-laboratorium', [
    LayananController::class,
    'laboratory'
])->name('laboratory.index');

Route::get('/layanan/laboratorium/{noOrder}/{lab}/detail', [
    LaboratoryController::class,
    'detailhasil'
])->name('laboratory.detail');


/*
|--------------------------------------------------------------------------
| 9. RADIOLOGI
|--------------------------------------------------------------------------
*/

Route::get('/cek-hasil-radiologi', [
    RadiologyController::class,
    'index'
])->name('radiology.index');

Route::get('/layanan/radiologi/{id}', [
    RadiologyController::class,
    'detail'
])->name('radiology.detail');


/*
|--------------------------------------------------------------------------
| 10. VIDEO EDUKASI
|--------------------------------------------------------------------------
*/

Route::get('/video-edukasi', [
    VideoEdukasiController::class,
    'index'
])->name('video-edukasi.index');


/*
|--------------------------------------------------------------------------
| 11. ATURAN MINUM OBAT
|--------------------------------------------------------------------------
*/

Route::prefix('aturan-minum-obat')
    ->name('aturan-minum.')
    ->group(function () {

        Route::get('/', [
            AturanMinumObatController::class,
            'index'
        ])->name('index');

        Route::get('/{noregistrasi}', [
            AturanMinumObatController::class,
            'show'
        ])->name('show');
    });


/*
|--------------------------------------------------------------------------
| 12. BPJS - SURAT KONTROL DAN RUJUKAN
|--------------------------------------------------------------------------
*/

Route::get('/cek-surat-kontrol', [
    SuratKontrolBPJSController::class,
    'check'
])->name('bpjs.surat-kontrol.check');

// Route::get('/bpjs/rujukan/check', [
//     WaktuTungguController::class,
//     'cekRujukan'
// ])->name('bpjs.rujukan.check');


/*
|--------------------------------------------------------------------------
| 13. CEK ANTREAN PASIEN
|--------------------------------------------------------------------------
*/

Route::post('/cek-antrean', [
    WaktuTungguController::class,
    'check'
])->name('queue.check');

Route::post('/cek-antrean/refresh', [
    WaktuTungguController::class,
    'refresh'
])->name('queue.refresh');


/*
|--------------------------------------------------------------------------
| 14. CEK WAKTU TUNGGU
|--------------------------------------------------------------------------
*/

Route::get('/cek-waktu-tunggu', [
    WaktuTungguController::class,
    'check'
])->name('waktu-tunggu.index');

Route::post('/cek-waktu-tunggu/refresh', [
    WaktuTungguController::class,
    'refresh'
])->name('waktu-tunggu.refresh');
