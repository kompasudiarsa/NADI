@extends('layouts.app', ['title' => 'Detail Laboratorium | NADI RSBM'])

@section('content')

<style>
    .lab-page {
        width: min(100%, 1080px);
        margin: 0 auto;
        padding: 10px 0 40px;
    }

    .lab-header {
        margin-bottom: 22px;
        text-align: center;
    }

    .lab-header h1 {
        margin: 0 0 8px;
        color: #0f172a;
        font-size: clamp(28px, 5vw, 42px);
        font-weight: 900;
        letter-spacing: -0.04em;
    }

    .lab-header p {
        max-width: 680px;
        margin: 0 auto;
        color: #64748b;
        font-size: 14px;
        line-height: 1.7;
    }

    .lab-card {
        margin-bottom: 20px;
        padding: clamp(18px, 4vw, 28px);
        border: 1px solid #e2e8f0;
        border-radius: 26px;
        background: #fff;
        box-shadow: 0 14px 40px rgba(15, 23, 42, .06);
    }

    .result-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 18px;
    }

    .result-title {
        color: #0f172a;
        font-size: 21px;
        font-weight: 900;
    }

    .lab-meta {
        margin-top: 7px;
        color: #94a3b8;
        font-size: 11px;
        line-height: 1.55;
    }

    .badge-row {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-end;
        gap: 8px;
    }

    .badge {
        display: inline-flex;
        align-items: center;
        padding: 8px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 900;
    }

    .badge.is-type {
        background: #f1f5f9;
        color: #475569;
    }

    .badge.is-micro {
        background: #f3e8ff;
        color: #7e22ce;
    }

    .badge.is-pa {
        background: #ffedd5;
        color: #9a3412;
    }

    .badge.is-order {
        background: #dcfce7;
        color: #166534;
    }

    .badge.is-billing {
        background: #e0f2fe;
        color: #0369a1;
    }

    .lab-alert {
        padding: 15px 17px;
        border-radius: 15px;
        font-size: 14px;
        font-weight: 700;
        line-height: 1.55;
    }

    .lab-alert.is-success {
        border: 1px solid #bbf7d0;
        background: #f0fdf4;
        color: #166534;
    }

    .lab-alert.is-error {
        border: 1px solid #fecaca;
        background: #fef2f2;
        color: #b91c1c;
    }

    .lab-alert.is-warning {
        border: 1px solid #fde68a;
        background: #fffbeb;
        color: #92400e;
    }

    /* =========================================================
       PATOLOGI KLINIK
       ========================================================= */

    .lab-group {
        margin-top: 18px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        background: #fff;
    }

    .lab-group-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 16px 18px;
        border-bottom: 1px solid #e2e8f0;
        background: #f8fafc;
    }

    .lab-group-title {
        color: #0f172a;
        font-size: 16px;
        font-weight: 900;
    }

    .lab-group-count {
        padding: 5px 9px;
        border-radius: 999px;
        background: #e2e8f0;
        color: #475569;
        font-size: 11px;
        font-weight: 900;
    }

    .group-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 10px 24px;
        padding: 12px 18px;
        border-bottom: 1px solid #e2e8f0;
        background: #fff;
    }

    .lab-table-wrapper {
        overflow-x: auto;
    }

    .lab-table {
        width: 100%;
        min-width: 720px;
        border-collapse: collapse;
    }

    .lab-table th {
        padding: 12px 14px;
        border-bottom: 1px solid #e2e8f0;
        background: #fff;
        color: #64748b;
        font-size: 11px;
        font-weight: 900;
        text-align: left;
        text-transform: uppercase;
        letter-spacing: .05em;
    }

    .lab-table td {
        padding: 14px;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
        font-size: 13px;
        vertical-align: middle;
    }

    .lab-table tr:last-child td {
        border-bottom: 0;
    }

    .test-name {
        color: #0f172a;
        font-weight: 800;
    }

    .result-value {
        color: #0f172a;
        font-size: 15px;
        font-weight: 900;
    }

    .result-value.is-high {
        color: #dc2626;
    }

    .result-value.is-low {
        color: #d97706;
    }

    .flag {
        display: inline-flex;
        min-width: 28px;
        align-items: center;
        justify-content: center;
        padding: 5px 8px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 900;
    }

    .flag.is-normal {
        background: #dcfce7;
        color: #166534;
    }

    .flag.is-high {
        background: #fee2e2;
        color: #b91c1c;
    }

    .flag.is-low {
        background: #fef3c7;
        color: #92400e;
    }

    .flag.is-other {
        background: #e2e8f0;
        color: #475569;
    }

    /* =========================================================
       MIKROBIOLOGI
       ========================================================= */

    .micro-report-title {
        margin-top: 18px;
        padding: 15px 18px;
        border: 1px solid #cbd5e1;
        border-radius: 16px 16px 0 0;
        background: #f8fafc;
        color: #0f172a;
        text-align: center;
        font-size: 17px;
        font-weight: 900;
    }

    .micro-info-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        border-right: 1px solid #cbd5e1;
        border-bottom: 1px solid #cbd5e1;
        border-left: 1px solid #cbd5e1;
    }

    .micro-panel {
        padding: 18px;
    }

    .micro-panel + .micro-panel {
        border-left: 1px solid #cbd5e1;
    }

    .section-title {
        margin-bottom: 13px;
        color: #0f172a;
        font-size: 14px;
        font-weight: 900;
        text-align: center;
    }

    .info-list {
        display: grid;
        gap: 8px;
    }

    .info-row {
        display: grid;
        grid-template-columns: 150px 12px minmax(0, 1fr);
        gap: 4px;
        color: #334155;
        font-size: 13px;
        line-height: 1.45;
    }

    .info-label {
        color: #64748b;
        font-weight: 700;
    }

    .micro-result {
        padding: 18px;
        border-right: 1px solid #cbd5e1;
        border-bottom: 1px solid #cbd5e1;
        border-left: 1px solid #cbd5e1;
    }

    .micro-result-grid {
        display: grid;
        grid-template-columns: 170px 12px minmax(0, 1fr);
        gap: 8px 4px;
        color: #334155;
        font-size: 14px;
        line-height: 1.55;
    }

    .micro-result-value {
        color: #0f172a;
        font-size: 16px;
        font-weight: 900;
    }

    .micro-comment {
        margin-top: 16px;
        padding: 15px 17px;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        background: #f8fafc;
        color: #475569;
        font-size: 13px;
        line-height: 1.7;
        white-space: pre-line;
    }

    .micro-culture-card {
        padding: 18px;
        border-bottom: 1px solid #e2e8f0;
    }

    .micro-culture-card:last-child {
        border-bottom: 0;
    }

    .micro-culture-heading {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
    }

    .micro-culture-product {
        color: #0f172a;
        font-size: 17px;
        font-weight: 900;
    }

    .micro-culture-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 10px 24px;
        margin-top: 14px;
        padding: 12px 14px;
        border-radius: 12px;
        background: #f8fafc;
    }

    .micro-culture-meta > div {
        display: flex;
        flex-direction: column;
        gap: 2px;
        color: #334155;
        font-size: 12px;
    }

    .micro-culture-meta span {
        color: #94a3b8;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .04em;
    }

    .micro-culture-section {
        margin-top: 16px;
    }

    .micro-culture-report {
        margin: 0;
        padding: 16px;
        overflow-x: auto;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        background: #0f172a;
        color: #e2e8f0;
        font-family: Consolas, Monaco, monospace;
        font-size: 12px;
        line-height: 1.65;
        white-space: pre-wrap;
        word-break: break-word;
    }

    /* =========================================================
       WATERMARK MDRO
       Tampil hanya jika commentheader mengandung "MDRO".
       ========================================================= */

    .mdro-watermark {
        position: relative;
        overflow: hidden;
        isolation: isolate;
    }

    .mdro-watermark::after {
        content: "MDRO";
        position: absolute;
        top: 50%;
        left: 50%;
        z-index: 20;
        transform: translate(-50%, -50%) rotate(-28deg);
        color: rgba(220, 38, 38, .16);
        font-size: clamp(72px, 13vw, 150px);
        font-weight: 1000;
        line-height: 1;
        letter-spacing: .08em;
        white-space: nowrap;
        pointer-events: none;
        user-select: none;
    }

    .signature-wrap {
        display: flex;
        justify-content: flex-end;
        margin-top: 24px;
    }

    .signature-box {
        width: min(100%, 430px);
        color: #334155;
        font-size: 13px;
        line-height: 1.7;
    }

    .signature-space {
        height: 56px;
    }

    .signature-name {
        color: #0f172a;
        font-weight: 800;
        text-decoration: underline;
    }

    /* =========================================================
       PATOLOGI ANATOMI
       ========================================================= */

    .pa-box {
        margin-top: 18px;
        overflow: hidden;
        border: 1px solid #fed7aa;
        border-radius: 18px;
        background: #fff;
    }

    .pa-title {
        padding: 15px 18px;
        border-bottom: 1px solid #fed7aa;
        background: #fff7ed;
        color: #9a3412;
        font-size: 16px;
        font-weight: 900;
    }

    .pa-content {
        padding: 18px;
    }

    .pa-field {
        margin-bottom: 16px;
    }

    .pa-field:last-child {
        margin-bottom: 0;
    }

    .pa-label {
        margin-bottom: 5px;
        color: #64748b;
        font-size: 12px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .04em;
    }

    .pa-value {
        color: #0f172a;
        font-size: 14px;
        line-height: 1.7;
        white-space: pre-line;
    }

    /* =========================================================
       NAVIGASI SAPA RSBM
       ========================================================= */

    .lab-navigation {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 18px;
        padding: 10px;
        border: 1px solid rgba(203, 213, 225, .85);
        border-radius: 18px;
        background: rgba(255, 255, 255, .96);
        box-shadow: 0 10px 28px rgba(15, 23, 42, .055);
    }

    .lab-navigation-left,
    .lab-navigation-right,
    .lab-bottom-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .lab-navigation-right {
        justify-content: flex-end;
    }

    .lab-nav-button {
        display: inline-flex;
        min-height: 40px;
        align-items: center;
        justify-content: center;
        gap: 7px;
        padding: 0 13px;
        border: 1px solid transparent;
        border-radius: 12px;
        font-family: inherit;
        font-size: 11px;
        font-weight: 900;
        line-height: 1;
        text-decoration: none;
        white-space: nowrap;
        cursor: pointer;
        transition:
            transform .18s ease,
            box-shadow .18s ease,
            border-color .18s ease,
            background .18s ease;
    }

    .lab-nav-button:hover {
        transform: translateY(-1px);
        text-decoration: none;
    }

    .lab-nav-button.is-lab {
        border-color: #bbf7d0;
        background: #f0fdf4;
        color: #15803d;
    }

    .lab-nav-button.is-lab:hover {
        border-color: #86efac;
        background: #dcfce7;
        color: #166534;
    }

    .lab-nav-button.is-menu {
        border-color: #c7d2fe;
        background: #eef1ff;
        color: #26358f;
    }

    .lab-nav-button.is-menu:hover {
        border-color: #a5b4fc;
        background: #e0e7ff;
        color: #1d286d;
    }

    .lab-nav-button.is-logout {
        border-color: #fecaca;
        background: #fff5f5;
        color: #b91c1c;
    }

    .lab-nav-button.is-logout:hover {
        border-color: #fca5a5;
        background: #fee2e2;
        color: #991b1b;
    }

    .lab-nav-button svg {
        flex: 0 0 auto;
    }

    .lab-bottom-actions {
        justify-content: center;
        margin-top: 22px;
        padding-top: 18px;
        border-top: 1px solid #e2e8f0;
    }

    .lab-logout-form {
        margin: 0;
    }

    @media (max-width: 720px) {
        .lab-navigation {
            align-items: stretch;
            flex-direction: column;
            padding: 9px;
        }

        .lab-navigation-left,
        .lab-navigation-right {
            display: grid;
            width: 100%;
            grid-template-columns: 1fr;
        }

        .lab-nav-button {
            width: 100%;
            min-height: 42px;
        }

        .lab-bottom-actions {
            display: grid;
            grid-template-columns: 1fr;
        }

        .lab-bottom-actions .lab-logout-form,
        .lab-bottom-actions .lab-nav-button {
            width: 100%;
        }

        .result-header {
            flex-direction: column;
        }

        .badge-row {
            justify-content: flex-start;
        }

        .micro-info-grid {
            grid-template-columns: 1fr;
        }

        .micro-panel + .micro-panel {
            border-top: 1px solid #cbd5e1;
            border-left: 0;
        }

        .info-row {
            grid-template-columns: 115px 10px minmax(0, 1fr);
            font-size: 12px;
        }

        .micro-result-grid {
            grid-template-columns: 120px 10px minmax(0, 1fr);
            font-size: 13px;
        }
    }
