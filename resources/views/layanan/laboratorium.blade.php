@extends('layouts.app', ['title' => 'Laboratorium | NADI RSBM'])

@section('content')

@php
    $patientName = data_get($patient, 'name', data_get($patient, 'namapasien', 'Pasien'));
    $medicalRecord = data_get(
        $patient,
        'medical_record',
        data_get($patient, 'rm', data_get($patient, 'nocm', '-'))
    );
    $birthDate = data_get($patient, 'birth_date', data_get($patient, 'tgllahir'));
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
@endphp

<style>

    /* =========================================================
       RESET layouts.app
       Samakan dengan halaman Detail Laboratorium:
       - hilangkan navbar/bar bawaan layout
       - hilangkan padding py-4 / ruang orange di atas
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

    /* Intro */
    .lab-intro {
        position: relative;
        margin-bottom: 13px;
        padding: 15px 15px 14px;
        overflow: hidden;
        border: 1px solid #d8efdf;
        border-radius: 20px;
        background: linear-gradient(135deg, rgba(237, 252, 244, .98), rgba(248, 253, 255, .98));
        box-shadow: 0 10px 26px rgba(24, 170, 97, .06);
    }

    .lab-intro::after {
        position: absolute;
        top: -31px;
        right: -28px;
        width: 92px;
        height: 92px;
        border: 18px solid rgba(24, 170, 97, .055);
        border-radius: 50%;
        content: '';
    }

    .lab-intro-head {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .lab-intro-icon {
        display: grid;
        width: 45px;
        height: 45px;
        flex: 0 0 45px;
        place-items: center;
        border-radius: 13px;
        background: var(--nadi-green-soft);
        color: var(--nadi-green);
    }

    .lab-intro-title {
        margin: 0;
        color: var(--nadi-blue);
        font-size: 18px;
        font-weight: 950;
        line-height: 1.2;
        letter-spacing: -.025em;
    }

    .lab-intro-text {
        margin: 4px 0 0;
        color: #66758e;
        font-size: 10.5px;
        font-weight: 650;
        line-height: 1.45;
    }

    /* Pasien */
    .lab-patient {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        margin-bottom: 13px;
        overflow: hidden;
        border: 1px solid var(--nadi-line);
        border-radius: 17px;
        background: #fff;
        box-shadow: 0 8px 22px rgba(15, 45, 115, .045);
    }

    .lab-patient-item {
        min-width: 0;
        padding: 12px 13px;
        border-top: 1px solid #edf2f7;
    }

    .lab-patient-item:nth-child(2n) {
        border-left: 1px solid #edf2f7;
    }

    .lab-patient-item.is-wide {
        grid-column: 1 / -1;
        border-top: 0;
        border-left: 0;
    }

    .lab-label {
        margin-bottom: 3px;
        color: #8a99ad;
        font-size: 8.5px;
        font-weight: 900;
        letter-spacing: .04em;
        text-transform: uppercase;
    }

    .lab-value {
        overflow-wrap: anywhere;
        color: var(--nadi-dark);
        font-size: 11.5px;
        font-weight: 900;
        line-height: 1.4;
    }

    /* Alert */
    .lab-alert,
    .lab-empty {
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

    .lab-alert {
        border-color: #fecaca;
        background: #fff6f6;
        color: #b42318;
    }

    /* Filter */
    .lab-filter {
        margin-bottom: 14px;
        padding: 13px;
        border: 1px solid var(--nadi-line);
        border-radius: 17px;
        background: #fff;
        box-shadow: 0 8px 22px rgba(15, 45, 115, .04);
    }

    .lab-filter-title {
        display: flex;
        align-items: center;
        gap: 7px;
        margin-bottom: 10px;
        color: var(--nadi-blue);
        font-size: 11.5px;
        font-weight: 950;
    }

    .lab-filter-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 9px;
    }

    .lab-form-group {
        display: grid;
        min-width: 0;
        gap: 5px;
    }

    .lab-form-group.is-wide {
        grid-column: 1 / -1;
    }

    .lab-form-label {
        color: #49617f;
        font-size: 9px;
        font-weight: 900;
    }

    .lab-form-control {
        display: block;
        width: 100%;
        height: 40px;
        padding: 0 11px;
        border: 1px solid #dbe5ef;
        border-radius: 10px;
        outline: none;
        background: #fbfdff;
        color: var(--nadi-dark);
        font: inherit;
        font-size: 10.5px;
        font-weight: 700;
        transition: .18s ease;
    }

    .lab-form-control:focus {
        border-color: #93baf0;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(20, 119, 238, .08);
    }

    .lab-filter-actions {
        display: grid;
        grid-column: 1 / -1;
        grid-template-columns: 1fr auto;
        gap: 8px;
        margin-top: 1px;
    }

    .lab-btn-primary,
    .lab-btn-reset,
    .lab-btn-detail,
    .infinite-retry {
        display: inline-flex;
        min-height: 39px;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 0 13px;
        border-radius: 10px;
        font: inherit;
        font-size: 10px;
        font-weight: 900;
        line-height: 1;
        text-decoration: none;
        cursor: pointer;
        transition: .18s ease;
    }

    .lab-btn-primary {
        border: 0;
        background: linear-gradient(135deg, var(--nadi-blue-2), var(--nadi-blue));
        color: #fff;
        box-shadow: 0 7px 16px rgba(20, 119, 238, .18);
    }

    .lab-btn-primary:hover {
        transform: translateY(-1px);
        color: #fff;
        box-shadow: 0 9px 19px rgba(20, 119, 238, .24);
    }

    .lab-btn-reset {
        border: 1px solid #dbe5ef;
        background: #f8fafc;
        color: #66758e;
    }

    .lab-btn-reset:hover {
        border-color: #bfd2e5;
        background: #f0f6fb;
        color: var(--nadi-blue);
        text-decoration: none;
    }

    /* Result title */
    .lab-result-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin: 2px 1px 10px;
    }

    .lab-result-title {
        margin: 0;
        color: var(--nadi-blue);
        font-size: 13px;
        font-weight: 950;
        letter-spacing: -.015em;
    }

    .lab-result-count {
        padding: 5px 8px;
        border-radius: 999px;
        background: var(--nadi-green-soft);
        color: var(--nadi-green);
        font-size: 8.5px;
        font-weight: 900;
    }

    /* Card order */
    .order-card {
        position: relative;
        margin-bottom: 11px;
        overflow: hidden;
        border: 1px solid var(--nadi-line);
        border-radius: 18px;
        background: #fff;
        box-shadow: 0 9px 24px rgba(15, 45, 115, .045);
    }

    .order-card::before {
        display: block;
        height: 3px;
        background: linear-gradient(90deg, var(--nadi-green) 0 35%, var(--nadi-blue-2) 35% 100%);
        content: '';
    }

    .order-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 10px;
        padding: 13px 13px 11px;
        border-bottom: 1px solid #edf2f7;
        background: linear-gradient(90deg, rgba(234, 250, 241, .70), rgba(237, 246, 255, .38), #fff);
    }

    .order-main {
        min-width: 0;
        flex: 1 1 auto;
    }

    .order-number {
        overflow-wrap: anywhere;
        color: var(--nadi-dark);
        font-size: 13px;
        font-weight: 950;
        line-height: 1.35;
    }

    .order-date {
        margin-top: 4px;
        color: #72839a;
        font-size: 9px;
        font-weight: 750;
        line-height: 1.45;
    }

    .order-header-actions {
        display: flex;
        flex: 0 0 auto;
        flex-direction: column;
        align-items: flex-end;
        gap: 6px;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 24px;
        padding: 0 8px;
        border-radius: 999px;
        background: var(--nadi-green-soft);
        color: #118148;
        font-size: 8.5px;
        font-weight: 900;
        text-transform: capitalize;
        white-space: nowrap;
    }

    .lab-btn-detail {
        min-height: 30px;
        padding: 0 9px;
        border: 1px solid #cfe2f6;
        background: var(--nadi-blue-soft);
        color: var(--nadi-blue-2);
        font-size: 8.8px;
    }

    .lab-btn-detail:hover {
        transform: translateY(-1px);
        border-color: #acccea;
        background: #e4f1ff;
        color: var(--nadi-blue);
        text-decoration: none;
    }

    .lab-btn-detail.is-disabled {
        border-color: #e5eaf0;
        background: #f8fafc;
        color: #a5b0bd;
        cursor: not-allowed;
        pointer-events: none;
    }

    .order-body {
        padding: 12px 13px 13px;
    }

    .order-info {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px 13px;
    }

    .order-info-item {
        min-width: 0;
    }

    .order-info-item.is-wide {
        grid-column: 1 / -1;
    }

    .info-label {
        margin-bottom: 3px;
        color: #91a0b2;
        font-size: 8px;
        font-weight: 900;
        letter-spacing: .035em;
        text-transform: uppercase;
    }

    .info-value {
        overflow-wrap: anywhere;
        color: #405775;
        font-size: 10px;
        font-weight: 850;
        line-height: 1.45;
    }

    .detail-section {
        margin-top: 11px;
        padding-top: 10px;
        border-top: 1px dashed #e3ebf3;
    }

    .detail-title {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 8px;
        color: var(--nadi-blue);
        font-size: 10px;
        font-weight: 950;
    }

    .detail-list {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    .detail-item {
        display: inline-flex;
        max-width: 100%;
        align-items: center;
        padding: 6px 8px;
        border: 1px solid #d7eee0;
        border-radius: 9px;
        background: #f5fcf8;
        color: #376b50;
        font-size: 8.8px;
        font-weight: 800;
        line-height: 1.35;
    }

    /* Infinite scroll */
    .infinite-scroll-sentinel {
        display: flex;
        min-height: 70px;
        align-items: center;
        justify-content: center;
        margin-top: 4px;
    }

    .infinite-loader {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 9px 12px;
        border: 1px solid var(--nadi-line);
        border-radius: 12px;
        background: #fff;
        color: #71839a;
        font-size: 9.5px;
        font-weight: 850;
    }

    .loading-spinner {
        width: 17px;
        height: 17px;
        flex: 0 0 17px;
        border: 2.5px solid var(--nadi-blue-soft);
        border-top-color: var(--nadi-blue-2);
        border-radius: 50%;
        animation: lab-spin .7s linear infinite;
    }

    @keyframes lab-spin {
        to { transform: rotate(360deg); }
    }

    .infinite-scroll-error {
        display: grid;
        gap: 8px;
        justify-items: center;
        padding: 12px;
        border: 1px solid #fecaca;
        border-radius: 12px;
        background: #fff5f5;
        color: #b42318;
        font-size: 9.5px;
        font-weight: 800;
        text-align: center;
    }

    .infinite-retry {
        min-height: 32px;
        border: 1px solid #fecaca;
        background: #fff;
        color: #b42318;
    }

    .infinite-retry:hover {
        background: #feecec;
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

    @media (min-width: 700px) {
        .nadi-page {
            padding-top: 34px;
            padding-bottom: 44px;
        }

        .nadi-phone {
            border-radius: 32px;
        }

        .nadi-main {
            padding: 26px 22px 108px;
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

        .order-header {
            flex-direction: column;
        }

        .order-header-actions {
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

        .nadi-greeting-name {
            max-width: 150px;
        }

        .lab-filter-grid,
        .order-info,
        .lab-patient {
            grid-template-columns: 1fr;
        }

        .lab-patient-item:nth-child(2n) {
            border-left: 0;
        }

        .lab-patient-item.is-wide,
        .lab-form-group.is-wide,
        .order-info-item.is-wide,
        .lab-filter-actions {
            grid-column: auto;
        }
    }

    /* =========================================================
       DESKTOP / PC
       Sama seperti Detail Laboratorium:
       full width, tanpa wrapper/phone 520px.
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

        .lab-intro,
        .lab-patient,
        .lab-filter,
        .order-card,
        .lab-alert,
        .lab-empty {
            width: 100%;
        }

        .lab-intro {
            padding: 18px 20px;
        }

        .lab-intro-title {
            font-size: 20px;
        }

        .lab-intro-text {
            font-size: 11.5px;
        }

        .lab-patient {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .lab-patient-item {
            padding: 14px 16px;
        }

        .lab-filter {
            padding: 16px;
        }

        .lab-filter-grid {
            grid-template-columns:
                minmax(280px, 1.6fr)
                minmax(170px, .6fr)
                minmax(150px, .5fr)
                auto;
            align-items: end;
        }

        .lab-form-group.is-wide {
            grid-column: auto;
        }

        .lab-filter-actions {
            display: flex;
            grid-column: auto;
            align-items: center;
            justify-content: flex-end;
            margin-top: 0;
        }

        .lab-btn-primary,
        .lab-btn-reset {
            min-height: 40px;
        }

        .order-header {
            padding: 15px 17px 13px;
        }

        .order-header-actions {
            flex-direction: row;
            align-items: center;
        }

        .order-body {
            padding: 14px 17px 16px;
        }

        .order-info {
            grid-template-columns: 1.2fr 1fr 1fr .55fr;
            gap: 14px 20px;
        }

        .order-info-item.is-wide {
            grid-column: auto;
        }

        .detail-item {
            font-size: 9.5px;
        }

        .nadi-bottom-nav {
            width: 100vw;
        }
    }



    /* =========================================================
       KONSISTENSI DENGAN MENU UTAMA NADI
       Desktop full width, typography lebih terbaca,
       card/spacing/footer mengikuti dashboard menu utama.
       ========================================================= */

    /* Typography pasien dibuat lebih nyaman dibaca */
    .lab-label,
    .info-label {
        font-size: 10.5px;
    }

    .lab-value {
        font-size: 13px;
    }

    .lab-form-label {
        font-size: 11px;
    }

    .lab-form-control {
        font-size: 12px;
    }

    .lab-btn-primary,
    .lab-btn-reset,
    .lab-btn-detail,
    .infinite-retry {
        font-size: 11px;
    }

    .order-number {
        font-size: 15px;
    }

    .order-date,
    .info-value,
    .detail-title,
    .detail-item,
    .infinite-loader,
    .infinite-scroll-error {
        font-size: 11px;
    }

    .status-badge,
    .lab-result-count {
        font-size: 10.5px;
    }

    /* Footer harus selalu tersedia seperti menu utama */
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
        visibility: visible !important;
        opacity: 1 !important;
    }

    /* =========================================================
       DESKTOP / PC - mengikuti dashboard menu NADI
       ========================================================= */
    @media (min-width: 1024px) {
        .nadi-page {
            position: relative;
            left: 50%;
            width: 100vw;
            min-height: 100vh;
            margin-left: -50vw;
            padding: 28px clamp(28px, 3vw, 56px) 110px;
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

        .nadi-greeting-small {
            font-size: 13px;
        }

        .nadi-greeting-name {
            max-width: min(520px, 45vw);
            font-size: 30px;
        }

        .lab-intro {
            margin-bottom: 16px;
            padding: 20px;
            border-radius: 22px;
        }

        .lab-intro-icon {
            width: 52px;
            height: 52px;
            flex-basis: 52px;
            border-radius: 15px;
        }

        .lab-intro-title {
            font-size: 20px;
        }

        .lab-intro-text {
            max-width: 820px;
            font-size: 12px;
        }

        /* Identitas pasien dibuat satu baris seperti information strip */
        .lab-patient {
            grid-template-columns: minmax(0, 1.5fr) minmax(180px, .65fr) minmax(180px, .65fr);
            margin-bottom: 16px;
            border-radius: 18px;
        }

        .lab-patient-item,
        .lab-patient-item.is-wide {
            grid-column: auto;
            padding: 15px 18px;
            border-top: 0;
        }

        .lab-patient-item + .lab-patient-item,
        .lab-patient-item:nth-child(2n) {
            border-left: 1px solid #edf2f7;
        }

        .lab-label {
            margin-bottom: 5px;
            font-size: 11px;
        }

        .lab-value {
            font-size: 14px;
        }

        .lab-filter {
            margin-bottom: 18px;
            padding: 18px;
            border-radius: 20px;
        }

        .lab-filter-title {
            margin-bottom: 13px;
            font-size: 14px;
        }

        .lab-filter-grid {
            grid-template-columns:
                minmax(320px, 1.7fr)
                minmax(180px, .55fr)
                minmax(160px, .5fr)
                auto;
            gap: 12px;
            align-items: end;
        }

        .lab-form-group.is-wide,
        .lab-filter-actions {
            grid-column: auto;
        }

        .lab-form-label {
            font-size: 11px;
        }

        .lab-form-control {
            height: 44px;
            font-size: 12.5px;
        }

        .lab-filter-actions {
            display: flex;
            justify-content: flex-end;
            gap: 9px;
            margin-top: 0;
        }

        .lab-btn-primary,
        .lab-btn-reset {
            min-height: 44px;
            padding: 0 16px;
            font-size: 11.5px;
        }

        .lab-result-head {
            margin: 3px 2px 12px;
        }

        .lab-result-title {
            font-size: 18px;
        }

        .lab-result-count {
            padding: 6px 10px;
            font-size: 11px;
        }

        .order-card {
            margin-bottom: 14px;
            border-radius: 20px;
            box-shadow: 0 10px 28px rgba(20, 70, 130, .055);
        }

        .order-header {
            align-items: center;
            padding: 16px 18px 14px;
        }

        .order-number {
            font-size: 16px;
        }

        .order-date {
            margin-top: 5px;
            font-size: 11.5px;
        }

        .order-header-actions {
            flex-direction: row;
            align-items: center;
            gap: 8px;
        }

        .status-badge {
            min-height: 30px;
            padding: 0 10px;
            font-size: 10.5px;
        }

        .lab-btn-detail {
            min-height: 34px;
            padding: 0 12px;
            font-size: 10.5px;
        }

        .order-body {
            padding: 16px 18px 18px;
        }

        .order-info {
            grid-template-columns: 1.25fr 1fr 1fr .55fr;
            gap: 14px 22px;
        }

        .order-info-item.is-wide {
            grid-column: auto;
        }

        .info-label {
            margin-bottom: 5px;
            font-size: 10.5px;
        }

        .info-value {
            font-size: 12.5px;
        }

        .detail-section {
            margin-top: 14px;
            padding-top: 13px;
        }

        .detail-title {
            margin-bottom: 10px;
            font-size: 12px;
        }

        .detail-list {
            gap: 7px;
        }

        .detail-item {
            padding: 7px 10px;
            font-size: 11px;
        }

        /* Footer desktop sama seperti menu NADI */
        .nadi-bottom-nav {
            border-top: 1px solid #e1e9f2 !important;
            background: rgba(255, 255, 255, .97) !important;
            box-shadow: 0 -7px 22px rgba(15, 45, 115, .08) !important;
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
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
       TABLET
       ========================================================= */
    @media (min-width: 768px) and (max-width: 1023px) {
        .nadi-page {
            padding: 24px 22px 110px;
        }

        .nadi-phone {
            width: 100%;
            max-width: none;
            border: 0;
            border-radius: 0;
            background: transparent;
            box-shadow: none;
        }

        .nadi-main {
            padding: 0 0 90px;
        }

        .lab-patient {
            grid-template-columns: 1.3fr .7fr .7fr;
        }

        .lab-patient-item.is-wide {
            grid-column: auto;
            border-top: 0;
        }

        .lab-filter-grid {
            grid-template-columns: minmax(0, 1.5fr) minmax(150px, .6fr) minmax(130px, .5fr);
        }

        .lab-filter-actions {
            grid-column: 1 / -1;
            display: flex;
            justify-content: flex-end;
        }

        .order-info {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .order-info-item.is-wide {
            grid-column: auto;
        }
    }

    /* =========================================================
       MOBILE - tetap seperti aplikasi NADI
       ========================================================= */
    @media (max-width: 767px), (hover: none) and (pointer: coarse) {
        .nadi-bottom-nav {
            padding-bottom: max(6px, env(safe-area-inset-bottom)) !important;
            background: rgba(255,255,255,.98) !important;
            box-shadow: 0 -8px 24px rgba(15,45,115,.12) !important;
            -webkit-transform: translateZ(0);
            transform: translateZ(0);
        }

        .nadi-main {
            padding-bottom: calc(100px + env(safe-area-inset-bottom)) !important;
        }

        .nadi-bottom-link {
            min-width: 0;
            min-height: 68px;
            font-size: 9px;
        }
    }

    @media (max-width: 420px) {
        .lab-intro-text {
            font-size: 11.5px;
        }

        .lab-label,
        .info-label,
        .lab-form-label {
            font-size: 10px;
        }

        .lab-value,
        .info-value,
        .lab-form-control {
            font-size: 12px;
        }

        .order-number {
            font-size: 14px;
        }

        .order-date {
            font-size: 10.5px;
        }

        .status-badge,
        .lab-result-count,
        .lab-btn-detail,
        .detail-item {
            font-size: 10px;
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
                        href="{{ $mainMenuUrl }}"
                        class="nadi-icon-button"
                        aria-label="Kembali ke menu"
                        title="Kembali ke menu"
                    >
                        <svg width="21" height="21" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
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
                                <svg width="21" height="21" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M10 5H6C4.9 5 4 5.9 4 7V17C4 18.1 4.9 19 6 19H10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    <path d="M14 8L18 12L14 16M18 12H9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </form>
                    @endif
                </div>
            </header>

            {{-- Intro Laboratorium --}}
            <section class="lab-intro">
                <div class="lab-intro-head">
                    <div class="lab-intro-icon" aria-hidden="true">
                        <svg width="27" height="27" viewBox="0 0 24 24" fill="none">
                            <path d="M9 3V8L5.5 15.2C4.2 17.9 6.2 21 9.2 21H14.8C17.8 21 19.8 17.9 18.5 15.2L15 8V3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M8 3H16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <path d="M7.5 14H16.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <circle cx="10" cy="17" r="1" fill="currentColor"/>
                            <circle cx="14" cy="16" r="1" fill="currentColor"/>
                        </svg>
                    </div>

                    <div>
                        <h1 class="lab-intro-title">Hasil Laboratorium</h1>
                        <p class="lab-intro-text">
                            Lihat riwayat order, status pemeriksaan, dokter, dan rincian pemeriksaan laboratorium.
                        </p>
                    </div>
                </div>
            </section>

            {{-- Identitas pasien - sama seperti Detail Laboratorium --}}
            <section class="lab-patient" aria-label="Identitas pasien">
                <div class="lab-patient-item is-wide">
                    <div class="lab-label">Nama Pasien</div>
                    <div class="lab-value">{{ $patientName ?: 'Pasien' }}</div>
                </div>

                <div class="lab-patient-item">
                    <div class="lab-label">No. Rekam Medis</div>
                    <div class="lab-value">{{ $medicalRecord ?: '-' }}</div>
                </div>

                <div class="lab-patient-item">
                    <div class="lab-label">Tanggal Lahir</div>
                    <div class="lab-value">{{ $birthDateLabel }}</div>
                </div>
            </section>

            @if(data_get($result, 'is_error'))
                <div class="lab-alert">
                    {{ data_get(
                        $result,
                        'message',
                        'API laboratorium belum berhasil diakses.'
                    ) }}
                </div>
            @else
                {{-- Filter --}}
                <section class="lab-filter">
                    <div class="lab-filter-title">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M4 6H20M7 12H17M10 18H14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        <span>Filter Pemeriksaan</span>
                    </div>

                    <form method="GET" action="{{ route('laboratory.index') }}">
                        <div class="lab-filter-grid">
                            <div class="lab-form-group is-wide">
                                <label class="lab-form-label">Cari Pemeriksaan</label>
                                <input
                                    type="text"
                                    name="keyword"
                                    class="lab-form-control"
                                    value="{{ request('keyword') }}"
                                    placeholder="No order, dokter, ruangan, pemeriksaan..."
                                >
                            </div>

                            <div class="lab-form-group">
                                <label class="lab-form-label">Status</label>
                                <select name="status" class="lab-form-control">
                                    <option
                                        value="ALL"
                                        {{ request('status', 'ALL') === 'ALL' ? 'selected' : '' }}
                                    >
                                        Semua Status
                                    </option>
                                    <option
                                        value="verifikasi"
                                        {{ request('status') === 'verifikasi' ? 'selected' : '' }}
                                    >
                                        Verifikasi
                                    </option>
                                </select>
                            </div>

                            <div class="lab-form-group">
                                <label class="lab-form-label">Tahun</label>
                                <select name="tahun" class="lab-form-control">
                                    <option
                                        value="ALL"
                                        {{ request('tahun', 'ALL') === 'ALL' ? 'selected' : '' }}
                                    >
                                        Semua Tahun
                                    </option>

                                    @foreach($yearOptions as $year)
                                        <option
                                            value="{{ $year }}"
                                            {{ request('tahun') == $year ? 'selected' : '' }}
                                        >
                                            {{ $year }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="lab-filter-actions">
                                <button type="submit" class="lab-btn-primary">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M4 5H20L14 12V18L10 20V12L4 5Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                    </svg>
                                    <span>Terapkan Filter</span>
                                </button>

                                <a href="{{ route('laboratory.index') }}" class="lab-btn-reset">
                                    Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </section>

                @php
                    /* Pertahankan filter saat mengambil page berikutnya. */
                    $orders->appends(request()->except('page'));
                @endphp

                <div class="lab-result-head">
                    <h2 class="lab-result-title">Riwayat Pemeriksaan</h2>
                    <span class="lab-result-count">
                        {{ $orders->count() }} Data
                    </span>
                </div>

                <div id="order-list">
                    @forelse($orders as $order)
                        @php
                            $orderDate = data_get($order, 'order_date');

                            $detailNoOrder = trim(
                                (string) data_get($order, 'order_number', '')
                            );

                            $detailLab = trim(
                                (string) data_get($order, 'destination_room', '')
                            );

                            $canViewDetail =
                                $detailNoOrder !== ''
                                && $detailLab !== '';

                            $detailItems = data_get($order, 'details', []);
                        @endphp

                        <article class="order-card">
                            <div class="order-header">
                                <div class="order-main">
                                    <div class="order-number">
                                        {{ data_get($order, 'order_number', '-') }}
                                    </div>

                                    <div class="order-date">
                                        @if($orderDate)
                                            {{ \Carbon\Carbon::parse($orderDate)->format('d-m-Y H:i') }}
                                        @else
                                            -
                                        @endif

                                        &nbsp;·&nbsp;
                                        Registrasi {{ data_get($order, 'registration_number', '-') }}
                                    </div>
                                </div>

                                <div class="order-header-actions">
                                    <span class="status-badge">
                                        {{ data_get($order, 'status', '-') }}
                                    </span>

                                    @if($canViewDetail)
                                        <a
                                            href="{{ route('laboratory.detail', [
                                                'noOrder' => $detailNoOrder,
                                                'lab' => $detailLab,
                                            ]) }}"
                                            class="lab-btn-detail"
                                        >
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                <path d="M2.5 12S6 6.5 12 6.5S21.5 12 21.5 12S18 17.5 12 17.5S2.5 12 2.5 12Z" stroke="currentColor" stroke-width="2"/>
                                                <circle cx="12" cy="12" r="2.5" stroke="currentColor" stroke-width="2"/>
                                            </svg>
                                            <span>Lihat Detail</span>
                                        </a>
                                    @else
                                        <span
                                            class="lab-btn-detail is-disabled"
                                            title="Nomor order hasil laboratorium belum tersedia"
                                        >
                                            Belum Tersedia
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="order-body">
                                <div class="order-info">
                                    <div class="order-info-item is-wide">
                                        <div class="info-label">Dokter</div>
                                        <div class="info-value">
                                            {{ data_get($order, 'doctor', '-') ?: '-' }}
                                        </div>
                                    </div>

                                    <div class="order-info-item">
                                        <div class="info-label">Ruangan Asal</div>
                                        <div class="info-value">
                                            {{ data_get($order, 'origin_room', '-') ?: '-' }}
                                        </div>
                                    </div>

                                    <div class="order-info-item">
                                        <div class="info-label">Laboratorium Tujuan</div>
                                        <div class="info-value">
                                            {{ data_get($order, 'destination_room', '-') ?: '-' }}
                                        </div>
                                    </div>

                                    <div class="order-info-item is-wide">
                                        <div class="info-label">Jumlah Pemeriksaan</div>
                                        <div class="info-value">
                                            {{ count($detailItems) }} pemeriksaan
                                        </div>
                                    </div>
                                </div>

                                <div class="detail-section">
                                    <div class="detail-title">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M9 3V8L5.5 15.2C4.2 17.9 6.2 21 9.2 21H14.8C17.8 21 19.8 17.9 18.5 15.2L15 8V3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M8 3H16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        </svg>
                                        <span>Daftar Pemeriksaan</span>
                                    </div>

                                    <div class="detail-list">
                                        @forelse($detailItems as $detail)
                                            <span class="detail-item">
                                                {{ data_get($detail, 'name', '-') }}
                                            </span>
                                        @empty
                                            <span class="detail-item">
                                                Detail pemeriksaan tidak tersedia
                                            </span>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="lab-empty">
                            Belum ada riwayat pemeriksaan laboratorium yang sesuai dengan filter.
                        </div>
                    @endforelse
                </div>

                @if($orders->hasMorePages())
                    <div
                        id="infinite-scroll-sentinel"
                        class="infinite-scroll-sentinel"
                        data-next-url="{{ $orders->nextPageUrl() }}"
                    >
                        <div class="infinite-loader">
                            <span class="loading-spinner" aria-hidden="true"></span>
                            <span>Memuat riwayat berikutnya...</span>
                        </div>
                    </div>
                @endif
            @endif
        </main>

        {{-- Bottom navigation mengikuti menu utama --}}
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    const orderList = document.getElementById('order-list');
    const sentinel = document.getElementById('infinite-scroll-sentinel');

    if (!orderList || !sentinel) {
        return;
    }

    let isLoading = false;
    let isFinished = false;
    let observer = null;

    async function loadNextPage() {
        if (isLoading || isFinished) {
            return;
        }

        const nextUrl = sentinel.dataset.nextUrl;

        if (!nextUrl) {
            finishInfiniteScroll();
            return;
        }

        isLoading = true;

        try {
            const response = await fetch(nextUrl, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html'
                },
                credentials: 'same-origin'
            });

            if (!response.ok) {
                throw new Error('HTTP ' + response.status);
            }

            const html = await response.text();
            const parser = new DOMParser();
            const nextDocument = parser.parseFromString(html, 'text/html');
            const nextOrderList = nextDocument.getElementById('order-list');

            if (!nextOrderList) {
                throw new Error('Daftar order halaman berikutnya tidak ditemukan.');
            }

            const newOrders = nextOrderList.querySelectorAll('.order-card');

            if (!newOrders.length) {
                finishInfiniteScroll();
                return;
            }

            const fragment = document.createDocumentFragment();

            newOrders.forEach(function (order) {
                fragment.appendChild(order.cloneNode(true));
            });

            orderList.appendChild(fragment);

            const nextSentinel = nextDocument.getElementById('infinite-scroll-sentinel');

            if (nextSentinel && nextSentinel.dataset.nextUrl) {
                sentinel.dataset.nextUrl = nextSentinel.dataset.nextUrl;
            } else {
                finishInfiniteScroll();
            }
        } catch (error) {
            console.error('Infinite scroll laboratorium:', error);
            showError();
        } finally {
            isLoading = false;
        }
    }

    function finishInfiniteScroll() {
        isFinished = true;

        if (observer) {
            observer.disconnect();
        }

        sentinel.remove();
    }

    function showError() {
        if (observer) {
            observer.unobserve(sentinel);
        }

        sentinel.innerHTML = `
            <div class="infinite-scroll-error">
                <span>Data berikutnya gagal dimuat.</span>
                <button
                    type="button"
                    class="infinite-retry"
                    id="infinite-retry"
                >
                    Coba lagi
                </button>
            </div>
        `;

        const retryButton = document.getElementById('infinite-retry');

        if (!retryButton) {
            return;
        }

        retryButton.addEventListener(
            'click',
            function () {
                sentinel.innerHTML = `
                    <div class="infinite-loader">
                        <span class="loading-spinner" aria-hidden="true"></span>
                        <span>Memuat riwayat berikutnya...</span>
                    </div>
                `;

                if (observer) {
                    observer.observe(sentinel);
                }

                loadNextPage();
            },
            { once: true }
        );
    }

    observer = new IntersectionObserver(
        function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting && !isLoading && !isFinished) {
                    loadNextPage();
                }
            });
        },
        {
            root: null,
            rootMargin: '350px 0px',
            threshold: 0
        }
    );

    observer.observe(sentinel);
});
</script>

@endsection