@extends('layouts.app', ['title' => 'Detail Hasil Radiologi | NADI RSBM'])

@section('content')

@php
    $radiologyHistoryUrl = Route::has('radiology.index')
        ? route('radiology.index', ['nrm' => $nrm])
        : url('/cek-hasil-radiologi');

    $mainMenuUrl = Route::has('layanan.menu')
        ? route('layanan.menu')
        : url('/layanan/menu');

    $logoutRouteName = Route::has('layanan.logout')
        ? 'layanan.logout'
        : (Route::has('logout') ? 'logout' : null);

    $examName = data_get(
        $item,
        'nama_pemeriksaan',
        'Hasil Radiologi'
    );

    $radiologEnd = trim(
        (string) data_get(
            $item,
            'radiolog_datetime_end',
            ''
        )
    );

    $hasValidRadiologEnd =
        $radiologEnd !== ''
        && strpos($radiologEnd, '1900-01-01') !== 0;
@endphp

<style>
    :root {
        --rsbm-blue: #26358f;
        --rsbm-blue-dark: #1d286d;
        --rsbm-blue-soft: #eef1ff;
        --rsbm-green: #19c83d;
        --rsbm-green-dark: #0f9f2e;
        --rsbm-green-soft: #eafbee;
        --rsbm-text: #182033;
        --rsbm-muted: #64748b;
        --rsbm-line: #e3e8ef;
        --rsbm-bg: #f7f9fc;
    }

    .rad-detail-page,
    .rad-detail-page * {
        box-sizing: border-box;
    }

    .rad-detail-page {
        position: relative;
        left: 50%;
        width: 100vw;
        min-height: 100vh;
        margin-left: -50vw;
        padding: 22px clamp(14px, 3vw, 38px) 48px;
        overflow-x: hidden;
        color: var(--rsbm-text);
        background:
            radial-gradient(
                circle at 92% 8%,
                rgba(38, 53, 143, .08),
                transparent 27%
            ),
            radial-gradient(
                circle at 6% 92%,
                rgba(25, 200, 61, .07),
                transparent 25%
            ),
            linear-gradient(
                180deg,
                #ffffff 0%,
                var(--rsbm-bg) 100%
            );
    }

    .rad-detail-page::before,
    .rad-detail-page::after {
        position: absolute;
        border-radius: 999px;
        content: "";
        pointer-events: none;
    }

    .rad-detail-page::before {
        top: -170px;
        right: -150px;
        width: 350px;
        height: 350px;
        border: 46px solid rgba(38, 53, 143, .022);
    }

    .rad-detail-page::after {
        bottom: -190px;
        left: -160px;
        width: 360px;
        height: 360px;
        border: 52px solid rgba(25, 200, 61, .022);
    }

    .rad-detail-container {
        position: relative;
        z-index: 1;
        width: min(1080px, 100%);
        margin: 0 auto;
    }

    /* =========================================================
       TOP BAR / IDENTITAS RSBM
       ========================================================= */

    .rad-detail-topbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 18px;
        padding: 11px 12px;
        border: 1px solid rgba(217, 224, 234, .95);
        border-radius: 18px;
        background: rgba(255, 255, 255, .96);
        box-shadow: 0 10px 28px rgba(28, 39, 90, .07);
        backdrop-filter: blur(10px);
    }

    .rad-detail-brand {
        display: flex;
        min-width: 0;
        align-items: center;
        gap: 10px;
    }

    .rad-detail-logo-shell {
        display: grid;
        width: 43px;
        height: 43px;
        flex: 0 0 43px;
        place-items: center;
        overflow: hidden;
        border: 1px solid rgba(38, 53, 143, .1);
        border-radius: 11px;
        background: #fff;
    }

    .rad-detail-logo {
        width: 37px;
        height: 37px;
        object-fit: contain;
    }

    .rad-detail-logo-fallback {
        display: none;
        width: 34px;
        height: 34px;
        place-items: center;
        border-radius: 9px;
        background:
            linear-gradient(
                145deg,
                var(--rsbm-blue),
                var(--rsbm-blue-dark)
            );
        color: #fff;
        font-size: 17px;
        font-weight: 950;
    }

    .rad-detail-government {
        color: #818b9a;
        font-size: 8px;
        font-weight: 900;
        letter-spacing: .1em;
        text-transform: uppercase;
    }

    .rad-detail-hospital {
        margin-top: 1px;
        color: var(--rsbm-blue-dark);
        font-size: 14px;
        font-weight: 950;
        letter-spacing: -.01em;
    }

    .rad-detail-location {
        margin-top: 1px;
        color: #98a1af;
        font-size: 9px;
        font-weight: 700;
    }

    .rad-detail-top-actions,
    .rad-detail-bottom-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 8px;
        flex-wrap: wrap;
    }

    .rad-detail-logout-form {
        margin: 0;
    }

    .rad-detail-nav {
        display: inline-flex;
        min-height: 38px;
        align-items: center;
        justify-content: center;
        gap: 7px;
        padding: 0 12px;
        border: 1px solid transparent;
        border-radius: 11px;
        font: inherit;
        font-size: 10.5px;
        font-weight: 900;
        line-height: 1;
        text-decoration: none;
        white-space: nowrap;
        cursor: pointer;
        transition:
            transform .18s ease,
            background .18s ease,
            border-color .18s ease,
            box-shadow .18s ease;
    }

    .rad-detail-nav:hover {
        transform: translateY(-1px);
        text-decoration: none;
    }

    .rad-detail-nav.is-history {
        border-color: #bfdbfe;
        background: #eff6ff;
        color: #1d4ed8;
    }

    .rad-detail-nav.is-history:hover {
        border-color: #93c5fd;
        background: #dbeafe;
        color: #1e40af;
    }

    .rad-detail-nav.is-menu {
        border-color: #c7d2fe;
        background: var(--rsbm-blue-soft);
        color: var(--rsbm-blue);
    }

    .rad-detail-nav.is-menu:hover {
        border-color: #a5b4fc;
        background: #e5e9ff;
        color: var(--rsbm-blue-dark);
    }

    .rad-detail-nav.is-logout {
        border-color: #fecaca;
        background: #fff5f5;
        color: #b91c1c;
    }

    .rad-detail-nav.is-logout:hover {
        border-color: #fca5a5;
        background: #fee2e2;
        color: #991b1b;
    }

    /* =========================================================
       PAGE HEADER
       ========================================================= */

    .rad-detail-heading {
        max-width: 760px;
        margin-bottom: 18px;
    }

    .rad-detail-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 6px 10px;
        border: 1px solid rgba(38, 53, 143, .09);
        border-radius: 999px;
        background: rgba(255, 255, 255, .85);
        color: var(--rsbm-blue-dark);
        font-size: 9px;
        font-weight: 900;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .rad-detail-eyebrow::before {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: var(--rsbm-green);
        box-shadow: 0 0 0 4px rgba(25, 200, 61, .11);
        content: "";
    }

    .rad-detail-page-title {
        margin: 10px 0 7px;
        color: var(--rsbm-text);
        font-size: clamp(29px, 4.1vw, 42px);
        font-weight: 950;
        line-height: 1.05;
        letter-spacing: -.04em;
    }

    .rad-detail-page-title span {
        color: var(--rsbm-blue);
    }

    .rad-detail-page-description {
        margin: 0;
        color: var(--rsbm-muted);
        font-size: 13px;
        line-height: 1.6;
    }

    /* =========================================================
       DETAIL CARD
       ========================================================= */

    .rad-detail-card {
        overflow: hidden;
        border: 1px solid rgba(217, 224, 234, .96);
        border-radius: 19px;
        background: rgba(255, 255, 255, .98);
        box-shadow: 0 14px 34px rgba(28, 39, 90, .065);
    }

    .rad-detail-card::before {
        display: block;
        height: 4px;
        background:
            linear-gradient(
                90deg,
                var(--rsbm-blue) 0 78%,
                var(--rsbm-green) 78% 100%
            );
        content: "";
    }

    .rad-result-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        padding: 19px 20px;
        border-bottom: 1px solid var(--rsbm-line);
        background:
            linear-gradient(
                90deg,
                rgba(238, 241, 255, .72),
                rgba(255, 255, 255, 0)
            );
    }

    .rad-result-title {
        margin: 0;
        color: var(--rsbm-text);
        font-size: 20px;
        font-weight: 950;
        line-height: 1.3;
        letter-spacing: -.02em;
    }

    .rad-result-subtitle {
        margin-top: 6px;
        color: #7b8797;
        font-size: 10.5px;
        font-weight: 700;
        line-height: 1.55;
    }

    .rad-badge-row {
        display: flex;
        flex: 0 0 auto;
        flex-wrap: wrap;
        justify-content: flex-end;
        gap: 6px;
    }

    .rad-status-badge {
        display: inline-flex;
        align-items: center;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 9.5px;
        font-weight: 900;
    }

    .rad-status-badge.is-ready {
        background: var(--rsbm-green-soft);
        color: var(--rsbm-green-dark);
    }

    .rad-status-badge.is-wait {
        background: #fff7e6;
        color: #a16207;
    }

    .rad-status-badge.is-critical {
        background: #fee2e2;
        color: #b91c1c;
    }

    .rad-detail-body {
        padding: 19px 20px 20px;
    }

    /* =========================================================
       META
       ========================================================= */

    .rad-meta-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 0;
        margin-bottom: 17px;
        overflow: hidden;
        border: 1px solid var(--rsbm-line);
        border-radius: 15px;
        background: #fbfcfe;
    }

    .rad-meta-item {
        min-width: 0;
        padding: 14px 15px;
        border-bottom: 1px solid var(--rsbm-line);
    }

    .rad-meta-item:not(:nth-child(3n + 1)) {
        border-left: 1px solid var(--rsbm-line);
    }

    .rad-meta-item:nth-last-child(-n + 3) {
        border-bottom: 0;
    }

    .rad-meta-label {
        margin-bottom: 5px;
        color: #98a1af;
        font-size: 8.5px;
        font-weight: 900;
        letter-spacing: .04em;
        text-transform: uppercase;
    }

    .rad-meta-value {
        overflow-wrap: anywhere;
        color: #344052;
        font-size: 11.5px;
        font-weight: 850;
        line-height: 1.5;
    }

    /* =========================================================
       EXPERTISE
       ========================================================= */

    .rad-expertise-section {
        margin-top: 13px;
        overflow: hidden;
        border: 1px solid var(--rsbm-line);
        border-radius: 15px;
        background: #fff;
    }

    .rad-expertise-header {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 11px 14px;
        border-bottom: 1px solid var(--rsbm-line);
        background: #f8fafc;
        color: var(--rsbm-text);
        font-size: 11px;
        font-weight: 950;
    }

    .rad-expertise-icon {
        display: grid;
        width: 25px;
        height: 25px;
        flex: 0 0 25px;
        place-items: center;
        border-radius: 7px;
        background: var(--rsbm-blue-soft);
        color: var(--rsbm-blue);
    }

    .rad-expertise-text {
        padding: 14px;
        color: #344052;
        font-size: 12px;
        line-height: 1.75;
        white-space: pre-line;
    }

    .rad-expertise-section.is-conclusion {
        border-color: #c7d2fe;
    }

    .rad-expertise-section.is-conclusion .rad-expertise-header {
        border-bottom-color: #c7d2fe;
        background: var(--rsbm-blue-soft);
        color: var(--rsbm-blue-dark);
    }

    .rad-expertise-section.is-waiting {
        border-color: #fde68a;
        background: #fffbeb;
    }

    .rad-expertise-section.is-waiting .rad-expertise-text {
        color: #92400e;
        font-weight: 750;
    }

    /* =========================================================
       BOTTOM NAVIGATION
       ========================================================= */

    .rad-detail-bottom-actions {
        justify-content: center;
        margin-top: 18px;
        padding-top: 16px;
        border-top: 1px solid rgba(227, 232, 239, .9);
    }

    /* =========================================================
       RESPONSIVE
       ========================================================= */

    @media (max-width: 760px) {
        .rad-meta-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .rad-meta-item:not(:nth-child(3n + 1)) {
            border-left: 0;
        }

        .rad-meta-item:nth-child(even) {
            border-left: 1px solid var(--rsbm-line);
        }

        .rad-meta-item:nth-last-child(-n + 3) {
            border-bottom: 1px solid var(--rsbm-line);
        }

        .rad-meta-item:nth-last-child(-n + 2) {
            border-bottom: 0;
        }
    }

    @media (max-width: 640px) {
        .rad-detail-page {
            padding: 10px 10px 36px;
        }

        .rad-detail-topbar {
            align-items: stretch;
            flex-direction: column;
            padding: 10px;
        }

        .rad-detail-top-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            width: 100%;
        }

        .rad-detail-top-actions .rad-detail-nav,
        .rad-detail-top-actions .rad-detail-logout-form,
        .rad-detail-top-actions .rad-detail-logout-form .rad-detail-nav {
            width: 100%;
        }

        .rad-detail-heading {
            margin-bottom: 15px;
        }

        .rad-detail-page-title {
            font-size: 29px;
        }

        .rad-result-header {
            flex-direction: column;
            padding: 16px;
        }

        .rad-badge-row {
            justify-content: flex-start;
        }

        .rad-detail-body {
            padding: 15px;
        }

        .rad-meta-grid {
            grid-template-columns: 1fr;
        }

        .rad-meta-item,
        .rad-meta-item:nth-child(even),
        .rad-meta-item:not(:nth-child(3n + 1)) {
            border-left: 0;
        }

        .rad-meta-item,
        .rad-meta-item:nth-last-child(-n + 2),
        .rad-meta-item:nth-last-child(-n + 3) {
            border-bottom: 1px solid var(--rsbm-line);
        }

        .rad-meta-item:last-child {
            border-bottom: 0;
        }

        .rad-detail-bottom-actions {
            display: grid;
            grid-template-columns: 1fr;
        }

        .rad-detail-bottom-actions .rad-detail-nav,
        .rad-detail-bottom-actions .rad-detail-logout-form,
        .rad-detail-bottom-actions .rad-detail-logout-form .rad-detail-nav {
            width: 100%;
        }
    }