</style>

@php
    /*
     * =========================================================
     * JENIS LAB BERASAL DARI PARAMETER ROUTE
     * =========================================================
     *
     * Nilai yang diharapkan:
     * - LAB - PATOLOGI KLINIK
     * - LAB - MIKROBIOLOGI KLINIK
     * - LAB - PATOLOGI ANATOMI
     */

    $labName = strtoupper(
        trim((string) ($lab ?? ''))
    );

    $isMikrobiologi = str_contains(
        $labName,
        'MIKROBIOLOGI'
    );

    $isPatologiAnatomi = str_contains(
        $labName,
        'PATOLOGI ANATOMI'
    );

    $isPatologiKlinik =
        str_contains(
            $labName,
            'PATOLOGI KLINIK'
        )
        && ! $isMikrobiologi
        && ! $isPatologiAnatomi;

    $success = (bool) data_get(
        $result,
        'success',
        false
    );

    $payload = data_get(
        $result,
        'data',
        []
    );

    $responseNoOrder = data_get(
        $payload,
        'noorder',
        $noOrder ?? '-'
    );

    $noBilling = data_get(
        $payload,
        'nobilling',
        '-'
    );

    /*
     * Patologi Klinik:
     * data.hasil = [
     *   [
     *     'group' => 'HEMATOLOGI',
     *     'items' => [...]
     *   ]
     * ]
     */
    $clinicalGroups = data_get(
        $payload,
        'hasil',
        []
    );

    /*
     * Mikrobiologi:
     * mendukung response langsung pada data
     * maupun data.mikrobiologi.
     */
    $microData = data_get(
        $payload,
        'mikrobiologi',
        $payload
    );

    /*
     * Patologi Anatomi:
     * sementara mendukung response langsung pada data
     * maupun data.patologi_anatomi.
     */
    $paData = data_get(
        $payload,
        'patologi_anatomi',
        $payload
    );

    /*
     * Navigasi halaman.
     * Menggunakan fallback URL agar Blade tidak error bila nama route
     * berbeda pada environment tertentu.
     */
    $laboratoryHistoryUrl = Route::has('laboratory.index')
        ? route('laboratory.index')
        : url('/cek-hasil-laboratorium');

    $mainMenuUrl = Route::has('layanan.menu')
        ? route('layanan.menu')
        : url('/layanan/menu');

    $logoutRouteName = Route::has('layanan.logout')
        ? 'layanan.logout'
        : (Route::has('logout') ? 'logout' : null);

    $labText = static function ($value, $default = '-') {
        if ($value === null) {
            return $default;
        }

        if ($value instanceof \Illuminate\Support\Collection) {
            $value = $value->all();
        }

        if (is_object($value)) {
            $value = (array) $value;
        }

        if (is_array($value)) {
            $parts = [];

            array_walk_recursive(
                $value,
                static function ($item) use (&$parts) {
                    if (
                        is_scalar($item)
                        && trim((string) $item) !== ''
                    ) {
                        $parts[] = trim((string) $item);
                    }
                }
            );

            $parts = array_values(
                array_unique($parts)
            );

            return count($parts)
                ? implode(', ', $parts)
                : $default;
        }

        if (is_bool($value)) {
            return $value ? 'Ya' : 'Tidak';
        }

        if (! is_scalar($value)) {
            return $default;
        }

        $value = trim((string) $value);

        return $value !== ''
            ? $value
            : $default;
    };
@endphp

