<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Throwable;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        $sessionPatient = session('pasien', []);

        /*
        |--------------------------------------------------------------------------
        | Ambil data pasien dari request / session
        |--------------------------------------------------------------------------
        */
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

        /*
        |--------------------------------------------------------------------------
        | Validasi
        |--------------------------------------------------------------------------
        */
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

        if (
            empty($validated['rm']) ||
            empty($validated['tanggal_lahir'])
        ) {
            return redirect()
                ->route('layanan.menu')
                ->withErrors([
                    'validasi' =>
                    'Data pasien tidak ditemukan. '
                        . 'Silakan masuk kembali ke SAPA RSBM.',
                ]);
        }

        // try {

        /*
        |--------------------------------------------------------------------------
        | Konfigurasi API
        |--------------------------------------------------------------------------
        */
        $baseUrl = rtrim(
            env('API_BASE_URL', ''),
            '/'
        );

        $token = (string) config(
            'api_simrs.token'
        );

        $secret = env(
            'API_SECRET',
            ''
        );

        $timestamp = (string) time();


        $params = [
            'rm' => $validated['rm'],
            'tglLahir' => $validated['tanggal_lahir'],
        ];

   

        $queryString = http_build_query(
            $params,
            '',
            '&',
            PHP_QUERY_RFC3986
        );

        $canonicalUri =
            '/api/service/getriwayatreservasi'
            . '?'
            . $queryString;


        $bodyHash = hash(
            'sha256',
            ''
        );


        $payload = implode("\n", [
            'GET',
            $canonicalUri,
            $timestamp,
            $bodyHash,
        ]);


        $signature = hash_hmac(
            'sha256',
            $payload,
            $secret
        );

    
        $response = Http::withHeaders([
            'X-Token' => $token,
            'X-Timestamp' => $timestamp,
            'X-Signature' => $signature,
            'Accept' => 'application/json',
        ])
            ->timeout(30)
            ->get(
                $baseUrl . $canonicalUri
            );


        if (! $response->successful()) {

            $message = data_get(
                $response->json(),
                'message',
                'API riwayat reservasi gagal diakses.'
            );

            throw new \RuntimeException(
                $message
                    . ' HTTP Status: '
                    . $response->status()
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Decode Response
        |--------------------------------------------------------------------------
        */

        $result = $response->json();

        /*
        |--------------------------------------------------------------------------
        | Normalisasi Data Reservasi
        |--------------------------------------------------------------------------
        */

        $reservations = collect(
            data_get($result, 'data', [])
        )
            ->sortByDesc(function ($item) {
                return data_get(
                    $item,
                    'tanggalreservasi',
                    ''
                );
            })
            ->values();

        $total = $reservations->count();

        /*
        |--------------------------------------------------------------------------
        | Data Pasien
        |--------------------------------------------------------------------------
        */

        $firstReservation = $reservations->first();

        $patientInfo = [
            'medical_record' =>
            $validated['rm'],

            'tanggal_lahir' =>
            $validated['tanggal_lahir'],

            'nama' => data_get(
                $firstReservation,
                'namapasien',
                data_get(
                    $sessionPatient,
                    'name',
                    ''
                )
            ),

            'no_bpjs' => data_get(
                $firstReservation,
                'nobpjs',
                data_get(
                    $sessionPatient,
                    'bpjs_number',
                    ''
                )
            ),

            'jenis_kelamin' => data_get(
                $firstReservation,
                'jeniskelamin',
                data_get(
                    $sessionPatient,
                    'gender',
                    ''
                )
            ),
        ];

        /*
        |--------------------------------------------------------------------------
        | Return View
        |--------------------------------------------------------------------------
        */

        return view(
            'reservation.index',
            compact(
                'reservations',
                'total',
                'patientInfo'
            )
        );
        // } catch (Throwable $e) {

        //     report($e);

        //     return redirect()
        //         ->route('layanan.menu')
        //         ->withErrors([
        //             'validasi' =>
        //             'Gagal mengambil riwayat reservasi. '
        //                 . $e->getMessage(),
        //         ]);
        // }
    }
}
