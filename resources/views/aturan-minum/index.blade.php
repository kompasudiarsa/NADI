@extends('layouts.app', ['title' => 'Aturan Minum Obat | NADI RSBM'])

@section('content')

@php
    $patientName = data_get(
        $patient,
        'name',
        data_get($patient, 'namapasien', 'Pasien')
    );

    $medicalRecord = data_get(
        $patient,
        'medical_record',
        data_get($patient, 'rm', data_get($patient, 'nocm', '-'))
    );

    $birthDate = data_get(
        $patient,
        'birth_date',
        data_get(
            $patient,
            'tanggal_lahir',
            data_get($patient, 'tgllahir')
        )
    );

    $birthDateLabel = '-';

    if ($birthDate) {
        try {
            $birthDateLabel = \Carbon\Carbon::parse($birthDate)->format('d-m-Y');
        } catch (\Throwable $e) {
            $birthDateLabel = (string) $birthDate;
        }
    }

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

    // Data dari controller index terbaru.
    $registrasiTerakhir = $registrasiTerakhir ?? null;
    $riwayatRegistrasi = collect($riwayatRegistrasi ?? []);
    $pelayananPasienTerakhir = collect($pelayananPasienTerakhir ?? []);
    $errorResepTerakhir = $errorResepTerakhir ?? null;

    $isTruthy = function ($value) {
        return $value === true
            || $value === 1
            || $value === '1'
            || $value === 'true'
            || $value === 't';
    };

@endphp

