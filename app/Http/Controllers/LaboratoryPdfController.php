<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class LaboratoryPdfController extends Controller
{
    /**
     * GET /laboratorium/pdf?token=...             (preview)
     * GET /laboratorium/pdf?token=...&download=1  (download)
     *
     * Compatible with the token built by laboratorium_detail.blade.php:
     * {path, noorder, sid: sha256(session id), exp: unix timestamp}.
     *
     * IMPORTANT: The Blade action which creates the token must obtain the
     * laboratory order from an API response already authorized for the patient
     * in session. Do not create tokens using arbitrary user-supplied orders.
     */
    public function show(Request $request)
    {
        $patient = $request->session()->get('pasien');
        abort_unless(is_array($patient) && ! empty($patient), 403, 'Sesi pasien telah berakhir.');

        $token = $request->query('token', '');
        abort_unless(is_string($token) && $token !== '' && strlen($token) <= 8192, 403, 'Token dokumen tidak valid.');

        try {
            $data = json_decode(Crypt::decryptString($token), true, 512, JSON_THROW_ON_ERROR);
        } catch (DecryptException | \JsonException $e) {
            abort(403, 'Token dokumen tidak valid.');
        }

        abort_unless(is_array($data), 403, 'Token dokumen tidak valid.');
        $expiration = $data['exp'] ?? null;
        abort_unless(is_numeric($expiration) && (int) $expiration >= time(), 403, 'Tautan PDF telah kedaluwarsa. Silakan buka ulang detail laboratorium.');

        $expectedSid = hash('sha256', $request->session()->getId());
        $tokenSid = $data['sid'] ?? null;
        abort_unless(is_string($tokenSid) && hash_equals($expectedSid, $tokenSid), 403, 'Sesi dokumen tidak cocok.');

        $noOrder = trim((string) ($data['noorder'] ?? ''));
        abort_unless((bool) preg_match('/\A[A-Za-z0-9-]{3,40}\z/D', $noOrder), 403, 'Nomor order tidak valid.');

        // Handles both normal UNC paths and repeated backslashes in API data.
        // Only permit a PDF directly under the approved LIS SMB share.
        $rawPath = $data['path'] ?? null;
        abort_unless(is_string($rawPath) && $rawPath !== '' && strlen($rawPath) <= 1024, 403, 'Lokasi PDF tidak valid.');
        $parts = preg_split('~[\\\\/]+~', trim($rawPath), -1, PREG_SPLIT_NO_EMPTY);
        abort_unless(is_array($parts) && count($parts) === 3, 403, 'Lokasi PDF tidak valid.');
        abort_unless($parts[0] === '10.60.1.45' && strcasecmp($parts[1], 'backup') === 0, 403, 'Server PDF tidak diizinkan.');

        $filename = $parts[2];
        abort_unless(
            (bool) preg_match('/\A[A-Za-z0-9][A-Za-z0-9 _().-]{0,220}\.pdf\z/iD', $filename)
                && str_starts_with(strtoupper($filename), strtoupper($noOrder . '_')),
            403,
            'Nama PDF tidak valid.'
        );

        if (PHP_OS_FAMILY === 'Windows') {
            // PHP must run as a Windows identity that can read this SMB share.
            $filePath = '\\\\10.60.1.45\\backup\\' . $filename;
        } else {
            // For Linux, mount //10.60.1.45/backup read-only first.
            $mount = rtrim((string) config('services.lis_pdf.mount', '/mnt/lab-backup'), '/');
            $filePath = $mount . '/' . $filename;
        }

        clearstatcache(true, $filePath);
        if (! @is_file($filePath)) {
            Log::warning('NADI laboratory PDF not found', ['order' => $noOrder, 'os' => PHP_OS_FAMILY]);
            abort(404, 'Dokumen hasil laboratorium tidak ditemukan.');
        }

        // Windows/SMB sometimes returns false from is_readable() even though
        // fopen(..., "rb") works. Test real read capability instead.
        $stream = @fopen($filePath, 'rb');
        if ($stream === false) {
            Log::warning('NADI laboratory PDF SMB fopen failed', ['order' => $noOrder, 'os' => PHP_OS_FAMILY]);
            abort(503, 'Dokumen laboratorium belum dapat dibaca dari server LIS.');
        }

        $signature = fread($stream, 5);
        if ($signature !== '%PDF-') {
            fclose($stream);
            Log::warning('NADI laboratory file is not a PDF', ['order' => $noOrder]);
            abort(422, 'Dokumen yang ditemukan bukan PDF yang valid.');
        }
        rewind($stream);

        $download = $request->boolean('download');
        $safeName = 'hasil-laboratorium-' . $noOrder . '.pdf';

        return response()->stream(static function () use ($stream) {
            try {
                while (! feof($stream)) {
                    $chunk = fread($stream, 65536);
                    if ($chunk === false || $chunk === '') {
                        break;
                    }
                    echo $chunk;
                }
            } finally {
                fclose($stream);
            }
        }, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => ($download ? 'attachment' : 'inline') . '; filename="' . $safeName . '"',
            'Cache-Control' => 'private, no-store, max-age=0',
            'Pragma' => 'no-cache',
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'SAMEORIGIN',
            'Content-Security-Policy' => "frame-ancestors 'self'",
        ]);
    }
}
