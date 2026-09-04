@extends('layouts.app', ['title' => 'Laboratorium | NADI RSBM'])

@section('content')
<style>
    .lab-page,
    .lab-page * {
        box-sizing: border-box;
    }

    /*
    |--------------------------------------------------------------------------
    | Halaman penuh
    |--------------------------------------------------------------------------
    |
    | Membuat halaman keluar dari batas container layouts.app sehingga
    | tidak menyisakan area kosong di sebelah kanan.
    */
    .lab-page {
        position: relative;
        left: 50%;
        width: 100vw;
        min-height: 100vh;
        margin-left: -50vw;
        padding: 32px clamp(16px, 4vw, 48px) 60px;
        overflow-x: hidden;
        color: #0f172a;
        background:
            radial-gradient(
                circle at top right,
                rgba(34, 197, 94, 0.13),
                transparent 34%
            ),
            radial-gradient(
                circle at bottom left,
                rgba(59, 130, 246, 0.08),
                transparent 30%
            ),
            linear-gradient(
                180deg,
                #f8fffb 0%,
                #f8fafc 48%,
                #f1f5f9 100%
            );
    }

    .lab-container {
        width: min(1180px, 100%);
        margin: 0 auto;
    }

    /*
    |--------------------------------------------------------------------------
    | Header
    |--------------------------------------------------------------------------
    */
    .lab-header {
        max-width: 780px;
        margin-bottom: 26px;
    }

    .lab-back {
        display: inline-flex;
        gap: 8px;
        align-items: center;
        margin-bottom: 20px;
        color: #475569;
        font-size: 13px;
        font-weight: 800;
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .lab-back:hover {
        color: #15803d;
        text-decoration: none;
    }

    .lab-eyebrow {
        display: inline-flex;
        align-items: center;
        padding: 7px 13px;
        border: 1px solid #bbf7d0;
        border-radius: 999px;
        background: #dcfce7;
        color: #166534;
        font-size: 12px;
        font-weight: 900;
    }

    .lab-title {
        max-width: 680px;
        margin: 14px 0 10px;
        color: #0f172a;
        font-size: clamp(32px, 5vw, 50px);
        font-weight: 950;
        line-height: 1.03;
        letter-spacing: -0.045em;
    }

    .lab-description {
        max-width: 680px;
        margin: 0;
        color: #64748b;
        font-size: 15px;
        line-height: 1.7;
    }

    /*
    |--------------------------------------------------------------------------
    | Komponen card
    |--------------------------------------------------------------------------
    */
    .patient-card,
    .filter-card,
    .order-card,
    .empty-card,
    .error-card {
        border: 1px solid rgba(203, 213, 225, 0.8);
        background: rgba(255, 255, 255, 0.96);
        box-shadow: 0 14px 35px rgba(15, 23, 42, 0.06);
    }

    /*
    |--------------------------------------------------------------------------
    | Informasi pasien
    |--------------------------------------------------------------------------
    */
    .patient-card {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 0;
        margin-bottom: 16px;
        overflow: hidden;
        border-radius: 22px;
    }

    .patient-card > div {
        min-width: 0;
        padding: 20px 22px;
    }

    .patient-card > div + div {
        border-left: 1px solid #e2e8f0;
    }

    .patient-label {
        margin-bottom: 6px;
        color: #94a3b8;
        font-size: 11px;
        font-weight: 900;
        letter-spacing: 0.035em;
        text-transform: uppercase;
    }

    .patient-value {
        overflow-wrap: anywhere;
        color: #0f172a;
        font-size: 15px;
        font-weight: 900;
        line-height: 1.45;
    }

    /*
    |--------------------------------------------------------------------------
    | Ringkasan
    |--------------------------------------------------------------------------
    */
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 18px;
    }

    .summary-box {
        min-width: 0;
        padding: 19px 20px;
        border: 1px solid rgba(203, 213, 225, 0.8);
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.96);
        box-shadow: 0 10px 28px rgba(15, 23, 42, 0.045);
    }

    .summary-label {
        min-height: 30px;
        color: #64748b;
        font-size: 11px;
        font-weight: 900;
        line-height: 1.35;
        letter-spacing: 0.025em;
        text-transform: uppercase;
    }

    .summary-value {
        margin-top: 5px;
        color: #0f172a;
        font-size: 30px;
        font-weight: 950;
        line-height: 1;
        letter-spacing: -0.04em;
    }

    /*
    |--------------------------------------------------------------------------
    | Filter
    |--------------------------------------------------------------------------
    */
    .filter-card {
        margin-bottom: 18px;
        padding: 20px;
        border-radius: 22px;
    }

    .filter-grid {
        display: grid;
        grid-template-columns:
            minmax(260px, 1.5fr)
            minmax(170px, 0.7fr)
            minmax(140px, 0.55fr);
        gap: 14px;
        align-items: end;
    }

    .form-group {
        display: grid;
        min-width: 0;
        gap: 7px;
    }

    .form-label {
        margin: 0;
        color: #334155;
        font-size: 12px;
        font-weight: 900;
    }

    .form-control {
        display: block;
        width: 100%;
        min-width: 0;
        height: 48px;
        padding: 0 14px;
        border: 1px solid #cbd5e1;
        border-radius: 14px;
        outline: none;
        background: #fff;
        color: #0f172a;
        font-size: 13px;
        transition:
            border-color 0.2s ease,
            box-shadow 0.2s ease;
    }

    .form-control:focus {
        border-color: #22c55e;
        box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.12);
    }

    .filter-actions {
        display: flex;
        grid-column: 1 / -1;
        gap: 10px;
        align-items: center;
        justify-content: flex-end;
        padding-top: 2px;
    }

    .btn-primary,
    .btn-secondary {
        display: inline-flex;
        height: 46px;
        min-width: 105px;
        align-items: center;
        justify-content: center;
        padding: 0 18px;
        border-radius: 14px;
        font-size: 13px;
        font-weight: 900;
        line-height: 1;
        text-decoration: none;
        white-space: nowrap;
        cursor: pointer;
        transition:
            transform 0.2s ease,
            box-shadow 0.2s ease,
            background 0.2s ease;
    }

    .btn-primary {
        border: 0;
        background: linear-gradient(135deg, #22c55e, #16a34a);
        color: #fff;
        box-shadow: 0 8px 18px rgba(22, 163, 74, 0.22);
    }

    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 11px 23px rgba(22, 163, 74, 0.27);
    }

    .btn-secondary {
        border: 1px solid #cbd5e1;
        background: #f8fafc;
        color: #475569;
    }

    .btn-secondary:hover {
        border-color: #94a3b8;
        background: #f1f5f9;
        color: #0f172a;
        text-decoration: none;
    }

    /*
    |--------------------------------------------------------------------------
    | Order laboratorium
    |--------------------------------------------------------------------------
    */
    .order-card {
        margin-bottom: 16px;
        overflow: hidden;
        border-radius: 22px;
    }

    .order-header {
        display: flex;
        gap: 18px;
        align-items: flex-start;
        justify-content: space-between;
        padding: 20px 22px;
        border-bottom: 1px solid #e2e8f0;
        background:
            linear-gradient(
                90deg,
                rgba(240, 253, 244, 0.7),
                rgba(255, 255, 255, 0)
            );
    }

    .order-number {
        color: #0f172a;
        font-size: 18px;
        font-weight: 950;
        letter-spacing: -0.015em;
    }

    .order-date {
        margin-top: 6px;
        color: #64748b;
        font-size: 12px;
        font-weight: 750;
        line-height: 1.5;
    }

    .status-badge {
        display: inline-flex;
        flex: 0 0 auto;
        align-items: center;
        padding: 7px 12px;
        border-radius: 999px;
        background: #dbeafe;
        color: #1d4ed8;
        font-size: 11px;
        font-weight: 900;
        text-transform: capitalize;
    }

    .order-body {
        padding: 22px;
    }

    .order-info {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 20px 26px;
        margin-bottom: 22px;
    }

    .order-info > div {
        min-width: 0;
    }

    .info-label {
        margin-bottom: 5px;
        color: #94a3b8;
        font-size: 10px;
        font-weight: 900;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }

    .info-value {
        overflow-wrap: anywhere;
        color: #334155;
        font-size: 13px;
        font-weight: 850;
        line-height: 1.55;
    }

    .detail-title {
        margin-bottom: 11px;
        color: #0f172a;
        font-size: 13px;
        font-weight: 950;
    }

    .detail-list {
        display: flex;
        gap: 8px;
        align-items: center;
        flex-wrap: wrap;
    }

    .detail-item {
        display: inline-flex;
        max-width: 100%;
        align-items: center;
        padding: 8px 11px;
        border: 1px solid #dcfce7;
        border-radius: 11px;
        background: #f0fdf4;
        color: #166534;
        font-size: 11px;
        font-weight: 850;
        line-height: 1.35;
    }

    /*
    |--------------------------------------------------------------------------
    | Empty dan error
    |--------------------------------------------------------------------------
    */
    .empty-card,
    .error-card {
        padding: 38px 22px;
        border-radius: 22px;
        text-align: center;
        font-size: 14px;
        font-weight: 750;
        line-height: 1.6;
    }

    .empty-card {
        color: #64748b;
    }

    .error-card {
        border-color: #fecaca;
        background: #fef2f2;
        color: #991b1b;
    }

    /*
    |--------------------------------------------------------------------------
    | Infinite Scroll
    |--------------------------------------------------------------------------
    */
    .infinite-scroll-sentinel {
        display: flex;
        min-height: 86px;
        align-items: center;
        justify-content: center;
        margin-top: 8px;
    }

    .infinite-loader {
        display: inline-flex;
        gap: 10px;
        align-items: center;
        justify-content: center;
        padding: 12px 16px;
        border: 1px solid rgba(203, 213, 225, 0.8);
        border-radius: 14px;
        background: rgba(255, 255, 255, 0.92);
        color: #64748b;
        font-size: 12px;
        font-weight: 850;
        box-shadow: 0 8px 22px rgba(15, 23, 42, 0.04);
    }

    .loading-spinner {
        width: 20px;
        height: 20px;
        flex: 0 0 auto;
        border: 3px solid #dcfce7;
        border-top-color: #16a34a;
        border-radius: 50%;
        animation: lab-spin 0.7s linear infinite;
    }

    @keyframes lab-spin {
        to {
            transform: rotate(360deg);
        }
    }

    .infinite-scroll-error {
        display: grid;
        gap: 9px;
        justify-items: center;
        padding: 14px 16px;
        border: 1px solid #fecaca;
        border-radius: 14px;
        background: #fef2f2;
        color: #991b1b;
        font-size: 12px;
        font-weight: 800;
        text-align: center;
    }

    .infinite-retry {
        display: inline-flex;
        height: 34px;
        align-items: center;
        justify-content: center;
        padding: 0 13px;
        border: 1px solid #fecaca;
        border-radius: 10px;
        background: #fff;
        color: #b91c1c;
        font-size: 11px;
        font-weight: 900;
        cursor: pointer;
    }

    .infinite-retry:hover {
        background: #fee2e2;
    }

    /*
    |--------------------------------------------------------------------------
    | Tablet
    |--------------------------------------------------------------------------
    */
    @media (max-width: 900px) {
        .lab-page {
            padding-top: 24px;
        }

        .lab-title {
            font-size: clamp(32px, 7vw, 44px);
        }

        .summary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .filter-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .filter-grid .form-group:first-child {
            grid-column: 1 / -1;
        }

        .filter-actions {
            grid-column: 1 / -1;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Mobile
    |--------------------------------------------------------------------------
    */
    @media (max-width: 640px) {
        .lab-page {
            padding: 18px 14px 42px;
        }

        .lab-header {
            margin-bottom: 20px;
        }

        .lab-back {
            margin-bottom: 16px;
        }

        .lab-title {
            margin-top: 12px;
            font-size: 34px;
            line-height: 1.05;
        }

        .patient-card {
            grid-template-columns: 1fr;
        }

        .patient-card > div {
            padding: 16px 18px;
        }

        .patient-card > div + div {
            border-top: 1px solid #e2e8f0;
            border-left: 0;
        }

        .summary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .summary-box {
            padding: 16px;
        }

        .summary-label {
            min-height: 28px;
            font-size: 10px;
        }

        .summary-value {
            font-size: 26px;
        }

        .filter-card {
            padding: 16px;
        }

        .filter-grid {
            grid-template-columns: 1fr;
        }

        .filter-grid .form-group:first-child,
        .filter-actions {
            grid-column: auto;
        }

        .filter-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        .btn-primary,
        .btn-secondary {
            width: 100%;
            min-width: 0;
        }

        .order-header {
            flex-direction: column;
            padding: 18px;
        }

        .order-body {
            padding: 18px;
        }

        .order-info {
            grid-template-columns: 1fr;
            gap: 15px;
        }

        .detail-item {
            width: 100%;
        }
    }

    @media (max-width: 380px) {
        .summary-grid {
            grid-template-columns: 1fr;
        }

        .filter-actions {
            grid-template-columns: 1fr;
        }
    }
    .order-header-actions {
    display: flex;
    flex: 0 0 auto;
    gap: 9px;
    align-items: center;
}

.btn-detail {
    display: inline-flex;
    height: 36px;
    align-items: center;
    justify-content: center;
    padding: 0 14px;
    border: 1px solid #bbf7d0;
    border-radius: 11px;
    background: #f0fdf4;
    color: #15803d;
    font-size: 11px;
    font-weight: 900;
    text-decoration: none;
    white-space: nowrap;
    transition:
        background 0.2s ease,
        border-color 0.2s ease,
        transform 0.2s ease;
}

.btn-detail:hover {
    transform: translateY(-1px);
    border-color: #86efac;
    background: #dcfce7;
    color: #166534;
    text-decoration: none;
}

.btn-detail.is-disabled {
    border-color: #e2e8f0;
    background: #f8fafc;
    color: #94a3b8;
    cursor: not-allowed;
    pointer-events: none;
    box-shadow: none;
}

@media (max-width: 640px) {
    .order-header-actions {
        width: 100%;
        justify-content: space-between;
    }

    .btn-detail {
        height: 38px;
    }
}


    /* ================================================================
       Penyelarasan visual SAPA RSBM / RSUD Bali Mandara
       ================================================================ */
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

    .lab-page {
        left: auto;
        width: auto;
        min-height: calc(100svh - 72px);
        margin-left: 0;
        padding: 16px 0 42px;
        color: var(--rsbm-text);
        background:
            radial-gradient(circle at 8% 8%, rgba(38, 53, 143, .08), transparent 25%),
            radial-gradient(circle at 93% 90%, rgba(25, 200, 61, .07), transparent 24%),
            linear-gradient(180deg, #fff 0%, var(--rsbm-bg) 100%);
    }

    .lab-container {
        width: min(1060px, calc(100% - 28px));
    }

    .lab-topbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        margin-bottom: 22px;
        padding-bottom: 15px;
        border-bottom: 1px solid rgba(38, 53, 143, .09);
    }

    .lab-brand {
        display: flex;
        min-width: 0;
        align-items: center;
        gap: 11px;
    }

    .lab-brand-logo-shell {
        display: grid;
        width: 48px;
        height: 48px;
        flex: 0 0 48px;
        place-items: center;
        overflow: hidden;
        border: 1px solid rgba(38, 53, 143, .11);
        border-radius: 12px;
        background: #fff;
        box-shadow: 0 8px 20px rgba(38, 53, 143, .08);
    }

    .lab-brand-logo {
        width: 41px;
        height: 41px;
        object-fit: contain;
    }

    .lab-brand-fallback {
        display: none;
        width: 36px;
        height: 36px;
        place-items: center;
        border-radius: 10px;
        background: linear-gradient(145deg, var(--rsbm-blue), var(--rsbm-blue-dark));
        color: #fff;
        font-size: 17px;
        font-weight: 950;
    }

    .lab-brand-government {
        color: #7d8797;
        font-size: 8.5px;
        font-weight: 850;
        letter-spacing: .11em;
        text-transform: uppercase;
    }

    .lab-brand-name {
        margin-top: 1px;
        color: var(--rsbm-blue-dark);
        font-size: 15px;
        font-weight: 950;
        letter-spacing: -.01em;
    }

    .lab-brand-location {
        margin-top: 1px;
        color: #939cab;
        font-size: 9.5px;
        font-weight: 650;
    }

    .nav-label-short {
        display: none;
    }

    .lab-nav-actions {
        display: flex;
        flex: 0 0 auto;
        align-items: center;
        gap: 8px;
    }

    .lab-nav-button,
    .lab-logout-button {
        display: inline-flex;
        min-height: 40px;
        align-items: center;
        justify-content: center;
        gap: 7px;
        padding: 0 13px;
        border-radius: 11px;
        font-family: inherit;
        font-size: 11px;
        font-weight: 900;
        line-height: 1;
        text-decoration: none;
        white-space: nowrap;
        cursor: pointer;
        transition: transform .18s ease, box-shadow .18s ease, background .18s ease;
    }

    .lab-nav-button {
        border: 1px solid rgba(38, 53, 143, .13);
        background: var(--rsbm-blue-soft);
        color: var(--rsbm-blue);
    }

    .lab-nav-button:hover {
        transform: translateY(-1px);
        background: #e4e8ff;
        color: var(--rsbm-blue-dark);
        text-decoration: none;
    }

    .lab-logout-form {
        margin: 0;
    }

    .lab-logout-button {
        border: 1px solid #fecaca;
        background: #fff;
        color: #b91c1c;
    }

    .lab-logout-button:hover {
        transform: translateY(-1px);
        background: #fef2f2;
        box-shadow: 0 6px 16px rgba(185, 28, 28, .07);
    }

    .lab-header {
        max-width: none;
        margin-bottom: 18px;
    }

    .lab-back {
        display: none;
    }

    .lab-eyebrow {
        gap: 7px;
        padding: 6px 10px;
        border-color: rgba(38, 53, 143, .09);
        background: rgba(255, 255, 255, .84);
        color: var(--rsbm-blue-dark);
        font-size: 10px;
        letter-spacing: .075em;
        text-transform: uppercase;
    }

    .lab-eyebrow::before {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: var(--rsbm-green);
        box-shadow: 0 0 0 4px rgba(25, 200, 61, .10);
        content: '';
    }

    .lab-title {
        max-width: 720px;
        margin: 10px 0 7px;
        color: var(--rsbm-text);
        font-size: clamp(30px, 4vw, 44px);
        line-height: 1.06;
    }

    .lab-title span {
        color: var(--rsbm-blue);
    }

    .lab-description {
        max-width: 700px;
        color: var(--rsbm-muted);
        font-size: 13px;
        line-height: 1.6;
    }

    .patient-card,
    .filter-card,
    .order-card,
    .empty-card,
    .error-card {
        border-color: rgba(217, 224, 234, .95);
        box-shadow: 0 12px 30px rgba(28, 39, 90, .055);
    }

    .patient-card {
        margin-bottom: 13px;
        border-radius: 18px;
    }

    .patient-card > div {
        padding: 15px 18px;
    }

    .patient-label {
        margin-bottom: 4px;
        color: #939daf;
        font-size: 9.5px;
    }

    .patient-value {
        color: var(--rsbm-text);
        font-size: 13.5px;
    }

    .filter-card {
        margin-bottom: 14px;
        padding: 16px;
        border-radius: 18px;
    }

    .filter-grid {
        grid-template-columns:
            minmax(250px, 1.55fr)
            minmax(160px, .7fr)
            minmax(135px, .55fr);
        gap: 10px;
    }

    .filter-actions {
        grid-column: 1 / -1;
        width: 100%;
        padding-top: 2px;
        justify-content: flex-end;
        flex-wrap: wrap;
    }

    .filter-actions .btn-primary,
    .filter-actions .btn-secondary {
        flex: 0 0 auto;
    }

    .form-label {
        font-size: 10.5px;
    }

    .form-control {
        height: 44px;
        border-color: var(--rsbm-line);
        border-radius: 11px;
        font-size: 12px;
    }

    .form-control:focus {
        border-color: var(--rsbm-blue);
        box-shadow: 0 0 0 3px rgba(38, 53, 143, .09);
    }

    .btn-primary,
    .btn-secondary {
        height: 44px;
        min-width: 88px;
        padding: 0 14px;
        border-radius: 11px;
        font-size: 11px;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--rsbm-blue), var(--rsbm-blue-dark));
        box-shadow: 0 8px 18px rgba(38, 53, 143, .18);
    }

    .btn-primary:hover {
        box-shadow: 0 10px 22px rgba(38, 53, 143, .23);
    }

    .order-card {
        margin-bottom: 13px;
        border-radius: 18px;
    }

    .order-header {
        padding: 15px 18px;
        background: linear-gradient(90deg, rgba(238, 241, 255, .8), rgba(234, 251, 238, .25), #fff);
    }

    .order-number {
        color: var(--rsbm-blue-dark);
        font-size: 16px;
    }

    .order-date {
        margin-top: 4px;
        font-size: 10.5px;
    }

    .status-badge {
        padding: 6px 10px;
        background: var(--rsbm-green-soft);
        color: var(--rsbm-green-dark);
        font-size: 9.5px;
    }

    .btn-detail {
        height: 34px;
        border-color: rgba(38, 53, 143, .13);
        border-radius: 9px;
        background: var(--rsbm-blue-soft);
        color: var(--rsbm-blue);
        font-size: 10px;
    }

    .btn-detail:hover {
        border-color: rgba(38, 53, 143, .2);
        background: #e4e8ff;
        color: var(--rsbm-blue-dark);
    }

    .order-body {
        padding: 17px 18px;
    }

    .order-info {
        gap: 14px 22px;
        margin-bottom: 16px;
    }

    .info-label {
        font-size: 9px;
    }

    .info-value {
        font-size: 11.5px;
    }

    .detail-title {
        margin-bottom: 8px;
        font-size: 11.5px;
    }

    .detail-item {
        padding: 6px 9px;
        border-color: #d7e8dc;
        border-radius: 9px;
        background: #f7fcf8;
        color: #3f6350;
        font-size: 10px;
    }

    .loading-spinner {
        border-color: var(--rsbm-blue-soft);
        border-top-color: var(--rsbm-blue);
    }

    @media (max-width: 900px) {
        .lab-page {
            padding-top: 12px;
        }

        .filter-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .filter-grid .form-group:first-child,
        .filter-actions {
            grid-column: 1 / -1;
        }
    }

    @media (max-width: 640px) {
        .lab-page {
            padding: 8px 0 32px;
        }

        .lab-container {
            width: min(100% - 18px, 520px);
        }

        .lab-topbar {
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 15px;
            padding-bottom: 12px;
        }

        .lab-brand-logo-shell {
            width: 43px;
            height: 43px;
            flex-basis: 43px;
        }

        .lab-brand-logo {
            width: 37px;
            height: 37px;
        }

        .lab-brand-government,
        .lab-brand-location {
            display: none;
        }

        .lab-brand-name {
            font-size: 13px;
        }

        .lab-nav-actions {
            gap: 6px;
        }

        .lab-nav-button,
        .lab-logout-button {
            min-height: 37px;
            padding: 0 10px;
            font-size: 10px;
        }

        .nav-label-long {
            display: none;
        }

        .nav-label-short {
            display: inline;
        }

        .lab-title {
            margin-top: 9px;
            font-size: 30px;
        }

        .lab-description {
            font-size: 12px;
        }

        .patient-card > div {
            padding: 13px 15px;
        }

        .filter-card {
            padding: 13px;
        }

        .filter-grid {
            grid-template-columns: 1fr;
        }

        .filter-grid .form-group:first-child,
        .filter-actions {
            grid-column: auto;
        }

        .filter-actions {
            display: grid;
            width: 100%;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px;
        }

        .filter-actions .btn-primary,
        .filter-actions .btn-secondary {
            width: 100%;
            min-width: 0;
        }

        .order-header,
        .order-body {
            padding: 14px;
        }
    }

</style>

@php
    $patientName = data_get($patient, 'name', '-');
    $medicalRecord = data_get($patient, 'medical_record', '-');
    $birthDate = data_get($patient, 'birth_date');

    // Gunakan route logout aplikasi bila tersedia.
    $logoutRouteName = null;

    if (Route::has('layanan.logout')) {
        $logoutRouteName = 'layanan.logout';
    } elseif (Route::has('logout')) {
        $logoutRouteName = 'logout';
    }
@endphp

<div class="lab-page">
    <div class="lab-container">
        <div class="lab-topbar">
            <div class="lab-brand">
                <div class="lab-brand-logo-shell">
                    <img
                        class="lab-brand-logo"
                        src="{{ asset('images/logo-rsbm.png') }}"
                        alt="Logo RSUD Bali Mandara"
                        onerror="this.style.display='none'; this.nextElementSibling.style.display='grid';"
                    >
                    <span class="lab-brand-fallback" aria-hidden="true">+</span>
                </div>

                <div>
                    <div class="lab-brand-government">Pemerintah Provinsi Bali</div>
                    <div class="lab-brand-name">RSUD Bali Mandara</div>
                    <div class="lab-brand-location">Sanur · Denpasar · Bali</div>
                </div>
            </div>

            <div class="lab-nav-actions">
                <a
                    href="{{ route('layanan.menu') }}"
                    class="lab-nav-button"
                    aria-label="Kembali ke menu layanan"
                >
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M4 10.5L12 4L20 10.5V20H14V14H10V20H4V10.5Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round" />
                    </svg>
                    <span class="nav-label-long">Menu Layanan</span>
                    <span class="nav-label-short">Menu</span>
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
                            class="lab-logout-button"
                            aria-label="Keluar dari SAPA RSBM"
                        >
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M10 5H6C4.9 5 4 5.9 4 7V17C4 18.1 4.9 19 6 19H10" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                <path d="M14 8L18 12L14 16M18 12H9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <span class="nav-label-long">Logout</span>
                            <span class="nav-label-short">Keluar</span>
                        </button>
                    </form>
                @else
                    <a
                        href="{{ url('/') }}"
                        class="lab-logout-button"
                        aria-label="Kembali ke halaman awal"
                    >
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M10 5H6C4.9 5 4 5.9 4 7V17C4 18.1 4.9 19 6 19H10" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            <path d="M14 8L18 12L14 16M18 12H9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <span class="nav-label-long">Keluar</span>
                        <span class="nav-label-short">Keluar</span>
                    </a>
                @endif
            </div>
        </div>

        <div class="lab-header">
            <div>
                <span class="lab-eyebrow">
                    SAPA RSBM · Laboratorium
                </span>
            </div>

         
        </div>

        <div class="patient-card">
            <div>
                <div class="patient-label">Nama Pasien</div>
                <div class="patient-value">
                    {{ $patientName }}
                </div>
            </div>

            <div>
                <div class="patient-label">Nomor Rekam Medis</div>
                <div class="patient-value">
                    {{ $medicalRecord }}
                </div>
            </div>

            <div>
                <div class="patient-label">Tanggal Lahir</div>
                <div class="patient-value">
                    @if($birthDate)
                        {{ \Carbon\Carbon::parse($birthDate)->format('d-m-Y') }}
                    @else
                        -
                    @endif
                </div>
            </div>
        </div>

        @if(data_get($result, 'is_error'))
            <div class="error-card">
                {{ data_get(
                    $result,
                    'message',
                    'API laboratorium belum berhasil diakses.'
                ) }}
            </div>
        @else
            <!-- <div class="summary-grid">
                <div class="summary-box">
                    <div class="summary-label">Total Order</div>
                    <div class="summary-value">
                        {{ $summary['total'] ?? 0 }}
                    </div>
                </div>

                <div class="summary-box">
                    <div class="summary-label">Terverifikasi</div>
                    <div class="summary-value">
                        {{ $summary['verified'] ?? 0 }}
                    </div>
                </div>

                <div class="summary-box">
                    <div class="summary-label">Patologi Klinik</div>
                    <div class="summary-value">
                        {{ $summary['clinical_pathology'] ?? 0 }}
                    </div>
                </div>

                <div class="summary-box">
                    <div class="summary-label">Patologi Anatomi</div>
                    <div class="summary-value">
                        {{ $summary['anatomical_pathology'] ?? 0 }}
                    </div>
                </div>
            </div> -->

            <div class="filter-card">
                <form
                    method="GET"
                    action="{{ route('laboratory.index') }}">

                    <div class="filter-grid">
                        <div class="form-group">
                            <label class="form-label">
                                Cari Pemeriksaan
                            </label>

                            <input
                                type="text"
                                name="keyword"
                                class="form-control"
                                value="{{ request('keyword') }}"
                                placeholder="No order, dokter, ruangan, pemeriksaan...">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Status</label>

                            <select
                                name="status"
                                class="form-control">

                                <option value="ALL">
                                    Semua Status
                                </option>

                                <option
                                    value="verifikasi"
                                    {{ request('status') === 'verifikasi'
                                        ? 'selected'
                                        : '' }}>
                                    Verifikasi
                                </option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Tahun</label>

                            <select
                                name="tahun"
                                class="form-control">

                                <option value="ALL">
                                    Semua Tahun
                                </option>

                                @foreach($yearOptions as $year)
                                    <option
                                        value="{{ $year }}"
                                        {{ request('tahun') == $year
                                            ? 'selected'
                                            : '' }}>
                                        {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="filter-actions">
                            <button
                                type="submit"
                                class="btn-primary">
                                Terapkan
                            </button>

                            <a
                                href="{{ route('laboratory.index') }}"
                                class="btn-secondary">
                                Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            @php
                /*
                 * Pertahankan filter saat mengambil page berikutnya.
                 */
                $orders->appends(request()->except('page'));
            @endphp

            <div id="order-list">
                @forelse($orders as $order)
                    <article class="order-card">
                    <div class="order-header">
    <div>
        <div class="order-number">
            {{ data_get(
                $order,
                'order_number',
                '-'
            ) }}
        </div>

        <div class="order-date">
            @php
                $orderDate = data_get(
                    $order,
                    'order_date'
                );
            @endphp

            @if($orderDate)
                {{ \Carbon\Carbon::parse(
                    $orderDate
                )->format('d-m-Y H:i') }}
            @else
                -
            @endif

            · Registrasi:
            {{ data_get(
                $order,
                'registration_number',
                '-'
            ) }}
        </div>
    </div>

    <div class="order-header-actions">
        <span class="status-badge">
            {{ data_get(
                $order,
                'status',
                '-'
            ) }}
        </span>

        @php
            $detailNoOrder = trim(
                (string) data_get(
                    $order,
                    'order_number',
                    ''
                )
            );

            $detailLab = trim(
                (string) data_get(
                    $order,
                    'destination_room',
                    ''
                )
            );

            $canViewDetail =
                $detailNoOrder !== ''
                && $detailLab !== '';
        @endphp

        @if($canViewDetail)
            <a
                href="{{ route('laboratory.detail', [
                    'noOrder' => $detailNoOrder,
                    'lab' => $detailLab,
                ]) }}"
                class="btn-detail"
            >
                Lihat Detail
            </a>
        @else
            <span
                class="btn-detail is-disabled"
                title="Nomor order hasil laboratorium belum tersedia"
            >
                Belum Tersedia
            </span>
        @endif
    </div>
