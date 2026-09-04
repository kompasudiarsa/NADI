@extends('layouts.app', ['title' => 'Riwayat Radiologi | NADI RSBM'])

@section('content')

@php
    $patientName = data_get($patient, 'name', '-');
    $medicalRecord = data_get($patient, 'medical_record', '-');
    $birthDate = data_get($patient, 'birth_date');

    $mainMenuUrl = Route::has('layanan.menu')
        ? route('layanan.menu')
        : url('/layanan/menu');

    $logoutRouteName = Route::has('layanan.logout')
        ? 'layanan.logout'
        : (Route::has('logout') ? 'logout' : null);
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

    .rad-page,
    .rad-page * {
        box-sizing: border-box;
    }

    .rad-page {
        position: relative;
        left: 50%;
        width: 100vw;
        min-height: 100vh;
        margin-left: -50vw;
        padding: 22px clamp(14px, 3vw, 38px) 48px;
        overflow-x: hidden;
        color: var(--rsbm-text);
        background:
            radial-gradient(circle at 92% 8%, rgba(38, 53, 143, .08), transparent 27%),
            radial-gradient(circle at 6% 92%, rgba(25, 200, 61, .07), transparent 25%),
            linear-gradient(180deg, #fff 0%, var(--rsbm-bg) 100%);
    }

    .rad-page::before,
    .rad-page::after {
        position: absolute;
        border-radius: 999px;
        content: "";
        pointer-events: none;
    }

    .rad-page::before {
        top: -170px;
        right: -150px;
        width: 350px;
        height: 350px;
        border: 46px solid rgba(38, 53, 143, .022);
    }

    .rad-page::after {
        bottom: -190px;
        left: -160px;
        width: 360px;
        height: 360px;
        border: 52px solid rgba(25, 200, 61, .022);
    }

    .rad-container {
        position: relative;
        z-index: 1;
        width: min(1180px, 100%);
        margin: 0 auto;
    }

    /* =========================================================
       NAVIGASI / IDENTITAS RSBM
       ========================================================= */

    .rad-topbar {
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

    .rad-brand {
        display: flex;
        min-width: 0;
        align-items: center;
        gap: 10px;
    }

    .rad-logo-shell {
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

    .rad-logo {
        width: 37px;
        height: 37px;
        object-fit: contain;
    }

    .rad-logo-fallback {
        display: none;
        width: 34px;
        height: 34px;
        place-items: center;
        border-radius: 9px;
        background: linear-gradient(145deg, var(--rsbm-blue), var(--rsbm-blue-dark));
        color: #fff;
        font-size: 17px;
        font-weight: 950;
    }

    .rad-government {
        color: #818b9a;
        font-size: 8px;
        font-weight: 900;
        letter-spacing: .1em;
        text-transform: uppercase;
    }

    .rad-hospital {
        margin-top: 1px;
        color: var(--rsbm-blue-dark);
        font-size: 14px;
        font-weight: 950;
        letter-spacing: -.01em;
    }

    .rad-location {
        margin-top: 1px;
        color: #98a1af;
        font-size: 9px;
        font-weight: 700;
    }

    .rad-top-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 8px;
        flex-wrap: wrap;
    }

    .rad-logout-form {
        margin: 0;
    }

    .nav-button {
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

    .nav-button:hover {
        transform: translateY(-1px);
        text-decoration: none;
    }

    .nav-button.is-menu {
        border-color: #c7d2fe;
        background: var(--rsbm-blue-soft);
        color: var(--rsbm-blue);
    }

    .nav-button.is-menu:hover {
        border-color: #a5b4fc;
        background: #e5e9ff;
        color: var(--rsbm-blue-dark);
    }

    .nav-button.is-logout {
        border-color: #fecaca;
        background: #fff5f5;
        color: #b91c1c;
    }

    .nav-button.is-logout:hover {
        border-color: #fca5a5;
        background: #fee2e2;
        color: #991b1b;
    }

    /* =========================================================
       HEADER
       ========================================================= */

    .rad-header {
        max-width: 780px;
        margin-bottom: 20px;
    }

    .rad-eyebrow {
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

    .rad-eyebrow::before {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: var(--rsbm-green);
        box-shadow: 0 0 0 4px rgba(25, 200, 61, .11);
        content: "";
    }

    .rad-title {
        max-width: 700px;
        margin: 10px 0 7px;
        color: var(--rsbm-text);
        font-size: clamp(29px, 4.3vw, 43px);
        font-weight: 950;
        line-height: 1.05;
        letter-spacing: -.04em;
    }

    .rad-title span {
        color: var(--rsbm-blue);
    }

    .rad-description {
        max-width: 720px;
        margin: 0;
        color: var(--rsbm-muted);
        font-size: 13px;
        line-height: 1.6;
    }

    /* =========================================================
       CARD UMUM
       ========================================================= */

    .patient-card,
    .filter-card,
    .rad-card,
    .empty-card,
    .error-card {
        border: 1px solid rgba(217, 224, 234, .96);
        background: rgba(255, 255, 255, .97);
        box-shadow: 0 12px 30px rgba(28, 39, 90, .055);
    }

    /* =========================================================
       PASIEN
       ========================================================= */

    .patient-card {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        margin-bottom: 14px;
        overflow: hidden;
        border-radius: 18px;
    }

    .patient-card > div {
        min-width: 0;
        padding: 15px 18px;
    }

    .patient-card > div + div {
        border-left: 1px solid var(--rsbm-line);
    }

    .info-label {
        margin-bottom: 5px;
        color: #98a1af;
        font-size: 9px;
        font-weight: 900;
        letter-spacing: .04em;
        text-transform: uppercase;
    }

    .info-value {
        overflow-wrap: anywhere;
        color: var(--rsbm-text);
        font-size: 13px;
        font-weight: 900;
        line-height: 1.45;
    }

    /* =========================================================
       FILTER
       ========================================================= */

    .filter-card {
        margin-bottom: 15px;
        padding: 16px;
        border-radius: 18px;
    }

    .filter-grid {
        display: grid;
        grid-template-columns:
            minmax(250px, 1.35fr)
            minmax(150px, .55fr)
            minmax(125px, .42fr)
            auto;
        gap: 10px;
        align-items: end;
    }

    .form-group {
        display: grid;
        min-width: 0;
        gap: 5px;
    }

    .form-label {
        color: #344052;
        font-size: 10.5px;
        font-weight: 900;
    }

    .form-control {
        display: block;
        width: 100%;
        min-width: 0;
        height: 43px;
        padding: 0 12px;
        border: 1.3px solid var(--rsbm-line);
        border-radius: 11px;
        outline: none;
        background: #fbfcfe;
        color: var(--rsbm-text);
        font: inherit;
        font-size: 11.5px;
        font-weight: 700;
        transition:
            border-color .18s ease,
            box-shadow .18s ease,
            background .18s ease;
    }

    .form-control:focus {
        border-color: var(--rsbm-blue);
        background: #fff;
        box-shadow: 0 0 0 3px rgba(38, 53, 143, .08);
    }

    .btn-primary,
    .btn-detail,
    .page-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
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
            border-color .18s ease,
            background .18s ease,
            box-shadow .18s ease;
    }

    .btn-primary {
        min-height: 43px;
        padding: 0 17px;
        border: 0;
        background: linear-gradient(135deg, var(--rsbm-blue), var(--rsbm-blue-dark));
        color: #fff;
        box-shadow: 0 8px 18px rgba(38, 53, 143, .17);
    }

    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 10px 22px rgba(38, 53, 143, .22);
    }

    /* =========================================================
       RADIOLOGI CARD
       ========================================================= */

    .rad-card {
        margin-bottom: 13px;
        overflow: hidden;
        border-radius: 18px;
    }

    .rad-card::before {
        display: block;
        height: 3px;
        background: linear-gradient(
            90deg,
            var(--rsbm-blue) 0 78%,
            var(--rsbm-green) 78% 100%
        );
        content: "";
    }

    .rad-card-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
        padding: 16px 18px;
        border-bottom: 1px solid var(--rsbm-line);
        background:
            linear-gradient(
                90deg,
                rgba(238, 241, 255, .72),
                rgba(255, 255, 255, 0)
            );
    }

    .rad-exam {
        color: var(--rsbm-text);
        font-size: 15px;
        font-weight: 950;
        line-height: 1.35;
    }

    .rad-number {
        margin-top: 5px;
        color: #7b8797;
        font-size: 10px;
        font-weight: 750;
        line-height: 1.5;
    }

    .badge-row {
        display: flex;
        flex: 0 0 auto;
        flex-wrap: wrap;
        justify-content: flex-end;
        gap: 6px;
    }

    .badge {
        display: inline-flex;
        align-items: center;
        padding: 6px 9px;
        border-radius: 999px;
        font-size: 9.5px;
        font-weight: 900;
    }

    .badge-ready {
        background: var(--rsbm-green-soft);
        color: var(--rsbm-green-dark);
    }

    .badge-wait {
        background: #fff7e6;
        color: #a16207;
    }

    .badge-critical {
        background: #fee2e2;
        color: #b91c1c;
    }

    .rad-card-body {
        padding: 16px 18px;
    }

    .rad-info {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px 20px;
    }

    .rad-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 7px;
        margin-top: 14px;
        padding-top: 13px;
        border-top: 1px solid #eef1f5;
    }

    .btn-detail {
        min-height: 36px;
        gap: 6px;
        padding: 0 13px;
        border: 1px solid #c7d2fe;
        background: var(--rsbm-blue-soft);
        color: var(--rsbm-blue);
    }

    .btn-detail:hover {
        transform: translateY(-1px);
        border-color: #a5b4fc;
        background: #e5e9ff;
        color: var(--rsbm-blue-dark);
        text-decoration: none;
    }

    /* =========================================================
       PAGINATION / EMPTY / ERROR
       ========================================================= */

    .pagination-simple {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-top: 18px;
        padding: 11px 12px;
        border: 1px solid var(--rsbm-line);
        border-radius: 14px;
        background: rgba(255, 255, 255, .96);
        box-shadow: 0 8px 22px rgba(28, 39, 90, .04);
    }

    .page-info {
        color: #7b8797;
        font-size: 10px;
        font-weight: 800;
    }

    .page-actions {
        display: flex;
        gap: 7px;
    }

    .page-btn {
        min-height: 34px;
        padding: 0 11px;
        border: 1px solid var(--rsbm-line);
        background: #fff;
        color: #5f6b7c;
    }

    .page-btn:hover {
        border-color: #c7d2fe;
        background: var(--rsbm-blue-soft);
        color: var(--rsbm-blue);
        text-decoration: none;
    }

    .page-btn.disabled {
        color: #bdc5d0;
        pointer-events: none;
        background: #f8fafc;
    }

    .empty-card,
    .error-card {
        padding: 28px 20px;
        border-radius: 18px;
        text-align: center;
        font-size: 12px;
        font-weight: 750;
        line-height: 1.6;
    }

    .empty-card {
        color: var(--rsbm-muted);
    }

    .error-card {
        border-color: #fecaca;
        background: #fff5f5;
        color: #991b1b;
    }

    /* =========================================================
       FOOTER NAV
       ========================================================= */

    .rad-footer-nav {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        flex-wrap: wrap;
        margin-top: 18px;
        padding-top: 16px;
        border-top: 1px solid rgba(227, 232, 239, .9);
    }

    /* =========================================================
       RESPONSIVE
       ========================================================= */

    @media (max-width: 900px) {
        .filter-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .filter-grid .form-group:first-child {
            grid-column: 1 / -1;
        }

        .filter-grid .btn-primary {
            width: 100%;
        }

        .rad-info {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 640px) {
        .rad-page {
            padding: 10px 10px 38px;
        }

        .rad-topbar {
            align-items: stretch;
            flex-direction: column;
            padding: 10px;
        }

        .rad-brand {
            padding: 1px 2px;
        }

        .rad-top-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            width: 100%;
        }

        .rad-top-actions .nav-button,
        .rad-top-actions .rad-logout-form,
        .rad-top-actions .rad-logout-form .nav-button {
            width: 100%;
        }

        .rad-header {
            margin-bottom: 16px;
        }

        .rad-title {
            font-size: 30px;
        }

        .rad-description {
            font-size: 12px;
        }

        .patient-card {
            grid-template-columns: 1fr;
        }

        .patient-card > div {
            padding: 13px 15px;
        }

        .patient-card > div + div {
            border-top: 1px solid var(--rsbm-line);
            border-left: 0;
        }

        .filter-card {
            padding: 13px;
        }

        .filter-grid,
        .rad-info {
            grid-template-columns: 1fr;
        }

        .filter-grid .form-group:first-child {
            grid-column: auto;
        }

        .filter-grid .btn-primary {
            width: 100%;
        }

        .rad-card-header {
            flex-direction: column;
            padding: 15px;
        }

        .rad-card-body {
            padding: 15px;
        }

        .badge-row {
            justify-content: flex-start;
        }

        .rad-actions .btn-detail {
            width: 100%;
        }

        .pagination-simple {
            display: grid;
            justify-content: stretch;
        }

        .page-actions {
            justify-content: space-between;
        }

        .page-actions .page-btn {
            flex: 1;
        }

        .rad-footer-nav {
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        .rad-footer-nav .nav-button,
        .rad-footer-nav .rad-logout-form,
        .rad-footer-nav .rad-logout-form .nav-button {
            width: 100%;
        }
    }
</style>

<div class="rad-page">
    <div class="rad-container">

        <div class="rad-topbar">
            <div class="rad-brand">
                <div class="rad-logo-shell">
                    <img
                        class="rad-logo"
                        src="{{ asset('images/logo-rsbm.png') }}"
                        alt="Logo RSUD Bali Mandara"
                        onerror="this.style.display='none'; this.nextElementSibling.style.display='grid';"
                    >
                    <span class="rad-logo-fallback" aria-hidden="true">+</span>
                </div>

                <div>
                    <div class="rad-government">Pemerintah Provinsi Bali</div>
                    <div class="rad-hospital">RSUD Bali Mandara</div>
                    <div class="rad-location">Sanur · Denpasar · Bali</div>
                </div>
            </div>

            <div class="rad-top-actions">
                <a href="{{ $mainMenuUrl }}" class="nav-button is-menu">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M3 11L12 4L21 11" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M5 10V20H19V10M9 20V14H15V20" stroke="currentColor" stroke-width="2" stroke-linejoin="round" />
                    </svg>
                    <span>Menu Utama</span>
                </a>

                @if($logoutRouteName)
                    <form method="POST" action="{{ route($logoutRouteName) }}" class="rad-logout-form">
                        @csrf
                        <button type="submit" class="nav-button is-logout">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M10 5H6C4.9 5 4 5.9 4 7V17C4 18.1 4.9 19 6 19H10" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                <path d="M14 8L18 12L14 16M18 12H9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <span>Logout</span>
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <header class="rad-header">
            <span class="rad-eyebrow">SAPA RSBM · Radiologi</span>

         
        </header>

        <div class="patient-card">
            <div>
                <div class="info-label">Nama Pasien</div>
                <div class="info-value">{{ $patientName }}</div>
            </div>

            <div>
                <div class="info-label">Nomor Rekam Medis</div>
                <div class="info-value">{{ $medicalRecord }}</div>
            </div>

            <div>
                <div class="info-label">Tanggal Lahir</div>
                <div class="info-value">
                    @if($birthDate)
                        {{ \Carbon\Carbon::parse($birthDate)->format('d-m-Y') }}
                    @else
                        -
                    @endif
                </div>
            </div>
        </div>

        @if(! data_get($result, 'success', false))
            <div class="error-card">
                {{ data_get(
                    $result,
                    'message',
                    'API radiologi belum berhasil diakses.'
                ) }}
            </div>
        @else
            <div class="filter-card">
                <form method="GET" action="{{ route('radiology.index') }}">
                    <input type="hidden" name="nrm" value="{{ $nrm }}">

                    <div class="filter-grid">
                        <div class="form-group">
                            <label class="form-label">Cari Pemeriksaan</label>
                            <input
                                type="text"
                                name="keyword"
                                class="form-control"
                                value="{{ $keyword }}"
                                placeholder="No. rontgen, pemeriksaan, radiolog..."
                            >
                        </div>

                        <div class="form-group">
                            <label class="form-label">Expertise</label>
                            <select name="expertise" class="form-control">
                                <option
                                    value="ALL"
                                    {{ $expertiseFilter === 'ALL' ? 'selected' : '' }}
                                >
                                    Semua
                                </option>
                                <option
                                    value="ADA"
                                    {{ $expertiseFilter === 'ADA' ? 'selected' : '' }}
                                >
                                    Sudah Ada
                                </option>
                                <option
                                    value="BELUM"
                                    {{ $expertiseFilter === 'BELUM' ? 'selected' : '' }}
                                >
                                    Belum Ada
                                </option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Per Halaman</label>
                            <select name="per_page" class="form-control">
                                @foreach([10, 20, 50] as $size)
                                    <option
                                        value="{{ $size }}"
                                        {{ $perPage == $size ? 'selected' : '' }}
                                    >
                                        {{ $size }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="btn-primary">
                            Terapkan
                        </button>
                    </div>
                </form>
            </div>

            @forelse($radiologyItems as $item)
                <article class="rad-card">
                    <div class="rad-card-header">
                        <div>
                            <div class="rad-exam">
                                {{ data_get($item, 'nama_pemeriksaan', '-') }}
                            </div>

                            <div class="rad-number">
                                No. Rontgen:
                                {{ data_get($item, 'no_rontgen', '-') }}
                                · Registrasi:
                                {{ data_get($item, 'no_register', '-') }}
                            </div>
                        </div>

                        <div class="badge-row">
                            @if(data_get($item, 'is_critical'))
                                <span class="badge badge-critical">
                                    Kritis
                                </span>
                            @endif

                            @if(data_get($item, 'has_expertise'))
                                <span class="badge badge-ready">
                                    Sudah Expertise
                                </span>
                            @else
                                <span class="badge badge-wait">
                                    Belum Expertise
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="rad-card-body">
                        <div class="rad-info">
                            <div>
                                <div class="info-label">Tanggal</div>
                                <div class="info-value">
                                    @if(data_get($item, 'display_date'))
                                        {{ data_get($item, 'display_date')->format('d-m-Y H:i') }}
                                    @else
                                        -
                                    @endif
                                </div>
                            </div>

                            <div>
                                <div class="info-label">Radiografer</div>
                                <div class="info-value">
                                    {{ data_get($item, 'nama_radiografer', '-') ?: '-' }}
                                </div>
                            </div>

                            <div>
                                <div class="info-label">Dokter Radiolog</div>
                                <div class="info-value">
                                    {{ data_get($item, 'nama_radiolog', '-') ?: '-' }}
                                </div>
                            </div>
                        </div>

                        <div class="rad-actions">
                            <a
                                href="{{ route('radiology.detail', [
                                    'id' => data_get($item, 'id'),
                                    'nrm' => $nrm
                                ]) }}"
                                class="btn-detail"
                            >
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M2.5 12S6 6.5 12 6.5S21.5 12 21.5 12S18 17.5 12 17.5S2.5 12 2.5 12Z" stroke="currentColor" stroke-width="2" />
                                    <circle cx="12" cy="12" r="2.5" stroke="currentColor" stroke-width="2" />
                                </svg>
                                <span>Lihat Detail</span>
                            </a>
                        </div>
                    </div>
                </article>
            @empty
                <div class="empty-card">
                    Belum ada riwayat radiologi yang sesuai dengan filter.
                </div>
            @endforelse

            @if($radiologyItems->hasPages())
                <div class="pagination-simple">
                    <div class="page-info">
                        Menampilkan
                        {{ $radiologyItems->firstItem() }}
                        -
                        {{ $radiologyItems->lastItem() }}
                        dari
                        {{ $radiologyItems->total() }}
                        data
                    </div>

                    <div class="page-actions">
                        @if($radiologyItems->onFirstPage())
                            <span class="page-btn disabled">
                                ← Sebelumnya
                            </span>
                        @else
                            <a
                                href="{{ $radiologyItems->previousPageUrl() }}"
                                class="page-btn"
                            >
                                ← Sebelumnya
                            </a>
                        @endif

                        @if($radiologyItems->hasMorePages())
                            <a
                                href="{{ $radiologyItems->nextPageUrl() }}"
                                class="page-btn"
                            >
                                Berikutnya →
                            </a>
                        @else
                            <span class="page-btn disabled">
                                Berikutnya →
                            </span>
                        @endif
                    </div>
                </div>
            @endif
        @endif

        <div class="rad-footer-nav">
            <a href="{{ $mainMenuUrl }}" class="nav-button is-menu">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M3 11L12 4L21 11" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M5 10V20H19V10M9 20V14H15V20" stroke="currentColor" stroke-width="2" stroke-linejoin="round" />
                </svg>
                <span>Kembali ke Menu Utama</span>
            </a>

            @if($logoutRouteName)
                <form method="POST" action="{{ route($logoutRouteName) }}" class="rad-logout-form">
                    @csrf
                    <button type="submit" class="nav-button is-logout">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M10 5H6C4.9 5 4 5.9 4 7V17C4 18.1 4.9 19 6 19H10" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            <path d="M14 8L18 12L14 16M18 12H9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <span>Logout</span>
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>

@endsection
