<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Throwable;

class PoliWaitingApiService
{
    public function check(array $input): array
    {
        try {
            $response = $this->sendRequest($input);

            if (! $response->successful()) {
                return $this->errorResult('API antrean belum berhasil diakses.', [
                    'http_status' => $response->status(),
                ]);
            }

            return $this->normalize($response->json(), $input['keyword'] ?? '-');
        } catch (Throwable $exception) {
            return $this->errorResult($exception->getMessage());
        }
    }

    private function sendRequest(array $input)
    {
        $baseUrl = rtrim((string) config('poli_waiting.base_url'), '/');
        $endpoint = '/' . ltrim((string) config('poli_waiting.endpoint'), '/');
        $method = strtoupper((string) config('poli_waiting.method', 'GET'));
        $token = (string) config('poli_waiting.token');
        $timeout = (int) config('poli_waiting.timeout', 12);

        if ($baseUrl === '') {
            throw new RuntimeException('Base URL API antrean belum diatur di .env.');
        }

        $client = Http::timeout($timeout)->acceptJson();

        if ($token !== '') {
            $client = $client->withToken($token);
        }

        $payload = $this->buildApiPayload($input);

        if ($method === 'POST') {
            return $client->post($baseUrl . $endpoint, $payload);
        }

        return $client->get($baseUrl . $endpoint, $payload);
    }

    private function buildApiPayload(array $input): array
    {
        if (($input['check_type'] ?? 'booking') === 'medical_record') {
            return [
                'check_type' => 'medical_record',
                'jenis_pencarian' => 'no_rm_tanggal_lahir',
                'keyword' => $input['keyword'] ?? $input['no_rm'] ?? '',
                'norm' => $input['no_rm'] ?? '',
                'no_rm' => $input['no_rm'] ?? '',
                'no_rekam_medis' => $input['no_rm'] ?? '',
                'tanggal_lahir' => $input['tanggal_lahir'] ?? '',
                'tgllahir' => $input['tanggal_lahir'] ?? '',
            ];
        }

        $kodeBooking = $input['kodebooking'] ?? $input['keyword'] ?? '';

        return [
            'check_type' => 'booking',
            'jenis_pencarian' => 'kodebooking',
            'keyword' => $kodeBooking,
            'kodebooking' => $kodeBooking,
            'kode_booking' => $kodeBooking,
        ];
    }

    private function normalize(?array $body, string $keyword): array
    {
        if (! is_array($body)) {
            return $this->notFoundResult($keyword, 'Response API kosong atau bukan JSON.');
        }

        $metadataCode = data_get($body, 'metaData.code') ?? data_get($body, 'metadata.code');
        $metadataMessage = data_get($body, 'metaData.message') ?? data_get($body, 'metadata.message');

        if ($metadataCode && (int) $metadataCode !== 200) {
            return $this->notFoundResult($keyword, $metadataMessage ?: 'Data antrean tidak ditemukan.');
        }

        $data = data_get($body, 'response', data_get($body, 'data', $body));

        if (! is_array($data) || count($data) === 0) {
            return $this->notFoundResult($keyword, 'Data antrean tidak ditemukan.');
        }

        $estimatedAt = $this->parseEstimatedAt(
            data_get($data, 'estimasidilayani')
                ?? data_get($data, 'estimated_at')
                ?? data_get($data, 'estimasi_dilayani')
        );

        $waitingMinutes = data_get($data, 'waktutunggu')
            ?? data_get($data, 'waktu_tunggu')
            ?? data_get($data, 'waiting_minutes');

        if ($waitingMinutes === null && $estimatedAt) {
            $waitingMinutes = max(0, now()->diffInMinutes($estimatedAt, false));
        }

        $status = (string) (
            data_get($data, 'status')
            ?? data_get($data, 'status_antrean')
            ?? data_get($data, 'namastatus')
            ?? 'Menunggu'
        );

        return [
            'found' => true,
            'message' => $metadataMessage ?: 'OK',
            'keyword' => $keyword,
            'booking_code' => data_get($data, 'kodebooking') ?? data_get($data, 'booking_code'),
            'queue_number' => data_get($data, 'noantrean') ?? data_get($data, 'nomor_antrean') ?? data_get($data, 'queue_number'),
            'patient_name' => data_get($data, 'namapasien') ?? data_get($data, 'nama_pasien') ?? data_get($data, 'patient_name'),
            'poli_name' => data_get($data, 'namapoli') ?? data_get($data, 'nama_poli') ?? data_get($data, 'poli_name'),
            'doctor_name' => data_get($data, 'namadokter') ?? data_get($data, 'nama_dokter') ?? data_get($data, 'doctor_name'),
            'status' => $status,
            'status_class' => $this->statusClass($status),
            'patients_ahead' => (int) (
                data_get($data, 'sisaantrean')
                ?? data_get($data, 'pasien_didepan')
                ?? data_get($data, 'patients_ahead')
                ?? 0
            ),
            'waiting_minutes' => (int) max(0, (int) $waitingMinutes),
            'estimated_at' => $estimatedAt,
            'raw' => $data,
        ];
    }