<div class="lab-page">

    <nav class="lab-navigation" aria-label="Navigasi halaman laboratorium">
        <div class="lab-navigation-left">
            <a
                href="{{ $laboratoryHistoryUrl }}"
                class="lab-nav-button is-lab"
            >
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <span>Kembali ke Hasil Laboratorium</span>
            </a>
        </div>

        <div class="lab-navigation-right">
            <a
                href="{{ $mainMenuUrl }}"
                class="lab-nav-button is-menu"
            >
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M3 11L12 4L21 11" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M5 10V20H19V10M9 20V14H15V20" stroke="currentColor" stroke-width="2" stroke-linejoin="round" />
                </svg>
                <span>Menu Utama</span>
            </a>

            @if($logoutRouteName)
                <form
                    method="POST"
                    action="{{ route($logoutRouteName) }}"
                    class="lab-logout-form"
                >
                    @csrf

                    <button
                        type="submit"
                        class="lab-nav-button is-logout"
                    >
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M10 5H6C4.9 5 4 5.9 4 7V17C4 18.1 4.9 19 6 19H10" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            <path d="M14 8L18 12L14 16M18 12H9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <span>Logout</span>
                    </button>
                </form>
            @endif
        </div>
    </nav>


    @if($result === null)

        <div class="lab-card">
            <div class="lab-alert is-error">
                Data hasil laboratorium belum tersedia.
            </div>
        </div>

    @elseif(! $success)

        <div class="lab-card">
            <div class="lab-alert is-error">
                {{ data_get(
                    $result,
                    'message',
                    'Hasil laboratorium tidak ditemukan.'
                ) }}
            </div>
        </div>

    @else

        <div class="lab-card">

            <div class="result-header">

                <div>

                    <div class="result-title">
                        @if($isMikrobiologi)
                            Hasil Pemeriksaan Mikrobiologi Klinik
                        @elseif($isPatologiAnatomi)
                            Hasil Pemeriksaan Patologi Anatomi
                        @elseif($isPatologiKlinik)
                            Hasil Pemeriksaan Patologi Klinik
                        @else
                            Hasil Pemeriksaan Laboratorium
                        @endif
                    </div>

                    <div class="lab-meta">
                        {{ $lab ?: 'Laboratorium RS Bali Mandara' }}
                    </div>

                </div>

                <div class="badge-row">

                    @if($isMikrobiologi)
                        <span class="badge is-type is-micro">
                            Mikrobiologi Klinik
                        </span>
                    @elseif($isPatologiAnatomi)
                        <span class="badge is-type is-pa">
                            Patologi Anatomi
                        </span>
                    @elseif($isPatologiKlinik)
                        <span class="badge is-type">
                            Patologi Klinik
                        </span>
                    @endif

                    @if(
                        $responseNoOrder
                        && $responseNoOrder !== '-'
                    )
                        <span class="badge is-order">
                            Order: {{ $labText($responseNoOrder) }}
                        </span>
                    @endif

                    @if(
                        $noBilling
                        && $noBilling !== '-'
                    )
                        <span class="badge is-billing">
                            Billing: {{ $labText($noBilling) }}
                        </span>
                    @endif

                </div>

            </div>

            <div class="lab-alert is-success">
                {{ data_get(
                    $result,
                    'message',
                    'Data hasil laboratorium berhasil diambil.'
                ) }}
            </div>

            {{-- =====================================================
                 LAB - PATOLOGI KLINIK
                 ===================================================== --}}
            @if($isPatologiKlinik)

                <div
                    class="lab-meta"
                    style="margin-top:10px;"
                >
                    Keterangan:
                    <strong>N</strong> = Normal,
                    <strong>H/HH</strong> = Tinggi,
                    <strong>L/LL</strong> = Rendah.
                </div>

                @forelse($clinicalGroups as $group)

                    @php
                        $items = data_get(
                            $group,
                            'items',
                            []
                        );
                    @endphp

                    <div class="lab-group">

                        <div class="lab-group-header">

                            <div class="lab-group-title">
                                {{ data_get(
                                    $group,
                                    'group',
                                    'Pemeriksaan'
                                ) }}
                            </div>

                            <div class="lab-group-count">
                                {{ count($items) }}
                                pemeriksaan
                            </div>

                        </div>

                        @if(
                            data_get($group, 'diotorisasi')
                            || data_get(
                                $group,
                                'dokterdiperiksa'
                            )
                        )

                            <div class="group-meta">

                                @if(
                                    data_get(
                                        $group,
                                        'diotorisasi'
                                    )
                                )
                                    <div
                                        class="lab-meta"
                                        style="margin-top:0;"
                                    >
                                        <strong
                                            style="color:#475569;"
                                        >
                                            Diotorisasi:
                                        </strong>

                                        {{ data_get(
                                            $group,
                                            'diotorisasi'
                                        ) }}
                                    </div>
                                @endif

                                @if(
                                    data_get(
                                        $group,
                                        'dokterdiperiksa'
                                    )
                                )
                                    <div
                                        class="lab-meta"
                                        style="margin-top:0;"
                                    >
                                        <strong
                                            style="color:#475569;"
                                        >
                                            Pemeriksa:
                                        </strong>

                                        {{ data_get(
                                            $group,
                                            'dokterdiperiksa'
                                        ) }}
                                    </div>
                                @endif

                            </div>

                        @endif

                        <div class="lab-table-wrapper">

                            <table class="lab-table">

                                <thead>
                                    <tr>
                                        <th>Pemeriksaan</th>
                                        <th>Hasil</th>
                                        <th>Flag</th>
                                        <th>Satuan</th>
                                        <th>Nilai Rujukan</th>
                                    </tr>
                                </thead>

                                <tbody>

                                @foreach($items as $item)

                                    @php
                                        $flag = strtoupper(
                                            trim(
                                                (string) data_get(
                                                    $item,
                                                    'flag',
                                                    ''
                                                )
                                            )
                                        );

                                        $flagClass = 'is-other';
                                        $resultClass = '';

                                        if (
                                            $flag === 'N'
                                            || $flag === ''
                                        ) {
                                            $flagClass =
                                                'is-normal';
                                        } elseif (
                                            str_contains(
                                                $flag,
                                                'H'
                                            )
                                        ) {
                                            $flagClass =
                                                'is-high';

                                            $resultClass =
                                                'is-high';
                                        } elseif (
                                            str_contains(
                                                $flag,
                                                'L'
                                            )
                                        ) {
                                            $flagClass =
                                                'is-low';

                                            $resultClass =
                                                'is-low';
                                        }

                                        $method = trim(
                                            (string) data_get(
                                                $item,
                                                'metode',
                                                ''
                                            )
                                        );
                                    @endphp

                                    <tr>

                                        <td>

                                            <div class="test-name">
                                                {{ data_get(
                                                    $item,
                                                    'detailpemeriksaan',
                                                    '-'
                                                ) }}
                                            </div>

                                            @if(
                                                data_get(
                                                    $item,
                                                    'namaproduk'
                                                )
                                            )
                                                <div class="lab-meta">
                                                    {{ data_get(
                                                        $item,
                                                        'namaproduk'
                                                    ) }}
                                                </div>
                                            @endif

                                            @if(
                                                data_get(
                                                    $item,
                                                    'tglhasil'
                                                )
                                            )
                                                <div class="lab-meta">
                                                    Hasil:
                                                    {{ data_get(
                                                        $item,
                                                        'tglhasil'
                                                    ) }}
                                                </div>
                                            @endif

                                            @if(
                                                $method !== ''
                                                && $method !== '-'
                                            )
                                                <div class="lab-meta">
                                                    Metode:
                                                    {{ $method }}
                                                </div>
                                            @endif

                                        </td>

                                        <td>
                                            <span
                                                class="result-value {{ $resultClass }}"
                                            >
                                                {{ data_get(
                                                    $item,
                                                    'hasil',
                                                    '-'
                                                ) }}
                                            </span>
                                        </td>

                                        <td>
                                            <span
                                                class="flag {{ $flagClass }}"
                                            >
                                                {{ $flag !== ''
                                                    ? $flag
                                                    : 'N' }}
                                            </span>
                                        </td>

                                        <td>
                                            {{ data_get(
                                                $item,
                                                'satuanstandar',
                                                '-'
                                            ) ?: '-' }}
                                        </td>

                                        <td>
                                            {{ data_get(
                                                $item,
                                                'nilaitext',
                                                '-'
                                            ) ?: '-' }}
                                        </td>

                                    </tr>

                                @endforeach

                                </tbody>

                            </table>

                        </div>

                    </div>

                @empty

                    <div
                        class="lab-alert is-error"
                        style="margin-top:18px;"
                    >
                        Data hasil pemeriksaan
                        Patologi Klinik belum tersedia.
                    </div>

                @endforelse

            {{-- =====================================================
                 LAB - MIKROBIOLOGI KLINIK
                 ===================================================== --}}
            @elseif($isMikrobiologi)

                @php
                    /*
                     * Struktur API Mikrobiologi terbaru:
                     * data.hasil = [
                     *   [
                     *     'group' => 'MIKROBIOLOGI',
                     *     'items' => [...],
                     *     'diotorisasi' => '...',
                     *     'dokterdiperiksa' => '...'
                     *   ]
                     * ]
                     *
                     * result_ft berisi laporan kultur/antibiogram
                     * dengan karakter ~ sebagai pemisah baris.
                     */
                    $microGroups = data_get(
                        $payload,
                        'hasil',
                        []
                    );

                    /*
                     * Jika ada items di data.hasil, gunakan format baru.
                     * Jika tidak, tampilan mikrobiologi lama di bawah
                     * tetap digunakan sebagai fallback.
                     */
                    $hasGroupedMicroResult =
                        is_array($microGroups)
                        && count($microGroups) > 0
                        && is_array(
                            data_get(
                                $microGroups,
                                '0.items'
                            )
                        );
                @endphp

                @if($hasGroupedMicroResult)

                    @forelse($microGroups as $group)

                        @php
                            $items = data_get(
                                $group,
                                'items',
                                []
                            );

                            $groupName = $labText(
                                data_get(
                                    $group,
                                    'group',
                                    'MIKROBIOLOGI'
                                )
                            );

                            $authorizedBy = trim(
                                (string) data_get(
                                    $group,
                                    'diotorisasi',
                                    ''
                                )
                            );

                            $examinedBy = trim(
                                (string) data_get(
                                    $group,
                                    'dokterdiperiksa',
                                    ''
                                )
                            );
                        @endphp

                        <div class="micro-report-title">
                            HASIL PEMERIKSAAN {{ strtoupper($groupName) }}
                        </div>

                        <div
                            class="lab-group"
                            style="margin-top:0; border-radius:0 0 20px 20px;"
                        >

                            <div class="lab-group-header">
                                <div class="lab-group-title">
                                    {{ $groupName }}
                                </div>

                                <div class="lab-group-count">
                                    {{ count($items) }} pemeriksaan
                                </div>
                            </div>

                            @if($authorizedBy !== '' || $examinedBy !== '')
                                <div class="group-meta">
                                    @if($examinedBy !== '')
                                        <div
                                            class="lab-meta"
                                            style="margin-top:0;"
                                        >
                                            <strong style="color:#475569;">
                                                Pemeriksa:
                                            </strong>
                                            {{ $examinedBy }}
                                        </div>
                                    @endif

                                    @if($authorizedBy !== '')
                                        <div
                                            class="lab-meta"
                                            style="margin-top:0;"
                                        >
                                            <strong style="color:#475569;">
                                                Diotorisasi:
                                            </strong>
                                            {{ $authorizedBy }}
                                        </div>
                                    @endif
                                </div>
                            @endif

                            @forelse($items as $item)

                                @php
                                    $productName = $labText(
                                        data_get(
                                            $item,
                                            'namaproduk',
                                            '-'
                                        )
                                    );

                                    $detailName = $labText(
                                        data_get(
                                            $item,
                                            'detailpemeriksaan',
                                            '-'
                                        )
                                    );

                                    $rawResultFt = data_get(
                                        $item,
                                        'result_ft',
                                        ''
                                    );

                                    $rawResult = data_get(
                                        $item,
                                        'hasil',
                                        ''
                                    );

                                    $formattedResultFt = trim(
                                        str_replace(
                                            '~',
                                            "\n",
                                            is_scalar($rawResultFt)
                                                ? (string) $rawResultFt
                                                : ''
                                        )
                                    );

                                    $formattedComment = trim(
                                        str_replace(
                                            '~',
                                            "\n",
                                            (string) data_get(
                                                $item,
                                                'comment',
                                                ''
                                            )
                                        )
                                    );

                                    $method = trim(
                                        (string) data_get(
                                            $item,
                                            'metode',
                                            ''
                                        )
                                    );

                                    $flag = strtoupper(
                                        trim(
                                            (string) data_get(
                                                $item,
                                                'flag',
                                                ''
                                            )
                                        )
                                    );

                                    $reportDate = trim(
                                        (string) data_get(
                                            $item,
                                            'tglhasil',
                                            ''
                                        )
                                    );

                                    /*
                                     * Watermark MDRO hanya untuk item
                                     * mikrobiologi yang commentheader-nya
                                     * mengandung kata MDRO.
                                     */
                                    $commentHeader = strtoupper(
                                        trim(
                                            (string) data_get(
                                                $item,
                                                'commentheader',
                                                ''
                                            )
                                        )
                                    );

                                    $isMdro = str_contains(
                                        $commentHeader,
                                        'MDRO'
                                    );
                                @endphp

                                <div class="micro-culture-card {{ $isMdro ? 'mdro-watermark' : '' }}">

                                    <div class="micro-culture-heading">
                                        <div>
                                            <div class="micro-culture-product">
                                                {{ $productName }}
                                            </div>

                                            <div class="lab-meta">
                                                Detail pemeriksaan:
                                                <strong style="color:#475569;">
                                                    {{ $detailName }}
                                                </strong>
                                            </div>
                                        </div>

                                        @if($flag !== '')
                                            <span class="flag is-other">
                                                {{ $flag }}
                                            </span>
                                        @endif
                                    </div>

                                    <div class="micro-culture-meta">
                                        @if($reportDate !== '')
                                            <div>
                                                <span>Tanggal hasil</span>
                                                <strong>
                                                    {{ $reportDate }}
                                                </strong>
                                            </div>
                                        @endif

                                        @if($method !== '' && $method !== '-')
                                            <div>
                                                <span>Metode</span>
                                                <strong>{{ $method }}</strong>
                                            </div>
                                        @endif

                                        @if(data_get($item, 'satuanstandar'))
                                            <div>
                                                <span>Satuan</span>
                                                <strong>
                                                    {{ data_get(
                                                        $item,
                                                        'satuanstandar'
                                                    ) }}
                                                </strong>
                                            </div>
                                        @endif
                                    </div>

                                    @if($formattedResultFt !== '')
                                        <div class="micro-culture-section">
                                            <div
                                                class="section-title"
                                                style="text-align:left; margin-bottom:8px;"
                                            >
                                                Hasil Kultur / Antibiogram
                                            </div>

                                            <pre class="micro-culture-report">{{ $formattedResultFt }}</pre>
                                        </div>
                                    @elseif(
                                        is_scalar($rawResult)
                                        && trim((string) $rawResult) !== ''
                                    )
                                        <div class="micro-culture-section">
                                            <div
                                                class="section-title"
                                                style="text-align:left; margin-bottom:8px;"
                                            >
                                                Hasil
                                            </div>

                                            <div class="micro-result-value">
                                                {{ trim((string) $rawResult) }}
                                            </div>
                                        </div>
                                    @else
                                        <div
                                            class="lab-alert is-warning"
                                            style="margin-top:16px;"
                                        >
                                            Hasil pemeriksaan belum tersedia.
                                        </div>
                                    @endif

                                    @if($formattedComment !== '')
                                        <div class="micro-comment">
                                            <strong>Komentar:</strong>
                                            {{ $formattedComment }}
                                        </div>
                                    @endif

                                </div>

                            @empty

                                <div
                                    class="lab-alert is-warning"
                                    style="margin:18px;"
                                >
                                    Item hasil mikrobiologi belum tersedia.
                                </div>

                            @endforelse

                        </div>

                    @empty

                        <div
                            class="lab-alert is-warning"
                            style="margin-top:18px;"
                        >
                            Data hasil pemeriksaan Mikrobiologi
                            belum tersedia.
                        </div>

                    @endforelse

                @else

                    {{-- Fallback struktur mikrobiologi lama --}}

                @php
                    $namaProduk = $labText(
                        data_get(
                            $microData,
                            'namaproduk',
                            '-'
                        )
                    );

                    $rawHasilSpesimen = data_get(
                        $microData,
                        'hasilspesimen'
                    );

                    if ($rawHasilSpesimen === null) {
                        $candidateHasil = data_get(
                            $microData,
                            'hasil'
                        );

                        $rawHasilSpesimen =
                            is_scalar($candidateHasil)
                                ? $candidateHasil
                                : '-';
                    }

                    $hasilSpesimen = $labText(
                        $rawHasilSpesimen
                    );

                    $microComment = $labText(
                        data_get(
                            $microData,
                            'comment',
                            data_get(
                                $microData,
                                'komentar',
                                ''
                            )
                        ),
                        ''
                    );

                    $microCommentHeader = strtoupper(
                        trim(
                            (string) data_get(
                                $microData,
                                'commentheader',
                                ''
                            )
                        )
                    );

                    $isMdroFallback = str_contains(
                        $microCommentHeader,
                        'MDRO'
                    );

                    $upperProduct = strtoupper(
                        $namaProduk
                    );

                    $isCovid =
                        str_contains(
                            $upperProduct,
                            'COVID'
                        )
                        || str_contains(
                            $upperProduct,
                            'SARS'
                        );

                    $rawReportDate = data_get(
                        $microData,
                        'tglkeluarhasil',
                        data_get(
                            $microData,
                            'tglhasil'
                        )
                    );

                    $reportDate =
                        is_scalar($rawReportDate)
                            ? trim(
                                (string) $rawReportDate
                            )
                            : '';

                    $reportDateText = '-';

                    if ($reportDate !== '') {
                        try {
                            $reportDateText =
                                \Carbon\Carbon::parse(
                                    $reportDate
                                )->isoFormat(
                                    'DD MMMM Y'
                                );
                        } catch (\Throwable $e) {
                            $reportDateText =
                                $reportDate;
                        }
                    }
                @endphp

                <div class="micro-report-title">
                    {{ $isCovid
                        ? 'HASIL PEMERIKSAAN COVID-19'
                        : 'HASIL PEMERIKSAAN MIKROBIOLOGI' }}
                </div>

                <div class="micro-info-grid">

                    <div class="micro-panel">

                        <div class="section-title">
                            Informasi Pasien
                        </div>

                        <div class="info-list">

                            <div class="info-row">
                                <div class="info-label">
                                    Nama
                                </div>
                                <div>:</div>
                                <div>
                                    {{ $labText(
                                        data_get(
                                            $microData,
                                            'namapasien',
                                            '-'
                                        )
                                    ) }}
                                </div>
                            </div>

                            <div class="info-row">
                                <div class="info-label">
                                    Tanggal Lahir
                                </div>
                                <div>:</div>
                                <div>
                                    {{ $labText(
                                        data_get(
                                            $microData,
                                            'tgllahir',
                                            '-'
                                        )
                                    ) }}

                                    @if(
                                        data_get(
                                            $microData,
                                            'umur'
                                        )
                                    )
                                        /
                                        {{ $labText(
                                            data_get(
                                                $microData,
                                                'umur'
                                            )
                                        ) }}
                                    @endif
                                </div>
                            </div>

                            <div class="info-row">
                                <div class="info-label">
                                    Jenis Kelamin
                                </div>
                                <div>:</div>
                                <div>
                                    {{ $labText(
                                        data_get(
                                            $microData,
                                            'jeniskelamin',
                                            '-'
                                        )
                                    ) }}
                                </div>
                            </div>

                            <div class="info-row">
                                <div class="info-label">
                                    No. RM
                                </div>
                                <div>:</div>
                                <div>
                                    {{ $labText(
                                        data_get(
                                            $microData,
                                            'nocm',
                                            '-'
                                        )
                                    ) }}
                                </div>
                            </div>

                            @if(
                                data_get(
                                    $microData,
                                    'noidentitas'
                                )
                            )
                                <div class="info-row">
                                    <div class="info-label">
                                        No. Identitas
                                    </div>
                                    <div>:</div>
                                    <div>
                                        {{ $labText(
                                            data_get(
                                                $microData,
                                                'noidentitas'
                                            )
                                        ) }}
                                    </div>
                                </div>
                            @endif

                            @if(
                                data_get(
                                    $microData,
                                    'nohp'
                                )
                            )
                                <div class="info-row">
                                    <div class="info-label">
                                        No. Telp/HP
                                    </div>
                                    <div>:</div>
                                    <div>
                                        {{ $labText(
                                            data_get(
                                                $microData,
                                                'nohp'
                                            )
                                        ) }}
                                    </div>
                                </div>
                            @endif

                            @if(
                                data_get(
                                    $microData,
                                    'alamatlengkap'
                                )
                            )
                                <div class="info-row">
                                    <div class="info-label">
                                        Alamat
                                    </div>
                                    <div>:</div>
                                    <div>
                                        {{ $labText(
                                            data_get(
                                                $microData,
                                                'alamatlengkap'
                                            )
                                        ) }}
                                    </div>
                                </div>
                            @endif

                        </div>

                    </div>

                    <div class="micro-panel">

                        <div class="section-title">
                            Informasi Spesimen
                        </div>

                        <div class="info-list">

                            <div class="info-row">
                                <div class="info-label">
                                    Jenis Spesimen
                                </div>
                                <div>:</div>
                                <div>
                                    {{ $labText(
                                        data_get(
                                            $microData,
                                            'jenisspesimen',
                                            '-'
                                        )
                                    ) }}
                                </div>
                            </div>

                            <div class="info-row">
                                <div class="info-label">
                                    Kode Spesimen
                                </div>
                                <div>:</div>
                                <div>
                                    {{ $labText(
                                        data_get(
                                            $microData,
                                            'kodespesimen',
                                            '-'
                                        )
                                    ) }}
                                </div>
                            </div>

                            <div class="info-row">
                                <div class="info-label">
                                    Spesimen Ke-
                                </div>
                                <div>:</div>
                                <div>
                                    {{ $labText(
                                        data_get(
                                            $microData,
                                            'spesimenke',
                                            '-'
                                        )
                                    ) }}
                                </div>
                            </div>

                            <div class="info-row">
                                <div class="info-label">
                                    Asal Spesimen
                                </div>
                                <div>:</div>
                                <div>
                                    {{ $labText(
                                        data_get(
                                            $microData,
                                            'asalspesimen',
                                            '-'
                                        )
                                    ) }}
                                </div>
                            </div>

                            <div class="info-row">
                                <div class="info-label">
                                    Tgl & Jam Pengambilan
                                </div>
                                <div>:</div>
                                <div>
                                    {{ $labText(
                                        data_get(
                                            $microData,
                                            'tglterimaspesimen',
                                            '-'
                                        )
                                    ) }}
                                </div>
                            </div>

                            <div class="info-row">
                                <div class="info-label">
                                    Tgl Diproses
                                </div>
                                <div>:</div>
                                <div>
                                    {{ $labText(
                                        data_get(
                                            $microData,
                                            'tgldikerjakanspesimen',
                                            '-'
                                        )
                                    ) }}
                                </div>
                            </div>

                            <div class="info-row">
                                <div class="info-label">
                                    Tgl Pelaporan
                                </div>
                                <div>:</div>
                                <div>
                                    {{ $labText(
                                        $reportDate,
                                        '-'
                                    ) }}
                                </div>
                            </div>

                        </div>

                    </div>

                </div>

                <div class="micro-result {{ $isMdroFallback ? 'mdro-watermark' : '' }}">

                    <div class="micro-result-grid">

                        <div class="info-label">
                            Jenis Pemeriksaan
                        </div>
                        <div>:</div>
                        <div>
                            {{ $namaProduk }}
                        </div>

                        <div class="info-label">
                            Hasil
                        </div>
                        <div>:</div>
                        <div class="micro-result-value">
                            {{ $hasilSpesimen }}

                            @if($isCovid)
                                SARS-CoV2
                            @endif
                        </div>

                    </div>

                    @if($microComment !== '')

                        <div class="micro-comment">
                            <strong>Komentar:</strong>
                            {{ $microComment }}
                        </div>

                    @elseif($isCovid)

                        <div class="micro-comment"><strong>Komentar:</strong>
