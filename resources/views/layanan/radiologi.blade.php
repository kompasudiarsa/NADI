@extends('layouts.app', ['title' => 'Riwayat Radiologi | NADI RSBM'])

@section('content')

@php
    /*
     * Data pasien bisa berasal dari controller maupun session.
     * Radiologi dibuat fallback ke session agar konsisten dengan menu NADI.
     */
    $viewPatient = isset($patient) ? $patient : [];
    $sessionPatient = session('pasien', []);

    $patientName =
        data_get($viewPatient, 'name')
        ?: data_get($viewPatient, 'namapasien')
        ?: data_get($viewPatient, 'patient_name')
        ?: data_get($sessionPatient, 'name')
        ?: data_get($sessionPatient, 'namapasien')
        ?: data_get($sessionPatient, 'patient_name')
        ?: 'Pasien';

    $medicalRecord =
        data_get($viewPatient, 'medical_record')
        ?: data_get($viewPatient, 'rm')
        ?: data_get($viewPatient, 'nocm')
        ?: data_get($sessionPatient, 'medical_record')
        ?: data_get($sessionPatient, 'rm')
        ?: data_get($sessionPatient, 'nocm')
        ?: '-';

    $birthDate =
        data_get($viewPatient, 'birth_date')
        ?: data_get($viewPatient, 'tgllahir')
        ?: data_get($viewPatient, 'tanggal_lahir')
        ?: data_get($sessionPatient, 'birth_date')
        ?: data_get($sessionPatient, 'tgllahir')
        ?: data_get($sessionPatient, 'tanggal_lahir');

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
       RESET LAYOUT.APP
       Samakan dengan menu utama NADI dan halaman Laboratorium:
       hilangkan navbar/padding bawaan layout pada bagian atas.
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

    /* Page intro */
    .rad-intro {
        position: relative;
        margin-bottom: 13px;
        padding: 15px 15px 14px;
        overflow: hidden;
        border: 1px solid #d9ebfb;
        border-radius: 20px;
        background:
            linear-gradient(135deg, rgba(235, 247, 255, .98), rgba(247, 252, 255, .98));
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
        font-size: 10.5px;
        font-weight: 650;
        line-height: 1.5;
    }

    /* Patient summary */
    .rad-patient {
        display: grid;
        grid-template-columns: 1.15fr .85fr;
        gap: 8px;
        margin-bottom: 13px;
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
        font-size: 8.5px;
        font-weight: 900;
        letter-spacing: .04em;
        text-transform: uppercase;
    }

    .rad-value {
        min-width: 0;
        overflow-wrap: anywhere;
        color: var(--nadi-dark);
        font-size: 11.5px;
        font-weight: 900;
        line-height: 1.35;
    }

    /* Filter */
    .rad-filter {
        margin-bottom: 14px;
        padding: 12px;
        border: 1px solid var(--nadi-line);
        border-radius: 17px;
        background: #fff;
        box-shadow: 0 8px 20px rgba(20, 70, 130, .04);
    }

    .rad-filter-title {
        display: flex;
        align-items: center;
        gap: 7px;
        margin: 0 0 10px;
        color: var(--nadi-blue);
        font-size: 12px;
        font-weight: 950;
    }

    .rad-filter-title svg {
        color: var(--nadi-blue-2);
    }

    .rad-filter-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(105px, .45fr);
        gap: 9px;
    }

    .rad-form-group {
        display: grid;
        min-width: 0;
        gap: 5px;
    }

    .rad-form-group.is-wide {
        grid-column: 1 / -1;
    }

    .rad-form-label {
        color: #53647c;
        font-size: 9px;
        font-weight: 900;
    }

    .rad-control {
        display: block;
        width: 100%;
        height: 39px;
        min-width: 0;
        padding: 0 10px;
        border: 1px solid var(--nadi-line);
        border-radius: 10px;
        outline: none;
        background: #fbfdff;
        color: var(--nadi-dark);
        font: inherit;
        font-size: 10.5px;
        font-weight: 750;
        transition: .18s ease;
    }

    .rad-control:focus {
        border-color: rgba(20, 119, 238, .65);
        background: #fff;
        box-shadow: 0 0 0 3px rgba(20, 119, 238, .08);
    }

    .rad-filter-actions {
        display: grid;
        grid-column: 1 / -1;
        grid-template-columns: 1fr auto;
        gap: 8px;
        margin-top: 1px;
    }

    .rad-btn-primary,
    .rad-btn-reset,
    .rad-btn-detail,
    .rad-page-btn {
        display: inline-flex;
        min-height: 38px;
        align-items: center;
        justify-content: center;
        gap: 6px;
        border-radius: 10px;
        font: inherit;
        font-size: 10px;
        font-weight: 900;
        line-height: 1;
        text-decoration: none;
        white-space: nowrap;
        transition: .18s ease;
    }

    .rad-btn-primary {
        border: 0;
        background: linear-gradient(135deg, var(--nadi-blue-2), var(--nadi-blue));
        color: #fff;
        box-shadow: 0 7px 16px rgba(20, 119, 238, .16);
        cursor: pointer;
    }

    .rad-btn-primary:hover {
        transform: translateY(-1px);
        color: #fff;
        box-shadow: 0 10px 20px rgba(20, 119, 238, .20);
    }

    .rad-btn-reset {
        padding: 0 12px;
        border: 1px solid var(--nadi-line);
        background: #fff;
        color: var(--nadi-muted);
    }

    .rad-btn-reset:hover {
        border-color: #c9ddf2;
        background: var(--nadi-blue-soft);
        color: var(--nadi-blue-2);
        text-decoration: none;
    }

    /* Result summary */
    .rad-result-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin: 1px 1px 9px;
    }

    .rad-result-title {
        margin: 0;
        color: var(--nadi-blue);
        font-size: 13px;
        font-weight: 950;
    }

    .rad-result-count {
        display: inline-flex;
        align-items: center;
        padding: 5px 8px;
        border-radius: 999px;
        background: var(--nadi-blue-soft);
        color: var(--nadi-blue-2);
        font-size: 8.5px;
        font-weight: 900;
    }

    /* Radiology cards */
    .rad-card {
        position: relative;
        margin-bottom: 10px;
        overflow: hidden;
        border: 1px solid var(--nadi-line);
        border-radius: 17px;
        background: #fff;
        box-shadow: 0 8px 20px rgba(20, 70, 130, .045);
    }

    .rad-card::before {
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        width: 4px;
        background: linear-gradient(180deg, var(--nadi-blue-2), var(--nadi-green));
        content: '';
    }

    .rad-card-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 9px;
        padding: 13px 13px 10px 16px;
    }

    .rad-exam-wrap {
        min-width: 0;
    }

    .rad-exam {
        color: var(--nadi-dark);
        font-size: 12.5px;
        font-weight: 950;
        line-height: 1.38;
    }

    .rad-number {
        margin-top: 4px;
        color: #7b8ba3;
        font-size: 8.7px;
        font-weight: 750;
        line-height: 1.45;
    }

    .rad-badges {
        display: flex;
        flex: 0 0 auto;
        flex-wrap: wrap;
        justify-content: flex-end;
        gap: 5px;
    }

    .rad-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 5px 7px;
        border-radius: 999px;
        font-size: 8px;
        font-weight: 900;
        line-height: 1;
    }

    .rad-badge.is-ready {
        background: var(--nadi-green-soft);
        color: #118148;
    }

    .rad-badge.is-wait {
        background: #fff7df;
        color: #a16207;
    }

    .rad-badge.is-critical {
        background: #fff0f0;
        color: #c63838;
    }

    .rad-card-body {
        padding: 0 13px 13px 16px;
    }

    .rad-info-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 7px;
    }

    .rad-info-item {
        min-width: 0;
        padding: 9px 10px;
        border-radius: 11px;
        background: #f8fbff;
    }

    .rad-info-item.is-wide {
        grid-column: 1 / -1;
    }

    .rad-info-label {
        margin-bottom: 3px;
        color: #8a98aa;
        font-size: 7.8px;
        font-weight: 900;
        letter-spacing: .035em;
        text-transform: uppercase;
    }

    .rad-info-value {
        overflow-wrap: anywhere;
        color: #40536f;
        font-size: 9.6px;
        font-weight: 850;
        line-height: 1.35;
    }

    .rad-card-actions {
        display: flex;
        justify-content: flex-end;
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px solid #edf2f7;
    }

    .rad-btn-detail {
        min-height: 34px;
        padding: 0 12px;
        border: 1px solid #cfe3fa;
        background: var(--nadi-blue-soft);
        color: var(--nadi-blue-2);
    }

    .rad-btn-detail:hover {
        transform: translateY(-1px);
        border-color: #aed2f8;
        background: #e4f2ff;
        color: var(--nadi-blue);
        text-decoration: none;
    }

    /* Alert / empty */
    .rad-alert,
    .rad-empty {
        margin-bottom: 12px;
        padding: 17px 14px;
        border-radius: 16px;
        text-align: center;
        font-size: 10.5px;
        font-weight: 800;
        line-height: 1.5;
    }

    .rad-alert {
        border: 1px solid #ffd4d4;
        background: #fff4f4;
        color: #b72e2e;
    }

    .rad-empty {
        border: 1px solid var(--nadi-line);
        background: #fbfdff;
        color: var(--nadi-muted);
    }

    /* Pagination */
    .rad-pagination {
        display: grid;
        gap: 8px;
        margin-top: 13px;
        padding: 11px;
        border: 1px solid var(--nadi-line);
        border-radius: 15px;
        background: #fff;
    }

    .rad-page-info {
        color: #7c8da5;
        font-size: 8.7px;
        font-weight: 800;
        text-align: center;
    }

    .rad-page-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 7px;
    }

    .rad-page-btn {
        min-height: 34px;
        padding: 0 8px;
        border: 1px solid var(--nadi-line);
        background: #fff;
        color: #62748d;
    }

    .rad-page-btn:hover {
        border-color: #c6dcf3;
        background: var(--nadi-blue-soft);
        color: var(--nadi-blue-2);
        text-decoration: none;
    }

    .rad-page-btn.is-disabled {
        background: #f8fafc;
        color: #b7c0cc;
        pointer-events: none;
    }

    /* Bottom navigation - mengikuti menu utama */
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

        .rad-card-head {
            flex-direction: column;
        }

        .rad-badges {
            justify-content: flex-start;
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

        .rad-filter-grid,
        .rad-info-grid,
        .rad-patient {
            grid-template-columns: 1fr;
        }

        .rad-patient-item.is-wide,
        .rad-form-group.is-wide,
        .rad-info-item.is-wide,
        .rad-filter-actions {
            grid-column: auto;
        }
    }

    /* =========================================================
       KONSISTENSI DENGAN MENU UTAMA NADI
       - desktop full width
       - typography lebih terbaca
       - patient strip, filter, kartu, dan footer seragam
       ========================================================= */

    .rad-label,
    .rad-info-label {
        font-size: 10.5px;
    }

    .rad-value {
        font-size: 13px;
    }

    .rad-form-label {
        font-size: 11px;
    }

    .rad-control {
        font-size: 12px;
    }

    .rad-btn-primary,
    .rad-btn-reset,
    .rad-btn-detail,
    .rad-page-btn {
        font-size: 11px;
    }

    .rad-exam {
        font-size: 15px;
    }

    .rad-number,
    .rad-info-value,
    .rad-page-info,
    .rad-alert,
    .rad-empty {
        font-size: 11px;
    }

    .rad-badge,
    .rad-result-count {
        font-size: 10.5px;
    }

    /* Footer selalu tersedia, sama dengan menu utama */
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
       DESKTOP / PC
       Mengikuti dashboard menu NADI: full width dan lebih lega.
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

        .rad-intro {
            margin-bottom: 16px;
            padding: 20px;
            border-radius: 22px;
        }

        .rad-intro-icon {
            width: 52px;
            height: 52px;
            flex-basis: 52px;
            border-radius: 15px;
        }

        .rad-intro-title {
            font-size: 20px;
        }

        .rad-intro-text {
            max-width: 820px;
            font-size: 12px;
        }

        /* Identitas pasien menjadi information strip satu baris */
        .rad-patient {
            grid-template-columns: minmax(0, 1.5fr) minmax(180px, .65fr) minmax(180px, .65fr);
            gap: 0;
            margin-bottom: 16px;
            overflow: hidden;
            border: 1px solid var(--nadi-line);
            border-radius: 18px;
            background: #fff;
            box-shadow: 0 8px 22px rgba(15, 45, 115, .045);
        }

        .rad-patient-item,
        .rad-patient-item.is-wide {
            grid-column: auto;
            padding: 15px 18px;
            border: 0;
            border-radius: 0;
            box-shadow: none;
        }

        .rad-patient-item + .rad-patient-item {
            border-left: 1px solid #edf2f7;
        }

        .rad-label {
            margin-bottom: 5px;
            font-size: 11px;
        }

        .rad-value {
            font-size: 14px;
        }

        .rad-filter {
            margin-bottom: 18px;
            padding: 18px;
            border-radius: 20px;
        }

        .rad-filter-title {
            margin-bottom: 13px;
            font-size: 14px;
        }

        .rad-filter-grid {
            grid-template-columns:
                minmax(340px, 1.7fr)
                minmax(180px, .55fr)
                minmax(160px, .5fr)
                auto;
            gap: 12px;
            align-items: end;
        }

        .rad-form-group.is-wide,
        .rad-filter-actions {
            grid-column: auto;
        }

        .rad-form-label {
            font-size: 11px;
        }

        .rad-control {
            height: 44px;
            font-size: 12.5px;
        }

        .rad-filter-actions {
            display: flex;
            justify-content: flex-end;
            gap: 9px;
            margin-top: 0;
        }

        .rad-btn-primary,
        .rad-btn-reset {
            min-height: 44px;
            padding: 0 16px;
            font-size: 11.5px;
        }

        .rad-result-head {
            margin: 3px 2px 12px;
        }

        .rad-result-title {
            font-size: 18px;
        }

        .rad-result-count {
            padding: 6px 10px;
            font-size: 11px;
        }

        .rad-card {
            margin-bottom: 14px;
            border-radius: 20px;
            box-shadow: 0 10px 28px rgba(20, 70, 130, .055);
        }

        .rad-card::before {
            width: 5px;
        }

        .rad-card-head {
            align-items: center;
            gap: 18px;
            padding: 17px 18px 13px 21px;
        }

        .rad-exam {
            font-size: 16px;
        }

        .rad-number {
            margin-top: 5px;
            font-size: 11.5px;
        }

        .rad-badges {
            gap: 7px;
        }

        .rad-badge {
            min-height: 29px;
            padding: 0 10px;
            font-size: 10.5px;
        }

        .rad-card-body {
            padding: 0 18px 17px 21px;
        }

        .rad-info-grid {
            grid-template-columns: minmax(180px, .7fr) minmax(220px, 1fr) minmax(260px, 1.2fr);
            gap: 10px;
        }

        .rad-info-item,
        .rad-info-item.is-wide {
            grid-column: auto;
            padding: 12px 13px;
            border: 1px solid #e7eef6;
            border-radius: 13px;
            background: #fbfdff;
        }

        .rad-info-label {
            margin-bottom: 5px;
            font-size: 10.5px;
        }

        .rad-info-value {
            font-size: 12.5px;
            line-height: 1.45;
        }

        .rad-card-actions {
            margin-top: 12px;
            padding-top: 12px;
        }

        .rad-btn-detail {
            min-height: 38px;
            padding: 0 14px;
            font-size: 11px;
        }

        .rad-pagination {
            grid-template-columns: 1fr auto;
            align-items: center;
            gap: 14px;
            padding: 13px 15px;
            border-radius: 17px;
        }

        .rad-page-info {
            font-size: 11px;
            text-align: left;
        }

        .rad-page-actions {
            display: flex;
            justify-content: flex-end;
        }

        .rad-page-btn {
            min-width: 120px;
            min-height: 38px;
            padding: 0 12px;
            font-size: 11px;
        }

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
            position: relative;
            left: 50%;
            width: 100vw;
            margin-left: -50vw;
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
            width: 100%;
            padding: 0 0 90px;
        }

        .rad-patient {
            grid-template-columns: 1.3fr .7fr .7fr;
        }

        .rad-patient-item.is-wide {
            grid-column: auto;
        }

        .rad-filter-grid {
            grid-template-columns: minmax(0, 1.4fr) minmax(150px, .65fr) minmax(130px, .55fr);
        }

        .rad-filter-actions {
            grid-column: 1 / -1;
            display: flex;
            justify-content: flex-end;
        }

        .rad-info-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .rad-info-item.is-wide {
            grid-column: 1 / -1;
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
        .rad-intro-text {
            font-size: 11.5px;
        }

        .rad-label,
        .rad-info-label {
            font-size: 10px;
        }

        .rad-value,
        .rad-info-value {
            font-size: 11.5px;
        }

        .rad-form-label {
            font-size: 10.5px;
        }

        .rad-control {
            font-size: 11.5px;
        }

        .rad-exam {
            font-size: 14px;
        }

        .rad-number {
            font-size: 10.5px;
        }

        .rad-badge {
            font-size: 9.5px;
        }
    }

