<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Throwable;

class VideoEdukasiController extends Controller
{
    public function index(Request $request)
    {
        try {

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

            /*
            |--------------------------------------------------------------------------
            | Parameter
            |--------------------------------------------------------------------------
            */

            $namaEduBoard = trim(
                (string) $request->input(
                    'namaeduboard',
                    ''
                )
            );

            $params = [
                'namaeduboard' => $namaEduBoard,
            ];

            /*
            |--------------------------------------------------------------------------
            | Canonical URI
            |--------------------------------------------------------------------------
            */

            $queryString = http_build_query(
                $params,
                '',
                '&',
                PHP_QUERY_RFC3986
            );

            $canonicalUri =
                '/api/service/getdataeduboard'
                . '?'
                . $queryString;

            /*
            |--------------------------------------------------------------------------
            | Generate Signature
            |--------------------------------------------------------------------------
            */

            $timestamp = (string) time();

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

            /*
            |--------------------------------------------------------------------------
            | Request API
            |--------------------------------------------------------------------------
            */

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

            /*
            |--------------------------------------------------------------------------
            | Validasi Response
            |--------------------------------------------------------------------------
            */

            if (! $response->successful()) {

                $message = data_get(
                    $response->json(),
                    'metadata.message',
                    'Gagal mengambil data video edukasi.'
                );

                throw new \RuntimeException(
                    $message
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Ambil Data
            |--------------------------------------------------------------------------
            */

            $result = $response->json();

            $videos = collect(
                data_get(
                    $result,
                    'data',
                    []
                )
            )

                /*
                |--------------------------------------------------------------------------
                | Hanya Data Aktif
                |--------------------------------------------------------------------------
                */
                ->filter(function ($item) {
                    return filter_var(
                        data_get(
                            $item,
                            'statusenabled',
                            false
                        ),
                        FILTER_VALIDATE_BOOLEAN
                    );
                })

                ->map(function ($item) {

                    $link = data_get(
                        $item,
                        'link',
                        ''
                    );

                    $youtubeId = $this->getYoutubeId(
                        $link
                    );

                    $item['youtube_id'] =
                        $youtubeId;

                    $item['thumbnail'] =
                        $youtubeId
                            ? 'https://img.youtube.com/vi/'
                                . $youtubeId
                                . '/hqdefault.jpg'
                            : null;

                    return $item;
                })

                ->values();

            /*
            |--------------------------------------------------------------------------
            | Filter Pencarian Tambahan
            |--------------------------------------------------------------------------
            */

            if ($namaEduBoard !== '') {

                $keyword = strtolower(
                    $namaEduBoard
                );

                $videos = $videos
                    ->filter(
                        function ($item) use ($keyword) {

                            $nama = strtolower(
                                data_get(
                                    $item,
                                    'namaeduboard',
                                    ''
                                )
                            );

                            $deskripsi = strtolower(
                                data_get(
                                    $item,
                                    'deskripsi',
                                    ''
                                )
                            );

                            return
                                str_contains(
                                    $nama,
                                    $keyword
                                )
                                ||
                                str_contains(
                                    $deskripsi,
                                    $keyword
                                );
                        }
                    )
                    ->values();
            }

            $total = $videos->count();

            /*
            |--------------------------------------------------------------------------
            | Return View
            |--------------------------------------------------------------------------
            */

            return view(
                'video-edukasi.index',
                compact(
                    'videos',
                    'total',
                    'namaEduBoard'
                )
            );

        } catch (Throwable $e) {

            report($e);

            return view(
                'video-edukasi.index',
                [
                    'videos' => collect(),
                    'total' => 0,
                    'namaEduBoard' => '',
                    'error' =>
                        'Data video edukasi belum dapat ditampilkan. '
                        . $e->getMessage(),
                ]
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Ambil Youtube ID
    |--------------------------------------------------------------------------
    */

    private function getYoutubeId(?string $url): ?string
    {
        if (empty($url)) {
            return null;
        }

        $patterns = [
            '/youtube\.com\/watch\?v=([^&]+)/',
            '/youtu\.be\/([^?&]+)/',
            '/youtube\.com\/embed\/([^?&]+)/',
            '/youtube\.com\/shorts\/([^?&]+)/',
        ];

        foreach ($patterns as $pattern) {

            if (
                preg_match(
                    $pattern,
                    $url,
                    $matches
                )
            ) {
                return $matches[1] ?? null;
            }
        }

        return null;
    }
}