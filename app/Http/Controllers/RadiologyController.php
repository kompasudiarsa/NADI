<?php

namespace App\Http\Controllers;

use App\Services\RadiologyApiService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class RadiologyController extends Controller
{
    public function index(Request $request, RadiologyApiService $radiology)
{
    /*
    |--------------------------------------------------------------------------
    | Ambil data pasien dari session
    |--------------------------------------------------------------------------
    */
    $sessionPatient = session('pasien', []);

    /*
    |--------------------------------------------------------------------------
    | Medical Record
    |--------------------------------------------------------------------------
    */
    $medicalRecord = trim(
        (string) (
            data_get($sessionPatient, 'medical_record')
            ?: data_get($sessionPatient, 'rm')
            ?: data_get($sessionPatient, 'nocm')
            ?: ''
        )
    );

    /*
    |--------------------------------------------------------------------------
    | Validasi session pasien
    |--------------------------------------------------------------------------
    */
    if ($medicalRecord === '') {
        return redirect('/')
            ->withErrors([
                'validasi' =>
                    'Silakan masuk menggunakan nomor rekam medis dan tanggal lahir terlebih dahulu.',
            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Validasi filter
    |--------------------------------------------------------------------------
    */
    $request->validate([
        'keyword'   => 'nullable|string|max:100',
        'expertise' => 'nullable|in:ALL,ADA,BELUM',
        'per_page'  => 'nullable|integer|in:10,20,50',
    ]);

    /*
    |--------------------------------------------------------------------------
    | NRM
    |--------------------------------------------------------------------------
    */
    $nrm = $medicalRecord;

    $keyword = trim(
        (string) $request->get('keyword', '')
    );

    $expertiseFilter = strtoupper(
        (string) $request->get('expertise', 'ALL')
    );

    $perPage = (int) $request->get('per_page', 10);

    /*
    |--------------------------------------------------------------------------
    | Ambil data Radiologi
    |--------------------------------------------------------------------------
    */
    $result = $radiology->getByNrm($nrm);

    $items = collect(
        data_get($result, 'data', [])
    )->map(function ($item) {
        return $this->normalizeItem($item);
    });

    /*
    |--------------------------------------------------------------------------
    | Filter keyword
    |--------------------------------------------------------------------------
    */
    if ($keyword !== '') {
        $needle = mb_strtolower($keyword);

        $items = $items->filter(function ($item) use ($needle) {
            $haystack = implode(' ', [
                data_get($item, 'no_rontgen', ''),
                data_get($item, 'no_register', ''),
                data_get($item, 'nama_pemeriksaan', ''),
                data_get($item, 'nama_radiolog', ''),
                data_get($item, 'nama_radiografer', ''),
            ]);

            return mb_strpos(
                mb_strtolower($haystack),
                $needle
            ) !== false;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Filter expertise
    |--------------------------------------------------------------------------
    */
    if ($expertiseFilter === 'ADA') {
        $items = $items->where(
            'has_expertise',
            true
        );
    } elseif ($expertiseFilter === 'BELUM') {
        $items = $items->where(
            'has_expertise',
            false
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Urutkan pemeriksaan terbaru
    |--------------------------------------------------------------------------
    */
    $items = $items
        ->sortByDesc('sort_timestamp')
        ->values();

    /*
    |--------------------------------------------------------------------------
    | Summary
    |--------------------------------------------------------------------------
    */
    $summary = [
        'total' => $items->count(),

        'with_expertise' => $items
            ->where('has_expertise', true)
            ->count(),

        'without_expertise' => $items
            ->where('has_expertise', false)
            ->count(),

        'critical' => $items
            ->where('is_critical', true)
            ->count(),
    ];

    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    */
    $page = LengthAwarePaginator::resolveCurrentPage();

    $radiologyItems = new LengthAwarePaginator(
        $items
            ->forPage($page, $perPage)
            ->values(),
        $items->count(),
        $perPage,
        $page,
        [
            'path'  => $request->url(),
            'query' => $request->except('page'),
        ]
    );

    /*
    |--------------------------------------------------------------------------
    | DATA PASIEN UNTUK VIEW
    |--------------------------------------------------------------------------
    */

    /*
     * Ambil nama dari session.
     *
     * Prioritas:
     * 1. name
     * 2. namapasien
     * 3. patient_name
     * 4. nama_pasien
     * 5. data radiologi
     */
    $patientName = trim(
        (string) (
            data_get($sessionPatient, 'name')
            ?: data_get($sessionPatient, 'namapasien')
            ?: data_get($sessionPatient, 'patient_name')
            ?: data_get($sessionPatient, 'nama_pasien')
            ?: ''
        )
    );

    /*
     * Jika nama tidak ada di session,
     * coba dari hasil radiologi.
     */
    if ($patientName === '') {
        $firstItem = $items->first();

        $patientName = trim(
            (string) (
                data_get($firstItem, 'nama_pasien')
                ?: data_get($firstItem, 'namapasien')
                ?: data_get($firstItem, 'patient_name')
                ?: data_get($firstItem, 'name')
                ?: ''
            )
        );
    }

    /*
     * Tanggal lahir
     */
    $birthDate = (
        data_get($sessionPatient, 'birth_date')
        ?: data_get($sessionPatient, 'tanggal_lahir')
        ?: data_get($sessionPatient, 'tgllahir')
        ?: null
    );

    /*
     * Patient ID bila diperlukan modul lain.
     */
    $patientId = (
        data_get($sessionPatient, 'patient_id')
        ?: data_get($sessionPatient, 'id')
        ?: null
    );

    /*
    |--------------------------------------------------------------------------
    | Bentuk data pasien yang konsisten untuk Blade
    |--------------------------------------------------------------------------
    */
    $patient = [
        'patient_id'     => $patientId,
        'medical_record' => $medicalRecord,
        'name'           => $patientName !== ''
            ? $patientName
            : 'Pasien',
        'birth_date'     => $birthDate,
    ];

    /*
    |--------------------------------------------------------------------------
    | View
    |--------------------------------------------------------------------------
    */
    return view(
        'layanan.radiologi',
        compact(
            'result',
            'radiologyItems',
            'summary',
            'patient',
            'nrm',
            'keyword',
            'expertiseFilter',
            'perPage'
        )
    );
}

    public function detail(Request $request, RadiologyApiService $radiology, $id)
    {
        $request->validate([
            'nrm' => 'required|string|max:30',
        ]);

        $nrm = trim((string) $request->get('nrm'));
        $result = $radiology->getByNrm($nrm);

        $item = collect(data_get($result, 'data', []))->first(function ($row) use ($id) {
            return (string) data_get($row, 'id') === (string) $id;
        });

        abort_if(! $item, 404, 'Data radiologi tidak ditemukan.');

        $item = $this->normalizeItem($item);

        return view('layanan.radiologi-detail', compact('item', 'nrm'));
    }

    protected function normalizeItem($item)
    {
        $item = is_array($item) ? $item : [];

        $finding = trim((string) data_get($item, 'expertise_text_finding', ''));
        $conclusion = trim((string) data_get($item, 'expertise_text_conclusion', ''));

        $radiologEnd = $this->normalizeDate(data_get($item, 'radiolog_datetime_end'));
        $radiograferEnd = $this->normalizeDate(data_get($item, 'radiografer_datetime_end'));
        $adminStart = $this->normalizeDate(data_get($item, 'admin_datetime_start'));

        $displayDate = $radiologEnd ?: $radiograferEnd ?: $adminStart;

        // Gunakan link PACS yang tidak menyertakan username/password di query string.
        $pacsUrl = trim((string) data_get($item, 'urllink1', ''));

        return array_merge($item, [
            'no_rontgen' => trim((string) data_get($item, 'no_rontgen', '')),
            'has_expertise' => $finding !== '' || $conclusion !== '',
            'is_critical' => (string) data_get($item, 'kritis', '0') === '1',
            'display_date' => $displayDate,
            'sort_timestamp' => $displayDate ? $displayDate->timestamp : 0,
            'pacs_url' => $pacsUrl,
        ]);
    }

    protected function normalizeDate($value)
    {
        $value = trim((string) $value);

        if ($value === '' || strpos($value, '1900-01-01') === 0) {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable $e) {
            return null;
        }
    }
}
