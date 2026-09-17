@extends('layouts.app', ['title' => 'Aturan Minum Obat | NADI RSBM'])

@section('content')

@php
    $patientName = data_get(
        $patient,
        'name',
        data_get(
            $patient,
            'namapasien',
            data_get($registrasi, 'namapasien', 'Pasien')
        )
    );

    $medicalRecord = data_get(
        $patient,
        'medical_record',
        data_get(
            $patient,
            'rm',
            data_get(
                $patient,
                'nocm',
                data_get($registrasi, 'nocm', '-')
            )
        )
    );

    $noRegistrasi = data_get(
        $registrasi,
        'noregistrasi',
        '-'
    );

    $poli = data_get(
        $registrasi,
        'namaruangan',
        '-'
    );

    $dokter = data_get(
        $registrasi,
        'dokter',
        '-'
    );

    $tanggalRaw = data_get(
        $registrasi,
        'tglregistrasi',
        data_get(
            $registrasi,
            'tanggalreservasi'
        )
    );

    try {
        $tanggalLabel = $tanggalRaw
            ? \Carbon\Carbon::parse($tanggalRaw)
                ->locale('id')
                ->translatedFormat('d F Y')
            : '-';

        $jamLabel = $tanggalRaw
            ? \Carbon\Carbon::parse($tanggalRaw)->format('H:i')
            : null;
    } catch (\Throwable $e) {
        $tanggalLabel = $tanggalRaw ?: '-';
        $jamLabel = null;
    }

    $backUrl = Route::has('aturan-minum.index')
        ? route('aturan-minum.index')
        : url('/aturan-minum-obat');

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

    $isTruthy = function ($value) {
        return $value === true
            || $value === 1
            || $value === '1'
            || $value === 'true'
            || $value === 't';
    };

@endphp