1. SARS-CoV2 adalah virus penyebab COVID-19.
2. Hasil AG-RDT negatif berarti komponen protein virus SARS-CoV2 tidak terdeteksi di atas ambang batas kit deteksi.
3. Hasil negatif belum dapat menyingkirkan COVID-19. Bila secara klinis masih mengarah COVID-19, pemeriksaan lanjutan dapat dipertimbangkan sesuai penilaian tenaga kesehatan.
4. Hubungi layanan kesehatan untuk informasi lebih lanjut dan ikuti anjuran kesehatan yang berlaku.</div>

                    @endif

                </div>

                @if($reportDate)

                    <div class="signature-wrap">

                        <div class="signature-box">

                            <div>
                                Denpasar,
                                {{ $reportDateText }}
                            </div>

                            <div>
                                <strong>
                                    Kepala Laboratorium
                                    Mikrobiologi Klinik
                                </strong>
                            </div>

                            <div class="signature-space"></div>

                            <div class="signature-name">
                                dr I Wy Agus Gede Manik S,
                                M.Ked.Klin, Sp.MK
                            </div>

                            <div>
                                NIP. 198308142009021004
                            </div>

                        </div>

                    </div>

                @endif

                @endif

            {{-- =====================================================
                 LAB - PATOLOGI ANATOMI
                 ===================================================== --}}
            @elseif($isPatologiAnatomi)

                @php
                    /*
                     * Struktur final API Patologi Anatomi
                     * belum diberikan.
                     *
                     * Field di bawah dibuat tolerant:
                     * bila field tersedia akan tampil,
                     * bila tidak tersedia tidak menyebabkan error.
                     */

                    $paNamaProduk = data_get(
                        $paData,
                        'namaproduk',
                        data_get(
                            $paData,
                            'pemeriksaan',
                            '-'
                        )
                    );

                    $paHasil = data_get(
                        $paData,
                        'hasil',
                        data_get(
                            $paData,
                            'hasilpemeriksaan'
                        )
                    );

                    $paMakroskopik = data_get(
                        $paData,
                        'makroskopik'
                    );

                    $paMikroskopik = data_get(
                        $paData,
                        'mikroskopik'
                    );

                    $paKesimpulan = data_get(
                        $paData,
                        'kesimpulan',
                        data_get(
                            $paData,
                            'diagnosa'
                        )
                    );

                    $paCatatan = data_get(
                        $paData,
                        'catatan',
                        data_get(
                            $paData,
                            'comment'
                        )
                    );

                    $paTanggal = data_get(
                        $paData,
                        'tglhasil',
                        data_get(
                            $paData,
                            'tglkeluarhasil'
                        )
                    );
                @endphp

                <div class="pa-box">

                    <div class="pa-title">
                        HASIL PEMERIKSAAN PATOLOGI ANATOMI
                    </div>

                    <div class="pa-content">

                        <div class="pa-field">
                            <div class="pa-label">
                                Pemeriksaan
                            </div>
                            <div class="pa-value">
                                {{ $labText($paNamaProduk) }}
                            </div>
                        </div>

                        @if($paMakroskopik)
                            <div class="pa-field">
                                <div class="pa-label">
                                    Makroskopik
                                </div>
                                <div class="pa-value">
                                    {{ $labText($paMakroskopik) }}
                                </div>
                            </div>
                        @endif

                        @if($paMikroskopik)
                            <div class="pa-field">
                                <div class="pa-label">
                                    Mikroskopik
                                </div>
                                <div class="pa-value">
                                    {{ $labText($paMikroskopik) }}
                                </div>
                            </div>
                        @endif

                        @if($paHasil)
                            <div class="pa-field">
                                <div class="pa-label">
                                    Hasil Pemeriksaan
                                </div>
                                <div class="pa-value">
                                    {{ $labText($paHasil) }}
                                </div>
                            </div>
                        @endif

                        @if($paKesimpulan)
                            <div class="pa-field">
                                <div class="pa-label">
                                    Kesimpulan / Diagnosis
                                </div>
                                <div class="pa-value">
                                    {{ $labText($paKesimpulan) }}
                                </div>
                            </div>
                        @endif

                        @if($paCatatan)
                            <div class="pa-field">
                                <div class="pa-label">
                                    Catatan
                                </div>
                                <div class="pa-value">
                                    {{ $labText($paCatatan) }}
                                </div>
                            </div>
                        @endif

                        @if($paTanggal)
                            <div class="pa-field">
                                <div class="pa-label">
                                    Tanggal Hasil
                                </div>
                                <div class="pa-value">
                                    {{ $labText($paTanggal) }}
                                </div>
                            </div>
                        @endif

                        @if(
                            ! $paHasil
                            && ! $paMakroskopik
                            && ! $paMikroskopik
                            && ! $paKesimpulan
                        )
                            <div class="lab-alert is-warning">
                                Data Patologi Anatomi ditemukan,
                                tetapi struktur detail hasil belum
                                sesuai dengan field yang tersedia
                                pada tampilan ini.
                            </div>
                        @endif

                    </div>

                </div>

            {{-- =====================================================
                 JENIS LAB TIDAK DIKENALI
                 ===================================================== --}}
            @else

                <div
                    class="lab-alert is-error"
                    style="margin-top:18px;"
                >
                    Jenis laboratorium tidak dikenali:
                    <strong>
                        {{ $lab ?: '-' }}
                    </strong>
                </div>

            @endif

        </div>

    @endif

    <div class="lab-bottom-actions">
        <a
            href="{{ $laboratoryHistoryUrl }}"
            class="lab-nav-button is-lab"
        >
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            <span>Kembali ke Hasil Laboratorium</span>
        </a>

        <a
            href="{{ $mainMenuUrl }}"
            class="lab-nav-button is-menu"
        >
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M3 11L12 4L21 11" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M5 10V20H19V10M9 20V14H15V20" stroke="currentColor" stroke-width="2" stroke-linejoin="round" />
            </svg>
            <span>Menu Utama</span>
        </a>

        @if($logoutRouteName)
            <form
                method="POST"
                action="{{ route($logoutRouteName) }}"
                class="lab-logout-form"
            >
                @csrf

                <button
                    type="submit"
                    class="lab-nav-button is-logout"
                >
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M10 5H6C4.9 5 4 5.9 4 7V17C4 18.1 4.9 19 6 19H10" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        <path d="M14 8L18 12L14 16M18 12H9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <span>Logout</span>
                </button>
            </form>
        @endif
    </div>

</div>

@endsection

@section('content')