</style>

<section class="nadi-page">
    <div class="nadi-phone">
        <main class="nadi-main">
            {{-- Header mengikuti menu utama NADI --}}
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

            {{-- Judul halaman --}}
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
                        <h1 class="rad-intro-title">Hasil Radiologi</h1>
                        <p class="rad-intro-text">
                            Lihat riwayat pemeriksaan, status expertise, radiografer, dan dokter radiolog.
                        </p>
                    </div>
                </div>
            </section>

          

            @if(! data_get($result, 'success', false))
                <div class="rad-alert">
                    {{ data_get(
                        $result,
                        'message',
                        'API radiologi belum berhasil diakses.'
                    ) }}
                </div>
            @else
                {{-- Filter --}}
                <section class="rad-filter">
                    <div class="rad-filter-title">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M4 5H20M7 12H17M10 19H14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        <span>Filter Pemeriksaan</span>
                    </div>

                    <form method="GET" action="{{ route('radiology.index') }}">
                        <input type="hidden" name="nrm" value="{{ $nrm }}">

                        <div class="rad-filter-grid">
                            <div class="rad-form-group is-wide">
                                <label class="rad-form-label" for="rad-keyword">Cari Pemeriksaan</label>
                                <input
                                    id="rad-keyword"
                                    type="text"
                                    name="keyword"
                                    class="rad-control"
                                    value="{{ $keyword }}"
                                    placeholder="No. rontgen, pemeriksaan, radiolog..."
                                >
                            </div>

                            <div class="rad-form-group">
                                <label class="rad-form-label" for="rad-expertise">Expertise</label>
                                <select id="rad-expertise" name="expertise" class="rad-control">
                                    <option value="ALL" {{ $expertiseFilter === 'ALL' ? 'selected' : '' }}>
                                        Semua
                                    </option>
                                    <option value="ADA" {{ $expertiseFilter === 'ADA' ? 'selected' : '' }}>
                                        Sudah Ada
                                    </option>
                                    <option value="BELUM" {{ $expertiseFilter === 'BELUM' ? 'selected' : '' }}>
                                        Belum Ada
                                    </option>
                                </select>
                            </div>

                            <div class="rad-form-group">
                                <label class="rad-form-label" for="rad-per-page">Per Halaman</label>
                                <select id="rad-per-page" name="per_page" class="rad-control">
                                    @foreach([10, 20, 50] as $size)
                                        <option value="{{ $size }}" {{ $perPage == $size ? 'selected' : '' }}>
                                            {{ $size }} Data
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="rad-filter-actions">
                                <button type="submit" class="rad-btn-primary">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M4 5H20M7 12H17M10 19H14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                    <span>Terapkan Filter</span>
                                </button>

                                <a
                                    href="{{ route('radiology.index', ['nrm' => $nrm]) }}"
                                    class="rad-btn-reset"
                                    title="Reset filter"
                                >
                                    Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </section>

                <div class="rad-result-head">
                    <h2 class="rad-result-title">Riwayat Pemeriksaan</h2>
                    <span class="rad-result-count">
                        {{ $radiologyItems->total() }} Data
                    </span>
                </div>

                {{-- Daftar radiologi --}}
                @forelse($radiologyItems as $item)
                    <article class="rad-card">
                        <div class="rad-card-head">
                            <div class="rad-exam-wrap">
                                <div class="rad-exam">
                                    {{ data_get($item, 'nama_pemeriksaan', '-') }}
                                </div>

                                <div class="rad-number">
                                    No. Rontgen {{ data_get($item, 'no_rontgen', '-') }}
                                    &nbsp;·&nbsp;
                                    Registrasi {{ data_get($item, 'no_register', '-') }}
                                </div>
                            </div>

                            <div class="rad-badges">
                                @if(data_get($item, 'is_critical'))
                                    <span class="rad-badge is-critical">
                                        Kritis
                                    </span>
                                @endif

                                @if(data_get($item, 'has_expertise'))
                                    <span class="rad-badge is-ready">
                                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M5 12L10 17L19 7" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        Expertise
                                    </span>
                                @else
                                    <span class="rad-badge is-wait">
                                        Menunggu Expertise
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="rad-card-body">
                            <div class="rad-info-grid">
                                <div class="rad-info-item">
                                    <div class="rad-info-label">Tanggal</div>
                                    <div class="rad-info-value">
                                        @if(data_get($item, 'display_date'))
                                            {{ data_get($item, 'display_date')->format('d-m-Y H:i') }}
                                        @else
                                            -
                                        @endif
                                    </div>
                                </div>

                                <div class="rad-info-item">
                                    <div class="rad-info-label">Radiografer</div>
                                    <div class="rad-info-value">
                                        {{ data_get($item, 'nama_radiografer', '-') ?: '-' }}
                                    </div>
                                </div>

                                <div class="rad-info-item is-wide">
                                    <div class="rad-info-label">Dokter Radiolog</div>
                                    <div class="rad-info-value">
                                        {{ data_get($item, 'nama_radiolog', '-') ?: '-' }}
                                    </div>
                                </div>
                            </div>

                            <div class="rad-card-actions">
                                <a
                                    href="{{ route('radiology.detail', [
                                        'id' => data_get($item, 'id'),
                                        'nrm' => $nrm
                                    ]) }}"
                                    class="rad-btn-detail"
                                >
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M2.5 12S6 6.5 12 6.5S21.5 12 21.5 12S18 17.5 12 17.5S2.5 12 2.5 12Z" stroke="currentColor" stroke-width="2"/>
                                        <circle cx="12" cy="12" r="2.5" stroke="currentColor" stroke-width="2"/>
                                    </svg>
                                    <span>Lihat Detail</span>
                                </a>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="rad-empty">
                        Belum ada riwayat radiologi yang sesuai dengan filter.
                    </div>
                @endforelse

                @if($radiologyItems->hasPages())
                    <div class="rad-pagination">
                        <div class="rad-page-info">
                            Menampilkan {{ $radiologyItems->firstItem() }} - {{ $radiologyItems->lastItem() }}
                            dari {{ $radiologyItems->total() }} data
                        </div>

                        <div class="rad-page-actions">
                            @if($radiologyItems->onFirstPage())
                                <span class="rad-page-btn is-disabled">
                                    ← Sebelumnya
                                </span>
                            @else
                                <a href="{{ $radiologyItems->previousPageUrl() }}" class="rad-page-btn">
                                    ← Sebelumnya
                                </a>
                            @endif

                            @if($radiologyItems->hasMorePages())
                                <a href="{{ $radiologyItems->nextPageUrl() }}" class="rad-page-btn">
                                    Berikutnya →
                                </a>
                            @else
                                <span class="rad-page-btn is-disabled">
                                    Berikutnya →
                                </span>
                            @endif
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

@endsection