<style>
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

    :root {
        --nadi-blue: #163a93;
        --nadi-blue-2: #1477ee;
        --nadi-blue-soft: #edf6ff;
        --nadi-green: #18aa61;
        --nadi-green-dark: #117845;
        --nadi-green-soft: #eafaf1;
        --nadi-text: #0f2d73;
        --nadi-dark: #183153;
        --nadi-muted: #66758e;
        --nadi-line: #dfe8f2;
        --nadi-bg: #f7fbff;
        --nadi-warning: #9a6a11;
        --nadi-warning-bg: #fff9ec;
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

    /* =========================
       HEADER NADI
       ========================= */
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

    /* =========================
       INTRO
       ========================= */
    .medicine-intro {
        position: relative;
        margin-bottom: 13px;
        padding: 15px;
        overflow: hidden;
        border: 1px solid #d8efdf;
        border-radius: 20px;
        background: linear-gradient(
            135deg,
            rgba(237, 252, 244, .98),
            rgba(248, 253, 255, .98)
        );
        box-shadow: 0 10px 26px rgba(24, 170, 97, .06);
    }

    .medicine-intro::after {
        position: absolute;
        top: -31px;
        right: -28px;
        width: 92px;
        height: 92px;
        border: 18px solid rgba(24, 170, 97, .055);
        border-radius: 50%;
        content: '';
    }

    .medicine-intro-head {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .medicine-intro-icon {
        display: grid;
        width: 45px;
        height: 45px;
        flex: 0 0 45px;
        place-items: center;
        border-radius: 13px;
        background: var(--nadi-green-soft);
        color: var(--nadi-green);
    }

    .medicine-intro-title {
        margin: 0;
        color: var(--nadi-blue);
        font-size: 18px;
        font-weight: 950;
        line-height: 1.2;
        letter-spacing: -.025em;
    }

    .medicine-intro-text {
        margin: 4px 0 0;
        color: var(--nadi-muted);
        font-size: 10.5px;
        font-weight: 650;
        line-height: 1.45;
    }

    /* =========================
       IDENTITAS - SATU BARIS
       ========================= */
    .patient-strip {
        display: grid;
        grid-template-columns: minmax(0, 1.5fr) minmax(120px, .7fr);
        margin-bottom: 15px;
        overflow: hidden;
        border: 1px solid var(--nadi-line);
        border-radius: 17px;
        background: #fff;
        box-shadow: 0 8px 22px rgba(15, 45, 115, .045);
    }

    .patient-strip-item {
        min-width: 0;
        padding: 12px 13px;
    }

    .patient-strip-item + .patient-strip-item {
        border-left: 1px solid #edf2f7;
    }

    .patient-label {
        margin-bottom: 3px;
        color: #8a99ad;
        font-size: 10px;
        font-weight: 900;
        letter-spacing: .04em;
        text-transform: uppercase;
    }

    .patient-value {
        overflow-wrap: anywhere;
        color: var(--nadi-dark);
        font-size: 12.5px;
        font-weight: 900;
        line-height: 1.4;
    }

    /* =========================
       VISIT SUMMARY
       ========================= */
    .visit-summary {
        margin-bottom: 17px;
        padding: 14px;
        border: 1px solid var(--nadi-line);
        border-radius: 18px;
        background: #fff;
        box-shadow: 0 9px 24px rgba(15, 45, 115, .045);
    }

    .visit-main {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
    }

    .visit-room {
        color: var(--nadi-dark);
        font-size: 15px;
        font-weight: 950;
        line-height: 1.35;
    }

    .visit-date {
        margin-top: 4px;
        color: #72839a;
        font-size: 10.5px;
        font-weight: 750;
    }

    .visit-status {
        display: inline-flex;
        min-height: 26px;
        align-items: center;
        padding: 0 9px;
        border-radius: 999px;
        background: var(--nadi-green-soft);
        color: var(--nadi-green-dark);
        font-size: 9.5px;
        font-weight: 900;
        white-space: nowrap;
    }

    /* =========================
       RESEP GROUP
       ========================= */
    .prescription-group + .prescription-group {
        margin-top: 11px;
    }

    /* =========================
       DRUG CARD
       ========================= */
    .drug-card {
        position: relative;
        margin-bottom: 11px;
        padding: 14px;
        overflow: hidden;
        border: 1px solid var(--nadi-line);
        border-radius: 16px;
        background: #fff;
        box-shadow: 0 9px 24px rgba(15, 45, 115, .045);
    }

    .drug-card::before {
        position: absolute;
        top: 0;
        right: 0;
        left: 0;
        height: 3px;
        background: linear-gradient(
            90deg,
            var(--nadi-green) 0 35%,
            var(--nadi-blue-2) 35% 100%
        );
        content: '';
    }

    .drug-head {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding-top: 2px;
    }

    .drug-icon {
        display: grid;
        width: 38px;
        height: 38px;
        flex: 0 0 38px;
        place-items: center;
        border-radius: 11px;
        background: var(--nadi-green-soft);
        color: var(--nadi-green);
    }

    .drug-main {
        min-width: 0;
        flex: 1 1 auto;
    }

    .drug-title-row {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 8px;
    }

    .drug-name {
        overflow-wrap: anywhere;
        color: var(--nadi-dark);
        font-size: 13px;
        font-weight: 950;
        line-height: 1.35;
    }

    .route-badge {
        display: inline-flex;
        min-height: 24px;
        flex: 0 0 auto;
        align-items: center;
        padding: 0 8px;
        border-radius: 999px;
        background: var(--nadi-blue-soft);
        color: var(--nadi-blue-2);
        font-size: 8.5px;
        font-weight: 900;
        white-space: nowrap;
    }

    .instruction {
        margin-top: 11px;
        padding: 11px 12px;
        border: 1px solid #d7eee0;
        border-radius: 11px;
        background: #f5fcf8;
    }

    .instruction-label {
        color: #57836b;
        font-size: 9px;
        font-weight: 900;
        text-transform: uppercase;
    }

    .instruction-value {
        margin-top: 4px;
        color: #245b3f;
        font-size: 14px;
        font-weight: 950;
        line-height: 1.4;
    }

    .schedule {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 9px;
    }

    .schedule-chip {
        display: inline-flex;
        min-height: 28px;
        align-items: center;
        gap: 5px;
        padding: 0 9px;
        border-radius: 999px;
        background: var(--nadi-blue-soft);
        color: var(--nadi-blue);
        font-size: 9px;
        font-weight: 900;
    }

    .schedule-dot {
        width: 6px;
        height: 6px;
        flex: 0 0 6px;
        border-radius: 50%;
        background: currentColor;
        opacity: .65;
    }

    .drug-note {
        margin-top: 9px;
        padding: 9px 10px;
        border-left: 3px solid #d9a229;
        border-radius: 8px;
        background: var(--nadi-warning-bg);
        color: var(--nadi-warning);
        font-size: 10px;
        font-weight: 750;
        line-height: 1.45;
    }

    .drug-note strong {
        font-weight: 950;
    }

    .state-box {
        padding: 20px 16px;
        border: 1px solid var(--nadi-line);
        border-radius: 17px;
        background: #fff;
        color: var(--nadi-muted);
        font-size: 11px;
        font-weight: 750;
        line-height: 1.55;
        text-align: center;
    }

    .state-box.is-error {
        border-color: #fecaca;
        background: #fff6f6;
        color: #b42318;
    }

    /* =========================
       BOTTOM NAV
       ========================= */
    .nadi-bottom-nav {
        position: fixed !important;
        z-index: 99999 !important;
        right: 0 !important;
        bottom: 0 !important;
        left: 0 !important;
        display: grid !important;
        width: 100% !important;
        grid-template-columns: repeat(5, minmax(0, 1fr)) !important;
        border-top: 1px solid #e6edf5;
        background: rgba(255, 255, 255, .98) !important;
        box-shadow: 0 -8px 24px rgba(15, 45, 115, .10);
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

    /* =========================
       MOBILE
       ========================= */
    @media (max-width: 420px) {
        .nadi-page {
            padding: 0;
            background: #fff;
        }

        .nadi-phone {
            width: 100%;
            min-height: calc(100svh - 68px);
            border: 0;
            border-radius: 0;
            box-shadow: none;
        }

        .nadi-main {
            padding: 18px 14px calc(100px + env(safe-area-inset-bottom));
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

        .patient-strip {
            grid-template-columns: minmax(0, 1.4fr) minmax(105px, .7fr);
        }

        .drug-title-row {
            flex-direction: column;
            gap: 5px;
        }

        .route-badge {
            align-self: flex-start;
        }

        .nadi-bottom-link {
            min-height: 68px;
            font-size: 8.5px;
        }
    }

    /* =========================
       DESKTOP
       ========================= */
    @media (min-width: 768px) {
        .nadi-page {
            position: relative;
            left: 50%;
            width: 100vw;
            min-height: 100vh;
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
            padding: 0 0 88px;
        }

        .nadi-header {
            margin-bottom: 20px;
            padding: 0 2px 18px;
            border-bottom: 1px solid rgba(22, 58, 147, .08);
        }

        .nadi-greeting-name {
            max-width: min(520px, 45vw);
            font-size: 30px;
        }

        .medicine-intro {
            padding: 20px;
            border-radius: 22px;
        }

        .patient-strip {
            grid-template-columns: minmax(0, 1.5fr) minmax(180px, .65fr);
        }

        .patient-strip-item {
            padding: 15px 18px;
        }

        .visit-summary {
            padding: 16px 18px;
        }

        .drug-card {
            padding: 16px 18px;
        }

        .drug-name {
            font-size: 14px;
        }

        .instruction-value {
            font-size: 15px;
        }

        .nadi-bottom-link {
            min-height: 66px;
            flex-direction: row;
            gap: 8px;
            font-size: 12px;
        }

        .nadi-bottom-link.is-active::after {
            right: 38%;
            bottom: 5px;
            left: 38%;
        }
    }
</style>

<section class="nadi-page">
    <div class="nadi-phone">
        <main class="nadi-main">

            {{-- =========================
                 HEADER NADI
                 ========================= --}}
            <header class="nadi-header">

                <div class="nadi-welcome">

                    <div class="nadi-user-icon" aria-hidden="true">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <circle cx="12" cy="8" r="4" fill="currentColor"/>
                            <path d="M4 21C4.7 16.4 7.3 14 12 14C16.7 14 19.3 16.4 20 21" fill="currentColor"/>
                        </svg>
                    </div>

                    <div class="nadi-greeting">
                        <div class="nadi-greeting-small">
                            Selamat Datang,
                        </div>

                        <div class="nadi-greeting-name">
                            {{ $patientName ?: 'Pasien' }}
                        </div>
                    </div>

                </div>

                <div class="nadi-header-actions">

                    <a
                        href="{{ $backUrl }}"
                        class="nadi-icon-button"
                        aria-label="Kembali"
                        title="Kembali"
                    >
                        <svg width="21" height="21" viewBox="0 0 24 24" fill="none">
                            <path
                                d="M15 18L9 12L15 6"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                        </svg>
                    </a>

                    @if($logoutRouteName)
                        <form
                            method="POST"
                            action="{{ route($logoutRouteName) }}"
                            class="nadi-logout-form"
                        >
                            @csrf

                            <button
                                type="submit"
                                class="nadi-logout-button"
                                aria-label="Logout"
                                title="Logout"
                            >
                                <svg width="21" height="21" viewBox="0 0 24 24" fill="none">
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
                            </button>
                        </form>
                    @endif

                </div>

            </header>


            {{-- =========================
                 INTRO
                 ========================= --}}
            <section class="medicine-intro">

                <div class="medicine-intro-head">

                    <div class="medicine-intro-icon" aria-hidden="true">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none">
                            <path
                                d="M8.5 4.5L19.5 15.5C21.2 17.2 21.2 19.8 19.5 21.5C17.8 23.2 15.2 23.2 13.5 21.5L2.5 10.5C.8 8.8.8 6.2 2.5 4.5C4.2 2.8 6.8 2.8 8.5 4.5Z"
                                stroke="currentColor"
                                stroke-width="2"
                            />
                            <path
                                d="M7 15L15 7"
                                stroke="currentColor"
                                stroke-width="2"
                            />
                        </svg>
                    </div>

                    <div>
                        <h1 class="medicine-intro-title">
                            Aturan Minum Obat
                        </h1>

                        <p class="medicine-intro-text">
                            Lihat cara penggunaan obat sesuai resep pada pelayanan yang dipilih.
                        </p>
                    </div>

                </div>

            </section>


            {{-- =========================
                 IDENTITAS PASIEN
                 ========================= --}}
            <section class="patient-strip" aria-label="Identitas pasien">

                <div class="patient-strip-item">
                    <div class="patient-label">
                        Nama Pasien
                    </div>

                    <div class="patient-value">
                        {{ $patientName ?: 'Pasien' }}
                    </div>
                </div>

                <div class="patient-strip-item">
                    <div class="patient-label">
                        No. Rekam Medis
                    </div>

                    <div class="patient-value">
                        {{ $medicalRecord ?: '-' }}
                    </div>
                </div>

            </section>


            {{-- =========================
                 RINGKASAN PELAYANAN
                 ========================= --}}
            <section class="visit-summary">

                <div class="visit-main">

                    <div>
                        <div class="visit-room">
                            {{ $poli ?: '-' }}
                        </div>

                        <div class="visit-date">
                            {{ $tanggalLabel }}

                            @if($jamLabel)
                                · {{ $jamLabel }}
                            @endif
                        </div>
                    </div>

                    <span class="visit-status">
                        Teregistrasi
                    </span>

                </div>


            </section>


            {{-- =========================
                 DATA OBAT
                 ========================= --}}
            @if(!empty($errorApi))

                <div class="state-box is-error">
                    {{ $errorApi }}
                </div>

            @elseif($pelayananPasien->isEmpty())

                <div class="state-box">
                    Belum ditemukan resep atau aturan obat pada pelayanan ini.
                </div>

            @else

                @foreach($pelayananPasien as $groupKey => $items)

                    <section class="prescription-group">

                        @foreach($items as $obat)

                            @php
                                $namaObat = trim(
                                    (string) data_get(
                                        $obat,
                                        'namaproduk',
                                        data_get(
                                            $obat,
                                            'productname',
                                            '-'
                                        )
                                    )
                                );

                                $aturanPakai = trim(
                                    (string) data_get(
                                        $obat,
                                        'aturanpakai',
                                        ''
                                    )
                                );

                                $aturanTambahan = trim(
                                    (string) data_get(
                                        $obat,
                                        'satuanresep',
                                        ''
                                    )
                                );

                                if (
                                    $aturanPakai !== ''
                                    && $aturanTambahan !== ''
                                ) {
                                    $aturanPakaiLengkap =
                                        $aturanPakai
                                        . ' • '
                                        . $aturanTambahan;

                                } elseif ($aturanPakai !== '') {

                                    $aturanPakaiLengkap =
                                        $aturanPakai;

                                } elseif ($aturanTambahan !== '') {

                                    $aturanPakaiLengkap =
                                        $aturanTambahan;

                                } else {

                                    $aturanPakaiLengkap =
                                        'Ikuti petunjuk dokter/farmasi';
                                }

                                $route = trim(
                                    (string) data_get(
                                        $obat,
                                        'route',
                                        ''
                                    )
                                );

                                $keterangan = trim(
                                    (string) data_get(
                                        $obat,
                                        'keteranganpakai',
                                        data_get(
                                            $obat,
                                            'keterangan',
                                            ''
                                        )
                                    )
                                );

                                $pagi = $isTruthy(
                                    data_get($obat, 'ispagi')
                                );

                                $siang = $isTruthy(
                                    data_get($obat, 'issiang')
                                );

                                $sore = $isTruthy(
                                    data_get($obat, 'issore')
                                );

                                $malam = $isTruthy(
                                    data_get($obat, 'ismalam')
                                );
                            @endphp


                            <article class="drug-card">

                                <div class="drug-head">

                                    <div class="drug-icon" aria-hidden="true">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                            <path
                                                d="M8.5 4.5L19.5 15.5C21.2 17.2 21.2 19.8 19.5 21.5C17.8 23.2 15.2 23.2 13.5 21.5L2.5 10.5C.8 8.8.8 6.2 2.5 4.5C4.2 2.8 6.8 2.8 8.5 4.5Z"
                                                stroke="currentColor"
                                                stroke-width="2"
                                            />
                                            <path
                                                d="M7 15L15 7"
                                                stroke="currentColor"
                                                stroke-width="2"
                                            />
                                        </svg>
                                    </div>

                                    <div class="drug-main">

                                        <div class="drug-title-row">

                                            <div class="drug-name">
                                                {{ $namaObat ?: '-' }}
                                            </div>

                                            @if($route !== '')
                                                <span class="route-badge">
                                                    {{ $route }}
                                                </span>
                                            @endif

                                        </div>

                                    </div>

                                </div>


                                <div class="instruction">

                                    <div class="instruction-label">
                                        Cara Menggunakan
                                    </div>

                                    <div class="instruction-value">
                                        {{ $aturanPakaiLengkap }}
                                    </div>

                                </div>


                                @if($pagi || $siang || $sore || $malam)

                                    <div class="schedule">

                                        @if($pagi)
                                            <span class="schedule-chip">
                                                <span class="schedule-dot"></span>
                                                Pagi
                                            </span>
                                        @endif

                                        @if($siang)
                                            <span class="schedule-chip">
                                                <span class="schedule-dot"></span>
                                                Siang
                                            </span>
                                        @endif

                                        @if($sore)
                                            <span class="schedule-chip">
                                                <span class="schedule-dot"></span>
                                                Sore
                                            </span>
                                        @endif

                                        @if($malam)
                                            <span class="schedule-chip">
                                                <span class="schedule-dot"></span>
                                                Malam
                                            </span>
                                        @endif

                                    </div>

                                @endif


                                @if($keterangan !== '')

                                    <div class="drug-note">
                                        <strong>Keterangan:</strong>
                                        {{ $keterangan }}
                                    </div>

                                @endif

                            </article>

                        @endforeach

                    </section>

                @endforeach

            @endif

        </main>


        {{-- =========================
             BOTTOM NAVIGATION
             ========================= --}}
        <nav class="nadi-bottom-nav" aria-label="Navigasi utama">

            <a
                href="{{ $mainMenuUrl }}"
                class="nadi-bottom-link"
            >
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                    <path
                        d="M3 11L12 3L21 11V21H14V15H10V21H3V11Z"
                        fill="currentColor"
                    />
                </svg>

                <span>Beranda</span>
            </a>

            <a
                href="{{ $mainMenuUrl }}#menu-layanan"
                class="nadi-bottom-link is-active"
            >
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                    <rect x="3" y="3" width="7" height="7" rx="2" stroke="currentColor" stroke-width="2"/>
                    <rect x="14" y="3" width="7" height="7" rx="2" stroke="currentColor" stroke-width="2"/>
                    <rect x="3" y="14" width="7" height="7" rx="2" stroke="currentColor" stroke-width="2"/>
                    <rect x="14" y="14" width="7" height="7" rx="2" stroke="currentColor" stroke-width="2"/>
                </svg>

                <span>Layanan</span>
            </a>

            <a
                href="{{ $routeOrUrl('riwayat.index', '/riwayat') }}"
                class="nadi-bottom-link"
            >
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                    <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/>
                    <path
                        d="M12 7V12L15.5 14"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                    />
                </svg>

                <span>Riwayat</span>
            </a>

            <a
                href="{{ $routeOrUrl('bantuan.index', '/bantuan') }}"
                class="nadi-bottom-link"
            >
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                    <path
                        d="M4 13V11C4 6.6 7.6 3 12 3C16.4 3 20 6.6 20 11V13"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                    />
                    <rect x="2.5" y="11.5" width="4" height="7" rx="2" fill="currentColor"/>
                    <rect x="17.5" y="11.5" width="4" height="7" rx="2" fill="currentColor"/>
                </svg>

                <span>Bantuan</span>
            </a>

            <a
                href="{{ $routeOrUrl('profile.index', '/profil') }}"
                class="nadi-bottom-link"
            >
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                    <circle
                        cx="12"
                        cy="8"
                        r="4"
                        stroke="currentColor"
                        stroke-width="2"
                    />
                    <path
                        d="M5 21C5.8 16.8 8.1 14.5 12 14.5C15.9 14.5 18.2 16.8 19 21"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                    />
                </svg>

                <span>Profil</span>
            </a>

        </nav>

    </div>
</section>

@endsection