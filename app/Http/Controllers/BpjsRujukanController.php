<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class BpjsRujukanController extends Controller
{
    public function check(Request $request)
    {
        $patient = session('pasien', []);
        if (!$patient || !data_get($patient, 'medical_record')) {
            return response()->json(['success' => false, 'message' => 'Sesi pasien telah berakhir. Silakan masuk kembali.'], 401);
        }

        $reservation = data_get($patient, 'next_appointment') ?: data_get($patient, 'reservation_raw', []);
        $card = trim((string) (data_get($reservation, 'nobpjs') ?: data_get($patient, 'reservation_raw.nobpjs') ?: data_get($patient, 'bpjs_number') ?: data_get($patient, 'no_bpjs')));
        $reference = trim((string) (data_get($reservation, 'norujukan') ?: data_get($patient, 'reservation_raw.norujukan')));
        if (!preg_match('/^[0-9]{10,20}$/', $card)) {
            return response()->json(['success' => false, 'message' => 'Nomor kartu BPJS belum tersedia atau tidak valid.'], 422);
        }

        $consId = trim((string) config('services.bpjs_vclaim.cons_id', env('BPJS_CONS_ID', '')));
        $secret = (string) config('services.bpjs_vclaim.secret_key', env('BPJS_SECRET_KEY', ''));
        $userKey = (string) config('services.bpjs_vclaim.user_key', env('BPJS_USER_KEY', ''));
        $base = rtrim((string) config('services.bpjs_vclaim.base_url', env('BPJS_VCLAIM_BASE_URL', 'https://new-apijkn.bpjs-kesehatan.go.id/vclaim-rest')), '/');
        if (!$consId || !$secret || !$userKey) {
            return response()->json(['success' => false, 'message' => 'Konfigurasi VClaim pada server belum lengkap.'], 503);
        }

        try {
            $timestamp = (string) time();
            $headers = [
                'Accept' => 'application/json',
                'X-cons-id' => $consId,
                'X-timestamp' => $timestamp,
                'X-signature' => base64_encode(hash_hmac('sha256', $consId . '&' . $timestamp, $secret, true)),
                'user_key' => $userKey,
            ];
            // Cari daftar rujukan FKTP, dan fallback ke rujukan RS bila diperlukan.
            $result = null;
            $errors = [];
            $successfulLookup = false;
          
            foreach (['Rujukan/List/Peserta/', 'Rujukan/RS/List/Peserta/'] as $path) {
                $stage = 'request';
                try {
                    $http = Http::withHeaders($headers)
                        ->acceptJson()
                        ->withOptions([
                            'connect_timeout' => 8,
                            'timeout' => 25,
                        ])
                        ->get($base . '/' . $path . rawurlencode($card));

                    if (!$http->successful()) {
                        $detail = 'HTTP ' . $http->status();
                        $errors[] = $path . ': ' . $detail;
                        Log::warning('NADI BPJS Rujukan HTTP gagal', [
                            'endpoint' => $path,
                            'http_status' => $http->status(),
                            'metadata' => data_get($http->json(), 'metaData'),
                        ]);
                        continue;
                    }

                    $stage = 'json';
                    $body = $http->json();
                    if (!is_array($body)) {
                        throw new \RuntimeException('Respons bukan JSON yang valid.');
                    }
                    $code = (int) data_get($body, 'metaData.code', 0);
                    if ($code !== 200) {
                        $detail = 'BPJS ' . $code . ': ' . data_get($body, 'metaData.message', 'Permintaan ditolak');
                        $errors[] = $path . ': ' . $detail;
                        Log::warning('NADI BPJS Rujukan ditolak', [
                            'endpoint' => $path, 'bpjs_code' => $code,
                            'bpjs_message' => data_get($body, 'metaData.message'),
                        ]);
                        continue;
                    }
                    $stage = 'decrypt';
                    $payload = data_get($body, 'response');
                    $decoded = is_array($payload) ? $payload : $this->decodeResponse((string) $payload, $consId . $secret . $timestamp);
                    $stage = 'parse';
                    $list = data_get($decoded, 'rujukan', []);
                    if (isset($list['noKunjungan'])) $list = [$list];
                    if (!is_array($list)) throw new \RuntimeException('Format daftar rujukan tidak dikenali.');
                    $successfulLookup = true;
                    if ($reference !== '') {
                        foreach ($list as $item) {
                            if ((string) data_get($item, 'noKunjungan') === $reference) {
                                $result = $item;
                                break;
                            }
                        }
                    } else {
                        $result = collect($list)->sortByDesc('tglKunjungan')->first();
                    }
                    if ($result) break;
                } catch (Throwable $endpointError) {
                    $errors[] = $path . ' (' . $stage . '): ' . $endpointError->getMessage();
                    Log::error('NADI BPJS Rujukan gagal di tahap ' . $stage, [
                        'endpoint' => $path,
                        'exception' => get_class($endpointError),
                        'message' => $endpointError->getMessage(),
                        // Jangan log nomor kartu, signature, secret, atau payload pasien.
                    ]);
                }
            }

            if (!$result && !$successfulLookup) {
                return response()->json([
                    'success' => false,
                    'message' => 'Layanan rujukan BPJS gagal diproses. Silakan hubungi petugas atau coba lagi.',
                    'error_stage' => 'vclaim',
                    // Detail hanya muncul di lingkungan local untuk debugging.
                    'debug' => app()->environment('local') ? $errors : null,
                ], 502);
            }

            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => $reference ? 'Nomor rujukan reservasi tidak ditemukan pada daftar rujukan kartu BPJS.' : 'Tidak ditemukan rujukan pada kartu BPJS ini.',
                ], 404);
            }

            $noRujukan = (string) data_get($result, 'noKunjungan', '');
            $visit = data_get($result, 'tglKunjungan');
            // VClaim daftar rujukan biasanya memberi tglKunjungan, bukan tanggal akhir.
            // Gunakan tanggal akhir hanya bila telah tersedia dari API SIMRS untuk rujukan YANG SAMA.
            $sameReference = $reference !== '' && $reference === $noRujukan;
            $expiry = $sameReference ? data_get($reservation, 'tanggal_akhir_rujukan', data_get($patient, 'reservation_raw.tanggal_akhir_rujukan')) : null;
            $reservationDate = data_get($reservation, 'tanggalreservasi') ?: data_get($patient, 'reservation_raw.tanggalreservasi');
            $remaining = null;
            $status = 'BELUM TERSEDIA';
            $reservationStatus = null;
            if ($expiry) {
                $expiryDate = Carbon::parse($expiry)->startOfDay();
                $remaining = (int) Carbon::today()->diffInDays($expiryDate, false);
                $status = $remaining >= 0 ? 'AKTIF' : 'KEDALUWARSA';
                if ($reservationDate) {
                    $atDate = Carbon::parse($reservationDate)->startOfDay();
                    $reservationStatus = $atDate->lte($expiryDate) && (!$visit || $atDate->gte(Carbon::parse($visit)->startOfDay())) ? 'BERLAKU' : 'TIDAK BERLAKU';
                }
            }

            return response()->json([
                'success' => true,
                'message' => !$expiry ? 'Rujukan ditemukan di BPJS. Tanggal akhir berlaku belum tersedia dari API SIMRS.' : 'Rujukan berhasil diverifikasi berdasarkan nomor kartu BPJS.',
                'data' => [
                    'norujukan' => $noRujukan,
                    'tanggal_kunjungan_rujukan' => $visit,
                    'tanggal_akhir_rujukan' => $expiry,
                    'sisa_hari_rujukan' => $remaining,
                    'status_rujukan' => $status,
                    'status_rujukan_saat_reservasi' => $reservationStatus,
                ],
            ])->header('Cache-Control', 'no-store');
        } catch (Throwable $e) {
            Log::error('NADI BPJS Rujukan: kesalahan di luar pemanggilan endpoint', [
                'exception' => get_class($e), 'message' => $e->getMessage(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Pemeriksaan rujukan BPJS gagal. Silakan cek log aplikasi.',
                'debug' => app()->environment('local') ? $e->getMessage() : null,
            ], 502);
        }
    }

    /**
     * VClaim: base64(AES-256-CBC) -> JSON biasa atau LZString.
     * Kompresi BPJS umumnya memakai URI-safe alphabet, tetapi implementasi
     * dapat berbeda. Jangan menganggap hasil AES selalu URI-safe LZString.
     */
    private function decodeResponse(string $encrypted, string $key): array
    {
        if ($encrypted === '') {
            throw new \RuntimeException('Respons BPJS kosong.');
        }
        $binary = base64_decode($encrypted, true);
        if ($binary === false) {
            throw new \RuntimeException('Base64 respons BPJS tidak valid.');
        }
        $cipherKey = hex2bin(hash('sha256', $key));
        // VClaim AES-CBC: IV adalah 16 byte pertama dari SHA-256 key material.
        $iv = substr($cipherKey, 0, 16);
        if (strlen($binary) % 16 !== 0) {
            throw new \RuntimeException('Ciphertext BPJS bukan kelipatan 16 byte; respons terenkripsi tidak utuh.');
        }
        $plaintext = openssl_decrypt($binary, 'AES-256-CBC', $cipherKey, OPENSSL_RAW_DATA, $iv);
        if ($plaintext === false) {
            throw new \RuntimeException('Dekripsi AES VClaim gagal. Periksa timestamp/secret key.');
        }

        // Beberapa layanan mengembalikan JSON tanpa kompresi.
        $direct = json_decode(trim($plaintext), true);
        if (is_array($direct)) {
            return $direct;
        }

        // Periksa bentuk hasil AES tanpa mencatat isi data pasien.
        $shape = $this->describeDecryptedFormat($plaintext);
        // Keluaran compressToEncodedURIComponent normal hanya berisi karakter ASCII URI-safe.
        // AES yang lolos padding belum tentu menghasilkan plaintext yang valid.
        if (preg_match('/[^A-Za-z0-9+\-\$]/', str_replace(' ', '+', $plaintext))) {
            throw new \RuntimeException('Plaintext AES tidak sesuai alfabet LZ-String URI-safe (' . $shape . '). Periksa key/timestamp yang digunakan saat dekripsi dan integritas ciphertext; jangan hapus karakter tidak valid.');
        }
        $candidates = [trim($plaintext)];
        $trimmed = trim($plaintext);
        // Tambahkan kandidat hanya jika transformasi memang sesuai format input.
        if (strpos($trimmed, '%') !== false) {
            $candidates[] = rawurldecode($trimmed);
        }
        if (preg_match('/^\".*\"$/s', $trimmed)) {
            $string = json_decode($trimmed, true);
            if (is_string($string)) $candidates[] = $string;
        }
        // Whitespace di tengah string dapat muncul akibat line wrapping.
        if (preg_match('/[\r\n\t]/', $trimmed)) {
            $candidates[] = preg_replace('/[\r\n\t]/', '', $trimmed);
        }
        $errors = [];
        foreach (array_unique($candidates) as $candidate) {
            foreach ([
                'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+-$',
                'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=',
            ] as $alphabet) {
                try {
                    $value = $this->decompressLzString($candidate, $alphabet);
                    $decoded = json_decode($value, true);
                    if (is_array($decoded)) return $decoded;
                    $errors[] = 'Dekompresi belum menghasilkan JSON.';
                } catch (\Throwable $e) {
                    $errors[] = $e->getMessage();
                }
            }
        }
        // Beberapa implementasi melakukan Base64 encoding pada hasil AES.
        // Hanya dicoba jika karakter candidate konsisten dengan Base64.
        if (preg_match('/^[A-Za-z0-9+\/=\r\n]+$/', $trimmed)) {
            $inner = base64_decode($trimmed, true);
            if ($inner !== false) {
                $json = json_decode($inner, true);
                if (is_array($json)) return $json;
                foreach (['gzdecode', 'gzuncompress'] as $fn) {
                    $unpacked = @call_user_func($fn, $inner);
                    if (is_string($unpacked)) {
                        $json = json_decode($unpacked, true);
                        if (is_array($json)) return $json;
                    }
                }
            }
        }
        // Format biner gzip/zlib mungkin dipakai oleh proxy/bridge.
        foreach (['gzdecode', 'gzuncompress'] as $fn) {
            $unpacked = @call_user_func($fn, $plaintext);
            if (is_string($unpacked)) {
                $json = json_decode($unpacked, true);
                if (is_array($json)) return $json;
            }
        }
        throw new \RuntimeException(
            'Format hasil AES belum dikenali (' . $shape . '). ' .
            'LZ: ' . implode('; ', array_unique($errors))
        );
    }

    /**
     * Statistik format aman: tanpa potongan teks, nomor kartu, atau payload.
     */
    private function describeDecryptedFormat(string $value): string
    {
        $len = strlen($value);
        $uri = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+-$';
        $base64 = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=';
        $invalidUri = 0;
        $invalidB64 = 0;
        $nonAscii = 0;
        $control = 0;
        $invalidPositions = [];
        foreach (str_split($value) as $offset => $char) {
            $ord = ord($char);
            if ($ord >= 128) $nonAscii++;
            if ($ord < 32 && !in_array($char, ["\n", "\r", "\t"], true)) $control++;
            if (strpos($uri, $char) === false) {
                $invalidUri++;
                if (count($invalidPositions) < 32) $invalidPositions[] = $offset;
            }
            if (strpos($base64, $char) === false) $invalidB64++;
        }
        return 'bytes=' . $len . ', valid_utf8=' . (preg_match('//u', $value) ? 'yes' : 'no') .
            ', non_ascii=' . $nonAscii . ', control=' . $control .
            ', non_uri_chars=' . $invalidUri . ', non_base64_chars=' . $invalidB64 .
            ', invalid_offsets=' . implode(',', $invalidPositions);
    }

    /**
     * Decoder LZString 6-bit (URI-safe maupun base64) tanpa dependency.
     * Menangani code point BMP untuk teks JSON respons VClaim.
     */
    private function decompressLzString(string $input, string $alphabet): string
    {
        if ($input === '') {
            return '';
        }
        $indices = [];
        foreach (str_split(str_replace(' ', '+', $input)) as $char) {
            $index = strpos($alphabet, $char);
            if ($index === false) {
                throw new \RuntimeException('Karakter LZ-String tidak valid.');
            }
            $indices[] = $index;
        }
        if (!$indices) {
            return '';
        }
        $position = 32;
        $index = 0;
        $value = $indices[0];
        $read = static function (int $bits) use (&$position, &$index, &$value, $indices): int {
            $result = 0;
            $power = 1;
            $max = 1 << $bits;
            while ($power !== $max) {
                $result |= (($value & $position) ? 1 : 0) * $power;
                $position >>= 1;
                if ($position === 0) {
                    $position = 32;
                    $index++;
                    if (!isset($indices[$index])) {
                        throw new \RuntimeException('Payload LZ-String terpotong.');
                    }
                    $value = $indices[$index];
                }
                $power <<= 1;
            }
            return $result;
        };
        $uchar = static function (int $code): string {
            if ($code < 128) return chr($code);
            if ($code < 2048) return chr(192 | ($code >> 6)) . chr(128 | ($code & 63));
            return chr(224 | ($code >> 12)) . chr(128 | (($code >> 6) & 63)) . chr(128 | ($code & 63));
        };
        $first = $read(2);
        if ($first === 2) return '';
        if ($first !== 0 && $first !== 1) {
            throw new \RuntimeException('Header LZ-String tidak dikenal.');
        }
        $char = $uchar($read($first === 0 ? 8 : 16));
        $dictionary = [0 => '', 1 => '', 2 => '', 3 => $char];
        $dictSize = 4;
        $numBits = 3;
        $enlargeIn = 4;
        $w = $char;
        $output = $char;
        $maxIterations = max(1024, strlen($input) * 16);
        for ($iteration = 0; $iteration < $maxIterations; $iteration++) {
            $code = $read($numBits);
            if ($code === 0 || $code === 1) {
                $dictionary[$dictSize++] = $uchar($read($code === 0 ? 8 : 16));
                $code = $dictSize - 1;
                $enlargeIn--;
            } elseif ($code === 2) {
                return $output;
            }
            if ($enlargeIn === 0) {
                $enlargeIn = 1 << $numBits;
                $numBits++;
            }
            if (array_key_exists($code, $dictionary)) {
                $entry = $dictionary[$code];
            } elseif ($code === $dictSize) {
                preg_match('/^./us', $w, $match);
                $entry = $w . ($match[0] ?? '');
            } else {
                throw new \RuntimeException('Kamus LZ-String tidak valid.');
            }
            $output .= $entry;
            if (strlen($output) > 2000000) {
                throw new \RuntimeException('Hasil dekompresi terlalu besar.');
            }
            preg_match('/^./us', $entry, $match);
            $dictionary[$dictSize++] = $w . ($match[0] ?? '');
            $enlargeIn--;
            $w = $entry;
            if ($enlargeIn === 0) {
                $enlargeIn = 1 << $numBits;
                $numBits++;
            }
        }
        throw new \RuntimeException('Batas iterasi LZ-String terlampaui.');
    }
}