<style>
    /* =========================================================
       RESET layouts.app
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

    /* =========================================================
       HEADER
       ========================================================= */
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

    /* =========================================================
       INTRO
       ========================================================= */
    .medicine-intro {
        position: relative;
        margin-bottom: 13px;
        padding: 15px 15px 14px;
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

    /* =========================================================
       IDENTITAS PASIEN
       ========================================================= */
    .medicine-patient {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        margin-bottom: 15px;
        overflow: hidden;
        border: 1px solid var(--nadi-line);
        border-radius: 17px;
        background: #fff;
        box-shadow: 0 8px 22px rgba(15, 45, 115, .045);
    }

    .medicine-patient-item {
        min-width: 0;
        padding: 12px 13px;
        border-top: 1px solid #edf2f7;
    }

    .medicine-patient-item:nth-child(2n) {
        border-left: 1px solid #edf2f7;
    }

    .medicine-patient-item.is-wide {
        grid-column: 1 / -1;
        border-top: 0;
        border-left: 0;
    }

    .medicine-label {
        margin-bottom: 3px;
        color: #8a99ad;
        font-size: 10.5px;
        font-weight: 900;
        letter-spacing: .04em;
        text-transform: uppercase;
    }

    .medicine-value {
        overflow-wrap: anywhere;
        color: var(--nadi-dark);
        font-size: 13px;
        font-weight: 900;
        line-height: 1.4;
    }

    /* =========================================================
       RESULT HEADER
       ========================================================= */
    .medicine-result-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin: 2px 1px 10px;
    }

    .medicine-result-title {
        margin: 0;
        color: var(--nadi-blue);
        font-size: 14px;
        font-weight: 950;
        letter-spacing: -.015em;
    }

    .medicine-result-count {
        padding: 5px 8px;
        border-radius: 999px;
        background: var(--nadi-green-soft);
        color: var(--nadi-green);
        font-size: 10.5px;
        font-weight: 900;
    }

    /* =========================================================
       REGISTRATION CARD
       ========================================================= */
    .registration-card {
        position: relative;
        margin-bottom: 11px;
        overflow: hidden;
        border: 1px solid var(--nadi-line);
        border-radius: 18px;
        background: #fff;
        box-shadow: 0 9px 24px rgba(15, 45, 115, .045);
    }

    .registration-card::before {
        display: block;
        height: 3px;
        background: linear-gradient(
            90deg,
            var(--nadi-green) 0 35%,
            var(--nadi-blue-2) 35% 100%
        );
        content: '';
    }

    .registration-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 10px;
        padding: 13px 13px 11px;
        border-bottom: 1px solid #edf2f7;
        background: linear-gradient(
            90deg,
            rgba(234, 250, 241, .70),
            rgba(237, 246, 255, .38),
            #fff
        );
    }

    .registration-main {
        min-width: 0;
        flex: 1 1 auto;
    }

    .registration-room {
        overflow-wrap: anywhere;
        color: var(--nadi-dark);
        font-size: 15px;
        font-weight: 950;
        line-height: 1.35;
    }

    .registration-date {
        margin-top: 4px;
        color: #72839a;
        font-size: 11px;
        font-weight: 750;
        line-height: 1.45;
    }

    .registration-header-actions {
        display: flex;
        flex: 0 0 auto;
        flex-direction: column;
        align-items: flex-end;
        gap: 6px;
    }

    .status-badge {
        display: inline-flex;
        min-height: 26px;
        align-items: center;
        justify-content: center;
        padding: 0 9px;
        border-radius: 999px;
        background: var(--nadi-green-soft);
        color: #118148;
        font-size: 10px;
        font-weight: 900;
        white-space: nowrap;
    }

    .medicine-btn-detail {
        display: inline-flex;
        min-height: 32px;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 0 10px;
        border: 1px solid #cfe2f6;
        border-radius: 10px;
        background: var(--nadi-blue-soft);
        color: var(--nadi-blue-2);
        font-size: 10px;
        font-weight: 900;
        line-height: 1;
        text-decoration: none;
        cursor: pointer;
        transition: .18s ease;
    }

    .medicine-btn-detail:hover {
        transform: translateY(-1px);
        border-color: #acccea;
        background: #e4f1ff;
        color: var(--nadi-blue);
        text-decoration: none;
    }

    .medicine-btn-detail.is-disabled {
        border-color: #e5eaf0;
        background: #f8fafc;
        color: #a5b0bd;
        cursor: not-allowed;
        pointer-events: none;
    }

    .registration-body {
        padding: 12px 13px 13px;
    }

    .registration-info {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px 13px;
    }

    .registration-info-item {
        min-width: 0;
    }

    .registration-info-item.is-wide {
        grid-column: 1 / -1;
    }

    .info-label {
        margin-bottom: 3px;
        color: #91a0b2;
        font-size: 10.5px;
        font-weight: 900;
        letter-spacing: .035em;
        text-transform: uppercase;
    }

    .info-value {
        overflow-wrap: anywhere;
        color: #405775;
        font-size: 12px;
        font-weight: 850;
        line-height: 1.45;
    }

    .medicine-hint {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        margin-top: 11px;
        padding-top: 10px;
        border-top: 1px dashed #e3ebf3;
        color: #64748b;
        font-size: 10.5px;
        font-weight: 700;
        line-height: 1.45;
    }

    .medicine-hint-icon {
        display: grid;
        width: 28px;
        height: 28px;
        flex: 0 0 28px;
        place-items: center;
        border-radius: 8px;
        background: var(--nadi-green-soft);
        color: var(--nadi-green);
    }

    /* =========================================================
       ALERT / EMPTY
       ========================================================= */
    .medicine-alert,
    .medicine-empty {
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

    .medicine-alert {
        border-color: #fecaca;
        background: #fff6f6;
        color: #b42318;
    }

    /* =========================================================
       BOTTOM NAV
       ========================================================= */
    .nadi-bottom-nav {
        position: fixed !important;
        z-index: 99999 !important;
        right: 0 !important;
        bottom: 0 !important;
        left: 0 !important;
        display: grid !important;
        width: 100% !important;
        max-width: none !important;
        grid-template-columns: repeat(5, minmax(0, 1fr)) !important;
        transform: none !important;
        border-top: 1px solid #e6edf5;
        background: rgba(255, 255, 255, .98) !important;
        box-shadow: 0 -8px 24px rgba(15, 45, 115, .10);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        padding-bottom: env(safe-area-inset-bottom);
        visibility: visible !important;
        opacity: 1 !important;
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
       MOBILE
       ========================================================= */
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

        .registration-header {
            flex-direction: column;
        }

        .registration-header-actions {
            width: 100%;
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
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

        .medicine-patient,
        .registration-info {
            grid-template-columns: 1fr;
        }

        .medicine-patient-item:nth-child(2n) {
            border-left: 0;
        }

        .medicine-patient-item.is-wide,
        .registration-info-item.is-wide {
            grid-column: auto;
        }
    }

    /* =========================================================
       TABLET / DESKTOP
       ========================================================= */
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
            margin-bottom: 16px;
            padding: 20px;
            border-radius: 22px;
        }

        .medicine-intro-icon {
            width: 52px;
            height: 52px;
            flex-basis: 52px;
            border-radius: 15px;
        }

        .medicine-intro-title {
            font-size: 20px;
        }

        .medicine-intro-text {
            max-width: 820px;
            font-size: 12px;
        }

        .medicine-patient {
            grid-template-columns:
                minmax(0, 1.5fr)
                minmax(180px, .65fr)
                minmax(180px, .65fr);
            margin-bottom: 16px;
            border-radius: 18px;
        }

        .medicine-patient-item,
        .medicine-patient-item.is-wide {
            grid-column: auto;
            padding: 15px 18px;
            border-top: 0;
        }

        .medicine-patient-item + .medicine-patient-item,
        .medicine-patient-item:nth-child(2n) {
            border-left: 1px solid #edf2f7;
        }

        .medicine-result-title {
            font-size: 18px;
        }

        .medicine-result-count {
            padding: 6px 10px;
            font-size: 11px;
        }

        .registration-card {
            margin-bottom: 14px;
            border-radius: 20px;
            box-shadow: 0 10px 28px rgba(20, 70, 130, .055);
        }

        .registration-header {
            align-items: center;
            padding: 16px 18px 14px;
        }

        .registration-room {
            font-size: 16px;
        }

        .registration-date {
            font-size: 11.5px;
        }

        .registration-header-actions {
            flex-direction: row;
            align-items: center;
            gap: 8px;
        }

        .status-badge {
            min-height: 30px;
            padding: 0 10px;
            font-size: 10.5px;
        }

        .medicine-btn-detail {
            min-height: 34px;
            padding: 0 12px;
            font-size: 10.5px;
        }

        .registration-body {
            padding: 16px 18px 18px;
        }

        .registration-info {
            grid-template-columns:
                minmax(0, 1.35fr)
                minmax(150px, .75fr)
                minmax(150px, .75fr)
                minmax(140px, .65fr);
            gap: 14px 22px;
        }

        .registration-info-item.is-wide {
            grid-column: auto;
        }

        .info-value {
            font-size: 12.5px;
        }

        .nadi-bottom-link {
            min-height: 66px;
            flex-direction: row;
            gap: 8px;
            font-size: 12px;
        }

        .nadi-bottom-link svg {
            width: 20px;
            height: 20px;
        }

        .nadi-bottom-link.is-active::after {
            right: 38%;
            bottom: 5px;
            left: 38%;
        }
    }

    /* =========================================================
       ATURAN OBAT TERBARU
       ========================================================= */
    .latest-medicine {
        margin-bottom: 24px;
    }

    .latest-visit {
        margin-bottom: 12px;
        padding: 14px 15px;
        border: 1px solid #d8efdf;
        border-radius: 17px;
        background: linear-gradient(135deg, #f0fbf5, #f7fbff);
    }

    .latest-visit-label {
        margin-bottom: 4px;
        color: var(--nadi-green);
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .05em;
    }

    .latest-visit-room {
        color: var(--nadi-dark);
        font-size: 15px;
        font-weight: 950;
        line-height: 1.35;
    }

    .latest-visit-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 5px 10px;
        margin-top: 4px;
        color: var(--nadi-muted);
        font-size: 11px;
        font-weight: 700;
    }

    .latest-visit-meta span {
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .rx-card {
        margin-bottom: 12px;
        overflow: hidden;
        border: 1px solid var(--nadi-line);
        border-radius: 18px;
        background: #fff;
        box-shadow: 0 9px 24px rgba(15, 45, 115, .045);
    }

    .rx-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 10px;
        padding: 12px 13px;
        border-bottom: 1px solid #edf2f7;
        background: linear-gradient(90deg, rgba(234, 250, 241, .72), #fff);
    }

    .rx-number {
        color: var(--nadi-dark);
        font-size: 12px;
        font-weight: 950;
    }

    .rx-meta {
        margin-top: 3px;
        color: var(--nadi-muted);
        font-size: 10px;
        font-weight: 700;
        line-height: 1.4;
    }

    .rx-badge {
        flex: 0 0 auto;
        padding: 5px 8px;
        border-radius: 999px;
        background: var(--nadi-green-soft);
        color: var(--nadi-green);
        font-size: 9.5px;
        font-weight: 900;
    }

    .rx-body {
        padding: 12px 13px 13px;
    }

    .drug-card {
        margin-bottom: 10px;
        padding: 13px;
        border: 1px solid #e6edf5;
        border-radius: 15px;
        background: #fff;
    }

    .drug-card:last-child {
        margin-bottom: 0;
    }

    .drug-head {
        display: flex;
        align-items: center;
        gap: 10px;
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

    .drug-name {
        overflow-wrap: anywhere;
        color: var(--nadi-dark);
        font-size: 13.5px;
        font-weight: 950;
        line-height: 1.4;
    }

    .drug-route {
        margin-top: 2px;
        color: var(--nadi-muted);
        font-size: 10.5px;
        font-weight: 700;
    }

    .rule-box {
        margin-top: 11px;
        padding: 10px 11px;
        border-radius: 11px;
        background: var(--nadi-blue-soft);
    }

    .rule-label {
        margin-bottom: 3px;
        color: var(--nadi-blue-2);
        font-size: 9.5px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .04em;
    }

    .rule-value {
        color: var(--nadi-dark);
        font-size: 13.5px;
        font-weight: 950;
        line-height: 1.45;
    }

    .schedule {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 9px;
    }

    .schedule-item {
        padding: 6px 8px;
        border-radius: 999px;
        background: #f3f7fb;
        color: #52677f;
        font-size: 9.5px;
        font-weight: 850;
    }

    .drug-note {
        margin-top: 9px;
        padding: 8px 9px;
        border-radius: 10px;
        background: #fff8e8;
        color: #8a6512;
        font-size: 10px;
        font-weight: 700;
        line-height: 1.5;
    }

    .history-divider {
        margin: 25px 0 13px;
        border-top: 1px solid #e8eef5;
    }


    /* =========================================================
       RIWAYAT COLLAPSE
       ========================================================= */
    .history-section { margin-top: 22px; }
    .history-toggle {
        display: flex; width: 100%; align-items: center; justify-content: space-between;
        gap: 12px; padding: 14px 15px; border: 1px solid var(--nadi-line);
        border-radius: 16px; background: #fff; color: var(--nadi-blue);
        cursor: pointer; box-shadow: 0 8px 22px rgba(15, 45, 115, .045);
        transition: .18s ease; text-align: left;
    }
    .history-toggle:hover { border-color: #cbdced; background: #f8fbff; }
    .history-toggle:focus-visible { outline: 3px solid rgba(20, 119, 238, .18); outline-offset: 2px; }
    .history-toggle-left { display: flex; min-width: 0; align-items: center; gap: 10px; }
    .history-toggle-icon {
        display: grid; width: 38px; height: 38px; flex: 0 0 38px; place-items: center;
        border-radius: 11px; background: var(--nadi-blue-soft); color: var(--nadi-blue-2);
    }
    .history-toggle-text { min-width: 0; }
    .history-toggle-title-row { display: flex; flex-wrap: wrap; align-items: center; gap: 6px; }
    .history-toggle-title { color: var(--nadi-dark); font-size: 13px; font-weight: 950; line-height: 1.3; }
    .history-toggle-subtitle { margin-top: 2px; color: var(--nadi-muted); font-size: 10px; font-weight: 700; line-height: 1.4; }
    .history-count-badge {
        display: inline-flex; min-height: 22px; align-items: center; justify-content: center;
        padding: 0 7px; border-radius: 999px; background: var(--nadi-green-soft);
        color: var(--nadi-green); font-size: 9px; font-weight: 900;
    }
    .history-toggle-arrow {
        display: grid; width: 30px; height: 30px; flex: 0 0 30px; place-items: center;
        border-radius: 9px; background: #f4f8fc; color: var(--nadi-blue-2); transition: transform .2s ease;
    }
    .history-section.is-open .history-toggle-arrow { transform: rotate(180deg); }
    .history-content { display: none; margin-top: 12px; }
    .history-section.is-open .history-content { display: block; }
    @media (prefers-reduced-motion: reduce) {
        .history-toggle, .history-toggle-arrow { transition: none; }
    }

    @media (min-width: 768px) {
        .latest-visit {
            padding: 16px 18px;
        }

        .rx-head,
        .rx-body {
            padding-right: 18px;
            padding-left: 18px;
        }

        .drug-card {
            padding: 14px 15px;
        }
    }

</style>

<section class="nadi-page">
    <div class="nadi-phone">
        <main class="nadi-main">

            {{-- =====================================================
                 HEADER NADI
                 ===================================================== --}}
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
                        href="{{ $mainMenuUrl }}"
                        class="nadi-icon-button"
                        aria-label="Kembali ke menu"
                        title="Kembali ke menu"
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

            {{-- =====================================================
                 INTRO ATURAN MINUM OBAT
                 ===================================================== --}}
            <section class="medicine-intro">
                <div class="medicine-intro-head">
                    <div class="medicine-intro-icon" aria-hidden="true">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none">
                            <path
                                d="M8.5 4.5L19.5 15.5C21.2 17.2 21.2 19.8 19.5 21.5C17.8 23.2 15.2 23.2 13.5 21.5L2.5 10.5C.8 8.8.8 6.2 2.5 4.5C4.2 2.8 6.8 2.8 8.5 4.5Z"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                            <path
                                d="M7 15L15 7"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                            />
                        </svg>
                    </div>

                    <div>
                        <h1 class="medicine-intro-title">
                            Aturan Minum Obat
                        </h1>

                        <p class="medicine-intro-text">
                            Pilih riwayat pelayanan untuk melihat obat dan aturan penggunaannya sesuai resep pada kunjungan tersebut.
                        </p>
                    </div>
                </div>
            </section>

            {{-- =====================================================
                 IDENTITAS PASIEN
                 ===================================================== --}}
          

            {{-- =====================================================
                 DATA
                 ===================================================== --}}
            @if(!empty($errorApi))
                <div class="medicine-alert">
                    {{ $errorApi }}
                </div>
            @elseif(empty($registrasiTerakhir))
                <div class="medicine-empty">
                    Belum ada riwayat pelayanan yang dapat ditampilkan.
                </div>
            @else
                @php
                    $tanggalTerakhirRaw = data_get(
                        $registrasiTerakhir,
                        'tglregistrasi',
                        data_get(
                            $registrasiTerakhir,
                            'tanggalregistrasi',
                            data_get(
                                $registrasiTerakhir,
                                'tanggalreservasi',
                                data_get($registrasiTerakhir, 'tglreservasi')
                            )
                        )
                    );

                    try {
                        $tanggalTerakhirLabel = $tanggalTerakhirRaw
                            ? \Carbon\Carbon::parse($tanggalTerakhirRaw)
                                ->locale('id')
                                ->translatedFormat('d F Y H:i')
                            : '-';
                    } catch (\Throwable $e) {
                        $tanggalTerakhirLabel = $tanggalTerakhirRaw ?: '-';
                    }

                    $poliTerakhir = data_get(
                        $registrasiTerakhir,
                        'namaruangan',
                        data_get(
                            $registrasiTerakhir,
                            'namapoli',
                            data_get($registrasiTerakhir, 'poli', '-')
                        )
                    );

                    $dokterTerakhir = data_get(
                        $registrasiTerakhir,
                        'dokter',
                        data_get($registrasiTerakhir, 'namadokter', '-')
                    );

                    $noRegistrasiTerakhir = trim(
                        (string) data_get($registrasiTerakhir, 'noregistrasi', '')
                    );
                @endphp

                {{-- =====================================================
                     ATURAN PAKAI REGISTRASI TERAKHIR
                     ===================================================== --}}
                <section class="latest-medicine">
                    

                    <div class="latest-visit">
                        <div class="latest-visit-label">
                            Registrasi Terakhir
                        </div>

                        <div class="latest-visit-room">
                            {{ $poliTerakhir ?: '-' }}
                        </div>

                        <div class="latest-visit-meta">
                            <span>{{ $tanggalTerakhirLabel }}</span>

                            @if($dokterTerakhir && $dokterTerakhir !== '-')
                                <span>• {{ $dokterTerakhir }}</span>
                            @endif

                            @if($noRegistrasiTerakhir !== '')
                                <span>• {{ $noRegistrasiTerakhir }}</span>
                            @endif
                        </div>
                    </div>

                    @if(!empty($errorResepTerakhir))
                        <div class="medicine-alert">
                            {{ $errorResepTerakhir }}
                        </div>
                    @elseif(empty($pelayananPasienTerakhir) || $pelayananPasienTerakhir->isEmpty())
                        <div class="medicine-empty">
                            Belum ditemukan resep atau aturan pakai obat pada registrasi terakhir.
                        </div>
                    @else
                        @foreach($pelayananPasienTerakhir as $groupKey => $items)
                            @php
                                $first = $items->first();

                                $noResep = data_get($first, 'noresep', '-');
                                $tglResep = data_get($first, 'tglresep');

                                try {
                                    $tglResepLabel = $tglResep
                                        ? \Carbon\Carbon::parse($tglResep)
                                            ->locale('id')
                                            ->translatedFormat('d F Y H:i')
                                        : '-';
                                } catch (\Throwable $e) {
                                    $tglResepLabel = $tglResep ?: '-';
                                }

                                $penulis = data_get($first, 'namalengkap', '-');
                            @endphp

                            <article class="rx-card">
                                <div class="rx-head">
                                    <div>
                                        <div class="rx-number">
                                            Resep {{ $noResep ?: '-' }}
                                        </div>

                                        <div class="rx-meta">
                                            {{ $tglResepLabel }}
                                            @if($penulis && $penulis !== '-')
                                                · {{ $penulis }}
                                            @endif
                                        </div>
                                    </div>

                                    <span class="rx-badge">
                                        {{ $items->count() }} Obat
                                    </span>
                                </div>

                                <div class="rx-body">
                                    @foreach($items as $obat)
                                        @php
                                            $namaObat = data_get(
                                                $obat,
                                                'namaproduk',
                                                data_get($obat, 'productname', '-')
                                            );

                                            $aturanPakai = trim(
                                                (string) data_get($obat, 'aturanpakai', '')
                                            );

                                            $aturanTambahan = trim(
                                                (string) data_get($obat, 'satuanresep', '')
                                            );

                                            if ($aturanPakai !== '' && $aturanTambahan !== '') {
                                                $aturanPakaiLengkap = $aturanPakai . ' • ' . $aturanTambahan;
                                            } elseif ($aturanPakai !== '') {
                                                $aturanPakaiLengkap = $aturanPakai;
                                            } elseif ($aturanTambahan !== '') {
                                                $aturanPakaiLengkap = $aturanTambahan;
                                            } else {
                                                $aturanPakaiLengkap = 'Ikuti petunjuk dokter/farmasi';
                                            }

                                            $route = trim(
                                                (string) data_get($obat, 'route', '')
                                            );

                                            $keterangan = trim(
                                                (string) data_get(
                                                    $obat,
                                                    'keteranganpakai',
                                                    data_get($obat, 'keterangan', '')
                                                )
                                            );

                                            $pagi = $isTruthy(data_get($obat, 'ispagi'));
                                            $siang = $isTruthy(data_get($obat, 'issiang'));
                                            $sore = $isTruthy(data_get($obat, 'issore'));
                                            $malam = $isTruthy(data_get($obat, 'ismalam'));
                                        @endphp

                                        <div class="drug-card">
                                            <div class="drug-head">
                                                <div class="drug-icon" aria-hidden="true">
                                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                                        <path
                                                            d="M8.5 4.5L19.5 15.5C21.2 17.2 21.2 19.8 19.5 21.5C17.8 23.2 15.2 23.2 13.5 21.5L2.5 10.5C.8 8.8.8 6.2 2.5 4.5C4.2 2.8 6.8 2.8 8.5 4.5Z"
                                                            stroke="currentColor"
                                                            stroke-width="2"
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                        />
                                                        <path
                                                            d="M7 15L15 7"
                                                            stroke="currentColor"
                                                            stroke-width="2"
                                                            stroke-linecap="round"
                                                        />
                                                    </svg>
                                                </div>

                                                <div>
                                                    <div class="drug-name">
                                                        {{ $namaObat ?: '-' }}
                                                    </div>

                                                    @if($route !== '')
                                                        <div class="drug-route">
                                                            {{ $route }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="rule-box">
                                                <div class="rule-label">
                                                    Aturan Pakai
                                                </div>

                                                <div class="rule-value">
                                                    {{ $aturanPakaiLengkap }}
                                                </div>
                                            </div>

                                            @if($pagi || $siang || $sore || $malam)
                                                <div class="schedule">
                                                    @if($pagi)
                                                        <span class="schedule-item">☀️ Pagi</span>
                                                    @endif

                                                    @if($siang)
                                                        <span class="schedule-item">🌤️ Siang</span>
                                                    @endif

                                                    @if($sore)
                                                        <span class="schedule-item">🌇 Sore</span>
                                                    @endif

                                                    @if($malam)
                                                        <span class="schedule-item">🌙 Malam</span>
                                                    @endif
                                                </div>
                                            @endif

                                            @if($keterangan !== '')
                                                <div class="drug-note">
                                                    <strong>Keterangan:</strong>
                                                    {{ $keterangan }}
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </article>
                        @endforeach
                    @endif
                </section>

                {{-- =====================================================
                     RIWAYAT REGISTRASI SEBELUMNYA - COLLAPSE
                     ===================================================== --}}
                @if($riwayatRegistrasi->isNotEmpty())
                    <section class="history-section" id="history-section">
                        <button
                            type="button"
                            class="history-toggle"
                            id="history-toggle"
                            aria-expanded="false"
                            aria-controls="history-content"
                        >
                            <div class="history-toggle-left">
                                <div class="history-toggle-icon" aria-hidden="true">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                        <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2" />
                                        <path d="M12 7V12L15.5 14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </div>

                                <div class="history-toggle-text">
                                    <div class="history-toggle-title-row">
                                        <div class="history-toggle-title">Riwayat Pelayanan</div>
                                        <span class="history-count-badge">
                                            {{ $riwayatRegistrasi->count() }} Data
                                        </span>
                                    </div>
                                    <div class="history-toggle-subtitle">
                                        Tekan untuk melihat aturan obat pada pelayanan sebelumnya.
                                    </div>
                                </div>
                            </div>

                            <div class="history-toggle-arrow" aria-hidden="true">
                                <svg width="17" height="17" viewBox="0 0 24 24" fill="none">
                                    <path d="M6 9L12 15L18 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>
                        </button>

                        <div class="history-content" id="history-content">
                            @foreach($riwayatRegistrasi as $item)
                                @php
                                    $tanggalRaw = data_get(
                                        $item,
                                        'tanggalregistrasi',
                                        data_get(
                                            $item,
                                            'tglregistrasi',
                                            data_get(
                                                $item,
                                                'tanggalreservasi',
                                                data_get($item, 'tglreservasi')
                                            )
                                        )
                                    );

                                    try {
                                        $tanggalLabel = $tanggalRaw
                                            ? \Carbon\Carbon::parse($tanggalRaw)
                                                ->locale('id')
                                                ->translatedFormat('d F Y')
                                            : '-';
                                    } catch (\Throwable $e) {
                                        $tanggalLabel = $tanggalRaw ?: '-';
                                    }

                                    $noReservasi = data_get(
                                        $item,
                                        'noreservasi',
                                        data_get($item, 'kodebooking')
                                    );

                                    $noRegistrasi = trim(
                                        (string) data_get($item, 'noregistrasi', '')
                                    );

                                    $poli = data_get(
                                        $item,
                                        'namaruangan',
                                        data_get(
                                            $item,
                                            'namapoli',
                                            data_get($item, 'poli', '-')
                                        )
                                    );

                                    $dokter = data_get(
                                        $item,
                                        'dokter',
                                        data_get($item, 'namadokter', '-')
                                    );

                                    $penjamin = data_get($item, 'kelompokpasien', '-');
                                @endphp

                                <article class="registration-card">
                                    <div class="registration-header">
                                        <div class="registration-main">
                                            <div class="registration-room">{{ $poli ?: '-' }}</div>
                                            <div class="registration-date">{{ $tanggalLabel }}</div>
                                        </div>

                                        <div class="registration-header-actions">
                                            <span class="status-badge">Riwayat</span>

                                            @if($noRegistrasi !== '')
                                                <a
                                                    href="{{ route(
                                                        'aturan-minum.show',
                                                        ['noregistrasi' => $noRegistrasi]
                                                    ) }}"
                                                    class="medicine-btn-detail"
                                                >
                                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                        <path d="M2.5 12S6 6.5 12 6.5S21.5 12 21.5 12S18 17.5 12 17.5S2.5 12 2.5 12Z" stroke="currentColor" stroke-width="2" />
                                                        <circle cx="12" cy="12" r="2.5" stroke="currentColor" stroke-width="2" />
                                                    </svg>
                                                    <span>Lihat Aturan Obat</span>
                                                </a>
                                            @else
                                                <span class="medicine-btn-detail is-disabled">Belum Tersedia</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="registration-body">
                                        <div class="registration-info">
                                            <div class="registration-info-item is-wide">
                                                <div class="info-label">Dokter</div>
                                                <div class="info-value">{{ $dokter ?: '-' }}</div>
                                            </div>

                                            <div class="registration-info-item">
                                                <div class="info-label">No. Registrasi</div>
                                                <div class="info-value">{{ $noRegistrasi !== '' ? $noRegistrasi : '-' }}</div>
                                            </div>

                                            <div class="registration-info-item">
                                                <div class="info-label">No. Reservasi</div>
                                                <div class="info-value">{{ $noReservasi ?: '-' }}</div>
                                            </div>

                                            <div class="registration-info-item">
                                                <div class="info-label">Penjamin</div>
                                                <div class="info-value">{{ $penjamin ?: '-' }}</div>
                                            </div>
                                        </div>

                                        <div class="medicine-hint">
                                            <div class="medicine-hint-icon" aria-hidden="true">
                                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none">
                                                    <path d="M8.5 4.5L19.5 15.5C21.2 17.2 21.2 19.8 19.5 21.5C17.8 23.2 15.2 23.2 13.5 21.5L2.5 10.5C.8 8.8.8 6.2 2.5 4.5C4.2 2.8 6.8 2.8 8.5 4.5Z" stroke="currentColor" stroke-width="2" />
                                                    <path d="M7 15L15 7" stroke="currentColor" stroke-width="2" />
                                                </svg>
                                            </div>
                                            <div>
                                                Pilih <strong>Lihat Aturan Obat</strong> untuk melihat resep dan aturan pakai pada pelayanan sebelumnya.
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </section>
                @endif
            @endif

        </main>

        {{-- =====================================================
             BOTTOM NAVIGATION
             ===================================================== --}}
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
                    <rect
                        x="3"
                        y="3"
                        width="7"
                        height="7"
                        rx="2"
                        stroke="currentColor"
                        stroke-width="2"
                    />
                    <rect
                        x="14"
                        y="3"
                        width="7"
                        height="7"
                        rx="2"
                        stroke="currentColor"
                        stroke-width="2"
                    />
                    <rect
                        x="3"
                        y="14"
                        width="7"
                        height="7"
                        rx="2"
                        stroke="currentColor"
                        stroke-width="2"
                    />
                    <rect
                        x="14"
                        y="14"
                        width="7"
                        height="7"
                        rx="2"
                        stroke="currentColor"
                        stroke-width="2"
                    />
                </svg>
                <span>Layanan</span>
            </a>

            <a
                href="{{ $routeOrUrl('riwayat.index', '/riwayat') }}"
                class="nadi-bottom-link"
            >
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                    <circle
                        cx="12"
                        cy="12"
                        r="9"
                        stroke="currentColor"
                        stroke-width="2"
                    />
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
                    <rect
                        x="2.5"
                        y="11.5"
                        width="4"
                        height="7"
                        rx="2"
                        fill="currentColor"
                    />
                    <rect
                        x="17.5"
                        y="11.5"
                        width="4"
                        height="7"
                        rx="2"
                        fill="currentColor"
                    />
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


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const section = document.getElementById('history-section');
        const button = document.getElementById('history-toggle');

        if (!section || !button) {
            return;
        }

        button.addEventListener('click', function () {
            const isOpen = section.classList.toggle('is-open');
            button.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });
    });
</script>

@endsection