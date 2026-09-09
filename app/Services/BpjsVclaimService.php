<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use LZCompressor\LZString;

class BpjsVclaimService
{
    protected $baseUrl;
    protected $consId;
    protected $secretKey;
    protected $userKey;
    protected $timeout;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('services.bpjs.vclaim_base_url', env('BPJS_VCLAIM_BASE_URL')), '/');
        $this->consId = (string) config('services.bpjs.cons_id', env('BPJS_CONS_ID'));
        $this->secretKey = (string) config('services.bpjs.secret_key', env('BPJS_SECRET_KEY'));
        $this->userKey = (string) config('services.bpjs.user_key', env('BPJS_USER_KEY'));
        $this->timeout = (int) config('services.bpjs.timeout', 15);
    }

    public function cekSuratKontrolReservasi($noKartu, $tanggalReservasi, $kodePoli, $noSuratKontrol = null)
    {
        $noKartu = $this->normalizeCard($noKartu);
        $tanggal = $this->normalizeDate($tanggalReservasi);
        $kodePoli = $this->normalizePoli($kodePoli);
        $noSuratKontrol = trim((string) $noSuratKontrol);

        if ($noKartu === '' || $tanggal === '' || $kodePoli === '') {
            return [
                'success' => false,
                'found' => false,
                'match' => false,
                'source' => null,
                'message' => 'No kartu, tanggal reservasi, dan kode poli wajib tersedia.',
                'data' => null,
            ];
        }

        // Cara 2: cek langsung dengan nomor Surat Kontrol.
        if ($noSuratKontrol !== '') {
            $bySurat = $this->cekSuratKontrolByNomor(
                $noSuratKontrol,
                $tanggal,
                $kodePoli,
                $noKartu
            );

            if (!empty($bySurat['found']) && !empty($bySurat['match'])) {
                $bySurat['source'] = 'no_surat_kontrol';
                return $bySurat;
            }
        }

        // Cara 1: cari berdasarkan nomor kartu pada tanggal reservasi,
        // lalu cocokkan kode poli BPJS.
        $byKartu = $this->cekSuratKontrolByNoKartu(
            $noKartu,
            $tanggal,
            $kodePoli
        );

        $byKartu['source'] = 'no_kartu';

        if (!empty($byKartu['found']) && $noSuratKontrol !== '') {
            $foundNoSurat = (string) data_get($byKartu, 'data.noSuratKontrol', '');

            if ($foundNoSurat !== '' && strcasecmp($foundNoSurat, $noSuratKontrol) !== 0) {
                $byKartu['warning'] =
                    'Nomor surat kontrol pada reservasi berbeda dengan surat kontrol BPJS yang cocok berdasarkan no kartu, tanggal, dan poli.';
            }
        }

        return $byKartu;
    }

    public function cekSuratKontrolByNoKartu($noKartu, $tanggalReservasi, $kodePoli)
    {
        $noKartu = $this->normalizeCard($noKartu);
        $tanggal = $this->normalizeDate($tanggalReservasi);
        $kodePoli = $this->normalizePoli($kodePoli);

        if ($noKartu === '' || $tanggal === '' || $kodePoli === '') {
            return [
                'success' => false,
                'found' => false,
                'match' => false,
                'message' => 'Parameter pencarian surat kontrol tidak lengkap.',
                'data' => null,
                'matches' => [],
            ];
        }

        // filter = 1 => berdasarkan tanggal rencana kontrol
        $result = $this->listRencanaKontrol($tanggal, $tanggal, 1);

        if (empty($result['success'])) {
            return [
                'success' => false,
                'found' => false,
                'match' => false,
                'message' => $result['message'] ?? 'Gagal mengambil list rencana kontrol.',
                'data' => null,
                'matches' => [],
                'bpjs' => $result['bpjs'] ?? null,
            ];
        }

        $list = $this->extractList($result['data']);

        $matches = array_values(array_filter($list, function ($item) use ($noKartu, $tanggal, $kodePoli) {
            if (!is_array($item)) {
                return false;
            }

            $itemNoKartu = $this->normalizeCard(
                $this->firstValue($item, [
                    'noKartu',
                    'nokartu',
                    'no_kartu',
                    'peserta.noKartu',
                ])
            );

            $itemTanggal = $this->normalizeDate(
                $this->firstValue($item, [
                    'tglRencanaKontrol',
                    'tglKontrol',
                    'tanggalRencanaKontrol',
                    'tanggalKontrol',
                ])
            );

            $itemPoli = $this->normalizePoli(
                $this->firstValue($item, [
                    'poliTujuan',
                    'kodePoli',
                    'kodePoliTujuan',
                    'poli.kode',
                    'poliTujuan.kode',
                ])
            );

            $jnsKontrol = trim((string) $this->firstValue($item, [
                'jnsKontrol',
                'jenisKontrol',
            ], ''));

            $jenisSesuai = ($jnsKontrol === '' || $jnsKontrol === '2');

            return $jenisSesuai
                && $itemNoKartu === $noKartu
                && $itemTanggal === $tanggal
                && $itemPoli === $kodePoli;
        }));

        if (empty($matches)) {
            return [
                'success' => true,
                'found' => false,
                'match' => false,
                'message' => 'Surat kontrol BPJS tidak ditemukan untuk no kartu, tanggal reservasi, dan kode poli tersebut.',
                'data' => null,
                'matches' => [],
            ];
        }

        usort($matches, function ($a, $b) {
            $aDate = (string) $this->firstValue($a, [
                'tglTerbitKontrol',
                'tglTerbit',
                'createdDate',
            ], '');

            $bDate = (string) $this->firstValue($b, [
                'tglTerbitKontrol',
                'tglTerbit',
                'createdDate',
            ], '');

            return strcmp($bDate, $aDate);
        });

        return [
            'success' => true,
            'found' => true,
            'match' => true,
            'message' => 'Surat kontrol BPJS ditemukan dan sesuai dengan reservasi.',
            'data' => $matches[0],
            'matches' => $matches,
        ];
    }

    public function cekSuratKontrolByNomor($noSuratKontrol, $tanggalReservasi = null, $kodePoli = null, $noKartu = null)
    {
        $noSuratKontrol = trim((string) $noSuratKontrol);

        if ($noSuratKontrol === '') {
            return [
                'success' => false,
                'found' => false,
                'match' => false,
                'message' => 'Nomor surat kontrol kosong.',
                'data' => null,
            ];
        }

        $result = $this->get(
            'RencanaKontrol/noSuratKontrol/' . rawurlencode($noSuratKontrol)
        );

        if (empty($result['success'])) {
            return [
                'success' => false,
                'found' => false,
                'match' => false,
                'message' => $result['message'] ?? 'Surat kontrol tidak ditemukan.',
                'data' => null,
                'bpjs' => $result['bpjs'] ?? null,
            ];
        }

        $data = $result['data'];

        if (isset($data['rencanaKontrol']) && is_array($data['rencanaKontrol'])) {
            $data = $data['rencanaKontrol'];
        }

        if (!is_array($data) || empty($data)) {
            return [
                'success' => true,
                'found' => false,
                'match' => false,
                'message' => 'Data surat kontrol BPJS kosong.',
                'data' => null,
            ];
        }

        $checks = [];

        if ($noKartu !== null && trim((string) $noKartu) !== '') {
            $expected = $this->normalizeCard($noKartu);
            $actual = $this->normalizeCard(
                $this->firstValue($data, [
                    'noKartu',
                    'nokartu',
                    'no_kartu',
                    'peserta.noKartu',
                ])
            );

            $checks['noKartu'] = [
                'expected' => $expected,
                'actual' => $actual,
                'match' => $expected !== '' && $actual === $expected,
            ];
        }

        if ($tanggalReservasi !== null && trim((string) $tanggalReservasi) !== '') {
            $expected = $this->normalizeDate($tanggalReservasi);
            $actual = $this->normalizeDate(
                $this->firstValue($data, [
                    'tglRencanaKontrol',
                    'tglKontrol',
                    'tanggalRencanaKontrol',
                ])
            );

            $checks['tanggal'] = [
                'expected' => $expected,
                'actual' => $actual,
                'match' => $expected !== '' && $actual === $expected,
            ];
        }

        if ($kodePoli !== null && trim((string) $kodePoli) !== '') {
            $expected = $this->normalizePoli($kodePoli);
            $actual = $this->normalizePoli(
                $this->firstValue($data, [
                    'poliTujuan',
                    'kodePoli',
                    'kodePoliTujuan',
                    'poli.kode',
                    'poliTujuan.kode',
                ])
            );

            $checks['kodePoli'] = [
                'expected' => $expected,
                'actual' => $actual,
                'match' => $expected !== '' && $actual === $expected,
            ];
        }

        $match = true;

        foreach ($checks as $check) {
            if (empty($check['match'])) {
                $match = false;
                break;
            }
        }

        return [
            'success' => true,
            'found' => true,
            'match' => $match,
            'message' => $match
                ? 'Surat kontrol BPJS ditemukan dan sesuai dengan reservasi.'
                : 'Surat kontrol BPJS ditemukan, tetapi terdapat data yang tidak sesuai dengan reservasi.',
            'data' => $data,
            'checks' => $checks,
        ];
    }

    public function listRencanaKontrol($tglAwal, $tglAkhir, $filter = 1)
    {
        $tglAwal = $this->normalizeDate($tglAwal);
        $tglAkhir = $this->normalizeDate($tglAkhir);
        $filter = ((int) $filter === 2) ? 2 : 1;

        if ($tglAwal === '' || $tglAkhir === '') {
            return [
                'success' => false,
                'message' => 'Tanggal awal/akhir tidak valid.',
                'data' => null,
            ];
        }

        $endpoint = sprintf(
            'RencanaKontrol/ListRencanaKontrol/tglAwal/%s/tglAkhir/%s/filter/%d',
            rawurlencode($tglAwal),
            rawurlencode($tglAkhir),
            $filter
        );

        return $this->get($endpoint);
    }

    protected function get($endpoint)
    {
        if (
            $this->baseUrl === '' ||
            $this->consId === '' ||
            $this->secretKey === '' ||
            $this->userKey === ''
        ) {
            return [
                'success' => false,
                'message' => 'Konfigurasi BPJS VClaim belum lengkap.',
                'data' => null,
            ];
        }

        $timestamp = (string) time();

        $signature = base64_encode(
            hash_hmac(
                'sha256',
                $this->consId . '&' . $timestamp,
                $this->secretKey,
                true
            )
        );

        $url = $this->baseUrl . '/' . ltrim($endpoint, '/');

        try {
            $response = Http::timeout($this->timeout)
                ->acceptJson()
                ->withHeaders([
                    'X-cons-id' => $this->consId,
                    'X-timestamp' => $timestamp,
                    'X-signature' => $signature,
                    'user_key' => $this->userKey,
                ])
                ->get($url);

            $body = $response->json();

            if (!is_array($body)) {
                return [
                    'success' => false,
                    'message' => 'Response BPJS tidak berupa JSON yang valid.',
                    'data' => null,
                    'http_status' => $response->status(),
                ];
            }

            $meta = $body['metaData'] ?? $body['metadata'] ?? [];
            $code = (string) ($meta['code'] ?? '');
            $message = (string) ($meta['message'] ?? 'Response BPJS tidak memiliki pesan.');

            if ($code !== '200') {
                return [
                    'success' => false,
                    'message' => $message,
                    'data' => null,
                    'bpjs' => [
                        'code' => $code,
                        'message' => $message,
                    ],
                    'http_status' => $response->status(),
                ];
            }

            $encrypted = $body['response'] ?? null;

            if (is_array($encrypted)) {
                return [
                    'success' => true,
                    'message' => $message,
                    'data' => $encrypted,
                    'bpjs' => [
                        'code' => $code,
                        'message' => $message,
                    ],
                ];
            }

            if ($encrypted === null || $encrypted === '') {
                return [
                    'success' => true,
                    'message' => $message,
                    'data' => [],
                    'bpjs' => [
                        'code' => $code,
                        'message' => $message,
                    ],
                ];
            }

            $decoded = $this->decryptResponse((string) $encrypted, $timestamp);

            if ($decoded === null) {
                return [
                    'success' => false,
                    'message' => 'Response BPJS diterima tetapi gagal didekripsi/dekompresi.',
                    'data' => null,
                    'bpjs' => [
                        'code' => $code,
                        'message' => $message,
                    ],
                ];
            }

            return [
                'success' => true,
                'message' => $message,
                'data' => $decoded,
                'bpjs' => [
                    'code' => $code,
                    'message' => $message,
                ],
            ];
        } catch (\Throwable $e) {
            Log::warning('BPJS VClaim request gagal', [
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Tidak dapat terhubung ke BPJS VClaim: ' . $e->getMessage(),
                'data' => null,
            ];
        }
    }

    protected function decryptResponse($encryptedResponse, $timestamp)
    {
        try {
            $key = $this->consId . $this->secretKey . $timestamp;

            $keyHash = hex2bin(hash('sha256', $key));
            $iv = substr(hex2bin(hash('sha256', $key)), 0, 16);

            $decrypted = openssl_decrypt(
                base64_decode($encryptedResponse),
                'AES-256-CBC',
                $keyHash,
                OPENSSL_RAW_DATA,
                $iv
            );

            if ($decrypted === false || $decrypted === '') {
                return null;
            }

            $decompressed = LZString::decompressFromEncodedURIComponent($decrypted);

            if ($decompressed === null || $decompressed === '') {
                return null;
            }

            $json = json_decode($decompressed, true);

            return is_array($json) ? $json : null;
        } catch (\Throwable $e) {
            Log::warning('Decrypt response BPJS VClaim gagal', [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    protected function extractList($data)
    {
        if (!is_array($data)) {
            return [];
        }

        foreach ([
            data_get($data, 'list'),
            data_get($data, 'List'),
            data_get($data, 'rencanaKontrol'),
            data_get($data, 'listRencanaKontrol'),
            data_get($data, 'response.list'),
        ] as $candidate) {
            if (is_array($candidate)) {
                return array_values($candidate);
            }
        }

        if ($this->isListArray($data)) {
            return array_values($data);
        }

        return [];
    }

    protected function firstValue(array $data, array $keys, $default = null)
    {
        foreach ($keys as $key) {
            $value = data_get($data, $key);

            if ($value !== null && $value !== '') {
                return $value;
            }
        }

        return $default;
    }

    protected function normalizeCard($value)
    {
        return preg_replace('/\D+/', '', (string) $value);
    }

    protected function normalizePoli($value)
    {
        return strtoupper(trim((string) $value));
    }

    protected function normalizeDate($value)
    {
        if ($value === null || trim((string) $value) === '') {
            return '';
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable $e) {
            return '';
        }
    }

    protected function isListArray(array $array)
    {
        if ($array === []) {
            return true;
        }

        return array_keys($array) === range(0, count($array) - 1);
    }
}
