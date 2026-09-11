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

    $routeOrUrl = function ($routeName, $fallback) {
        return Route::has($routeName)
            ? route($routeName)
            : url($fallback);
    };

    /* =========================================================
       Identitas pasien
       Prioritas session agar konsisten dengan menu utama NADI.
       Data dari item radiologi menjadi fallback.
       ========================================================= */
    $sessionPatient = session('pasien', []);

    $patientName = trim((string) (
        data_get($sessionPatient, 'name')
        ?: data_get($sessionPatient, 'namapasien')
        ?: data_get($sessionPatient, 'patient_name')
        ?: data_get($sessionPatient, 'nama_pasien')
        ?: data_get($item, 'nama_pasien')
        ?: 'Pasien'
    ));

    $medicalRecord = trim((string) (
        data_get($sessionPatient, 'medical_record')
        ?: data_get($sessionPatient, 'rm')
        ?: data_get($sessionPatient, 'nocm')
        ?: data_get($item, 'no_rm')
        ?: '-'
    ));

    $birthDate =
        data_get($sessionPatient, 'birth_date')
        ?: data_get($sessionPatient, 'tanggal_lahir')
        ?: data_get($sessionPatient, 'tgllahir')
        ?: data_get($item, 'tanggal_lahir')
        ?: data_get($item, 'tgllahir');

    $birthDateLabel = '-';

    if ($birthDate) {
        try {
            $birthDateLabel = \Carbon\Carbon::parse($birthDate)->format('d-m-Y');
        } catch (\Throwable $e) {
            $birthDateLabel = (string) $birthDate;
        }
    }

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

    $formatDateTime = static function ($value, $default = '-') {
        if ($value instanceof \Carbon\CarbonInterface) {
            return $value->format('d-m-Y H:i');
        }

        $value = trim((string) $value);

        if ($value === '' || strpos($value, '1900-01-01') === 0) {
            return $default;
        }

        try {
            return \Carbon\Carbon::parse($value)->format('d-m-Y H:i');
        } catch (\Throwable $e) {
            return $value;
        }
    };
@endphp

