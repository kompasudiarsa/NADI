<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Services\PoliWaitingApiService;
use Illuminate\Support\Facades\Crypt;
use Throwable;

class WaktuTungguController extends Controller
{
    //
    public function masuk(
        Request $request,
        PoliWaitingApiService $apiService
    ) {
        $validated = $request->validate([
            'rm' => [
                'required',
                'string',
                'max:30',
            ],

            'tanggal_lahir' => [
                'required',
                'date_format:Y-m-d',
            ],

            'captcha' => [
                'required',
                'integer',
            ],

            'captcha_token' => [
                'required',
                'string',
            ],
        ], [
            'rm.required' =>
            'Nomor rekam medis wajib diisi.',

            'tanggal_lahir.required' =>
            'Tanggal lahir wajib diisi.',

            'tanggal_lahir.date_format' =>
            'Format tanggal lahir tidak valid.',

            'captcha.required' =>
            'Verifikasi keamanan wajib diisi.',

            'captcha.integer' =>
            'Jawaban verifikasi harus berupa angka.',

            'captcha_token.required' =>
            'Verifikasi keamanan tidak valid. Silakan muat ulang halaman.',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Validasi CAPTCHA
        |--------------------------------------------------------------------------
        */

            try {
                $decrypted = Crypt::decryptString(
                    $validated['captcha_token']
                );

                $captchaData = json_decode(
                    $decrypted,
                    true
                );

                if (
                    ! is_array($captchaData) ||
                    ! isset($captchaData['answer']) ||
                    ! isset($captchaData['issued_at'])
                ) {
                    throw new \RuntimeException(
                        'Captcha tidak valid.'
                    );
                }

            /*
        |--------------------------------------------------------------------------
        | CAPTCHA berlaku maksimal 10 menit
        |--------------------------------------------------------------------------
        */

            $captchaAge =
                time() - (int) $captchaData['issued_at'];

            if (
                $captchaAge < 0 ||
                $captchaAge > 600
            ) {
                return back()
                    ->withInput(
                        $request->except([
                            'captcha',
                            'captcha_token',
                        ])
                    )
                    ->withErrors([
                        'captcha' =>
                        'Verifikasi keamanan telah kedaluwarsa. Silakan coba kembali.',
                    ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Bandingkan Jawaban
            |--------------------------------------------------------------------------
            */

            $expectedAnswer =
                (int) $captchaData['answer'];

            $userAnswer =
                (int) $validated['captcha'];

            if ($userAnswer !== $expectedAnswer) {
                return back()
                    ->withInput(
                        $request->except([
                            'captcha',
                            'captcha_token',
                        ])
                    )
                    ->withErrors([
                        'captcha' =>
                        'Jawaban verifikasi keamanan tidak sesuai.',
                    ]);
            }
        } catch (Throwable $e) {
            return back()
                ->withInput(
                    $request->except([
                        'captcha',
                        'captcha_token',
                    ])
                )
                ->withErrors([
                    'captcha' =>
                    'Verifikasi keamanan tidak valid atau telah kedaluwarsa. Silakan coba kembali.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | CAPTCHA BENAR
        | Baru lanjut cek pasien
        |--------------------------------------------------------------------------
        */

        $result = $apiService->getPatient(
            $validated['rm'],
            $validated['tanggal_lahir']
        );

        /*
        |--------------------------------------------------------------------------
        | Pasien Tidak Ditemukan
        |--------------------------------------------------------------------------
        */

        if (! data_get($result, 'found')) {
            return back()
                ->withInput(
                    $request->except([
                        'captcha',
                        'captcha_token',
                    ])
                )
                ->withErrors([
                    'validasi' => data_get(
                        $result,
                        'message',
                        'Data pasien tidak ditemukan.'
                    ),
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Ambil Data Pasien
        |--------------------------------------------------------------------------
        */

        $patient = data_get(
            $result,
            'patient',
            []
        );

        /*
        |--------------------------------------------------------------------------
        | Simpan Session Pasien
        |--------------------------------------------------------------------------
        */

        session([
            'pasien' => $patient,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Redirect
        |--------------------------------------------------------------------------
        */

        return redirect()->route(
            'layanan.menu'
        );
    }
    private function generateCaptcha(): array
    {
        $angka1 = random_int(1, 9);
        $angka2 = random_int(1, 9);

        $answer = $angka1 + $angka2;

        $payload = [
            'answer' => $answer,
            'issued_at' => time(),
        ];

        return [
            'question' => $angka1 . ' + ' . $angka2,
            'token' => Crypt::encryptString(
                json_encode($payload)
            ),
        ];
    }
    /**
     * Menampilkan halaman menu utama pelayanan pasien.
     */

    public function menu()
    {
        $rm = session('pasien.medical_record');
        $tanggalLahir = session('pasien.birth_date');

        if (empty($rm) || empty($tanggalLahir)) {
            return redirect('/')
                ->withErrors([
                    'validasi' =>
                    'Silakan masukkan nomor rekam medis dan tanggal lahir terlebih dahulu.',
                ]);
        }

        return view('layanan.menu', [
            'rm' => $rm,
            'tanggalLahir' => $tanggalLahir,
            'pasien' => session('pasien'),
        ]);
    }

    /**
     * Menghapus identitas pasien dari session.
     */
    public function keluar()
    {
        session()->forget([
            'pasien.rm',
            'pasien.tanggal_lahir',
        ]);

        return redirect()->route('');
    }
    public function check(Request $request)
    {
        $sessionPatient = session('pasien', []);

        $request->merge([
            'rm' => $request->input(
                'rm',
                data_get($sessionPatient, 'medical_record')
            ),

            'tanggal_lahir' => $request->input(
                'tanggal_lahir',
                data_get($sessionPatient, 'birth_date')
            ),
        ]);

        $validated = $request->validate([
            'rm' => [
                'required',
                'string',
                'max:30',
            ],

            'tanggal_lahir' => [
                'required',
                'date_format:Y-m-d',
            ],
        ], [
            'rm.required' =>
            'Nomor RM wajib tersedia.',

            'tanggal_lahir.required' =>
            'Tanggal lahir wajib tersedia.',

            'tanggal_lahir.date_format' =>
            'Format tanggal lahir harus YYYY-MM-DD.',
        ]);

        try {
            $ringkasanAntrean = $this->getRingkasanAntrean(
                $validated['rm'],
                $validated['tanggal_lahir']
            );

            return view('layanan.waktu-tunggu', [
                'result' => [
                    'found' => true,
                    'response' => $ringkasanAntrean,
                    'metaData' => [
                        'code' => 200,
                        'message' => 'OK',
                    ],
                ],
                'keyword' => $validated['rm'],
                'tanggalLahir' => $validated['tanggal_lahir'],
            ]);
        } catch (\Throwable $e) {
            report($e);

            return redirect()
                ->route('layanan.menu')
                ->withErrors([
                    'validasi' => $e->getMessage(),
                ]);
        }
    }
    public function refresh(Request $request)
    {
        $validated = $request->validate([
            'rm' => ['required', 'string', 'max:30'],
            'tanggal_lahir' => ['required', 'date_format:Y-m-d'],
        ]);

        try {
            // Query database yang sama dengan proses cek pertama.
            $ringkasanAntrean = $this->getRingkasanAntrean(
                $validated['rm'],
                $validated['tanggal_lahir']
            );

            $statusPasien = data_get(
                $ringkasanAntrean,
                'status_pasien',
                '-'
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'status_pasien' => $statusPasien,

                    'status_class' => $this->getStatusClass(
                        $statusPasien
                    ),

                    'label_statusperiksa' => data_get(
                        $ringkasanAntrean,
                        'label_statusperiksa',
                        '-'
                    ),

                    'status_asli' => data_get(
                        $ringkasanAntrean,
                        'status_asli',
                        '-'
                    ),

                    'sisa_pasien_di_depan' => (int) data_get(
                        $ringkasanAntrean,
                        'sisa_pasien_di_depan',
                        0
                    ),

                    'sisa_reservasi_di_depan' => (int) data_get(
                        $ringkasanAntrean,
                        'sisa_reservasi_di_depan',
                        0
                    ),

                    'sisa_teregistrasi_di_depan' => (int) data_get(
                        $ringkasanAntrean,
                        'sisa_teregistrasi_di_depan',
                        0
                    ),

                    'antrianloket' => data_get(
                        $ringkasanAntrean,
                        'antrianloket',
                        '-'
                    ) ?: '-',

                    'noantrian' => data_get(
                        $ringkasanAntrean,
                        'noantrian',
                        '-'
                    ) ?: '-',

                    'noantrian_apd' => data_get(
                        $ringkasanAntrean,
                        'noantrian_apd',
                        '-'
                    ) ?: '-',

                    'tanggal_antrean' => data_get(
                        $ringkasanAntrean,
                        'tanggal_antrean',
                        '-'
                    ) ?: '-',

                    'sedang_dilayani' => data_get(
                        $ringkasanAntrean,
                        'sedang_dilayani'
                    ),
                ],
            ]);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
    private function getRingkasanAntrean(
        string $rm,
        string $tanggalLahir
    ): array {
        $baseUrl = rtrim(
            config(
                'services.queue_api.base_url',
                'http://127.0.0.1:8000'
            ),
            '/'
        );

        $url = $baseUrl . '/api/service/getantreanpasien';

        $result = $this->curlGetSigned($url, [
            'rm' => trim($rm),
            'tanggal_lahir' => $tanggalLahir,
        ]);

        $code = (int) data_get(
            $result,
            'metaData.code',
            0
        );

        $message = data_get(
            $result,
            'metaData.message',
            'Gagal mendapatkan data antrean.'
        );

        if ($code !== 200) {
            throw new \RuntimeException($message);
        }

        $ringkasanAntrean = data_get(
            $result,
            'response'
        );

        if (
            ! is_array($ringkasanAntrean)
            || empty($ringkasanAntrean)
        ) {
            throw new \RuntimeException(
                'Data ringkasan antrean tidak tersedia.'
            );
        }

        return $ringkasanAntrean;
    }


    /**
     * GET API dengan autentikasi:
     *
     * X-Token
     * X-Timestamp
     * X-Signature
     *
     * Signature:
     *
     * METHOD
     * REQUEST_PATH
     * TIMESTAMP
     * SHA256_BODY
     */
    private function curlGetSigned(
        string $url,
        array $query = []
    ): array {
        $token = config(
            'services.queue_api.token'
        );

        $secret = config(
            'services.queue_api.secret'
        );

        if (empty($token)) {
            throw new \RuntimeException(
                'Token API antrean belum dikonfigurasi.'
            );
        }

        if (empty($secret)) {
            throw new \RuntimeException(
                'Secret API antrean belum dikonfigurasi.'
            );
        }

        /*
    |--------------------------------------------------------------------------
    | Canonical Query
    |--------------------------------------------------------------------------
    |
    | Query harus diurutkan supaya signature client dan server sama.
    |
    */

        ksort($query);

        $queryString = http_build_query(
            $query,
            '',
            '&',
            PHP_QUERY_RFC3986
        );

        /*
    |--------------------------------------------------------------------------
    | Final URL
    |--------------------------------------------------------------------------
    */

        $finalUrl = $url;

        if ($queryString !== '') {
            $finalUrl .= '?' . $queryString;
        }

        /*
    |--------------------------------------------------------------------------
    | Timestamp
    |--------------------------------------------------------------------------
    */

        $timestamp = (string) time();

        /*
    |--------------------------------------------------------------------------
    | Canonical Request Path
    |--------------------------------------------------------------------------
    |
    | Contoh:
    |
    | /api/service/getantreanpasien
    | ?rm=21.77.58&tanggal_lahir=1968-11-23
    |
    */

        $path = parse_url(
            $url,
            PHP_URL_PATH
        );

        $canonicalUri = $path;

        if ($queryString !== '') {
            $canonicalUri .= '?' . $queryString;
        }

        /*
    |--------------------------------------------------------------------------
    | HTTP Method
    |--------------------------------------------------------------------------
    */

        $method = 'GET';

        /*
    |--------------------------------------------------------------------------
    | Body Hash
    |--------------------------------------------------------------------------
    |
    | GET tidak memiliki body.
    |
    */

        $bodyHash = hash(
            'sha256',
            ''
        );

        /*
    |--------------------------------------------------------------------------
    | Signature Payload
    |--------------------------------------------------------------------------
    |
    | GET
    | /api/service/getantreanpasien?rm=...&tanggal_lahir=...
    | TIMESTAMP
    | SHA256_EMPTY_BODY
    |
    */

        $signaturePayload = implode("\n", [
            $method,
            $canonicalUri,
            $timestamp,
            $bodyHash,
        ]);

        /*
    |--------------------------------------------------------------------------
    | Generate HMAC SHA256
    |--------------------------------------------------------------------------
    */

        $signature = hash_hmac(
            'sha256',
            $signaturePayload,
            $secret
        );

        /*
    |--------------------------------------------------------------------------
    | CURL
    |--------------------------------------------------------------------------
    */

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $finalUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,

            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 10,

            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,

            CURLOPT_CUSTOMREQUEST => 'GET',

            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'X-Token: ' . $token,
                'X-Timestamp: ' . $timestamp,
                'X-Signature: ' . $signature,
            ],
        ]);

        $response = curl_exec($curl);

        $httpCode = curl_getinfo(
            $curl,
            CURLINFO_HTTP_CODE
        );

        $curlError = curl_error($curl);

        curl_close($curl);

        /*
    |--------------------------------------------------------------------------
    | CURL Error
    |--------------------------------------------------------------------------
    */

        if (
            $response === false
            || $curlError !== ''
        ) {
            throw new \RuntimeException(
                'Gagal menghubungi API antrean: '
                    . $curlError
            );
        }

        /*
    |--------------------------------------------------------------------------
    | Decode JSON
    |--------------------------------------------------------------------------
    */

        $result = json_decode(
            $response,
            true
        );

        if (! is_array($result)) {
            throw new \RuntimeException(
                'Respons API antrean bukan JSON yang valid. '
                    . 'HTTP '
                    . $httpCode
                    . '. Respons: '
                    . substr(
                        $response,
                        0,
                        300
                    )
            );
        }

        /*
    |--------------------------------------------------------------------------
    | HTTP Error
    |--------------------------------------------------------------------------
    */

        if ($httpCode >= 400) {
            throw new \RuntimeException(
                data_get(
                    $result,
                    'metaData.message',
                    data_get(
                        $result,
                        'message',
                        'API mengembalikan HTTP '
                            . $httpCode
                    )
                )
            );
        }

        return $result;
    }


    private function getStatusClass(
        string $status
    ): string {
        $status = Str::lower($status);

        if (Str::contains(
            $status,
            'menunggu'
        )) {
            return 'is-warning';
        }

        if (Str::contains(
            $status,
            'sedang'
        )) {
            return 'is-primary';
        }

        if (Str::contains(
            $status,
            'selesai'
        )) {
            return 'is-success';
        }

        return 'is-info';
    }
    public function logout(Request $request)
{
    /*
    |--------------------------------------------------------------------------
    | Hapus Data Pasien
    |--------------------------------------------------------------------------
    */

    $request->session()->forget('pasien');

    /*
    |--------------------------------------------------------------------------
    | Hapus Session Lama
    |--------------------------------------------------------------------------
    */

    $request->session()->invalidate();

    /*
    |--------------------------------------------------------------------------
    | Generate CSRF Token Baru
    |--------------------------------------------------------------------------
    */

    $request->session()->regenerateToken();

    /*
    |--------------------------------------------------------------------------
    | Kembali ke Halaman Utama
    |--------------------------------------------------------------------------
    */

    return redirect()
        ->route('queue.home')
        ->with(
            'success',
            'Anda telah berhasil keluar dari sesi pasien.'
        );
}
}