<style>
    .lab-page {
        width: min(100%, 1080px);
        margin: 0 auto;
        padding: 10px 0 40px;
    }

    .lab-header {
        margin-bottom: 22px;
        text-align: center;
    }

    .lab-header h1 {
        margin: 0 0 8px;
        color: #0f172a;
        font-size: clamp(28px, 5vw, 42px);
        font-weight: 900;
        letter-spacing: -0.04em;
    }

    .lab-header p {
        max-width: 680px;
        margin: 0 auto;
        color: #64748b;
        font-size: 14px;
        line-height: 1.7;
    }

    .lab-card {
        margin-bottom: 20px;
        padding: clamp(18px, 4vw, 28px);
        border: 1px solid #e2e8f0;
        border-radius: 26px;
        background: #fff;
        box-shadow: 0 14px 40px rgba(15, 23, 42, .06);
    }

    .result-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 18px;
    }

    .result-title {
        color: #0f172a;
        font-size: 21px;
        font-weight: 900;
    }

    .lab-meta {
        margin-top: 7px;
        color: #94a3b8;
        font-size: 11px;
        line-height: 1.55;
    }

    .badge-row {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-end;
        gap: 8px;
    }

    .badge {
        display: inline-flex;
        align-items: center;
        padding: 8px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 900;
    }

    .badge.is-type {
        background: #f1f5f9;
        color: #475569;
    }

    .badge.is-micro {
        background: #f3e8ff;
        color: #7e22ce;
    }

    .badge.is-pa {
        background: #ffedd5;
        color: #9a3412;
    }

    .badge.is-order {
        background: #dcfce7;
        color: #166534;
    }

    .badge.is-billing {
        background: #e0f2fe;
        color: #0369a1;
    }

    .lab-alert {
        padding: 15px 17px;
        border-radius: 15px;
        font-size: 14px;
        font-weight: 700;
        line-height: 1.55;
    }

    .lab-alert.is-success {
        border: 1px solid #bbf7d0;
        background: #f0fdf4;
        color: #166534;
    }

    .lab-alert.is-error {
        border: 1px solid #fecaca;
        background: #fef2f2;
        color: #b91c1c;
    }

    .lab-alert.is-warning {
        border: 1px solid #fde68a;
        background: #fffbeb;
        color: #92400e;
    }

    /* =========================================================
       PATOLOGI KLINIK
       ========================================================= */

    .lab-group {
        margin-top: 18px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        background: #fff;
    }

    .lab-group-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 16px 18px;
        border-bottom: 1px solid #e2e8f0;
        background: #f8fafc;
    }

    .lab-group-title {
        color: #0f172a;
        font-size: 16px;
        font-weight: 900;
    }

    .lab-group-count {
        padding: 5px 9px;
        border-radius: 999px;
        background: #e2e8f0;
        color: #475569;
        font-size: 11px;
        font-weight: 900;
    }

    .group-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 10px 24px;
        padding: 12px 18px;
        border-bottom: 1px solid #e2e8f0;
        background: #fff;
    }

    .lab-table-wrapper {
        overflow-x: auto;
    }

    .lab-table {
        width: 100%;
        min-width: 720px;
        border-collapse: collapse;
    }

    .lab-table th {
        padding: 12px 14px;
        border-bottom: 1px solid #e2e8f0;
        background: #fff;
        color: #64748b;
        font-size: 11px;
        font-weight: 900;
        text-align: left;
        text-transform: uppercase;
        letter-spacing: .05em;
    }

    .lab-table td {
        padding: 14px;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
        font-size: 13px;
        vertical-align: middle;
    }

    .lab-table tr:last-child td {
        border-bottom: 0;
    }

    .test-name {
        color: #0f172a;
        font-weight: 800;
    }

    .result-value {
        color: #0f172a;
        font-size: 15px;
        font-weight: 900;
    }

    .result-value.is-high {
        color: #dc2626;
    }

    .result-value.is-low {
        color: #d97706;
    }

    .flag {
        display: inline-flex;
        min-width: 28px;
        align-items: center;
        justify-content: center;
        padding: 5px 8px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 900;
    }

    .flag.is-normal {
        background: #dcfce7;
        color: #166534;
    }

    .flag.is-high {
        background: #fee2e2;
        color: #b91c1c;
    }

    .flag.is-low {
        background: #fef3c7;
        color: #92400e;
    }

    .flag.is-other {
        background: #e2e8f0;
        color: #475569;
    }

    /* =========================================================
       MIKROBIOLOGI
       ========================================================= */

    .micro-report-title {
        margin-top: 18px;
        padding: 15px 18px;
        border: 1px solid #cbd5e1;
        border-radius: 16px 16px 0 0;
        background: #f8fafc;
        color: #0f172a;
        text-align: center;
        font-size: 17px;
        font-weight: 900;
    }

    .micro-info-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        border-right: 1px solid #cbd5e1;
        border-bottom: 1px solid #cbd5e1;
        border-left: 1px solid #cbd5e1;
    }

    .micro-panel {
        padding: 18px;
    }

    .micro-panel + .micro-panel {
        border-left: 1px solid #cbd5e1;
    }

    .section-title {
        margin-bottom: 13px;
        color: #0f172a;
        font-size: 14px;
        font-weight: 900;
        text-align: center;
    }

    .info-list {
        display: grid;
        gap: 8px;
    }

    .info-row {
        display: grid;
        grid-template-columns: 150px 12px minmax(0, 1fr);
        gap: 4px;
        color: #334155;
        font-size: 13px;
        line-height: 1.45;
    }

    .info-label {
        color: #64748b;
        font-weight: 700;
    }

    .micro-result {
        padding: 18px;
        border-right: 1px solid #cbd5e1;
        border-bottom: 1px solid #cbd5e1;
        border-left: 1px solid #cbd5e1;
    }

    .micro-result-grid {
        display: grid;
        grid-template-columns: 170px 12px minmax(0, 1fr);
        gap: 8px 4px;
        color: #334155;
        font-size: 14px;
        line-height: 1.55;
    }

    .micro-result-value {
        color: #0f172a;
        font-size: 16px;
        font-weight: 900;
    }

    .micro-comment {
        margin-top: 16px;
        padding: 15px 17px;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        background: #f8fafc;
        color: #475569;
        font-size: 13px;
        line-height: 1.7;
        white-space: pre-line;
    }

    .micro-culture-card {
        padding: 18px;
        border-bottom: 1px solid #e2e8f0;
    }

    .micro-culture-card:last-child {
        border-bottom: 0;
    }

    .micro-culture-heading {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
    }

    .micro-culture-product {
        color: #0f172a;
        font-size: 17px;
        font-weight: 900;
    }

    .micro-culture-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 10px 24px;
        margin-top: 14px;
        padding: 12px 14px;
        border-radius: 12px;
        background: #f8fafc;
    }

    .micro-culture-meta > div {
        display: flex;
        flex-direction: column;
        gap: 2px;
        color: #334155;
        font-size: 12px;
    }

    .micro-culture-meta span {
        color: #94a3b8;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .04em;
    }

    .micro-culture-section {
        margin-top: 16px;
    }

    .micro-culture-report {
        margin: 0;
        padding: 16px;
        overflow-x: auto;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        background: #0f172a;
        color: #e2e8f0;
        font-family: Consolas, Monaco, monospace;
        font-size: 12px;
        line-height: 1.65;
        white-space: pre-wrap;
        word-break: break-word;
    }

    /* =========================================================
       WATERMARK MDRO
       Tampil hanya jika commentheader mengandung "MDRO".
       ========================================================= */

    .mdro-watermark {
        position: relative;
        overflow: hidden;
        isolation: isolate;
    }

    .mdro-watermark::after {
        content: "MDRO";
        position: absolute;
        top: 50%;
        left: 50%;
        z-index: 20;
        transform: translate(-50%, -50%) rotate(-28deg);
        color: rgba(220, 38, 38, .16);
        font-size: clamp(72px, 13vw, 150px);
        font-weight: 1000;
        line-height: 1;
        letter-spacing: .08em;
        white-space: nowrap;
        pointer-events: none;
        user-select: none;
    }

    .signature-wrap {
        display: flex;
        justify-content: flex-end;
        margin-top: 24px;
    }

    .signature-box {
        width: min(100%, 430px);
        color: #334155;
        font-size: 13px;
        line-height: 1.7;
    }

    .signature-space {
        height: 56px;
    }

    .signature-name {
        color: #0f172a;
        font-weight: 800;
        text-decoration: underline;
    }

    /* =========================================================
       PATOLOGI ANATOMI
       ========================================================= */

    .pa-box {
        margin-top: 18px;
        overflow: hidden;
        border: 1px solid #fed7aa;
        border-radius: 18px;
        background: #fff;
    }

    .pa-title {
        padding: 15px 18px;
        border-bottom: 1px solid #fed7aa;
        background: #fff7ed;
        color: #9a3412;
        font-size: 16px;
        font-weight: 900;
    }

    .pa-content {
        padding: 18px;
    }

    .pa-field {
        margin-bottom: 16px;
    }

    .pa-field:last-child {
        margin-bottom: 0;
    }

    .pa-label {
        margin-bottom: 5px;
        color: #64748b;
        font-size: 12px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .04em;
    }

    .pa-value {
        color: #0f172a;
        font-size: 14px;
        line-height: 1.7;
        white-space: pre-line;
    }

    /* =========================================================
       NAVIGASI SAPA RSBM
       ========================================================= */

    .lab-navigation {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 18px;
        padding: 10px;
        border: 1px solid rgba(203, 213, 225, .85);
        border-radius: 18px;
        background: rgba(255, 255, 255, .96);
        box-shadow: 0 10px 28px rgba(15, 23, 42, .055);
    }

    .lab-navigation-left,
    .lab-navigation-right,
    .lab-bottom-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .lab-navigation-right {
        justify-content: flex-end;
    }

    .lab-nav-button {
        display: inline-flex;
        min-height: 40px;
        align-items: center;
        justify-content: center;
        gap: 7px;
        padding: 0 13px;
        border: 1px solid transparent;
        border-radius: 12px;
        font-family: inherit;
        font-size: 11px;
        font-weight: 900;
        line-height: 1;
        text-decoration: none;
        white-space: nowrap;
        cursor: pointer;
        transition:
            transform .18s ease,
            box-shadow .18s ease,
            border-color .18s ease,
            background .18s ease;
    }

    .lab-nav-button:hover {
        transform: translateY(-1px);
        text-decoration: none;
    }

    .lab-nav-button.is-lab {
        border-color: #bbf7d0;
        background: #f0fdf4;
        color: #15803d;
    }

    .lab-nav-button.is-lab:hover {
        border-color: #86efac;
        background: #dcfce7;
        color: #166534;
    }

    .lab-nav-button.is-menu {
        border-color: #c7d2fe;
        background: #eef1ff;
        color: #26358f;
    }

    .lab-nav-button.is-menu:hover {
        border-color: #a5b4fc;
        background: #e0e7ff;
        color: #1d286d;
    }

    .lab-nav-button.is-logout {
        border-color: #fecaca;
        background: #fff5f5;
        color: #b91c1c;
    }

    .lab-nav-button.is-logout:hover {
        border-color: #fca5a5;
        background: #fee2e2;
        color: #991b1b;
    }

    .lab-nav-button svg {
        flex: 0 0 auto;
    }

    .lab-bottom-actions {
        justify-content: center;
        margin-top: 22px;
        padding-top: 18px;
        border-top: 1px solid #e2e8f0;
    }

    .lab-logout-form {
        margin: 0;
    }

    @media (max-width: 720px) {
        .lab-navigation {
            align-items: stretch;
            flex-direction: column;
            padding: 9px;
        }

        .lab-navigation-left,
        .lab-navigation-right {
            display: grid;
            width: 100%;
            grid-template-columns: 1fr;
        }

        .lab-nav-button {
            width: 100%;
            min-height: 42px;
        }

        .lab-bottom-actions {
            display: grid;
            grid-template-columns: 1fr;
        }

        .lab-bottom-actions .lab-logout-form,
        .lab-bottom-actions .lab-nav-button {
            width: 100%;
        }

        .result-header {
            flex-direction: column;
        }

        .badge-row {
            justify-content: flex-start;
        }

        .micro-info-grid {
            grid-template-columns: 1fr;
        }

        .micro-panel + .micro-panel {
            border-top: 1px solid #cbd5e1;
            border-left: 0;
        }

        .info-row {
            grid-template-columns: 115px 10px minmax(0, 1fr);
            font-size: 12px;
        }

        .micro-result-grid {
            grid-template-columns: 120px 10px minmax(0, 1fr);
            font-size: 13px;
        }
    }
