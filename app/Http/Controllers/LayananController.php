<?php

namespace App\Http\Controllers;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use App\Services\PoliWaitingApiService;

class LayananController extends Controller
{
    //
    public function laboratory(
        Request $request,
        PoliWaitingApiService $apiService
    ) {
        $patient = session('pasien', []);

        $patientId = trim(
            (string) data_get(
                $patient,
                'patient_id'
            )
        );

        if ($patientId === '') {
            return redirect('/')
                ->withErrors([
                    'validasi' =>
                    'Silakan masuk menggunakan nomor rekam medis dan tanggal lahir terlebih dahulu.',
                ]);
        }

        $result = $apiService->getLaboratoryOrders(
            $patientId
        );

        $orders = collect(
            data_get($result, 'orders', [])
        );

        /*
    |--------------------------------------------------------------------------
    | Filter Pencarian
    |--------------------------------------------------------------------------
    */
        $keyword = strtolower(
            trim((string) $request->get('keyword', ''))
        );

        $status = strtolower(
            trim((string) $request->get('status', 'ALL'))
        );

        $tahun = trim(
            (string) $request->get('tahun', 'ALL')
        );

        if ($keyword !== '') {
            $orders = $orders->filter(
                function ($order) use ($keyword) {
                    $detailNames = collect(
                        data_get($order, 'details', [])
                    )
                        ->pluck('name')
                        ->implode(' ');

                    $searchText = strtolower(
                        implode(' ', [
                            data_get(
                                $order,
                                'order_number',
                                ''
                            ),
                            data_get(
                                $order,
                                'registration_number',
                                ''
                            ),
                            data_get(
                                $order,
                                'origin_room',
                                ''
                            ),
                            data_get(
                                $order,
                                'destination_room',
                                ''
                            ),
                            data_get(
                                $order,
                                'doctor',
                                ''
                            ),
                            data_get(
                                $order,
                                'status',
                                ''
                            ),
                            $detailNames,
                        ])
                    );

                    return str_contains(
                        $searchText,
                        $keyword
                    );
                }
            );
        }

        if ($status !== 'all') {
            $orders = $orders->filter(
                function ($order) use ($status) {
                    return strtolower(
                        trim(
                            (string) data_get(
                                $order,
                                'status',
                                ''
                            )
                        )
                    ) === $status;
                }
            );
        }

        if ($tahun !== 'ALL') {
            $orders = $orders->filter(
                function ($order) use ($tahun) {
                    $orderDate = data_get(
                        $order,
                        'order_date'
                    );

                    if (! $orderDate) {
                        return false;
                    }

                    try {
                        return \Carbon\Carbon::parse(
                            $orderDate
                        )->format('Y') === $tahun;
                    } catch (Throwable $exception) {
                        return false;
                    }
                }
            );
        }

        $orders = $orders
            ->values();

        /*
    |--------------------------------------------------------------------------
    | Daftar Tahun
    |--------------------------------------------------------------------------
    */
        $yearOptions = collect(
            data_get($result, 'orders', [])
        )
            ->map(function ($order) {
                try {
                    return \Carbon\Carbon::parse(
                        data_get($order, 'order_date')
                    )->format('Y');
                } catch (Throwable $exception) {
                    return null;
                }
            })
            ->filter()
            ->unique()
            ->sortDesc()
            ->values();

        /*
    |--------------------------------------------------------------------------
    | Ringkasan
    |--------------------------------------------------------------------------
    */
        $summary = [
            'total' => $orders->count(),

            'verified' => $orders
                ->filter(function ($order) {
                    return str_contains(
                        strtolower(
                            (string) data_get(
                                $order,
                                'status',
                                ''
                            )
                        ),
                        'verifikasi'
                    );
                })
                ->count(),

            'clinical_pathology' => $orders
                ->filter(function ($order) {
                    return str_contains(
                        strtoupper(
                            (string) data_get(
                                $order,
                                'destination_room',
                                ''
                            )
                        ),
                        'PATOLOGI KLINIK'
                    );
                })
                ->count(),

            'anatomical_pathology' => $orders
                ->filter(function ($order) {
                    return str_contains(
                        strtoupper(
                            (string) data_get(
                                $order,
                                'destination_room',
                                ''
                            )
                        ),
                        'PATOLOGI ANATOMI'
                    );
                })
                ->count(),
        ];

        /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    */
        $perPage = 10;

        $currentPage = LengthAwarePaginator::resolveCurrentPage();

        $pageItems = $orders
            ->slice(
                ($currentPage - 1) * $perPage,
                $perPage
            )
            ->values();

        $orders = new LengthAwarePaginator(
            $pageItems,
            $orders->count(),
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );
// dd($orders);
        return view(
            'layanan.laboratorium',
            compact(
                'patient',
                'result',
                'orders',
                'summary',
                'keyword',
                'status',
                'tahun',
                'yearOptions'
            )
        );
    }
    public function laboratoryDetail(
        string $recordId,
        PoliWaitingApiService $apiService
    ) {
        $patient = session('pasien', []);

        $patientId = trim(
            (string) data_get(
                $patient,
                'patient_id'
            )
        );

        if ($patientId === '') {
            return redirect('/')
                ->withErrors([
                    'validasi' =>
                    'Silakan masuk menggunakan nomor rekam medis dan tanggal lahir terlebih dahulu.',
                ]);
        }

        $result = $apiService->getLaboratoryOrders(
            $patientId
        );

        if (data_get($result, 'is_error', false)) {
            return redirect()
                ->route('laboratory.index')
                ->withErrors([
                    'laboratory' => data_get(
                        $result,
                        'message',
                        'API laboratorium belum berhasil diakses.'
                    ),
                ]);
        }

        $order = collect(
            data_get($result, 'orders', [])
        )->first(function ($item) use ($recordId) {
            return trim(
                (string) data_get(
                    $item,
                    'record_id'
                )
            ) === trim($recordId);
        });

        if (! $order) {
            abort(
                404,
                'Data pemeriksaan laboratorium tidak ditemukan.'
            );
        }

        return view(
            'layanan.laboratorium-detail',
            compact(
                'patient',
                'order'
            )
        );
    }
}