<style>
    /* =========================================================
       RESET LAYOUT.APP
       Samakan dengan menu utama NADI / Laboratorium / Radiologi.
       ========================================================= */
    html,
    body {
        margin: 0 !important;
        padding: 0 !important;
    }

    #app > nav.navbar,
    #app > .navbar,
    body > nav.navbar,
    nav.navbar.navbar-expand-md,
    .navbar.navbar-expand-md {
        display: none !important;
        height: 0 !important;
        min-height: 0 !important;
        margin: 0 !important;
        padding: 0 !important;
        border: 0 !important;
        box-shadow: none !important;
    }

    #app,
    #app > main,
    main.py-4,
    .py-4 {
        margin-top: 0 !important;
        padding-top: 0 !important;
    }

    #app {
        min-height: 100vh;
    }

    .content-wrapper,
    .content,
    .app-content,
    .page-content,
    .main-content {
        margin-top: 0 !important;
        padding-top: 0 !important;
    }

    .content-header {
        display: none !important;
        height: 0 !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    :root {
        --nadi-blue: #163a93;
        --nadi-blue-2: #1477ee;
        --nadi-blue-soft: #edf6ff;
        --nadi-green: #18aa61;
        --nadi-green-soft: #eafaf1;
        --nadi-text: #0f2d73;
        --nadi-dark: #183153;
        --nadi-muted: #66758e;
        --nadi-line: #dfe8f2;
        --nadi-bg: #f7fbff;
        --nadi-danger: #ef4444;
        --nadi-warning: #d88908;
    }

    .nadi-page,
    .nadi-page * {
        box-sizing: border-box;
    }

    .nadi-page {
        min-height: calc(100svh - 72px);
        padding: 22px 12px 34px;
        background:
            radial-gradient(circle at 8% 5%, rgba(24, 170, 97, .10), transparent 28%),
            radial-gradient(circle at 96% 8%, rgba(20, 119, 238, .09), transparent 30%),
            linear-gradient(180deg, #fff 0%, var(--nadi-bg) 100%);
        color: var(--nadi-text);
    }

    .nadi-phone {
        width: min(100%, 520px);
        margin: 0 auto;
        overflow: hidden;
        border: 1px solid rgba(22, 58, 147, .10);
        border-radius: 28px;
        background: rgba(255, 255, 255, .98);
        box-shadow:
            0 26px 70px rgba(15, 45, 115, .11),
            0 2px 10px rgba(15, 45, 115, .04);
    }

    .nadi-main {
        padding: 22px 18px 105px;
    }

    /* Header */
    .nadi-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 16px;
    }

    .nadi-welcome {
        display: flex;
        min-width: 0;
        align-items: center;
        gap: 11px;
    }

    .nadi-user-icon {
        display: grid;
        width: 48px;
        height: 48px;
        flex: 0 0 48px;
        place-items: center;
        border-radius: 50%;
        background: linear-gradient(145deg, #dff8c9, #bfeea7);
        color: var(--nadi-green);
    }

    .nadi-greeting {
        min-width: 0;
    }

    .nadi-greeting-small {
        color: var(--nadi-blue);
        font-size: 12px;
        font-weight: 800;
        line-height: 1.2;
    }

    .nadi-greeting-name {
        max-width: 285px;
        overflow: hidden;
        color: var(--nadi-text);
        font-size: clamp(20px, 5.5vw, 28px);
        font-weight: 950;
        line-height: 1.05;
        text-overflow: ellipsis;
        white-space: nowrap;
        letter-spacing: -.035em;
    }

    .nadi-header-actions {
        display: flex;
        align-items: center;
        gap: 7px;
    }

    .nadi-icon-button,
    .nadi-logout-button {
        display: grid;
        width: 40px;
        height: 40px;
        flex: 0 0 40px;
        place-items: center;
        border: 0;
        border-radius: 12px;
        background: #fff;
        color: var(--nadi-blue-2);
        cursor: pointer;
        text-decoration: none;
        transition: .18s ease;
    }

    .nadi-icon-button:hover,
    .nadi-logout-button:hover {
        background: var(--nadi-blue-soft);
        color: var(--nadi-blue);
        text-decoration: none;
    }

    .nadi-logout-form {
        margin: 0;
    }

    /* Intro detail radiologi */
    .rad-intro {
        position: relative;
        margin-bottom: 13px;
        padding: 15px;
        overflow: hidden;
        border: 1px solid #d9ebfb;
        border-radius: 20px;
        background: linear-gradient(135deg, rgba(235, 247, 255, .98), rgba(247, 252, 255, .98));
        box-shadow: 0 10px 26px rgba(31, 110, 191, .06);
    }

    .rad-intro::after {
        position: absolute;
        top: -30px;
        right: -28px;
        width: 92px;
        height: 92px;
        border: 18px solid rgba(20, 119, 238, .055);
        border-radius: 50%;
        content: '';
    }

    .rad-intro-head {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .rad-intro-icon {
        display: grid;
        width: 45px;
        height: 45px;
        flex: 0 0 45px;
        place-items: center;
        border-radius: 13px;
        background: var(--nadi-blue-soft);
        color: var(--nadi-blue-2);
    }

    .rad-intro-title {
        margin: 0;
        color: var(--nadi-blue);
        font-size: 18px;
        font-weight: 950;
        line-height: 1.2;
        letter-spacing: -.025em;
    }

    .rad-intro-text {
        margin: 4px 0 0;
        color: var(--nadi-muted);
        font-size: 11px;
        font-weight: 650;
        line-height: 1.5;
    }

    /* Identitas pasien */
    .rad-patient {
        display: grid;
        grid-template-columns: 1.15fr .85fr;
        gap: 8px;
        margin-bottom: 12px;
    }

    .rad-patient-item {
        min-width: 0;
        padding: 11px 12px;
        border: 1px solid var(--nadi-line);
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 6px 16px rgba(20, 70, 130, .035);
    }

    .rad-patient-item.is-wide {
        grid-column: 1 / -1;
    }

    .rad-label {
        margin-bottom: 4px;
        color: #8795a9;
        font-size: 10px;
        font-weight: 900;
        letter-spacing: .04em;
        text-transform: uppercase;
    }

    .rad-value {
        min-width: 0;
        overflow-wrap: anywhere;
        color: var(--nadi-dark);
        font-size: 12.5px;
        font-weight: 900;
        line-height: 1.4;
    }

    .rad-back {
        display: inline-flex;
        min-height: 38px;
        align-items: center;
        justify-content: center;
        gap: 6px;
        margin-bottom: 12px;
        padding: 0 12px;
        border: 1px solid #cfe3fa;
        border-radius: 10px;
        background: var(--nadi-blue-soft);
        color: var(--nadi-blue-2);
        font-size: 11px;
        font-weight: 900;
        text-decoration: none;
        transition: .18s ease;
    }

    .rad-back:hover {
        transform: translateY(-1px);
        background: #e4f2ff;
        color: var(--nadi-blue);
        text-decoration: none;
    }

    /* Card hasil */
    .rad-detail-card {
        margin-bottom: 14px;
        overflow: hidden;
        border: 1px solid var(--nadi-line);
        border-radius: 18px;
        background: #fff;
        box-shadow: 0 9px 24px rgba(15, 45, 115, .045);
    }

    .rad-detail-card::before {
        display: block;
        height: 3px;
        background: linear-gradient(90deg, var(--nadi-blue-2) 0 70%, var(--nadi-green) 70% 100%);
        content: '';
    }

    .rad-result-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
        padding: 15px 16px 13px;
        border-bottom: 1px solid #edf2f7;
        background: linear-gradient(90deg, rgba(237, 246, 255, .75), rgba(255, 255, 255, 0));
    }

    .rad-result-title {
        margin: 0;
        color: var(--nadi-dark);
        font-size: 17px;
        font-weight: 950;
        line-height: 1.35;
        letter-spacing: -.02em;
    }

    .rad-result-subtitle {
        margin-top: 5px;
        color: #72839a;
        font-size: 11px;
        font-weight: 750;
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
        padding: 6px 9px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 900;
        line-height: 1.2;
    }

    .rad-status-badge.is-ready {
        background: var(--nadi-green-soft);
        color: #118148;
    }

    .rad-status-badge.is-wait {
        background: #fff7df;
        color: #a16207;
    }

    .rad-status-badge.is-critical {
        background: #fff0f0;
        color: #c63838;
    }

    .rad-detail-body {
        padding: 14px 16px 16px;
    }

    /* Informasi pemeriksaan */
    .rad-meta-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 8px;
        margin-bottom: 13px;
    }

    .rad-meta-item {
        min-width: 0;
        padding: 11px 12px;
        border: 1px solid #e7edf5;
        border-radius: 12px;
        background: #f8fbff;
    }

    .rad-meta-label {
        margin-bottom: 4px;
        color: #8795a9;
        font-size: 10px;
        font-weight: 900;
        letter-spacing: .035em;
        text-transform: uppercase;
    }

    .rad-meta-value {
        overflow-wrap: anywhere;
        color: #40536f;
        font-size: 12px;
        font-weight: 850;
        line-height: 1.45;
    }

    /* Expertise */
    .rad-expertise-section {
        margin-top: 11px;
        overflow: hidden;
        border: 1px solid var(--nadi-line);
        border-radius: 14px;
        background: #fff;
    }

    .rad-expertise-header {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 11px 12px;
        border-bottom: 1px solid var(--nadi-line);
        background: #f8fbff;
        color: var(--nadi-blue);
        font-size: 12px;
        font-weight: 950;
    }

    .rad-expertise-icon {
        display: grid;
        width: 28px;
        height: 28px;
        flex: 0 0 28px;
        place-items: center;
        border-radius: 8px;
        background: var(--nadi-blue-soft);
        color: var(--nadi-blue-2);
    }

    .rad-expertise-text {
        padding: 13px;
        color: #40536f;
        font-size: 12.5px;
        line-height: 1.75;
        white-space: pre-line;
    }

    .rad-expertise-section.is-conclusion {
        border-color: #cfe3fa;
    }

    .rad-expertise-section.is-conclusion .rad-expertise-header {
        border-bottom-color: #cfe3fa;
        background: var(--nadi-blue-soft);
        color: var(--nadi-blue);
    }

    .rad-expertise-section.is-waiting {
        border-color: #f6df9e;
        background: #fffaf0;
    }

    .rad-expertise-section.is-waiting .rad-expertise-text {
        color: #93630d;
        font-weight: 750;
    }

    /* Bottom navigation */
    .nadi-bottom-nav {
        position: fixed;
        z-index: 1000;
        left: 50%;
        bottom: 0;
        display: grid;
        width: min(100%, 520px);
        grid-template-columns: repeat(5, 1fr);
        transform: translateX(-50%);
        border-top: 1px solid #e6edf5;
        background: rgba(255, 255, 255, .98);
        box-shadow: 0 -8px 24px rgba(15, 45, 115, .08);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        padding-bottom: env(safe-area-inset-bottom);
    }

    .nadi-bottom-link {
        position: relative;
        display: flex;
        min-height: 72px;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 5px;
        color: #667891;
        font-size: 9px;
        font-weight: 750;
        text-decoration: none;
    }

    .nadi-bottom-link:hover {
        color: var(--nadi-blue-2);
        text-decoration: none;
    }

    .nadi-bottom-link.is-active {
        color: var(--nadi-blue-2);
        font-weight: 900;
    }

    .nadi-bottom-link.is-active::after {
        position: absolute;
        right: 28%;
        bottom: 7px;
        left: 28%;
        height: 3px;
        border-radius: 999px;
        background: var(--nadi-blue-2);
        content: '';
    }

    /* =========================================================
       DESKTOP / PC
       Full width seperti menu utama NADI dan halaman radiologi.
       ========================================================= */
    @media (min-width: 768px) {
        .nadi-page {
            position: relative;
            left: 50%;
            width: 100vw;
            min-height: calc(100svh - 72px);
            margin-left: -50vw;
            padding: 26px clamp(24px, 3vw, 48px) 110px;
        }

        .nadi-phone {
            width: 100%;
            max-width: none;
            margin: 0;
            overflow: visible;
            border: 0;
            border-radius: 0;
            background: transparent;
            box-shadow: none;
        }

        .nadi-main {
            width: 100%;
            padding: 0;
        }

        .nadi-header {
            margin-bottom: 20px;
        }

        .rad-intro,
        .rad-patient,
        .rad-detail-card {
            width: 100%;
        }

        .rad-intro {
            padding: 18px 20px;
        }

        .rad-intro-title {
            font-size: 20px;
        }

        .rad-intro-text {
            font-size: 12px;
        }

        .rad-patient {
            grid-template-columns: 1.5fr .75fr .75fr;
            gap: 0;
            overflow: hidden;
            border: 1px solid var(--nadi-line);
            border-radius: 16px;
            background: #fff;
            box-shadow: 0 7px 18px rgba(15, 45, 115, .035);
        }

        .rad-patient-item,
        .rad-patient-item.is-wide {
            grid-column: auto;
            border: 0;
            border-radius: 0;
            box-shadow: none;
        }

        .rad-patient-item + .rad-patient-item {
            border-left: 1px solid #edf2f7;
        }

        .rad-result-header {
            padding: 18px 20px 16px;
        }

        .rad-result-title {
            font-size: 20px;
        }

        .rad-result-subtitle {
            font-size: 12px;
        }

        .rad-status-badge {
            font-size: 11px;
        }

        .rad-detail-body {
            padding: 18px 20px 20px;
        }

        .rad-meta-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
        }

        .rad-meta-item {
            padding: 13px 14px;
        }

        .rad-meta-label {
            font-size: 10.5px;
        }

        .rad-meta-value {
            font-size: 12.5px;
        }

        .rad-expertise-header {
            padding: 12px 14px;
            font-size: 13px;
        }

        .rad-expertise-text {
            padding: 15px 16px;
            font-size: 13px;
        }

        .nadi-bottom-nav {
            width: 100vw;
        }
    }

    @media (max-width: 767px) {
        .rad-meta-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 420px) {
        .nadi-page {
            padding: 0;
            background: #fff;
        }

        .nadi-phone {
            width: 100%;
            min-height: calc(100svh - 72px);
            border: 0;
            border-radius: 0;
            box-shadow: none;
        }

        .nadi-main {
            padding: 18px 14px 100px;
        }

        .nadi-greeting-name {
            max-width: 205px;
            font-size: 22px;
        }

        .nadi-user-icon {
            width: 43px;
            height: 43px;
            flex-basis: 43px;
        }

        .rad-result-header {
            flex-direction: column;
        }

        .rad-badge-row {
            justify-content: flex-start;
        }

        .rad-meta-grid {
            grid-template-columns: 1fr;
        }

        .nadi-bottom-link {
            min-height: 68px;
            font-size: 8.5px;
        }
    }

    @media (max-width: 350px) {
        .nadi-main {
            padding-right: 10px;
            padding-left: 10px;
        }

        .nadi-greeting-name {
            max-width: 150px;
        }

        .rad-patient {
            grid-template-columns: 1fr;
        }

        .rad-patient-item.is-wide {
            grid-column: auto;
        }
    }