</style>

<div class="rad-detail-page">
    <div class="rad-detail-container">

        <div class="rad-detail-topbar">
            <div class="rad-detail-brand">
                <div class="rad-detail-logo-shell">
                    <img
                        class="rad-detail-logo"
                        src="{{ asset('images/logo-rsbm.png') }}"
                        alt="Logo RSUD Bali Mandara"
                        onerror="this.style.display='none'; this.nextElementSibling.style.display='grid';"
                    >
                    <span class="rad-detail-logo-fallback" aria-hidden="true">
                        +
                    </span>
                </div>

                <div>
                    <div class="rad-detail-government">
                        Pemerintah Provinsi Bali
                    </div>
                    <div class="rad-detail-hospital">
                        RSUD Bali Mandara
                    </div>
                    <div class="rad-detail-location">
                        Sanur · Denpasar · Bali
                    </div>
                </div>
            </div>

            <div class="rad-detail-top-actions">
                <a
                    href="{{ $radiologyHistoryUrl }}"
                    class="rad-detail-nav is-history"
                >
                    <svg
                        width="14"
                        height="14"
                        viewBox="0 0 24 24"
                        fill="none"
                        aria-hidden="true"
                    >
                        <path
                            d="M15 18L9 12L15 6"
                            stroke="currentColor"
                            stroke-width="2.2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />
                    </svg>
                    <span>Riwayat Radiologi</span>
                </a>

                <a
                    href="{{ $mainMenuUrl }}"
                    class="rad-detail-nav is-menu"
                >
                    <svg
                        width="14"
                        height="14"
                        viewBox="0 0 24 24"
                        fill="none"
                        aria-hidden="true"
                    >
                        <path
                            d="M3 11L12 4L21 11"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />
                        <path
                            d="M5 10V20H19V10M9 20V14H15V20"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linejoin="round"
                        />
                    </svg>
                    <span>Menu Utama</span>
                </a>

                @if($logoutRouteName)
                    <form
                        method="POST"
                        action="{{ route($logoutRouteName) }}"
                        class="rad-detail-logout-form"
                    >
                        @csrf

                        <button
                            type="submit"
                            class="rad-detail-nav is-logout"
                        >
                            <svg
                                width="14"
                                height="14"
                                viewBox="0 0 24 24"
                                fill="none"
                                aria-hidden="true"
                            >
                                <path
                                    d="M10 5H6C4.9 5 4 5.9 4 7V17C4 18.1 4.9 19 6 19H10"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                />
                                <path
                                    d="M14 8L18 12L14 16M18 12H9"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                            </svg>
                            <span>Logout</span>
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <header class="rad-detail-heading">
            <span class="rad-detail-eyebrow">
                SAPA RSBM · Radiologi
            </span>

          
        </header>

        <article class="rad-detail-card">

            <div class="rad-result-header">
                <div>
                    <h2 class="rad-result-title">
                        {{ $examName }}
                    </h2>

                    <div class="rad-result-subtitle">
                        No. Rontgen:
                        <strong>
                            {{ data_get($item, 'no_rontgen', '-') }}
                        </strong>

                        · RM:
                        <strong>
                            {{ data_get($item, 'no_rm', '-') }}
                        </strong>

                        · Registrasi:
                        <strong>
                            {{ data_get($item, 'no_register', '-') }}
                        </strong>
                    </div>
                </div>

                <div class="rad-badge-row">
                    @if(data_get($item, 'is_critical'))
                        <span class="rad-status-badge is-critical">
                            Hasil Kritis
                        </span>
                    @elseif(data_get($item, 'has_expertise'))
                        <span class="rad-status-badge is-ready">
                            Sudah Expertise
                        </span>
                    @else
                        <span class="rad-status-badge is-wait">
                            Belum Expertise
                        </span>
                    @endif
                </div>
            </div>

            <div class="rad-detail-body">

                <div class="rad-meta-grid">
                    <div class="rad-meta-item">
                        <div class="rad-meta-label">
                            Nama Pasien
                        </div>
                        <div class="rad-meta-value">
                            {{ data_get(
                                $item,
                                'nama_pasien',
                                '-'
                            ) }}
                        </div>
                    </div>

                    <div class="rad-meta-item">
                        <div class="rad-meta-label">
                            Radiografer
                        </div>
                        <div class="rad-meta-value">
                            {{ data_get(
                                $item,
                                'nama_radiografer',
                                '-'
                            ) ?: '-' }}
                        </div>
                    </div>

                    <div class="rad-meta-item">
                        <div class="rad-meta-label">
                            Dokter Radiolog
                        </div>
                        <div class="rad-meta-value">
                            {{ data_get(
                                $item,
                                'nama_radiolog',
                                '-'
                            ) ?: '-' }}
                        </div>
                    </div>

                    <div class="rad-meta-item">
                        <div class="rad-meta-label">
                            Mulai Radiografer
                        </div>
                        <div class="rad-meta-value">
                            {{ data_get(
                                $item,
                                'radiografer_datetime_start',
                                '-'
                            ) ?: '-' }}
                        </div>
                    </div>

                    <div class="rad-meta-item">
                        <div class="rad-meta-label">
                            Selesai Radiografer
                        </div>
                        <div class="rad-meta-value">
                            {{ data_get(
                                $item,
                                'radiografer_datetime_end',
                                '-'
                            ) ?: '-' }}
                        </div>
                    </div>

                    <div class="rad-meta-item">
                        <div class="rad-meta-label">
                            Selesai Expertise
                        </div>
                        <div class="rad-meta-value">
                            {{ $hasValidRadiologEnd
                                ? $radiologEnd
                                : '-' }}
                        </div>
                    </div>
                </div>

                @if(
                    trim(
                        (string) data_get(
                            $item,
                            'expertise_text_finding',
                            ''
                        )
                    ) !== ''
                )
                    <section class="rad-expertise-section">
                        <div class="rad-expertise-header">
                            <span class="rad-expertise-icon">
                                <svg
                                    width="14"
                                    height="14"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    aria-hidden="true"
                                >
                                    <path
                                        d="M4 5H20M4 10H20M4 15H14M4 20H11"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                    />
                                </svg>
                            </span>

                            <span>
                                Hasil Pemeriksaan / Finding
                            </span>
                        </div>

                        <div class="rad-expertise-text">
                            {{ data_get(
                                $item,
                                'expertise_text_finding'
                            ) }}
                        </div>
                    </section>
                @endif

                @if(
                    trim(
                        (string) data_get(
                            $item,
                            'expertise_text_conclusion',
                            ''
                        )
                    ) !== ''
                )
                    <section class="rad-expertise-section is-conclusion">
                        <div class="rad-expertise-header">
                            <span class="rad-expertise-icon">
                                <svg
                                    width="14"
                                    height="14"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    aria-hidden="true"
                                >
                                    <path
                                        d="M5 12L10 17L19 7"
                                        stroke="currentColor"
                                        stroke-width="2.4"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    />
                                </svg>
                            </span>

                            <span>
                                Kesan / Kesimpulan
                            </span>
                        </div>

                        <div class="rad-expertise-text">
                            {{ data_get(
                                $item,
                                'expertise_text_conclusion'
                            ) }}
                        </div>
                    </section>
                @endif

                @if(! data_get($item, 'has_expertise'))
                    <section class="rad-expertise-section is-waiting">
                        <div class="rad-expertise-text">
                            Hasil expertise dokter radiologi belum tersedia.
                        </div>
                    </section>
                @endif

            </div>
        </article>

        <div class="rad-detail-bottom-actions">
            <a
                href="{{ $radiologyHistoryUrl }}"
                class="rad-detail-nav is-history"
            >
                <svg
                    width="14"
                    height="14"
                    viewBox="0 0 24 24"
                    fill="none"
                    aria-hidden="true"
                >
                    <path
                        d="M15 18L9 12L15 6"
                        stroke="currentColor"
                        stroke-width="2.2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    />
                </svg>
                <span>Kembali ke Riwayat Radiologi</span>
            </a>

            <a
                href="{{ $mainMenuUrl }}"
                class="rad-detail-nav is-menu"
            >
                <svg
                    width="14"
                    height="14"
                    viewBox="0 0 24 24"
                    fill="none"
                    aria-hidden="true"
                >
                    <path
                        d="M3 11L12 4L21 11"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    />
                    <path
                        d="M5 10V20H19V10M9 20V14H15V20"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linejoin="round"
                    />
                </svg>
                <span>Menu Utama</span>
            </a>

            @if($logoutRouteName)
                <form
                    method="POST"
                    action="{{ route($logoutRouteName) }}"
                    class="rad-detail-logout-form"
                >
                    @csrf

                    <button
                        type="submit"
                        class="rad-detail-nav is-logout"
                    >
                        <svg
                            width="14"
                            height="14"
                            viewBox="0 0 24 24"
                            fill="none"
                            aria-hidden="true"
                        >
                            <path
                                d="M10 5H6C4.9 5 4 5.9 4 7V17C4 18.1 4.9 19 6 19H10"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                            />
                            <path
                                d="M14 8L18 12L14 16M18 12H9"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                        </svg>
                        <span>Logout</span>
                    </button>
                </form>
            @endif
        </div>

    </div>
</div>

@endsection
