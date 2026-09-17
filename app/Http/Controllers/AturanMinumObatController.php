<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

class AturanMinumObatController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LIST RIWAYAT REGISTRASI
    |--------------------------------------------------------------------------
    |
    | Menampilkan registrasi pasien hari ini dan sebelumnya.
    | Registrasi yang akan datang tidak ditampilkan.
    |
    */
    public function index(Request $request)
    {
        $patient = session('pasien', []);

        $rm = trim((string) data_get(
            $patient,
            'medical_record',
            data_get(
                $patient,
                'rm',
                data_get($patient, 'nocm', '')
            )
        ));

        $tanggalLahir = data_get(
            $patient,
            'tanggal_lahir',
            data_get(
                $patient,
                'birth_date',
                data_get(
                    $patient,
                    'tgllahir',
                    data_get($patient, 'date_of_birth')
                )
            )
        );

        if ($rm === '' || !$tanggalLahir) {
            return redirect('/')
                ->withErrors([
                    'validasi' =>
                    'Data pasien pada session tidak lengkap.',
                ]);
        }

        try {
            $tanggalLahir = Carbon::parse(
                $tanggalLahir
            )->format('Y-m-d');
        } catch (\Throwable $e) {
            return redirect('/')
                ->withErrors([
                    'validasi' =>
                    'Format tanggal lahir pasien tidak valid.',
                ]);
        }

        /*
    |--------------------------------------------------------------------------
    | Ambil Riwayat Registrasi
    |--------------------------------------------------------------------------
    */

        $riwayat = $this->getRiwayatRegistrasi(
            $rm,
            $tanggalLahir
        );

        if (!$riwayat['success']) {
            return view(
                'aturan-minum.index',
                [
                    'patient' => $patient,
                    'registrasiTerakhir' => null,
                    'riwayatRegistrasi' => collect(),
                    'pelayananPasienTerakhir' => collect(),
                    'errorApi' => $riwayat['message'],
                    'errorResepTerakhir' => null,
                ]
            );
        }

        /*
    |--------------------------------------------------------------------------
    | Urutkan Registrasi Terbaru
    |--------------------------------------------------------------------------
    */

        $registrasi = collect(
            data_get(
                $riwayat,
                'response.data',
                []
            )
        )
            ->sortByDesc(function ($item) {

                $tanggal = data_get(
                    $item,
                    'tglregistrasi',
                    data_get(
                        $item,
                        'tanggalregistrasi',
                        data_get(
                            $item,
                            'tanggalreservasi',
                            data_get($item, 'tglreservasi')
                        )
                    )
                );

                if (!$tanggal) {
                    return 0;
                }

                try {
                    return Carbon::parse(
                        $tanggal
                    )->timestamp;
                } catch (\Throwable $e) {
                    return 0;
                }
            })
            ->values();

        /*
    |--------------------------------------------------------------------------
    | Registrasi Terakhir dan Riwayat
    |--------------------------------------------------------------------------
    */

        $registrasiTerakhir = $registrasi->first();

        // Registrasi pertama jangan ikut masuk ke riwayat
        $riwayatRegistrasi = $registrasi
            ->slice(1)
            ->values();

        /*
    |--------------------------------------------------------------------------
    | Ambil Aturan Obat Registrasi Terakhir
    |--------------------------------------------------------------------------
    */

        $pelayananPasienTerakhir = collect();

        $errorResepTerakhir = null;

        if ($registrasiTerakhir) {

            $noRegistrasi = trim(
                (string) data_get(
                    $registrasiTerakhir,
                    'noregistrasi',
                    ''
                )
            );

            if ($noRegistrasi !== '') {

                $result = $this->getDetailResepByRegistrasi(
                    $noRegistrasi
                );

                if ($result['success']) {

                    $response = $result['response'];

                    $pelayananPasienTerakhir = collect(
                        data_get(
                            $response,
                            'pelayananPasien',
                            []
                        )
                    )
                        ->groupBy(function ($item) {

                            return (string) data_get(
                                $item,
                                'norec_resep',
                                data_get(
                                    $item,
                                    'noresep',
                                    'LAINNYA'
                                )
                            );
                        });
                } else {

                    $errorResepTerakhir =
                        $result['message'];
                }
            }
        }

        return view(
            'aturan-minum.index',
            [
                'patient' =>
                $patient,

                'registrasiTerakhir' =>
                $registrasiTerakhir,

                'riwayatRegistrasi' =>
                $riwayatRegistrasi,

                'pelayananPasienTerakhir' =>
                $pelayananPasienTerakhir,

                'errorApi' =>
                null,

                'errorResepTerakhir' =>
                $errorResepTerakhir,
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | DETAIL REGISTRASI
    |--------------------------------------------------------------------------
    |
    | Untuk sementara mengambil kembali registrasi pasien dan mencari
    | berdasarkan nomor reservasi.
    |
    | Nanti pada fungsi ini kita tambahkan API resep / aturan pakai obat.
    |
    */



    /*
    |--------------------------------------------------------------------------
    | API RIWAYAT REGISTRASI
    |--------------------------------------------------------------------------
    */
    private function getRiwayatRegistrasi(
        string $rm,
        string $tanggalLahir
    ): array {
        $baseUrl = rtrim(
            (string) config(
                'services.queue_api.base_url'
            ),
            '/'
        );

        $token = (string) config(
            'services.queue_api.token'
        );

        $secret = (string) config(
            'services.queue_api.secret'
        );

        if (
            $baseUrl === ''
            || $token === ''
            || $secret === ''
        ) {
            return [
                'success' => false,
                'message' =>
                'Konfigurasi API belum lengkap.',
                'response' => null,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Query
        |--------------------------------------------------------------------------
        |
        | Query diurutkan agar URI yang ditandatangani sama persis
        | dengan URI yang dikirim.
        |
        */
        $query = [
            'rm' => $rm,
            'tanggal_lahir' => $tanggalLahir,
        ];

        ksort($query);

        $queryString = http_build_query(
            $query,
            '',
            '&',
            PHP_QUERY_RFC3986
        );

        $path = '/api/service/listriwayatregistrasipasien';

        $canonicalUri =
            $path . '?' . $queryString;

        $url =
            $baseUrl . $canonicalUri;

        /*
        |--------------------------------------------------------------------------
        | Signature
        |--------------------------------------------------------------------------
        |
        | Payload:
        |
        | METHOD
        | URI
        | TIMESTAMP
        | SHA256 BODY
        |
        */
        $timestamp = (string) time();

        $bodyHash = hash(
            'sha256',
            ''
        );

        $payload =
            "GET\n"
            . $canonicalUri
            . "\n"
            . $timestamp
            . "\n"
            . $bodyHash;

        $signature = hash_hmac(
            'sha256',
            $payload,
            $secret
        );

        /*
        |--------------------------------------------------------------------------
        | CURL
        |--------------------------------------------------------------------------
        */
        $curl = curl_init();

        curl_setopt_array(
            $curl,
            [
                CURLOPT_URL => $url,

                CURLOPT_RETURNTRANSFER => true,

                CURLOPT_ENCODING => '',

                CURLOPT_MAXREDIRS => 10,

                CURLOPT_TIMEOUT => 20,

                CURLOPT_CONNECTTIMEOUT => 10,

                CURLOPT_FOLLOWLOCATION => true,

                CURLOPT_HTTP_VERSION =>
                CURL_HTTP_VERSION_1_1,

                CURLOPT_CUSTOMREQUEST => 'GET',

                CURLOPT_HTTPHEADER => [
                    'Accept: application/json',

                    'X-Token: ' . $token,

                    'X-Timestamp: ' . $timestamp,

                    'X-Signature: ' . $signature,
                ],
            ]
        );

        $response = curl_exec($curl);

        $curlError = curl_error($curl);

        $httpCode = (int) curl_getinfo(
            $curl,
            CURLINFO_HTTP_CODE
        );

        curl_close($curl);

        if ($response === false) {
            return [
                'success' => false,
                'message' =>
                'Gagal menghubungi API: '
                    . $curlError,
                'response' => null,
            ];
        }

        $json = json_decode(
            $response,
            true
        );

        if (!is_array($json)) {
            return [
                'success' => false,
                'message' =>
                'Respons API bukan JSON yang valid.',
                'response' => null,
            ];
        }

        if (
            $httpCode < 200
            || $httpCode >= 300
        ) {
            return [
                'success' => false,

                'message' =>
                data_get(
                    $json,
                    'message',
                    data_get(
                        $json,
                        'metaData.message',
                        'API mengembalikan HTTP '
                            . $httpCode
                    )
                ),

                'response' => $json,
            ];
        }

        return [
            'success' => true,
            'message' => 'OK',
            'response' => $json,
        ];
    }
    public function show(
        Request $request,
        $noregistrasi
    ) {
        $patient = session('pasien', []);

        $rm = trim((string) data_get(
            $patient,
            'medical_record',
            data_get(
                $patient,
                'rm',
                data_get($patient, 'nocm', '')
            )
        ));

        $tanggalLahir = data_get(
            $patient,
            'tanggal_lahir',
            data_get(
                $patient,
                'birth_date',
                data_get(
                    $patient,
                    'tgllahir',
                    data_get($patient, 'date_of_birth')
                )
            )
        );

        if (
            $rm === ''
            || !$tanggalLahir
        ) {
            return redirect('/')
                ->withErrors([
                    'validasi' =>
                    'Data pasien pada session tidak lengkap.',
                ]);
        }

        try {
            $tanggalLahir = Carbon::parse(
                $tanggalLahir
            )->format('Y-m-d');
        } catch (\Throwable $e) {
            return redirect('/')
                ->withErrors([
                    'validasi' =>
                    'Format tanggal lahir pasien tidak valid.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Validasi Registrasi Milik Pasien
        |--------------------------------------------------------------------------
        */
        $riwayat = $this->getRiwayatRegistrasi(
            $rm,
            $tanggalLahir
        );

        if (!$riwayat['success']) {
            return redirect()
                ->route('aturan-minum.index')
                ->withErrors([
                    'api' => $riwayat['message'],
                ]);
        }

        $registrasi = collect(
            data_get(
                $riwayat,
                'response.data',
                []
            )
        )->first(function ($item) use ($noregistrasi) {

            return (string) data_get(
                $item,
                'noregistrasi'
            ) === (string) $noregistrasi;
        });

        if (!$registrasi) {
            abort(
                404,
                'Registrasi pasien tidak ditemukan.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Ambil Detail Resep
        |--------------------------------------------------------------------------
        */
        $result = $this->getDetailResepByRegistrasi(
            (string) $noregistrasi
        );

        if (!$result['success']) {
            return view(
                'aturan-minum.show',
                [
                    'patient' => $patient,
                    'registrasi' => $registrasi,
                    'detailResep' => null,
                    'daftarResep' => collect(),
                    'pelayananPasien' => collect(),
                    'errorApi' => $result['message'],
                ]
            );
        }

        $response = $result['response'];

        $daftarResep = collect(
            data_get(
                $response,
                'daftarResep',
                []
            )
        );

        $pelayananPasien = collect(
            data_get(
                $response,
                'pelayananPasien',
                []
            )
        );

        /*
        |--------------------------------------------------------------------------
        | Group Obat Berdasarkan Resep
        |--------------------------------------------------------------------------
        */
        $pelayananPasien = $pelayananPasien
            ->groupBy(function ($item) {
                return (string) data_get(
                    $item,
                    'norec_resep',
                    data_get(
                        $item,
                        'noresep',
                        'LAINNYA'
                    )
                );
            });

        return view(
            'aturan-minum.show',
            [
                'patient' => $patient,
                'registrasi' => $registrasi,
                'detailResep' => data_get(
                    $response,
                    'detailresep'
                ),
                'daftarResep' => $daftarResep,
                'pelayananPasien' => $pelayananPasien,
                'errorApi' => null,
            ]
        );
    }


    private function getDetailResepByRegistrasi(
        string $noregistrasi
    ): array {
        $baseUrl = rtrim(
            (string) config(
                'services.queue_api.base_url'
            ),
            '/'
        );

        $token = (string) config(
            'services.queue_api.token'
        );

        $secret = (string) config(
            'services.queue_api.secret'
        );

        if (
            $baseUrl === ''
            || $token === ''
            || $secret === ''
        ) {
            return [
                'success' => false,
                'message' =>
                'Konfigurasi API belum lengkap.',
                'response' => null,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Query
        |--------------------------------------------------------------------------
        */
        $query = [
            'noregistrasi' => $noregistrasi,
        ];

        ksort($query);

        $queryString = http_build_query(
            $query,
            '',
            '&',
            PHP_QUERY_RFC3986
        );

        $path =
            '/api/service/getdetailresep';

        $canonicalUri =
            $path . '?' . $queryString;

        $url =
            $baseUrl . $canonicalUri;

        /*
        |--------------------------------------------------------------------------
        | Signature
        |--------------------------------------------------------------------------
        */
        $timestamp = (string) time();

        $bodyHash = hash(
            'sha256',
            ''
        );

        $payload =
            "GET\n"
            . $canonicalUri
            . "\n"
            . $timestamp
            . "\n"
            . $bodyHash;

        $signature = hash_hmac(
            'sha256',
            $payload,
            $secret
        );

        /*
        |--------------------------------------------------------------------------
        | CURL
        |--------------------------------------------------------------------------
        */
        $curl = curl_init();

        curl_setopt_array(
            $curl,
            [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 20,
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION =>
                CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => [
                    'Accept: application/json',
                    'X-Token: ' . $token,
                    'X-Timestamp: ' . $timestamp,
                    'X-Signature: ' . $signature,
                ],
            ]
        );

        $response = curl_exec($curl);

        $curlError = curl_error($curl);

        $httpCode = (int) curl_getinfo(
            $curl,
            CURLINFO_HTTP_CODE
        );

        curl_close($curl);

        if ($response === false) {
            return [
                'success' => false,
                'message' =>
                'Gagal menghubungi API resep: '
                    . $curlError,
                'response' => null,
            ];
        }

        $json = json_decode(
            $response,
            true
        );

        if (!is_array($json)) {
            return [
                'success' => false,
                'message' =>
                'Respons API resep bukan JSON yang valid.',
                'response' => null,
            ];
        }

        if (
            $httpCode < 200
            || $httpCode >= 300
        ) {
            return [
                'success' => false,
                'message' =>
                data_get(
                    $json,
                    'message',
                    'API resep mengembalikan HTTP '
                        . $httpCode
                ),
                'response' => $json,
            ];
        }

        return [
            'success' => true,
            'message' => 'OK',
            'response' => $json,
        ];
    }
}