</style>

@php
    /*
     * =========================================================
     * JENIS LAB BERASAL DARI PARAMETER ROUTE
     * =========================================================
     *
     * Nilai yang diharapkan:
     * - LAB - PATOLOGI KLINIK
     * - LAB - MIKROBIOLOGI KLINIK
     * - LAB - PATOLOGI ANATOMI
     */

    $labName = strtoupper(
        trim((string) ($lab ?? ''))
    );

    $isMikrobiologi = str_contains(
        $labName,
        'MIKROBIOLOGI'
    );

    $isPatologiAnatomi = str_contains(
        $labName,
        'PATOLOGI ANATOMI'
    );

    $isPatologiKlinik =
        str_contains(
            $labName,
            'PATOLOGI KLINIK'
        )
        && ! $isMikrobiologi
        && ! $isPatologiAnatomi;

    $success = (bool) data_get(
        $result,
        'success',
        false
    );

    $payload = data_get(
        $result,
        'data',
        []
    );

    $responseNoOrder = data_get(
        $payload,
        'noorder',
        $noOrder ?? '-'
    );

    $noBilling = data_get(
        $payload,
        'nobilling',
        '-'
    );

    /*
     * Patologi Klinik:
     * data.hasil = [
     *   [
     *     'group' => 'HEMATOLOGI',
     *     'items' => [...]
     *   ]
     * ]
     */
    $clinicalGroups = data_get(
        $payload,
        'hasil',
        []
    );

    /*
     * Mikrobiologi:
     * mendukung response langsung pada data
     * maupun data.mikrobiologi.
     */
    $microData = data_get(
        $payload,
        'mikrobiologi',
        $payload
    );

    /*
     * Patologi Anatomi:
     * sementara mendukung response langsung pada data
     * maupun data.patologi_anatomi.
     */
    $paData = data_get(
        $payload,
        'patologi_anatomi',
        $payload
    );

    /*
     * Navigasi halaman.
     * Menggunakan fallback URL agar Blade tidak error bila nama route
     * berbeda pada environment tertentu.
     */
    $laboratoryHistoryUrl = Route::has('laboratory.index')
        ? route('laboratory.index')
        : url('/cek-hasil-laboratorium');

    $mainMenuUrl = Route::has('layanan.menu')
        ? route('layanan.menu')
        : url('/layanan/menu');

    $logoutRouteName = Route::has('layanan.logout')
        ? 'layanan.logout'
        : (Route::has('logout') ? 'logout' : null);

    $labText = static function ($value, $default = '-') {
        if ($value === null) {
            return $default;
        }

        if ($value instanceof \Illuminate\Support\Collection) {
            $value = $value->all();
        }

        if (is_object($value)) {
            $value = (array) $value;
        }

        if (is_array($value)) {
            $parts = [];

            array_walk_recursive(
                $value,
                static function ($item) use (&$parts) {
                    if (
                        is_scalar($item)
                        && trim((string) $item) !== ''
                    ) {
                        $parts[] = trim((string) $item);
                    }
                }
            );

            $parts = array_values(
                array_unique($parts)
            );

            return count($parts)
                ? implode(', ', $parts)
                : $default;
        }

        if (is_bool($value)) {
            return $value ? 'Ya' : 'Tidak';
        }

        if (! is_scalar($value)) {
            return $default;
        }

        $value = trim((string) $value);

        return $value !== ''
            ? $value
            : $default;
    };
@endphp