</div>

                    <div class="order-body">
                        <div class="order-info">
                            <div>
                                <div class="info-label">
                                    Dokter
                                </div>

                                <div class="info-value">
                                    {{ data_get(
                                        $order,
                                        'doctor',
                                        '-'
                                    ) }}
                                </div>
                            </div>

                            <div>
                                <div class="info-label">
                                    Ruangan Asal
                                </div>

                                <div class="info-value">
                                    {{ data_get(
                                        $order,
                                        'origin_room',
                                        '-'
                                    ) }}
                                </div>
                            </div>

                            <div>
                                <div class="info-label">
                                    Laboratorium Tujuan
                                </div>

                                <div class="info-value">
                                    {{ data_get(
                                        $order,
                                        'destination_room',
                                        '-'
                                    ) }}
                                </div>
                            </div>

                            <div>
                                <div class="info-label">
                                    Jumlah Pemeriksaan
                                </div>

                                <div class="info-value">
                                    {{ count(
                                        data_get(
                                            $order,
                                            'details',
                                            []
                                        )
                                    ) }}
                                    pemeriksaan
                                </div>
                            </div>
                        </div>

                        <div class="detail-title">
                            Daftar pemeriksaan
                        </div>

                        <div class="detail-list">
                            @forelse(
                                data_get($order, 'details', [])
                                as $detail
                            )
                                <span class="detail-item">
                                    {{ data_get(
                                        $detail,
                                        'name',
                                        '-'
                                    ) }}
                                </span>
                            @empty
                                <span class="detail-item">
                                    Detail pemeriksaan tidak tersedia
                                </span>
                            @endforelse
                        </div>
                    </div>
                    </article>
                @empty
                    <div class="empty-card">
                        Belum ada riwayat pemeriksaan laboratorium
                        yang sesuai dengan filter.
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
                        <span
                            class="loading-spinner"
                            aria-hidden="true"
                        ></span>

                        <span>
                            Memuat riwayat berikutnya...
                        </span>
                    </div>
                </div>
            @endif
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const orderList = document.getElementById('order-list');
    const sentinel = document.getElementById(
        'infinite-scroll-sentinel'
    );

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
                throw new Error(
                    'HTTP ' + response.status
                );
            }

            const html = await response.text();

            const parser = new DOMParser();

            const nextDocument = parser.parseFromString(
                html,
                'text/html'
            );

            const nextOrderList =
                nextDocument.getElementById(
                    'order-list'
                );

            if (!nextOrderList) {
                throw new Error(
                    'Daftar order halaman berikutnya tidak ditemukan.'
                );
            }

            const newOrders =
                nextOrderList.querySelectorAll(
                    '.order-card'
                );

            if (!newOrders.length) {
                finishInfiniteScroll();
                return;
            }

            const fragment =
                document.createDocumentFragment();

            newOrders.forEach(function (order) {
                fragment.appendChild(
                    order.cloneNode(true)
                );
            });

            orderList.appendChild(fragment);

            const nextSentinel =
                nextDocument.getElementById(
                    'infinite-scroll-sentinel'
                );

            if (
                nextSentinel
                && nextSentinel.dataset.nextUrl
            ) {
                sentinel.dataset.nextUrl =
                    nextSentinel.dataset.nextUrl;
            } else {
                finishInfiniteScroll();
            }
        } catch (error) {
            console.error(
                'Infinite scroll laboratorium:',
                error
            );

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
                <span>
                    Data berikutnya gagal dimuat.
                </span>

                <button
                    type="button"
                    class="infinite-retry"
                    id="infinite-retry"
                >
                    Coba lagi
                </button>
            </div>
        `;

        const retryButton =
            document.getElementById(
                'infinite-retry'
            );

        if (!retryButton) {
            return;
        }

        retryButton.addEventListener(
            'click',
            function () {
                sentinel.innerHTML = `
                    <div class="infinite-loader">
                        <span
                            class="loading-spinner"
                            aria-hidden="true"
                        ></span>

                        <span>
                            Memuat riwayat berikutnya...
                        </span>
                    </div>
                `;

                if (observer) {
                    observer.observe(sentinel);
                }

                loadNextPage();
            },
            {
                once: true
            }
        );
    }

    observer = new IntersectionObserver(
        function (entries) {
            entries.forEach(function (entry) {
                if (
                    entry.isIntersecting
                    && !isLoading
                    && !isFinished
                ) {
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