    private function parseEstimatedAt($value): ?Carbon
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            if (is_numeric($value)) {
                $timestamp = (int) $value;

                if ($timestamp > 9999999999) {
                    $timestamp = (int) floor($timestamp / 1000);
                }

                return Carbon::createFromTimestamp($timestamp);
            }

            return Carbon::parse($value);
        } catch (Throwable $exception) {
            return null;
        }
    }

    private function statusClass(string $status): string
    {
        $normalized = strtolower($status);

        if (strpos($normalized, 'panggil') !== false || strpos($normalized, 'called') !== false) {
            return 'status-called';
        }

        if (strpos($normalized, 'layan') !== false || strpos($normalized, 'serving') !== false) {
            return 'status-serving';
        }

        if (strpos($normalized, 'selesai') !== false || strpos($normalized, 'done') !== false) {
            return 'status-done';
        }

        if (strpos($normalized, 'batal') !== false || strpos($normalized, 'cancel') !== false) {
            return 'status-cancelled';
        }

        return 'status-waiting';
    }

    private function notFoundResult(string $keyword, string $message): array
    {
        return [
            'found' => false,
            'keyword' => $keyword,
            'message' => $message,
        ];
    }

    private function errorResult(string $message, array $extra = []): array
    {
        return array_merge([
            'found' => false,
            'keyword' => null,
            'message' => $message,
            'is_error' => true,
        ], $extra);
    }
    public function getPatient(
        string $medicalRecord,
        string $birthDate
    ): array {
        try {
            $response = $this->sendPatientRequest(
                $medicalRecord,
                $birthDate
            );

            if (! $response->successful()) {
                return $this->patientErrorResult(
                    'API pasien belum berhasil diakses.',
                    [
                        'http_status' => $response,
                    ]
                );
            }

            return $this->normalizePatient(
                $response->json(),
                $medicalRecord,
                $birthDate
            );
        } catch (Throwable $exception) {
            return $this->patientErrorResult(
                $exception->getMessage()
            );
        }
    }
    private function sendPatientRequest(
        string $medicalRecord,
        string $birthDate
    ) {
        $baseUrl = rtrim(
            (string) config('poli_waiting.base_url'),
            '/'
        );

        $endpoint = '/' . ltrim(
            (string) config('poli_waiting.patient_endpoint'),
            '/'
        );

        $token = (string) config('poli_waiting.token');

        $timeout = (int) config(
            'poli_waiting.timeout',
            12
        );

        if ($baseUrl === '') {
            throw new RuntimeException(
                'Base URL API belum diatur di .env.'
            );
        }

        if ($token === '') {
            throw new RuntimeException(
                'Token API belum diatur di .env.'
            );
        }

        $url = $baseUrl
            . $endpoint
            . '/'
            . rawurlencode(trim($medicalRecord))
            . '/'
            . rawurlencode($birthDate);

        return Http::timeout($timeout)
            ->acceptJson()
            ->withHeaders([
                'token' => $token,
            ])
            ->get($url);
    }
    private function normalizePatient(
        ?array $body,
        string $medicalRecord,
        string $birthDate
    ): array {
        if (! is_array($body)) {
            return $this->patientNotFoundResult(
                $medicalRecord,
                'Respons API pasien kosong atau bukan JSON.'
            );
        }

        $metadataCode = data_get(
            $body,
            'metaData.code'
        ) ?? data_get(
            $body,
            'metadata.code'
        );

        $metadataMessage = data_get(
            $body,
            'metaData.message'
        ) ?? data_get(
            $body,
            'metadata.message'
        );

        if ((int) $metadataCode !== 200) {
            return $this->patientNotFoundResult(
                $medicalRecord,
                $metadataMessage
                    ?: 'Data pasien tidak ditemukan.'
            );
        }

        $patients = data_get(
            $body,
            'response.data',
            []
        );

        if (
            ! is_array($patients) ||
            count($patients) === 0
        ) {
            return $this->patientNotFoundResult(
                $medicalRecord,
                'Data pasien tidak ditemukan. Periksa kembali nomor rekam medis dan tanggal lahir.'
            );
        }

        $patient = $patients[0] ?? null;

        if (! is_array($patient)) {
            return $this->patientNotFoundResult(
                $medicalRecord,
                'Format data pasien dari API tidak sesuai.'
            );
        }

        $apiBirthDate = data_get(
            $patient,
            'tgllahir'
        );

        if (
            $apiBirthDate !== null &&
            $apiBirthDate !== '' &&
            $apiBirthDate !== $birthDate
        ) {
            return $this->patientNotFoundResult(
                $medicalRecord,
                'Tanggal lahir pasien tidak sesuai.'
            );
        }

        return [
            'found' => true,
            'message' => $metadataMessage ?: 'OK',

            'patient' => [
                'medical_record' => data_get(
                    $patient,
                    'nocm'
                ),

                'patient_id' => data_get(
                    $patient,
                    'nocmfk'
                ),

                'name' => data_get(
                    $patient,
                    'namapasien'
                ),

                'gender_id' => data_get(
                    $patient,
                    'objectjeniskelaminfk'
                ),

                'gender' => data_get(
                    $patient,
                    'jeniskelamin'
                ),

                'address' => data_get(
                    $patient,
                    'alamatlengkap'
                ),

                'education' => data_get(
                    $patient,
                    'pendidikan'
                ),

                'occupation' => data_get(
                    $patient,
                    'pekerjaan'
                ),

                'identity_number' => data_get(
                    $patient,
                    'noidentitas'
                ),

                'phone_number' => data_get(
                    $patient,
                    'notelepon'
                ),

                'birth_place' => data_get(
                    $patient,
                    'tempatlahir'
                ),

                'bpjs_number' => data_get(
                    $patient,
                    'nobpjs'
                ),

                'birth_date' => data_get(
                    $patient,
                    'tgllahir'
                ),
            ],

            'raw' => $patient,
        ];
    }
    private function patientNotFoundResult(
        string $medicalRecord,
        string $message
    ): array {
        return [
            'found' => false,
            'medical_record' => $medicalRecord,
            'message' => $message,
        ];
    }

    private function patientErrorResult(
        string $message,
        array $extra = []
    ): array {
        return array_merge([
            'found' => false,
            'medical_record' => null,
            'message' => $message,
            'is_error' => true,
        ], $extra);
    }
    public function getLaboratoryOrders(
        string $patientId,
        string $room = ''
    ): array {
        try {
            $patientId = trim($patientId);
            $room = trim($room);

            if ($patientId === '') {
                return $this->laboratoryErrorResult(
                    'ID pasien tidak tersedia.'
                );
            }

            $baseUrl = rtrim(
                (string) config('poli_waiting.base_url'),
                '/'
            );

            $endpoint = trim(
                (string) config(
                    'poli_waiting.laboratory_endpoint'
                )
            );

            $token = trim(
                (string) config('poli_waiting.token')
            );

            $timeout = (int) config(
                'poli_waiting.timeout',
                30
            );

            if ($baseUrl === '') {
                throw new RuntimeException(
                    'Base URL API belum diatur.'
                );
            }

            if ($endpoint === '') {
                throw new RuntimeException(
                    'Endpoint laboratorium belum diatur.'
                );
            }

            if ($token === '') {
                throw new RuntimeException(
                    'Token API belum diatur.'
                );
            }

            $url = $baseUrl . '/' . ltrim(
                $endpoint,
                '/'
            );

            $response = Http::timeout($timeout)
                ->acceptJson()
                ->withHeaders([
                    'token' => $token,
                ])
                ->get($url, [
                    'nocmfk' => $patientId,
                    'ruangan' => $room,
                ]);

            if (! $response->successful()) {
                logger()->error(
                    'API riwayat laboratorium gagal diakses.',
                    [
                        'http_status' => $response->status(),
                        'response_body' => $response->body(),
                        'patient_id' => $patientId,
                        'url' => $url,
                    ]
                );

                return $this->laboratoryErrorResult(
                    'API laboratorium belum berhasil diakses. HTTP '
                        . $response->status(),
                    [
                        'http_status' => $response->status(),
                    ]
                );
            }

            return $this->normalizeLaboratoryOrders(
                $response->json(),
                $patientId
            );
        } catch (Throwable $exception) {
            logger()->error(
                'Terjadi kesalahan saat mengakses API laboratorium.',
                [
                    'message' => $exception->getMessage(),
                    'patient_id' => $patientId,
                ]
            );

            return $this->laboratoryErrorResult(
                'Gagal menghubungi API laboratorium: '
                    . $exception->getMessage()
            );
        }
    }
    private function normalizeLaboratoryOrders(
        ?array $body,
        string $patientId
    ): array {
        if (! is_array($body)) {
            return $this->laboratoryErrorResult(
                'Respons API laboratorium kosong atau bukan JSON.'
            );
        }

        $metadataCode = data_get(
            $body,
            'metaData.code'
        ) ?? data_get(
            $body,
            'metadata.code'
        );

        $metadataMessage = data_get(
            $body,
            'metaData.message'
        ) ?? data_get(
            $body,
            'metadata.message'
        );

        if ((int) $metadataCode !== 200) {
            return [
                'found' => false,
                'is_error' => false,
                'patient_id' => $patientId,
                'message' => $metadataMessage
                    ?: 'Riwayat laboratorium tidak ditemukan.',
                'orders' => [],
                'total' => 0,
            ];
        }

        $orders = data_get(
            $body,
            'response',
            []
        );

        if (! is_array($orders)) {
            $orders = [];
        }

        $orders = collect($orders)
            ->map(function ($order) {
                $details = data_get(
                    $order,
                    'details',
                    []
                );

                if (! is_array($details)) {
                    $details = [];
                }

                return [
                    'order_date' => data_get(
                        $order,
                        'tglorder'
                    ),

                    'registration_number' => data_get(
                        $order,
                        'noregistrasi'
                    ),

                    'order_number' => data_get(
                        $order,
                        'noorder'
                    ),

                    'record_id' => data_get(
                        $order,
                        'norec'
                    ),

                    'registration_record_id' => data_get(
                        $order,
                        'norec_apd'
                    ),

                    'origin_room' => data_get(
                        $order,
                        'ruanganasal'
                    ),

                    'destination_room' => data_get(
                        $order,
                        'ruangantujuan'
                    ),

                    'doctor' => data_get(
                        $order,
                        'dokter'
                    ),

                    'status' => data_get(
                        $order,
                        'status',
                        '-'
                    ),

                    'status_color' => data_get(
                        $order,
                        'color_status',
                        'secondary'
                    ),

                    'origin_room_id' => data_get(
                        $order,
                        'asalruanganfk'
                    ),

                    'destination_room_id' => data_get(
                        $order,
                        'idruangantujuan'
                    ),

                    'details' => collect($details)
                        ->map(function ($detail) {
                            return [
                                'product_id' => data_get(
                                    $detail,
                                    'idproduk'
                                ),

                                'name' => data_get(
                                    $detail,
                                    'namaproduk',
                                    '-'
                                ),

                                'order_record_id' => data_get(
                                    $detail,
                                    'pp_norec'
                                ),
                            ];
                        })
                        ->values()
                        ->all(),

                    'raw' => $order,
                ];
            })
            ->sortByDesc(function ($order) {
                return data_get(
                    $order,
                    'order_date',
                    ''
                );
            })
            ->values()
            ->all();

        return [
            'found' => count($orders) > 0,
            'is_error' => false,
            'patient_id' => $patientId,
            'message' => $metadataMessage ?: 'OK',
            'orders' => $orders,
            'total' => count($orders),
        ];
    }
    private function laboratoryErrorResult(
        string $message,
        array $extra = []
    ): array {
        return array_merge([
            'found' => false,
            'is_error' => true,
            'patient_id' => null,
            'message' => $message,
            'orders' => [],
            'total' => 0,
        ], $extra);
    }
    public function getRiwayatReservasi(
    string $medicalRecord,
    string $birthDate
): array {
    try {

        $baseUrl = rtrim(
            (string) config(
                'api_simrs.base_url',
                'http://127.0.0.1:8000'
            ),
            '/'
        );

        $endpoint =
            '/api/service/getdatariwayatreservasi';

        $url = $baseUrl . $endpoint;

        $response =
            $this->sendRiwayatReservasiRequest(
                $medicalRecord,
                $birthDate
            );

        if (! $response->successful()) {

            logger()->error(
                'API riwayat reservasi gagal.',
                [
                    'url' => $url,

                    'http_status' =>
                        $response->status(),

                    'response_body' =>
                        $response->body(),

                    'rm' =>
                        $medicalRecord,

                    'tanggal_lahir' =>
                        $birthDate,
                ]
            );

            return [
                'total' => 0,
                'data' => [],
                'is_error' => true,

                'message' =>
                    'API riwayat reservasi belum berhasil diakses.',

                'http_status' =>
                    $response->status(),

                'request_url' =>
                    $url,

                'response_body' =>
                    $response->body(),
            ];
        }

        $body = $response->json();

        if (! is_array($body)) {
            return [
                'total' => 0,
                'data' => [],
                'is_error' => true,

                'message' =>
                    'Respons API riwayat reservasi tidak valid.',

                'request_url' =>
                    $url,
            ];
        }

        return $body;

    } catch (Throwable $exception) {

        logger()->error(
            'Exception API riwayat reservasi.',
            [
                'message' =>
                    $exception->getMessage(),

                'rm' =>
                    $medicalRecord,
            ]
        );

        return [
            'total' => 0,
            'data' => [],
            'is_error' => true,
            'message' =>
                $exception->getMessage(),
        ];
    }
}
   private function sendRiwayatReservasiRequest(
    string $medicalRecord,
    string $birthDate
) {
    /*
    |--------------------------------------------------------------------------
    | Base URL
    |--------------------------------------------------------------------------
    */
    $baseUrl = rtrim(
        (string) config(
            'api_simrs.base_url',
            'http://127.0.0.1:8000'
        ),
        '/'
    );

    $path =
        '/api/service/getdatariwayatreservasi';

    /*
    |--------------------------------------------------------------------------
    | Parameter GET
    |--------------------------------------------------------------------------
    |
    | URUTAN PARAMETER PENTING.
    |
    */
    $query = [
        'rm' => trim($medicalRecord),
        'tanggal_lahir' => $birthDate,
        'tgllahir' => $birthDate,
    ];

    /*
    |--------------------------------------------------------------------------
    | Query String
    |--------------------------------------------------------------------------
    */
    $queryString = http_build_query(
        $query,
        '',
        '&',
        PHP_QUERY_RFC3986
    );

    /*
    |--------------------------------------------------------------------------
    | Canonical URI
    |--------------------------------------------------------------------------
    */
    $canonicalUri =
        $path . '?' . $queryString;

    /*
    |--------------------------------------------------------------------------
    | Timestamp
    |--------------------------------------------------------------------------
    */
    $timestamp =
        (string) time();

    /*
    |--------------------------------------------------------------------------
    | GET tidak mempunyai body
    |--------------------------------------------------------------------------
    |
    | SHA256 dari string kosong:
    |
    | e3b0c44298fc1c149afbf4c8996fb924
    | 27ae41e4649b934ca495991b7852b855
    |
    */
    $bodyHash = hash(
        'sha256',
        ''
    );

    /*
    |--------------------------------------------------------------------------
    | Token
    |--------------------------------------------------------------------------
    */
    $token = trim(
        (string) config(
            'api_simrs.token'
        )
    );

    if ($token === '') {
        throw new RuntimeException(
            'API SIMRS token belum dikonfigurasi.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Signature
    |--------------------------------------------------------------------------
    */
    $signature =
        $this->generateApiSignature(
            'GET',
            $canonicalUri,
            $timestamp,
            $bodyHash
        );

    /*
    |--------------------------------------------------------------------------
    | URL final
    |--------------------------------------------------------------------------
    */
    $url =
        $baseUrl . $canonicalUri;

    /*
    |--------------------------------------------------------------------------
    | Debug sementara
    |--------------------------------------------------------------------------
    */
    logger()->info(
        'API Riwayat Reservasi Signature',
        [
            'method' =>
                'GET',

            'canonical_uri' =>
                $canonicalUri,

            'timestamp' =>
                $timestamp,

            'body_hash' =>
                $bodyHash,

            'payload' =>
                implode("\n", [
                    'GET',
                    $canonicalUri,
                    $timestamp,
                    $bodyHash,
                ]),

            'signature' =>
                $signature,

            'url' =>
                $url,
        ]
    );

    /*
    |--------------------------------------------------------------------------
    | Request
    |--------------------------------------------------------------------------
    */
    return Http::timeout(20)
        ->acceptJson()
        ->withHeaders([
            'X-Token' =>
                $token,

            'X-Timestamp' =>
                $timestamp,

            'X-Signature' =>
                $signature,
        ])
        ->get($url);
}
    private function generateApiSignature(
    string $method,
    string $canonicalUri,
    string $timestamp,
    string $bodyHash
): string {
    $secret = trim(
        (string) config(
            'api_simrs.secret'
        )
    );

    if ($secret === '') {
        throw new RuntimeException(
            'API client secret belum dikonfigurasi.'
        );
    }

    $payload = implode("\n", [
        strtoupper($method),
        $canonicalUri,
        $timestamp,
        $bodyHash,
    ]);

    return hash_hmac(
        'sha256',
        $payload,
        $secret
    );
}
}