<div class="lab-page">

    <nav class="lab-navigation" aria-label="Navigasi halaman laboratorium">
        <div class="lab-navigation-left">
            <a
                href="{{ $laboratoryHistoryUrl }}"
                class="lab-nav-button is-lab"
            >
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <span>Kembali ke Hasil Laboratorium</span>
            </a>
        </div>

        <div class="lab-navigation-right">
            <a
                href="{{ $mainMenuUrl }}"
                class="lab-nav-button is-menu"
            >
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M3 11L12 4L21 11" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M5 10V20H19V10M9 20V14H15V20" stroke="currentColor" stroke-width="2" stroke-linejoin="round" />
                </svg>
                <span>Menu Utama</span>
            </a>

            @if($logoutRouteName)
                <form
                    method="POST"
                    action="{{ route($logoutRouteName) }}"
                    class="lab-logout-form"
                >
                    @csrf

                    <button
                        type="submit"
                        class="lab-nav-button is-logout"
                    >
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M10 5H6C4.9 5 4 5.9 4 7V17C4 18.1 4.9 19 6 19H10" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            <path d="M14 8L18 12L14 16M18 12H9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <span>Logout</span>
                    </button>
                </form>
            @endif
        </div>
    </nav>

    <header class="lab-header">
        <h1>Detail Hasil Laboratorium</h1>

        <p>
            Hasil pemeriksaan laboratorium RS Bali Mandara
            berdasarkan nomor order yang dipilih.
        </p>
    </header>

    @if($result === null)

        <div class="lab-card">
            <div class="lab-alert is-error">
                Data hasil laboratorium belum tersedia.
            </div>
        </div>

    @elseif(! $success)

        <div class="lab-card">
            <div class="lab-alert is-error">
                {{ data_get(
                    $result,
                    'message',
                    'Hasil laboratorium tidak ditemukan.'
                ) }}
            </div>
        </div>

    @else

        <div class="lab-card">

            <div class="result-header">

                <div>

                    <div class="result-title">
                        @if($isMikrobiologi)
                            Hasil Pemeriksaan Mikrobiologi Klinik
                        @elseif($isPatologiAnatomi)
                            Hasil Pemeriksaan Patologi Anatomi
                        @elseif($isPatologiKlinik)
                            Hasil Pemeriksaan Patologi Klinik
                        @else
                            Hasil Pemeriksaan Laboratorium
                        @endif
                    </div>

                    <div class="lab-meta">
                        {{ $lab ?: 'Laboratorium RS Bali Mandara' }}
                    </div>

                </div>

                <div class="badge-row">

                    @if($isMikrobiologi)
                        <span class="badge is-type is-micro">
                            Mikrobiologi Klinik
                        </span>
                    @elseif($isPatologiAnatomi)
                        <span class="badge is-type is-pa">
                            Patologi Anatomi
                        </span>
                    @elseif($isPatologiKlinik)
                        <span class="badge is-type">
                            Patologi Klinik
                        </span>
                    @endif

                    @if(
                        $responseNoOrder
                        && $responseNoOrder !== '-'
                    )
                        <span class="badge is-order">
                            Order: {{ $labText($responseNoOrder) }}
                        </span>
                    @endif

                    @if(
                        $noBilling
                        && $noBilling !== '-'
                    )
                        <span class="badge is-billing">
                            Billing: {{ $labText($noBilling) }}
                        </span>
                    @endif

                </div>

            </div>

            <div class="lab-alert is-success">
                {{ data_get(
                    $result,
                    'message',
                    'Data hasil laboratorium berhasil diambil.'
                ) }}
            </div>

            {{-- =====================================================
                 LAB - PATOLOGI KLINIK
                 ===================================================== --}}
            @if($isPatologiKlinik)

                <div
                    class="lab-meta"
                    style="margin-top:10px;"
                >
                    Keterangan:
                    <strong>N</strong> = Normal,
                    <strong>H/HH</strong> = Tinggi,
                    <strong>L/LL</strong> = Rendah.
                </div>

                @forelse($clinicalGroups as $group)

                    @php
                        $items = data_get(
                            $group,
                            'items',
                            []
                        );
                    @endphp

                    <div class="lab-group">

                        <div class="lab-group-header">

                            <div class="lab-group-title">
                                {{ data_get(
                                    $group,
                                    'group',
                                    'Pemeriksaan'
                                ) }}
                            </div>

                            <div class="lab-group-count">
                                {{ count($items) }}
                                pemeriksaan
                            </div>

                        </div>

                        @if(
                            data_get($group, 'diotorisasi')
                            || data_get(
                                $group,
                                'dokterdiperiksa'
                            )
                        )

                            <div class="group-meta">

                                @if(
                                    data_get(
                                        $group,
                                        'diotorisasi'
                                    )
                                )
                                    <div
                                        class="lab-meta"
                                        style="margin-top:0;"
                                    >
                                        <strong
                                            style="color:#475569;"
                                        >
                                            Diotorisasi:
                                        </strong>

                                        {{ data_get(
                                            $group,
                                            'diotorisasi'
                                        ) }}
                                    </div>
                                @endif

                                @if(
                                    data_get(
                                        $group,
                                        'dokterdiperiksa'
                                    )
                                )
                                    <div
                                        class="lab-meta"
                                        style="margin-top:0;"
                                    >
                                        <strong
                                            style="color:#475569;"
                                        >
                                            Pemeriksa:
                                        </strong>

                                        {{ data_get(
                                            $group,
                                            'dokterdiperiksa'
                                        ) }}
                                    </div>
                                @endif

                            </div>

                        @endif

                        <div class="lab-table-wrapper">

                            <table class="lab-table">

                                <thead>
                                    <tr>
                                        <th>Pemeriksaan</th>
                                        <th>Hasil</th>
                                        <th>Flag</th>
                                        <th>Satuan</th>
                                        <th>Nilai Rujukan</th>
                                    </tr>
                                </thead>

                                <tbody>

                                @foreach($items as $item)

                                    @php
                                        $flag = strtoupper(
                                            trim(
                                                (string) data_get(
                                                    $item,
                                                    'flag',
                                                    ''
                                                )
                                            )
                                        );

                                        $flagClass = 'is-other';
                                        $resultClass = '';

                                        if (
                                            $flag === 'N'
                                            || $flag === ''
                                        ) {
                                            $flagClass =
                                                'is-normal';
                                        } elseif (
                                            str_contains(
                                                $flag,
                                                'H'
                                            )
                                        ) {
                                            $flagClass =
                                                'is-high';

                                            $resultClass =
                                                'is-high';
                                        } elseif (
                                            str_contains(
                                                $flag,
                                                'L'
                                            )
                                        ) {
                                            $flagClass =
                                                'is-low';

                                            $resultClass =
                                                'is-low';
                                        }

                                        $method = trim(
                                            (string) data_get(
                                                $item,
                                                'metode',
                                                ''
                                            )
                                        );
                                    @endphp

                                    <tr>

                                        <td>

                                            <div class="test-name">
                                                {{ data_get(
                                                    $item,
                                                    'detailpemeriksaan',
                                                    '-'
                                                ) }}
                                            </div>

                                            @if(
                                                data_get(
                                                    $item,
                                                    'namaproduk'
                                                )
                                            )
                                                <div class="lab-meta">
                                                    {{ data_get(
                                                        $item,
                                                        'namaproduk'
                                                    ) }}
                                                </div>
                                            @endif

                                            @if(
                                                data_get(
                                                    $item,
                                                    'tglhasil'
                                                )
                                            )
                                                <div class="lab-meta">
                                                    Hasil:
                                                    {{ data_get(
                                                        $item,
                                                        'tglhasil'
                                                    ) }}
                                                </div>
                                            @endif

                                            @if(
                                                $method !== ''
                                                && $method !== '-'
                                            )
                                                <div class="lab-meta">
                                                    Metode:
                                                    {{ $method }}
                                                </div>
                                            @endif

                                        </td>

                                        <td>
                                            <span
                                                class="result-value {{ $resultClass }}"
                                            >
                                                {{ data_get(
                                                    $item,
                                                    'hasil',
                                                    '-'
                                                ) }}
                                            </span>
                                        </td>

                                        <td>
                                            <span
                                                class="flag {{ $flagClass }}"
                                            >
                                                {{ $flag !== ''
                                                    ? $flag
                                                    : 'N' }}
                                            </span>
                                        </td>

                                        <td>
                                            {{ data_get(
                                                $item,
                                                'satuanstandar',
                                                '-'
                                            ) ?: '-' }}
                                        </td>

                                        <td>
                                            {{ data_get(
                                                $item,
                                                'nilaitext',
                                                '-'
                                            ) ?: '-' }}
                                        </td>

                                    </tr>

                                @endforeach

                                </tbody>

                            </table>

                        </div>

                    </div>

                @empty

                    <div
                        class="lab-alert is-error"
                        style="margin-top:18px;"
                    >
                        Data hasil pemeriksaan
                        Patologi Klinik belum tersedia.
                    </div>

                @endforelse

            {{-- =====================================================
                 LAB - MIKROBIOLOGI KLINIK
                 ===================================================== --}}
            @elseif($isMikrobiologi)

                @php
                    /*
                     * Struktur API Mikrobiologi terbaru:
                     * data.hasil = [
                     *   [
                     *     'group' => 'MIKROBIOLOGI',
                     *     'items' => [...],
                     *     'diotorisasi' => '...',
                     *     'dokterdiperiksa' => '...'
                     *   ]
                     * ]
                     *
                     * result_ft berisi laporan kultur/antibiogram
                     * dengan karakter ~ sebagai pemisah baris.
                     */
                    $microGroups = data_get(
                        $payload,
                        'hasil',
                        []
                    );

                    /*
                     * Jika ada items di data.hasil, gunakan format baru.
                     * Jika tidak, tampilan mikrobiologi lama di bawah
                     * tetap digunakan sebagai fallback.
                     */
                    $hasGroupedMicroResult =
                        is_array($microGroups)
                        && count($microGroups) > 0
                        && is_array(
                            data_get(
                                $microGroups,
                                '0.items'
                            )
                        );
                @endphp

                @if($hasGroupedMicroResult)

                    @forelse($microGroups as $group)

                        @php
                            $items = data_get(
                                $group,
                                'items',
                                []
                            );

                            $groupName = $labText(
                                data_get(
                                    $group,
                                    'group',
                                    'MIKROBIOLOGI'
                                )
                            );

                            $authorizedBy = trim(
                                (string) data_get(
                                    $group,
                                    'diotorisasi',
                                    ''
                                )
                            );

                            $examinedBy = trim(
                                (string) data_get(
                                    $group,
                                    'dokterdiperiksa',
                                    ''
                                )
                            );
                        @endphp

                        <div class="micro-report-title">
                            HASIL PEMERIKSAAN {{ strtoupper($groupName) }}
                        </div>

                        <div
                            class="lab-group"
                            style="margin-top:0; border-radius:0 0 20px 20px;"
                        >

                            <div class="lab-group-header">
                                <div class="lab-group-title">
                                    {{ $groupName }}
                                </div>

                                <div class="lab-group-count">
                                    {{ count($items) }} pemeriksaan
                                </div>
                            </div>

                            @if($authorizedBy !== '' || $examinedBy !== '')
                                <div class="group-meta">
                                    @if($examinedBy !== '')
                                        <div
                                            class="lab-meta"
                                            style="margin-top:0;"
                                        >
                                            <strong style="color:#475569;">
                                                Pemeriksa:
                                            </strong>
                                            {{ $examinedBy }}
                                        </div>
                                    @endif

                                    @if($authorizedBy !== '')
                                        <div
                                            class="lab-meta"
                                            style="margin-top:0;"
                                        >
                                            <strong style="color:#475569;">
                                                Diotorisasi:
                                            </strong>
                                            {{ $authorizedBy }}
                                        </div>
                                    @endif
                                </div>
                            @endif

                            @forelse($items as $item)

                                @php
                                    $productName = $labText(
                                        data_get(
                                            $item,
                                            'namaproduk',
                                            '-'
                                        )
                                    );

                                    $detailName = $labText(
                                        data_get(
                                            $item,
                                            'detailpemeriksaan',
                                            '-'
                                        )
                                    );

                                    $rawResultFt = data_get(
                                        $item,
                                        'result_ft',
                                        ''
                                    );

                                    $rawResult = data_get(
                                        $item,
                                        'hasil',
                                        ''
                                    );

                                    $formattedResultFt = trim(
                                        str_replace(
                                            '~',
                                            "\n",
                                            is_scalar($rawResultFt)
                                                ? (string) $rawResultFt
                                                : ''
                                        )
                                    );

                                    $formattedComment = trim(
                                        str_replace(
                                            '~',
                                            "\n",
                                            (string) data_get(
                                                $item,
                                                'comment',
                                                ''
                                            )
                                        )
                                    );

                                    $method = trim(
                                        (string) data_get(
                                            $item,
                                            'metode',
                                            ''
                                        )
                                    );

                                    $flag = strtoupper(
                                        trim(
                                            (string) data_get(
                                                $item,
                                                'flag',
                                                ''
                                            )
                                        )
                                    );

                                    $reportDate = trim(
                                        (string) data_get(
                                            $item,
                                            'tglhasil',
                                            ''
                                        )
                                    );

                                    /*
                                     * Watermark MDRO hanya untuk item
                                     * mikrobiologi yang commentheader-nya
                                     * mengandung kata MDRO.
                                     */
                                    $commentHeader = strtoupper(
                                        trim(
                                            (string) data_get(
                                                $item,
                                                'commentheader',
                                                ''
                                            )
                                        )
                                    );

                                    $isMdro = str_contains(
                                        $commentHeader,
                                        'MDRO'
                                    );
                                @endphp

                                <div class="micro-culture-card {{ $isMdro ? 'mdro-watermark' : '' }}">

                                    <div class="micro-culture-heading">
                                        <div>
                                            <div class="micro-culture-product">
                                                {{ $productName }}
                                            </div>

                                            <div class="lab-meta">
                                                Detail pemeriksaan:
                                                <strong style="color:#475569;">
                                                    {{ $detailName }}
                                                </strong>
                                            </div>
                                        </div>

                                        @if($flag !== '')
                                            <span class="flag is-other">
                                                {{ $flag }}
                                            </span>
                                        @endif
                                    </div>

                                    <div class="micro-culture-meta">
                                        @if($reportDate !== '')
                                            <div>
                                                <span>Tanggal hasil</span>
                                                <strong>
                                                    {{ $reportDate }}
                                                </strong>
                                            </div>
                                        @endif

                                        @if($method !== '' && $method !== '-')
                                            <div>
                                                <span>Metode</span>
                                                <strong>{{ $method }}</strong>
                                            </div>
                                        @endif

                                        @if(data_get($item, 'satuanstandar'))
                                            <div>
                                                <span>Satuan</span>
                                                <strong>
                                                    {{ data_get(
                                                        $item,
                                                        'satuanstandar'
                                                    ) }}
                                                </strong>
                                            </div>
                                        @endif
                                    </div>

                                    @if($formattedResultFt !== '')
                                        <div class="micro-culture-section">
                                            <div
                                                class="section-title"
                                                style="text-align:left; margin-bottom:8px;"
                                            >
                                                Hasil Kultur / Antibiogram
                                            </div>

                                            <pre class="micro-culture-report">{{ $formattedResultFt }}</pre>
                                        </div>
                                    @elseif(
                                        is_scalar($rawResult)
                                        && trim((string) $rawResult) !== ''
                                    )
                                        <div class="micro-culture-section">
                                            <div
                                                class="section-title"
                                                style="text-align:left; margin-bottom:8px;"
                                            >
                                                Hasil
                                            </div>

                                            <div class="micro-result-value">
                                                {{ trim((string) $rawResult) }}
                                            </div>
                                        </div>
                                    @else
                                        <div
                                            class="lab-alert is-warning"
                                            style="margin-top:16px;"
                                        >
                                            Hasil pemeriksaan belum tersedia.
                                        </div>
                                    @endif

                                    @if($formattedComment !== '')
                                        <div class="micro-comment">
                                            <strong>Komentar:</strong>
                                            {{ $formattedComment }}
                                        </div>
                                    @endif

                                </div>

                            @empty

                                <div
                                    class="lab-alert is-warning"
                                    style="margin:18px;"
                                >
                                    Item hasil mikrobiologi belum tersedia.
                                </div>

                            @endforelse

                        </div>

                    @empty

                        <div
                            class="lab-alert is-warning"
                            style="margin-top:18px;"
                        >
                            Data hasil pemeriksaan Mikrobiologi
                            belum tersedia.
                        </div>

                    @endforelse

                @else

                    {{-- Fallback struktur mikrobiologi lama --}}

                @php
                    $namaProduk = $labText(
                        data_get(
                            $microData,
                            'namaproduk',
                            '-'
                        )
                    );

                    $rawHasilSpesimen = data_get(
                        $microData,
                        'hasilspesimen'
                    );

                    if ($rawHasilSpesimen === null) {
                        $candidateHasil = data_get(
                            $microData,
                            'hasil'
                        );

                        $rawHasilSpesimen =
                            is_scalar($candidateHasil)
                                ? $candidateHasil
                                : '-';
                    }

                    $hasilSpesimen = $labText(
                        $rawHasilSpesimen
                    );

                    $microComment = $labText(
                        data_get(
                            $microData,
                            'comment',
                            data_get(
                                $microData,
                                'komentar',
                                ''
                            )
                        ),
                        ''
                    );

                    $microCommentHeader = strtoupper(
                        trim(
                            (string) data_get(
                                $microData,
                                'commentheader',
                                ''
                            )
                        )
                    );

                    $isMdroFallback = str_contains(
                        $microCommentHeader,
                        'MDRO'
                    );

                    $upperProduct = strtoupper(
                        $namaProduk
                    );

                    $isCovid =
                        str_contains(
                            $upperProduct,
                            'COVID'
                        )
                        || str_contains(
                            $upperProduct,
                            'SARS'
                        );

                    $rawReportDate = data_get(
                        $microData,
                        'tglkeluarhasil',
                        data_get(
                            $microData,
                            'tglhasil'
                        )
                    );

                    $reportDate =
                        is_scalar($rawReportDate)
                            ? trim(
                                (string) $rawReportDate
                            )
                            : '';

                    $reportDateText = '-';

                    if ($reportDate !== '') {
                        try {
                            $reportDateText =
                                \Carbon\Carbon::parse(
                                    $reportDate
                                )->isoFormat(
                                    'DD MMMM Y'
                                );
                        } catch (\Throwable $e) {
                            $reportDateText =
                                $reportDate;
                        }
                    }
                @endphp

                <div class="micro-report-title">
                    {{ $isCovid
                        ? 'HASIL PEMERIKSAAN COVID-19'
                        : 'HASIL PEMERIKSAAN MIKROBIOLOGI' }}
                </div>

                <div class="micro-info-grid">

                    <div class="micro-panel">

                        <div class="section-title">
                            Informasi Pasien
                        </div>

                        <div class="info-list">

                            <div class="info-row">
                                <div class="info-label">
                                    Nama
                                </div>
                                <div>:</div>
                                <div>
                                    {{ $labText(
                                        data_get(
                                            $microData,
                                            'namapasien',
                                            '-'
                                        )
                                    ) }}
                                </div>
                            </div>

                            <div class="info-row">
                                <div class="info-label">
                                    Tanggal Lahir
                                </div>
                                <div>:</div>
                                <div>
                                    {{ $labText(
                                        data_get(
                                            $microData,
                                            'tgllahir',
                                            '-'
                                        )
                                    ) }}

                                    @if(
                                        data_get(
                                            $microData,
                                            'umur'
                                        )
                                    )
                                        /
                                        {{ $labText(
                                            data_get(
                                                $microData,
                                                'umur'
                                            )
                                        ) }}
                                    @endif
                                </div>
                            </div>

                            <div class="info-row">
                                <div class="info-label">
                                    Jenis Kelamin
                                </div>
                                <div>:</div>
                                <div>
                                    {{ $labText(
                                        data_get(
                                            $microData,
                                            'jeniskelamin',
                                            '-'
                                        )
                                    ) }}
                                </div>
                            </div>

                            <div class="info-row">
                                <div class="info-label">
                                    No. RM
                                </div>
                                <div>:</div>
                                <div>
                                    {{ $labText(
                                        data_get(
                                            $microData,
                                            'nocm',
                                            '-'
                                        )
                                    ) }}
                                </div>
                            </div>

                            @if(
                                data_get(
                                    $microData,
                                    'noidentitas'
                                )
                            )
                                <div class="info-row">
                                    <div class="info-label">
                                        No. Identitas
                                    </div>
                                    <div>:</div>
                                    <div>
                                        {{ $labText(
                                            data_get(
                                                $microData,
                                                'noidentitas'
                                            )
                                        ) }}
                                    </div>
                                </div>
                            @endif

                            @if(
                                data_get(
                                    $microData,
                                    'nohp'
                                )
                            )
                                <div class="info-row">
                                    <div class="info-label">
                                        No. Telp/HP
                                    </div>
                                    <div>:</div>
                                    <div>
                                        {{ $labText(
                                            data_get(
                                                $microData,
                                                'nohp'
                                            )
                                        ) }}
                                    </div>
                                </div>
                            @endif

                            @if(
                                data_get(
                                    $microData,
                                    'alamatlengkap'
                                )
                            )
                                <div class="info-row">
                                    <div class="info-label">
                                        Alamat
                                    </div>
                                    <div>:</div>
                                    <div>
                                        {{ $labText(
                                            data_get(
                                                $microData,
                                                'alamatlengkap'
                                            )
                                        ) }}
                                    </div>
                                </div>
                            @endif

                        </div>

                    </div>

                    <div class="micro-panel">

                        <div class="section-title">
                            Informasi Spesimen
                        </div>

                        <div class="info-list">

                            <div class="info-row">
                                <div class="info-label">
                                    Jenis Spesimen
                                </div>
                                <div>:</div>
                                <div>
                                    {{ $labText(
                                        data_get(
                                            $microData,
                                            'jenisspesimen',
                                            '-'
                                        )
                                    ) }}
                                </div>
                            </div>

                            <div class="info-row">
                                <div class="info-label">
                                    Kode Spesimen
                                </div>
                                <div>:</div>
                                <div>
                                    {{ $labText(
                                        data_get(
                                            $microData,
                                            'kodespesimen',
                                            '-'
                                        )
                                    ) }}
                                </div>
                            </div>

                            <div class="info-row">
                                <div class="info-label">
                                    Spesimen Ke-
                                </div>
                                <div>:</div>
                                <div>
                                    {{ $labText(
                                        data_get(
                                            $microData,
                                            'spesimenke',
                                            '-'
                                        )
                                    ) }}
                                </div>
                            </div>

                            <div class="info-row">
                                <div class="info-label">
                                    Asal Spesimen
                                </div>
                                <div>:</div>
                                <div>
                                    {{ $labText(
                                        data_get(
                                            $microData,
                                            'asalspesimen',
                                            '-'
                                        )
                                    ) }}
                                </div>
                            </div>

                            <div class="info-row">
                                <div class="info-label">
                                    Tgl & Jam Pengambilan
                                </div>
                                <div>:</div>
                                <div>
                                    {{ $labText(
                                        data_get(
                                            $microData,
                                            'tglterimaspesimen',
                                            '-'
                                        )
                                    ) }}
                                </div>
                            </div>

                            <div class="info-row">
                                <div class="info-label">
                                    Tgl Diproses
                                </div>
                                <div>:</div>
                                <div>
                                    {{ $labText(
                                        data_get(
                                            $microData,
                                            'tgldikerjakanspesimen',
                                            '-'
                                        )
                                    ) }}
                                </div>
                            </div>

                            <div class="info-row">
                                <div class="info-label">
                                    Tgl Pelaporan
                                </div>
                                <div>:</div>
                                <div>
                                    {{ $labText(
                                        $reportDate,
                                        '-'
                                    ) }}
                                </div>
                            </div>

                        </div>

                    </div>

                </div>

                <div class="micro-result {{ $isMdroFallback ? 'mdro-watermark' : '' }}">

                    <div class="micro-result-grid">

                        <div class="info-label">
                            Jenis Pemeriksaan
                        </div>
                        <div>:</div>
                        <div>
                            {{ $namaProduk }}
                        </div>

                        <div class="info-label">
                            Hasil
                        </div>
                        <div>:</div>
                        <div class="micro-result-value">
                            {{ $hasilSpesimen }}

                            @if($isCovid)
                                SARS-CoV2
                            @endif
                        </div>

                    </div>

                    @if($microComment !== '')

                        <div class="micro-comment">
                            <strong>Komentar:</strong>
                            {{ $microComment }}
                        </div>

                    @elseif($isCovid)

                        <div class="micro-comment"><strong>Komentar:</strong>