</style>

<section class="nadi-page">
    <div class="nadi-phone">
        <main class="nadi-main">
            {{-- Header NADI --}}
            <header class="nadi-header">
                <div class="nadi-welcome">
                    <div class="nadi-user-icon" aria-hidden="true">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <circle cx="12" cy="8" r="4" fill="currentColor"/>
                            <path d="M4 21C4.7 16.4 7.3 14 12 14C16.7 14 19.3 16.4 20 21" fill="currentColor"/>
                        </svg>
                    </div>

                    <div class="nadi-greeting">
                        <div class="nadi-greeting-small">Selamat Datang,</div>
                        <div class="nadi-greeting-name">
                            {{ $patientName ?: 'Pasien' }}
                        </div>
                    </div>
                </div>

                <div class="nadi-header-actions">
                    <a
                        href="{{ $radiologyHistoryUrl }}"
                        class="nadi-icon-button"
                        aria-label="Kembali ke riwayat radiologi"
                        title="Kembali ke riwayat radiologi"
                    >
                        <svg width="21" height="21" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>

                    @if($logoutRouteName)
                        <form method="POST" action="{{ route($logoutRouteName) }}" class="nadi-logout-form">
                            @csrf
                            <button type="submit" class="nadi-logout-button" aria-label="Logout" title="Logout">
                                <svg width="21" height="21" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M10 5H6C4.9 5 4 5.9 4 7V17C4 18.1 4.9 19 6 19H10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    <path d="M14 8L18 12L14 16M18 12H9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </form>
                    @endif
                </div>
            </header>

            {{-- Intro Detail Radiologi --}}
            <section class="rad-intro">
                <div class="rad-intro-head">
                    <div class="rad-intro-icon" aria-hidden="true">
                        <svg width="27" height="27" viewBox="0 0 24 24" fill="none">
                            <rect x="4" y="3" width="16" height="18" rx="3" stroke="currentColor" stroke-width="2"/>
                            <circle cx="12" cy="10" r="3" stroke="currentColor" stroke-width="2"/>
                            <path d="M8 17C9.1 15.7 10.4 15 12 15C13.6 15 14.9 15.7 16 17" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <path d="M8 6H9M15 6H16" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                        </svg>
                    </div>

                    <div>
                        <h1 class="rad-intro-title">Detail Hasil Radiologi</h1>
                        <p class="rad-intro-text">
                            Lihat hasil pemeriksaan, informasi petugas, dan expertise dokter radiologi.
                        </p>
                    </div>
                </div>
            </section>

            {{-- Identitas pasien --}}
          

            <a href="{{ $radiologyHistoryUrl }}" class="rad-back">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span>Kembali ke Riwayat Radiologi</span>
            </a>

            <article class="rad-detail-card">
                <div class="rad-result-header">
                    <div>
                        <h2 class="rad-result-title">
                            {{ $examName }}
                        </h2>

                        <div class="rad-result-subtitle">
                            No. Rontgen:
                            <strong>{{ data_get($item, 'no_rontgen', '-') }}</strong>
                            &nbsp;·&nbsp;
                            Registrasi:
                            <strong>{{ data_get($item, 'no_register', '-') }}</strong>
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
                                Menunggu Expertise
                            </span>
                        @endif
                    </div>
                </div>

                <div class="rad-detail-body">
                    <div class="rad-meta-grid">
                        <div class="rad-meta-item">
                            <div class="rad-meta-label">No. Rontgen</div>
                            <div class="rad-meta-value">
                                {{ data_get($item, 'no_rontgen', '-') ?: '-' }}
                            </div>
                        </div>

                        <div class="rad-meta-item">
                            <div class="rad-meta-label">No. Registrasi</div>
                            <div class="rad-meta-value">
                                {{ data_get($item, 'no_register', '-') ?: '-' }}
                            </div>
                        </div>

                        <div class="rad-meta-item">
                            <div class="rad-meta-label">Dokter Radiolog</div>
                            <div class="rad-meta-value">
                                {{ data_get($item, 'nama_radiolog', '-') ?: '-' }}
                            </div>
                        </div>

                        <div class="rad-meta-item">
                            <div class="rad-meta-label">Radiografer</div>
                            <div class="rad-meta-value">
                                {{ data_get($item, 'nama_radiografer', '-') ?: '-' }}
                            </div>
                        </div>

                        <div class="rad-meta-item">
                            <div class="rad-meta-label">Mulai Pemeriksaan</div>
                            <div class="rad-meta-value">
                                {{ $formatDateTime(data_get($item, 'radiografer_datetime_start')) }}
                            </div>
                        </div>

                        <div class="rad-meta-item">
                            <div class="rad-meta-label">Selesai Pemeriksaan</div>
                            <div class="rad-meta-value">
                                {{ $formatDateTime(data_get($item, 'radiografer_datetime_end')) }}
                            </div>
                        </div>

                        <div class="rad-meta-item">
                            <div class="rad-meta-label">Selesai Expertise</div>
                            <div class="rad-meta-value">
                                {{ $hasValidRadiologEnd ? $formatDateTime($radiologEnd) : '-' }}
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
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M4 5H20M4 10H20M4 15H14M4 20H11" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                </span>
                                <span>Hasil Pemeriksaan / Finding</span>
                            </div>

                            <div class="rad-expertise-text">
                                {{ data_get($item, 'expertise_text_finding') }}
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
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M5 12L10 17L19 7" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                                <span>Kesan / Kesimpulan</span>
                            </div>

                            <div class="rad-expertise-text">
                                {{ data_get($item, 'expertise_text_conclusion') }}
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
        </main>

        {{-- Bottom navigation mengikuti menu utama NADI --}}
        <nav class="nadi-bottom-nav" aria-label="Navigasi utama">
            <a href="{{ $mainMenuUrl }}" class="nadi-bottom-link">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M3 11L12 3L21 11V21H14V15H10V21H3V11Z" fill="currentColor"/>
                </svg>
                <span>Beranda</span>
            </a>

            <a href="{{ $mainMenuUrl }}#menu-layanan" class="nadi-bottom-link is-active">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <rect x="3" y="3" width="7" height="7" rx="2" stroke="currentColor" stroke-width="2"/>
                    <rect x="14" y="3" width="7" height="7" rx="2" stroke="currentColor" stroke-width="2"/>
                    <rect x="3" y="14" width="7" height="7" rx="2" stroke="currentColor" stroke-width="2"/>
                    <rect x="14" y="14" width="7" height="7" rx="2" stroke="currentColor" stroke-width="2"/>
                </svg>
                <span>Layanan</span>
            </a>

            <a href="{{ $routeOrUrl('riwayat.index', '/riwayat') }}" class="nadi-bottom-link">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/>
                    <path d="M12 7V12L15.5 14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span>Riwayat</span>
            </a>

            <a href="{{ $routeOrUrl('bantuan.index', '/bantuan') }}" class="nadi-bottom-link">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M4 13V11C4 6.6 7.6 3 12 3C16.4 3 20 6.6 20 11V13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <rect x="2.5" y="11.5" width="4" height="7" rx="2" fill="currentColor"/>
                    <rect x="17.5" y="11.5" width="4" height="7" rx="2" fill="currentColor"/>
                </svg>
                <span>Bantuan</span>
            </a>

            <a href="{{ $routeOrUrl('profile.index', '/profil') }}" class="nadi-bottom-link">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <circle cx="12" cy="8" r="4" stroke="currentColor" stroke-width="2"/>
                    <path d="M5 21C5.8 16.8 8.1 14.5 12 14.5C15.9 14.5 18.2 16.8 19 21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
                <span>Profil</span>
            </a>
        </nav>
    </div>
</section>

@endsection