1. SARS-CoV2 adalah virus penyebab COVID-19.
2. Hasil AG-RDT negatif berarti komponen protein virus SARS-CoV2 tidak terdeteksi di atas ambang batas kit deteksi.
3. Hasil negatif belum dapat menyingkirkan COVID-19. Bila secara klinis masih mengarah COVID-19, pemeriksaan lanjutan dapat dipertimbangkan sesuai penilaian tenaga kesehatan.
4. Hubungi layanan kesehatan untuk informasi lebih lanjut dan ikuti anjuran kesehatan yang berlaku.</div>

                    @endif

                </div>

                @if($reportDate)

                    <div class="signature-wrap">

                        <div class="signature-box">

                            <div>
                                Denpasar,
                                {{ $reportDateText }}
                            </div>

                            <div>
                                <strong>
                                    Kepala Laboratorium
                                    Mikrobiologi Klinik
                                </strong>
                            </div>

                            <div class="signature-space"></div>

                            <div class="signature-name">
                                dr I Wy Agus Gede Manik S,
                                M.Ked.Klin, Sp.MK
                            </div>

                            <div>
                                NIP. 198308142009021004
                            </div>

                        </div>

                    </div>

                @endif

                @endif

            {{-- =====================================================
                 LAB - PATOLOGI ANATOMI
                 ===================================================== --}}
            @elseif($isPatologiAnatomi)

                @php
                    /*
                     * Struktur final API Patologi Anatomi
                     * belum diberikan.
                     *
                     * Field di bawah dibuat tolerant:
                     * bila field tersedia akan tampil,
                     * bila tidak tersedia tidak menyebabkan error.
                     */

                    $paNamaProduk = data_get(
                        $paData,
                        'namaproduk',
                        data_get(
                            $paData,
                            'pemeriksaan',
                            '-'
                        )
                    );

                    $paHasil = data_get(
                        $paData,
                        'hasil',
                        data_get(
                            $paData,
                            'hasilpemeriksaan'
                        )
                    );

                    $paMakroskopik = data_get(
                        $paData,
                        'makroskopik'
                    );

                    $paMikroskopik = data_get(
                        $paData,
                        'mikroskopik'
                    );

                    $paKesimpulan = data_get(
                        $paData,
                        'kesimpulan',
                        data_get(
                            $paData,
                            'diagnosa'
                        )
                    );

                    $paCatatan = data_get(
                        $paData,
                        'catatan',
                        data_get(
                            $paData,
                            'comment'
                        )
                    );

                    $paTanggal = data_get(
                        $paData,
                        'tglhasil',
                        data_get(
                            $paData,
                            'tglkeluarhasil'
                        )
                    );
                @endphp

                <div class="pa-box">

                    <div class="pa-title">
                        HASIL PEMERIKSAAN PATOLOGI ANATOMI
                    </div>

                    <div class="pa-content">

                        <div class="pa-field">
                            <div class="pa-label">
                                Pemeriksaan
                            </div>
                            <div class="pa-value">
                                {{ $labText($paNamaProduk) }}
                            </div>
                        </div>

                        @if($paMakroskopik)
                            <div class="pa-field">
                                <div class="pa-label">
                                    Makroskopik
                                </div>
                                <div class="pa-value">
                                    {{ $labText($paMakroskopik) }}
                                </div>
                            </div>
                        @endif

                        @if($paMikroskopik)
                            <div class="pa-field">
                                <div class="pa-label">
                                    Mikroskopik
                                </div>
                                <div class="pa-value">
                                    {{ $labText($paMikroskopik) }}
                                </div>
                            </div>
                        @endif

                        @if($paHasil)
                            <div class="pa-field">
                                <div class="pa-label">
                                    Hasil Pemeriksaan
                                </div>
                                <div class="pa-value">
                                    {{ $labText($paHasil) }}
                                </div>
                            </div>
                        @endif

                        @if($paKesimpulan)
                            <div class="pa-field">
                                <div class="pa-label">
                                    Kesimpulan / Diagnosis
                                </div>
                                <div class="pa-value">
                                    {{ $labText($paKesimpulan) }}
                                </div>
                            </div>
                        @endif

                        @if($paCatatan)
                            <div class="pa-field">
                                <div class="pa-label">
                                    Catatan
                                </div>
                                <div class="pa-value">
                                    {{ $labText($paCatatan) }}
                                </div>
                            </div>
                        @endif

                        @if($paTanggal)
                            <div class="pa-field">
                                <div class="pa-label">
                                    Tanggal Hasil
                                </div>
                                <div class="pa-value">
                                    {{ $labText($paTanggal) }}
                                </div>
                            </div>
                        @endif

                        @if(
                            ! $paHasil
                            && ! $paMakroskopik
                            && ! $paMikroskopik
                            && ! $paKesimpulan
                        )
                            <div class="lab-alert is-warning">
                                Data Patologi Anatomi ditemukan,
                                tetapi struktur detail hasil belum
                                sesuai dengan field yang tersedia
                                pada tampilan ini.
                            </div>
                        @endif

                    </div>

                </div>

            {{-- =====================================================
                 JENIS LAB TIDAK DIKENALI
                 ===================================================== --}}
            @else

                <div
                    class="lab-alert is-error"
                    style="margin-top:18px;"
                >
                    Jenis laboratorium tidak dikenali:
                    <strong>
                        {{ $lab ?: '-' }}
                    </strong>
                </div>

            @endif

        </div>

    @endif

    <div class="lab-bottom-actions">
        <a
            href="{{ $laboratoryHistoryUrl }}"
            class="lab-nav-button is-lab"
        >
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            <span>Kembali ke Hasil Laboratorium</span>
        </a>

        <a
            href="{{ $mainMenuUrl }}"
            class="lab-nav-button is-menu"
        >
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M3 11L12 4L21 11" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M5 10V20H19V10M9 20V14H15V20" stroke="currentColor" stroke-width="2" stroke-linejoin="round" />
            </svg>
            <span>Menu Utama</span>
        </a>

        @if($logoutRouteName)
            <form
                method="POST"
                action="{{ route($logoutRouteName) }}"
                class="lab-logout-form"
            >
                @csrf

                <button
                    type="submit"
                    class="lab-nav-button is-logout"
                >
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M10 5H6C4.9 5 4 5.9 4 7V17C4 18.1 4.9 19 6 19H10" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        <path d="M14 8L18 12L14 16M18 12H9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <span>Logout</span>
                </button>
            </form>
        @endif
    </div>

</div>

@endsection