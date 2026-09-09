@extends('layouts.app', ['title' => 'Cek Reservasi | SAPA RSBM'])

@section('content')

@php
    $mainMenuUrl = Route::has('layanan.menu')
        ? route('layanan.menu')
        : url('/layanan/menu');

    $logoutRouteName = Route::has('layanan.logout')
        ? 'layanan.logout'
        : (Route::has('logout') ? 'logout' : null);

    /*
    |--------------------------------------------------------------------------
    | Endpoint internal cek Surat Kontrol BPJS
    |--------------------------------------------------------------------------
    */
    $bpjsCheckBaseUrl = Route::has('bpjs.surat-kontrol.check')
        ? route('bpjs.surat-kontrol.check')
        : (
            Route::has('bpjs.surat-kontrol.index')
                ? route('bpjs.surat-kontrol.index')
                : null
        );
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

    .reservation-page,
    .reservation-page * {
        box-sizing: border-box;
    }

    .reservation-page {
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

    .reservation-page::before,
    .reservation-page::after {
        position: absolute;
        border-radius: 999px;
        content: "";
        pointer-events: none;
    }

    .reservation-page::before {
        top: -170px;
        right: -150px;
        width: 350px;
        height: 350px;
        border: 46px solid rgba(38, 53, 143, .022);
    }

    .reservation-page::after {
        bottom: -190px;
        left: -160px;
        width: 360px;
        height: 360px;
        border: 52px solid rgba(25, 200, 61, .022);
    }

    .reservation-container {
        position: relative;
        z-index: 1;
        width: min(1180px, 100%);
        margin: 0 auto;
    }

    /* =========================================================
       TOPBAR RSBM
       ========================================================= */

    .reservation-topbar {
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

    .reservation-brand {
        display: flex;
        min-width: 0;
        align-items: center;
        gap: 10px;
    }

    .reservation-logo-shell {
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

    .reservation-logo {
        width: 37px;
        height: 37px;
        object-fit: contain;
    }

    .reservation-logo-fallback {
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

    .reservation-government {
        color: #818b9a;
        font-size: 8px;
        font-weight: 900;
        letter-spacing: .1em;
        text-transform: uppercase;
    }

    .reservation-hospital {
        margin-top: 1px;
        color: var(--rsbm-blue-dark);
        font-size: 14px;
        font-weight: 950;
        letter-spacing: -.01em;
    }

    .reservation-location {
        margin-top: 1px;
        color: #98a1af;
        font-size: 9px;
        font-weight: 700;
    }

    .reservation-top-actions,
    .reservation-bottom-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 8px;
        flex-wrap: wrap;
    }

    .reservation-logout-form {
        margin: 0;
    }

    .reservation-nav-button {
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
            border-color .18s ease;
    }

    .reservation-nav-button:hover {
        transform: translateY(-1px);
        text-decoration: none;
    }

    .reservation-nav-button.is-menu {
        border-color: #c7d2fe;
        background: var(--rsbm-blue-soft);
        color: var(--rsbm-blue);
    }

    .reservation-nav-button.is-menu:hover {
        border-color: #a5b4fc;
        background: #e5e9ff;
        color: var(--rsbm-blue-dark);
    }

    .reservation-nav-button.is-logout {
        border-color: #fecaca;
        background: #fff5f5;
        color: #b91c1c;
    }

    .reservation-nav-button.is-logout:hover {
        border-color: #fca5a5;
        background: #fee2e2;
        color: #991b1b;
    }

    /* =========================================================
       HEADER
       ========================================================= */

    .reservation-header {
        max-width: 780px;
        margin-bottom: 20px;
    }

    .reservation-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 6px 10px;
        border: 1px solid rgba(38, 53, 143, .09);
        border-radius: 999px;
        background: rgba(255,255,255,.85);
        color: var(--rsbm-blue-dark);
        font-size: 9px;
        font-weight: 900;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .reservation-eyebrow::before {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: var(--rsbm-green);
        box-shadow: 0 0 0 4px rgba(25, 200, 61, .11);
        content: "";
    }

    .reservation-title {
        margin: 10px 0 7px;
        color: var(--rsbm-text);
        font-size: clamp(29px, 4.3vw, 43px);
        font-weight: 950;
        line-height: 1.05;
        letter-spacing: -.04em;
    }

    .reservation-title span {
        color: var(--rsbm-blue);
    }

    .reservation-description {
        max-width: 720px;
        margin: 0;
        color: var(--rsbm-muted);
        font-size: 13px;
        line-height: 1.6;
    }

    /* =========================================================
       PATIENT CARD
       ========================================================= */

    .patient-card {
        display: grid;
        grid-template-columns: minmax(0, 1.4fr) repeat(3, minmax(120px, .72fr));
        margin-bottom: 16px;
        overflow: hidden;
        border: 1px solid rgba(217, 224, 234, .96);
        border-radius: 18px;
        background: rgba(255, 255, 255, .97);
        box-shadow: 0 12px 30px rgba(28, 39, 90, .055);
    }

    .patient-info-item {
        min-width: 0;
        padding: 15px 18px;
        background: rgba(255,255,255,.98);
    }

    .patient-info-item + .patient-info-item {
        border-left: 1px solid var(--rsbm-line);
    }

    .patient-info-label {
        display: block;
        margin-bottom: 5px;
        color: #98a1af;
        font-size: 9px;
        font-weight: 900;
        letter-spacing: .04em;
        text-transform: uppercase;
    }

    .patient-info-value {
        display: block;
        overflow: hidden;
        color: var(--rsbm-text);
        font-size: 13px;
        font-weight: 900;
        line-height: 1.45;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    /* =========================================================
       SUMMARY
       ========================================================= */

    .reservation-summary {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 12px;
    }

    .reservation-total {
        color: #7b8797;
        font-size: 11px;
        font-weight: 750;
    }

    .reservation-total strong {
        color: var(--rsbm-blue-dark);
        font-size: 13px;
        font-weight: 950;
    }

    /* =========================================================
       RESERVATION LIST
       ========================================================= */

    .reservation-list {
        display: grid;
        gap: 13px;
    }

    .reservation-card {
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(217, 224, 234, .96);
        border-radius: 18px;
        background: rgba(255,255,255,.98);
        box-shadow: 0 12px 30px rgba(28, 39, 90, .055);
    }

    .reservation-card::before {
        display: block;
        height: 3px;
        background: linear-gradient(
            90deg,
            var(--rsbm-blue) 0 78%,
            var(--rsbm-green) 78% 100%
        );
        content: "";
    }

    .reservation-card-main {
        display: grid;
        grid-template-columns:
            minmax(180px, .7fr)
            minmax(0, 1.35fr)
            minmax(145px, .55fr);
        gap: 18px;
        align-items: center;
        padding: 16px 18px;
        border-bottom: 1px solid var(--rsbm-line);
        background:
            linear-gradient(
                90deg,
                rgba(238, 241, 255, .55),
                rgba(255, 255, 255, 0)
            );
    }

    .reservation-date {
        display: flex;
        align-items: center;
        gap: 11px;
    }

    .date-icon {
        display: grid;
        width: 42px;
        height: 42px;
        flex: 0 0 42px;
        place-items: center;
        border-radius: 11px;
        background: var(--rsbm-blue-soft);
        color: var(--rsbm-blue);
    }

    .date-content strong {
        display: block;
        margin-bottom: 3px;
        color: var(--rsbm-text);
        font-size: 13px;
        font-weight: 950;
    }

    .date-content span {
        color: #7b8797;
        font-size: 10px;
        font-weight: 750;
    }

    .reservation-service {
        min-width: 0;
    }

    .reservation-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
        margin-bottom: 7px;
    }

    .reservation-badge {
        display: inline-flex;
        min-height: 22px;
        align-items: center;
        padding: 3px 8px;
        border-radius: 999px;
        font-size: 9px;
        font-weight: 900;
        line-height: 1;
    }

    .reservation-badge.is-status {
        background: var(--rsbm-green-soft);
        color: var(--rsbm-green-dark);
    }

    .reservation-badge.is-bpjs {
        background: var(--rsbm-blue-soft);
        color: var(--rsbm-blue);
    }

    .reservation-badge.is-type {
        background: #f1f5f9;
        color: #64748b;
    }

    .reservation-service h2 {
        margin: 0 0 5px;
        color: var(--rsbm-text);
        font-size: 16px;
        font-weight: 950;
        line-height: 1.35;
        letter-spacing: -.02em;
    }

    .reservation-doctor {
        display: flex;
        align-items: center;
        gap: 6px;
        color: #64748b;
        font-size: 11px;
        font-weight: 750;
    }

    .reservation-queue {
        text-align: right;
    }

    .queue-label {
        display: block;
        margin-bottom: 4px;
        color: #98a1af;
        font-size: 8.5px;
        font-weight: 900;
        letter-spacing: .04em;
        text-transform: uppercase;
    }

    .queue-number {
        color: var(--rsbm-blue);
        font-size: 29px;
        font-weight: 950;
        line-height: 1;
        letter-spacing: -.03em;
    }

    .reservation-details {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        background: #fbfcfe;
    }

    .detail-item {
        min-width: 0;
        padding: 12px 15px;
    }

    .detail-item + .detail-item {
        border-left: 1px solid var(--rsbm-line);
    }

    .detail-label {
        display: block;
        margin-bottom: 4px;
        color: #98a1af;
        font-size: 8px;
        font-weight: 900;
        letter-spacing: .04em;
        text-transform: uppercase;
    }

    .detail-value {
        display: block;
        overflow-wrap: anywhere;
        color: #4b5565;
        font-size: 10.5px;
        font-weight: 800;
        line-height: 1.45;
    }

    /* =========================================================
       EMPTY
       ========================================================= */

    .empty-state {
        padding: 42px 20px;
        border: 1px solid rgba(217, 224, 234, .96);
        border-radius: 18px;
        background: rgba(255,255,255,.97);
        text-align: center;
        box-shadow: 0 12px 30px rgba(28, 39, 90, .055);
    }

    .empty-icon {
        display: grid;
        width: 56px;
        height: 56px;
        margin: 0 auto 14px;
        place-items: center;
        border-radius: 16px;
        background: var(--rsbm-blue-soft);
        color: var(--rsbm-blue);
    }

    .empty-state h2 {
        margin: 0;
        color: var(--rsbm-text);
        font-size: 18px;
        font-weight: 950;
    }

    .empty-state p {
        max-width: 440px;
        margin: 7px auto 0;
        color: var(--rsbm-muted);
        font-size: 12px;
        line-height: 1.6;
    }

    /* =========================================================
       FOOTER NAV
       ========================================================= */

    .reservation-bottom-actions {
        justify-content: center;
        margin-top: 18px;
        padding-top: 16px;
        border-top: 1px solid rgba(227, 232, 239, .9);
    }


    /* =========================================================
       ACTION BUTTONS
       ========================================================= */

    .reservation-card-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 8px;
        padding: 11px 15px 13px;
        border-top: 1px solid var(--rsbm-line);
        background: #fff;
    }

    .reservation-action-button {
        display: inline-flex;
        min-height: 34px;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 0 11px;
        border: 1px solid transparent;
        border-radius: 10px;
        background: #fff;
        font: inherit;
        font-size: 9.5px;
        font-weight: 900;
        line-height: 1;
        cursor: pointer;
        transition:
            transform .15s ease,
            background .15s ease,
            border-color .15s ease,
            box-shadow .15s ease;
    }

    .reservation-action-button:hover {
        transform: translateY(-1px);
    }

    .reservation-action-button.is-detail {
        border-color: #d6dffc;
        background: var(--rsbm-blue-soft);
        color: var(--rsbm-blue);
    }

    .reservation-action-button.is-detail:hover {
        border-color: #b8c5f5;
        background: #e5e9ff;
    }

    .reservation-action-button.is-bpjs {
        border-color: #ccebd7;
        background: var(--rsbm-green-soft);
        color: var(--rsbm-green-dark);
    }

    .reservation-action-button.is-bpjs:hover {
        border-color: #a9dfbb;
        background: #dcf8e5;
    }

    .reservation-action-button[disabled] {
        opacity: .58;
        cursor: wait;
        transform: none;
    }

    /* =========================================================
       MODAL DETAIL RESERVASI / SURAT KONTROL
       ========================================================= */

    .reservation-modal-backdrop {
        position: fixed;
        z-index: 11000;
        inset: 0;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 18px;
        background: rgba(15, 35, 75, .52);
        backdrop-filter: blur(5px);
        -webkit-backdrop-filter: blur(5px);
    }

    .reservation-modal-backdrop.is-open {
        display: flex;
    }

    .reservation-modal {
        width: min(100%, 450px);
        max-height: min(86vh, 760px);
        overflow: hidden;
        border: 1px solid rgba(38, 53, 143, .10);
        border-radius: 22px;
        background: #fff;
        box-shadow: 0 28px 80px rgba(15, 45, 115, .24);
    }

    .reservation-modal-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 17px 18px 14px;
        border-bottom: 1px solid #eaf0f7;
        background: linear-gradient(135deg, #eef2ff, #f9fbff);
    }

    .reservation-modal-kicker {
        margin-bottom: 3px;
        color: var(--rsbm-green-dark);
        font-size: 9px;
        font-weight: 950;
        text-transform: uppercase;
        letter-spacing: .08em;
    }

    .reservation-modal-title {
        margin: 0;
        color: var(--rsbm-blue-dark);
        font-size: 17px;
        font-weight: 950;
        line-height: 1.2;
    }

    .reservation-modal-close {
        display: grid;
        width: 34px;
        height: 34px;
        flex: 0 0 34px;
        place-items: center;
        border: 0;
        border-radius: 10px;
        background: #fff;
        color: #64748b;
        cursor: pointer;
        box-shadow: 0 3px 10px rgba(15, 45, 115, .08);
    }

    .reservation-modal-body {
        max-height: calc(min(86vh, 760px) - 68px);
        overflow-y: auto;
        padding: 16px 18px 20px;
    }


    /* =========================================================
       QR / BARCODE NOMOR RESERVASI
       Sama konsepnya dengan modal menu.blade
       ========================================================= */

    .reservation-detail-qr {
        display: none;
        margin-bottom: 14px;
        padding: 14px;
        border: 1px solid #dbe4fb;
        border-radius: 16px;
        background:
            linear-gradient(
                180deg,
                #ffffff 0%,
                #f8faff 100%
            );
        text-align: center;
    }

    .reservation-detail-qr.is-visible {
        display: block;
    }

    .reservation-detail-qr-box {
        display: grid;
        width: 174px;
        min-height: 174px;
        margin: 0 auto;
        place-items: center;
        padding: 8px;
        border: 1px solid #e2e8f4;
        border-radius: 14px;
        background: #fff;
        box-shadow:
            0 7px 20px rgba(28, 39, 90, .08);
    }

    .reservation-detail-qr-box svg {
        display: block;
        width: 156px;
        height: 156px;
    }

    .reservation-detail-qr-title {
        margin-top: 9px;
        color: var(--rsbm-blue-dark);
        font-size: 11px;
        font-weight: 950;
    }

    .reservation-detail-qr-number {
        margin-top: 3px;
        overflow-wrap: anywhere;
        color: #4f5f8d;
        font-size: 11px;
        font-weight: 900;
        letter-spacing: .06em;
    }

    .reservation-detail-qr-help {
        margin-top: 5px;
        color: #7a899f;
        font-size: 8.5px;
        font-weight: 650;
        line-height: 1.45;
    }

    .reservation-detail-qr-error {
        display: flex;
        min-height: 156px;
        align-items: center;
        justify-content: center;
        padding: 12px;
        color: #9a5e0b;
        font-size: 10px;
        font-weight: 800;
        line-height: 1.45;
        text-align: center;
    }

    .reservation-modal-highlight {
        margin-bottom: 13px;
        padding: 13px 14px;
        border: 1px solid #dbe4fb;
        border-radius: 15px;
        background: linear-gradient(135deg, #f7f9ff, #eef2ff);
    }

    .reservation-modal-highlight-label {
        color: #718096;
        font-size: 8.5px;
        font-weight: 850;
        letter-spacing: .04em;
        text-transform: uppercase;
    }

    .reservation-modal-highlight-value {
        margin-top: 3px;
        overflow-wrap: anywhere;
        color: var(--rsbm-blue-dark);
        font-size: 17px;
        font-weight: 950;
        letter-spacing: .025em;
    }

    .reservation-modal-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 9px;
    }

    .reservation-modal-item {
        min-width: 0;
        padding: 10px 11px;
        border: 1px solid #e6edf5;
        border-radius: 12px;
        background: #fff;
    }

    .reservation-modal-item.is-wide {
        grid-column: 1 / -1;
    }

    .reservation-modal-label {
        margin-bottom: 3px;
        color: #8290a5;
        font-size: 8.5px;
        font-weight: 850;
    }

    .reservation-modal-value {
        overflow-wrap: anywhere;
        color: #25355f;
        font-size: 11px;
        font-weight: 850;
        line-height: 1.4;
    }

    .reservation-modal-value.is-status {
        color: var(--rsbm-green-dark);
    }

    /* BPJS result */
    .bpjs-modal-loading {
        display: none;
        min-height: 210px;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        gap: 11px;
        color: #60728c;
        text-align: center;
    }

    .bpjs-modal-loading.is-visible {
        display: flex;
    }

    .bpjs-modal-spinner {
        width: 34px;
        height: 34px;
        border: 3px solid #dbe8f6;
        border-top-color: var(--rsbm-blue);
        border-radius: 50%;
        animation: reservationBpjsSpin .8s linear infinite;
    }

    @keyframes reservationBpjsSpin {
        to { transform: rotate(360deg); }
    }

    .bpjs-modal-loading-title {
        color: var(--rsbm-blue-dark);
        font-size: 11px;
        font-weight: 950;
    }

    .bpjs-modal-loading-text {
        max-width: 300px;
        font-size: 9px;
        line-height: 1.45;
    }

    .bpjs-modal-status {
        display: none;
        margin-bottom: 14px;
        padding: 12px 13px;
        border: 1px solid #dbe9f8;
        border-radius: 15px;
        background: #f7fbff;
    }

    .bpjs-modal-status.is-visible {
        display: flex;
        align-items: flex-start;
        gap: 10px;
    }

    .bpjs-modal-status.is-success {
        border-color: #ccebd9;
        background: #eefbf4;
        color: #137a48;
    }

    .bpjs-modal-status.is-warning {
        border-color: #f2dfad;
        background: #fff9e9;
        color: #94600a;
    }

    .bpjs-modal-status.is-danger {
        border-color: #f0cccc;
        background: #fff4f4;
        color: #b63838;
    }

    .bpjs-modal-status-icon {
        display: grid;
        width: 32px;
        height: 32px;
        flex: 0 0 32px;
        place-items: center;
        border-radius: 10px;
        background: rgba(255,255,255,.80);
        font-size: 15px;
        font-weight: 950;
    }

    .bpjs-modal-status-title {
        font-size: 11px;
        font-weight: 950;
        line-height: 1.35;
    }

    .bpjs-modal-status-message {
        margin-top: 2px;
        font-size: 9.5px;
        font-weight: 650;
        line-height: 1.45;
        opacity: .88;
    }

    .bpjs-modal-result {
        display: none;
    }

    .bpjs-modal-result.is-visible {
        display: block;
    }

    .bpjs-modal-note {
        display: none;
        margin-top: 12px;
        padding: 10px 11px;
        border-radius: 12px;
        background: #fff8e7;
        color: #8d620e;
        font-size: 9px;
        font-weight: 700;
        line-height: 1.45;
    }

    .bpjs-modal-note.is-visible {
        display: block;
    }

    /* =========================================================
       RESPONSIVE
       ========================================================= */

    @media (max-width: 900px) {
        .patient-card {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .patient-info-item:nth-child(odd) {
            border-left: 0;
        }

        .patient-info-item:nth-child(n + 3) {
            border-top: 1px solid var(--rsbm-line);
        }

        .reservation-card-main {
            grid-template-columns: 1fr 1fr;
        }

        .reservation-queue {
            grid-column: 1 / -1;
            text-align: left;
        }

        .reservation-details {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .detail-item:nth-child(odd) {
            border-left: 0;
        }

        .detail-item:nth-child(n + 3) {
            border-top: 1px solid var(--rsbm-line);
        }
    }

    @media (max-width: 640px) {
        .reservation-page {
            padding: 10px 10px 38px;
        }

        .reservation-topbar {
            align-items: stretch;
            flex-direction: column;
            padding: 10px;
        }

        .reservation-top-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            width: 100%;
        }

        .reservation-top-actions .reservation-nav-button,
        .reservation-top-actions .reservation-logout-form,
        .reservation-top-actions .reservation-logout-form .reservation-nav-button {
            width: 100%;
        }

        .reservation-title {
            font-size: 30px;
        }

        .reservation-description {
            font-size: 12px;
        }

        .patient-card {
            grid-template-columns: 1fr;
        }

        .patient-info-item,
        .patient-info-item:nth-child(odd) {
            border-left: 0;
        }

        .patient-info-item + .patient-info-item {
            border-top: 1px solid var(--rsbm-line);
        }

        .reservation-card-main {
            grid-template-columns: 1fr;
            gap: 14px;
            padding: 15px;
        }

        .reservation-queue {
            grid-column: auto;
        }

        .queue-number {
            font-size: 27px;
        }

        .reservation-details {
            grid-template-columns: 1fr;
        }

        .detail-item,
        .detail-item:nth-child(odd) {
            border-left: 0;
        }

        .detail-item + .detail-item {
            border-top: 1px solid var(--rsbm-line);
        }

        .reservation-bottom-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        .reservation-bottom-actions .reservation-nav-button,
        .reservation-bottom-actions .reservation-logout-form,
        .reservation-bottom-actions .reservation-logout-form .reservation-nav-button {
            width: 100%;
        }

        .reservation-card-actions {
            display: grid;
            grid-template-columns: 1fr;
        }

        .reservation-action-button {
            width: 100%;
        }

        .reservation-modal {
            border-radius: 18px;
        }

        .reservation-modal-grid {
            grid-template-columns: 1fr;
        }

        .reservation-modal-item.is-wide {
            grid-column: auto;
        }
    }
</style>

<section class="reservation-page">
    <div class="reservation-container">

        <div class="reservation-topbar">
            <div class="reservation-brand">
                <div class="reservation-logo-shell">
                    <img
                        class="reservation-logo"
                        src="{{ asset('images/logo-rsbm.png') }}"
                        alt="Logo RSUD Bali Mandara"
                        onerror="this.style.display='none'; this.nextElementSibling.style.display='grid';"
                    >
                    <span
                        class="reservation-logo-fallback"
                        aria-hidden="true"
                    >
                        +
                    </span>
                </div>

                <div>
                    <div class="reservation-government">
                        Pemerintah Provinsi Bali
                    </div>
                    <div class="reservation-hospital">
                        RSUD Bali Mandara
                    </div>
                    <div class="reservation-location">
                        Sanur · Denpasar · Bali
                    </div>
                </div>
            </div>

            <div class="reservation-top-actions">
                <a
                    href="{{ $mainMenuUrl }}"
                    class="reservation-nav-button is-menu"
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
                        class="reservation-logout-form"
                    >
                        @csrf

                        <button
                            type="submit"
                            class="reservation-nav-button is-logout"
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

        <header class="reservation-header">
            <span class="reservation-eyebrow">
                SAPA RSBM · Reservasi
            </span>

           
        </header>

        <div class="patient-card">
            <div class="patient-info-item">
                <span class="patient-info-label">
                    Nama Pasien
                </span>
                <span class="patient-info-value">
                    {{ $patientInfo['nama'] ?: '-' }}
                </span>
            </div>

            <div class="patient-info-item">
                <span class="patient-info-label">
                    Nomor RM
                </span>
                <span class="patient-info-value">
                    {{ $patientInfo['medical_record'] ?: '-' }}
                </span>
            </div>

            <div class="patient-info-item">
                <span class="patient-info-label">
                    Nomor BPJS
                </span>
                <span class="patient-info-value">
                    {{ $patientInfo['no_bpjs'] ?: '-' }}
                </span>
            </div>

            <div class="patient-info-item">
                <span class="patient-info-label">
                    Jenis Kelamin
                </span>
                <span class="patient-info-value">
                    {{ $patientInfo['jenis_kelamin'] ?: '-' }}
                </span>
            </div>
        </div>

        <div class="reservation-summary">
            <div class="reservation-total">
                Ditemukan
                <strong>{{ number_format($total) }}</strong>
                reservasi
            </div>
        </div>

        @if($reservations->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">
                    <svg
                        width="28"
                        height="28"
                        viewBox="0 0 24 24"
                        fill="none"
                        aria-hidden="true"
                    >
                        <rect
                            x="3"
                            y="5"
                            width="18"
                            height="16"
                            rx="3"
                            stroke="currentColor"
                            stroke-width="2"
                        />
                        <path
                            d="M8 3V7M16 3V7M3 10H21"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                        />
                    </svg>
                </div>

                <h2>Belum ada reservasi</h2>

                <p>
                    Tidak ditemukan data reservasi pasien
                    pada sistem e-Reservasi RSUD Bali Mandara.
                </p>
            </div>
        @else
            <div class="reservation-list">
                @foreach($reservations as $reservation)
                    @php
                        $tanggalReservasi = data_get(
                            $reservation,
                            'tanggalreservasi'
                        );

                        $tanggalDisplay = '-';

                        if ($tanggalReservasi) {
                            try {
                                $tanggalDisplay =
                                    \Carbon\Carbon::parse(
                                        $tanggalReservasi
                                    )->format('d-m-Y');
                            } catch (\Throwable $e) {
                                $tanggalDisplay =
                                    $tanggalReservasi;
                            }
                        }

                        $noAntrian = data_get(
                            $reservation,
                            'noantrianpoli'
                        );

                        $status = data_get(
                            $reservation,
                            'status',
                            'Reservasi'
                        );

                        /*
                        |--------------------------------------------------------------------------
                        | Data untuk Cek Surat Kontrol BPJS
                        |--------------------------------------------------------------------------
                        | kodepolisubspesialis menjadi prioritas kode poli.
                        */
                        $noKartuBpjs = data_get(
                            $reservation,
                            'nobpjs',
                            data_get($patientInfo, 'no_bpjs')
                        );

                        $noSuratKontrol = data_get(
                            $reservation,
                            'nosuratkontrol',
                            data_get($reservation, 'noSuratKontrol')
                        );

                        $kodePoliSubspesialis = data_get(
                            $reservation,
                            'kodepolisubspesialis',
                            data_get(
                                $reservation,
                                'kodesubspesialis',
                                data_get(
                                    $reservation,
                                    'kodePoliSubspesialis',
                                    data_get(
                                        $reservation,
                                        'kodesubspesialisbpjs',
                                        data_get(
                                            $reservation,
                                            'kodepoli'
                                        )
                                    )
                                )
                            )
                        );

                        $tanggalBpjs = null;

                        if ($tanggalReservasi) {
                            try {
                                $tanggalBpjs = \Carbon\Carbon::parse(
                                    $tanggalReservasi
                                )->format('Y-m-d');
                            } catch (\Throwable $e) {
                                $tanggalBpjs = substr(
                                    (string) $tanggalReservasi,
                                    0,
                                    10
                                );
                            }
                        }

                        $bpjsCheckUrl = null;

                        if (
                            $bpjsCheckBaseUrl
                            && ($noSuratKontrol || $noKartuBpjs)
                        ) {
                            $bpjsCheckParams = array_filter(
                                [
                                    'nosuratkontrol' => $noSuratKontrol,
                                    'nokartu' => $noKartuBpjs,
                                    'tanggalreservasi' => $tanggalBpjs,
                                    'kodepoli' => $kodePoliSubspesialis,
                                ],
                                static function ($value) {
                                    return $value !== null
                                        && $value !== '';
                                }
                            );

                            $bpjsCheckUrl =
                                $bpjsCheckBaseUrl
                                . '?'
                                . http_build_query(
                                    $bpjsCheckParams
                                );
                        }

                        $isBpjsReservation =
                            strtoupper(
                                trim(
                                    (string) data_get(
                                        $reservation,
                                        'kelompokpasien',
                                        ''
                                    )
                                )
                            ) === 'BPJS'
                            || !empty($noKartuBpjs);
                    @endphp

                    <article class="reservation-card">

                        <div class="reservation-card-main">

                            <div class="reservation-date">
                                <div class="date-icon">
                                    <svg
                                        width="21"
                                        height="21"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        aria-hidden="true"
                                    >
                                        <rect
                                            x="3"
                                            y="5"
                                            width="18"
                                            height="16"
                                            rx="3"
                                            stroke="currentColor"
                                            stroke-width="2"
                                        />
                                        <path
                                            d="M8 3V7M16 3V7M3 10H21"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                        />
                                    </svg>
                                </div>

                                <div class="date-content">
                                    <strong>
                                        {{ $tanggalDisplay }}
                                    </strong>
                                    <span>
                                        {{ data_get(
                                            $reservation,
                                            'jamreservasi',
                                            '-'
                                        ) }}
                                    </span>
                                </div>
                            </div>

                            <div class="reservation-service">
                                <div class="reservation-badges">

                                    <span class="reservation-badge is-status">
                                        {{ $status }}
                                    </span>

                                    @if(data_get($reservation, 'kelompokpasien'))
                                        <span class="reservation-badge is-bpjs">
                                            {{ data_get(
                                                $reservation,
                                                'kelompokpasien'
                                            ) }}
                                        </span>
                                    @endif

                                    @if(data_get($reservation, 'type'))
                                        <span class="reservation-badge is-type">
                                            Pasien
                                            {{ data_get(
                                                $reservation,
                                                'type'
                                            ) }}
                                        </span>
                                    @endif
                                </div>

                                <h2>
                                    {{ data_get(
                                        $reservation,
                                        'namaruangan',
                                        '-'
                                    ) }}
                                </h2>

                                <div class="reservation-doctor">
                                    <svg
                                        width="14"
                                        height="14"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        aria-hidden="true"
                                    >
                                        <circle
                                            cx="12"
                                            cy="8"
                                            r="4"
                                            stroke="currentColor"
                                            stroke-width="2"
                                        />
                                        <path
                                            d="M5 21C5.8 16.9 8.2 15 12 15C15.8 15 18.2 16.9 19 21"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                        />
                                    </svg>

                                    {{ data_get(
                                        $reservation,
                                        'dokter',
                                        '-'
                                    ) }}
                                </div>
                            </div>

                            <div class="reservation-queue">
                                <span class="queue-label">
                                    No. Antrean Poli
                                </span>

                                <div class="queue-number">
                                    {{ $noAntrian !== null
                                        ? $noAntrian
                                        : '-' }}
                                </div>
                            </div>

                        </div>

                        <div class="reservation-details">

                            <div class="detail-item">
                                <span class="detail-label">
                                    Kode Reservasi
                                </span>
                                <span class="detail-value">
                                    {{ data_get(
                                        $reservation,
                                        'noreservasi',
                                        '-'
                                    ) }}
                                </span>
                            </div>

                            <div class="detail-item">
                                <span class="detail-label">
                                    No. Surat Kontrol
                                </span>
                                <span class="detail-value">
                                    {{ data_get(
                                        $reservation,
                                        'nosuratkontrol',
                                        '-'
                                    ) }}
                                </span>
                            </div>

                            <div class="detail-item">
                                <span class="detail-label">
                                    No. Rujukan
                                </span>
                                <span class="detail-value">
                                    {{ data_get(
                                        $reservation,
                                        'norujukan',
                                        '-'
                                    ) }}
                                </span>
                            </div>

                            <div class="detail-item">
                                <span class="detail-label">
                                    Loket
                                </span>
                                <span class="detail-value">
                                    {{ data_get(
                                        $reservation,
                                        'loketkiosk',
                                        '-'
                                    ) }}
                                </span>
                            </div>

                        </div>

                        <div class="reservation-card-actions">
                            <button
                                type="button"
                                class="reservation-action-button is-detail js-reservation-detail"
                                data-noreservasi="{{ data_get($reservation, 'noreservasi', '') }}"
                                data-dokter="{{ data_get($reservation, 'dokter', '') }}"
                                data-poli="{{ data_get($reservation, 'poli', data_get($reservation, 'namaruangan', '')) }}"
                                data-tanggal="{{ data_get($reservation, 'tanggalreservasi', '') }}"
                                data-jam="{{ data_get($reservation, 'jamreservasi', data_get($reservation, 'jam', '')) }}"
                                data-penjamin="{{ data_get($reservation, 'kelompokpasien', '') }}"
                                data-nobpjs="{{ $noKartuBpjs ?: '' }}"
                                data-nosuratkontrol="{{ $noSuratKontrol ?: '' }}"
                                data-status-registrasi="{{ data_get($reservation, 'status_registrasi', '') }}"
                                data-status-pelayanan="{{ data_get($reservation, 'status_pasien', data_get($reservation, 'label_statusperiksa', $status)) }}"
                                data-noregistrasi="{{ data_get($reservation, 'noregistrasi', '') }}"
                                data-noantrian="{{ data_get($reservation, 'noantrianpoli', data_get($reservation, 'noantrian', '')) }}"
                                data-norujukan="{{ data_get($reservation, 'norujukan', '') }}"
                                data-kodepoli="{{ $kodePoliSubspesialis ?: '' }}"
                                aria-label="Lihat detail reservasi"
                            >
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M2.5 12C4.8 7.8 8 5.7 12 5.7C16 5.7 19.2 7.8 21.5 12C19.2 16.2 16 18.3 12 18.3C8 18.3 4.8 16.2 2.5 12Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                    <circle cx="12" cy="12" r="2.6" stroke="currentColor" stroke-width="2"/>
                                </svg>
                                Detail Reservasi
                            </button>

                            @if($isBpjsReservation)
                                <button
                                    type="button"
                                    class="reservation-action-button is-bpjs js-bpjs-control-check"
                                    data-check-url="{{ $bpjsCheckUrl ?: '' }}"
                                    data-nokartu="{{ $noKartuBpjs ?: '' }}"
                                    data-tanggal="{{ $tanggalBpjs ?: '' }}"
                                    data-kodepoli="{{ $kodePoliSubspesialis ?: '' }}"
                                    data-nosuratkontrol="{{ $noSuratKontrol ?: '' }}"
                                    aria-label="Cek Surat Kontrol BPJS"
                                >
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M7 3.5H14.5L18.5 7.5V20.5H7C5.9 20.5 5 19.6 5 18.5V5.5C5 4.4 5.9 3.5 7 3.5Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                        <path d="M14 3.5V8H18.5" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                        <path d="M8.5 12H15M8.5 15.5H13" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    </svg>
                                    Cek Surat Kontrol BPJS
                                </button>
                            @endif
                        </div>

                    </article>
                @endforeach
            </div>
        @endif


        {{-- =========================================================
             MODAL DETAIL RESERVASI
        ========================================================== --}}
        <div
            id="reservation-detail-backdrop"
            class="reservation-modal-backdrop"
            role="dialog"
            aria-modal="true"
            aria-labelledby="reservation-detail-title"
        >
            <div class="reservation-modal">
                <div class="reservation-modal-head">
                    <div>
                        <div class="reservation-modal-kicker">
                            NADI RSBM
                        </div>
                        <h3
                            id="reservation-detail-title"
                            class="reservation-modal-title"
                        >
                            Detail Kontrol / Reservasi
                        </h3>
                    </div>

                    <button
                        id="reservation-detail-close"
                        class="reservation-modal-close"
                        type="button"
                        aria-label="Tutup detail reservasi"
                    >
                        <svg width="19" height="19" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M6 6L18 18M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </button>
                </div>

                <div class="reservation-modal-body">

                    {{-- QR Code / Barcode Nomor Reservasi --}}
                    <div
                        id="reservation-detail-qr-section"
                        class="reservation-detail-qr"
                    >
                        <div
                            id="reservation-detail-qr-code"
                            class="reservation-detail-qr-box"
                            aria-label="QR Code Nomor Reservasi"
                        ></div>

                        <div class="reservation-detail-qr-title">
                            QR Code Nomor Reservasi
                        </div>

                        <div
                            id="reservation-detail-qr-number"
                            class="reservation-detail-qr-number"
                        >
                            -
                        </div>

                        <div class="reservation-detail-qr-help">
                            Tunjukkan QR Code ini saat diperlukan
                            untuk identifikasi reservasi.
                        </div>
                    </div>

                    <div class="reservation-modal-highlight">
                        <div class="reservation-modal-highlight-label">
                            KODE BOOKING / NOMOR RESERVASI
                        </div>
                        <div
                            id="modal-detail-booking"
                            class="reservation-modal-highlight-value"
                        >
                            -
                        </div>
                    </div>

                    <div class="reservation-modal-grid">
                        <div class="reservation-modal-item is-wide">
                            <div class="reservation-modal-label">Dokter</div>
                            <div id="modal-detail-dokter" class="reservation-modal-value">-</div>
                        </div>

                        <div class="reservation-modal-item">
                            <div class="reservation-modal-label">Poliklinik</div>
                            <div id="modal-detail-poli" class="reservation-modal-value">-</div>
                        </div>

                        <div class="reservation-modal-item">
                            <div class="reservation-modal-label">Kode Poli Subspesialis BPJS</div>
                            <div id="modal-detail-kodepoli" class="reservation-modal-value">-</div>
                        </div>

                        <div class="reservation-modal-item">
                            <div class="reservation-modal-label">Tanggal Kontrol</div>
                            <div id="modal-detail-tanggal" class="reservation-modal-value">-</div>
                        </div>

                        <div class="reservation-modal-item">
                            <div class="reservation-modal-label">Jam Layanan</div>
                            <div id="modal-detail-jam" class="reservation-modal-value">-</div>
                        </div>

                        <div class="reservation-modal-item">
                            <div class="reservation-modal-label">Penjamin</div>
                            <div id="modal-detail-penjamin" class="reservation-modal-value">-</div>
                        </div>

                        <div class="reservation-modal-item">
                            <div class="reservation-modal-label">No. Kartu BPJS</div>
                            <div id="modal-detail-nobpjs" class="reservation-modal-value">-</div>
                        </div>

                        <div class="reservation-modal-item is-wide">
                            <div class="reservation-modal-label">No. Surat Kontrol</div>
                            <div id="modal-detail-nosurat" class="reservation-modal-value">-</div>
                        </div>

                        <div class="reservation-modal-item">
                            <div class="reservation-modal-label">Status Registrasi</div>
                            <div id="modal-detail-status-registrasi" class="reservation-modal-value is-status">-</div>
                        </div>

                        <div class="reservation-modal-item">
                            <div class="reservation-modal-label">Status Pelayanan</div>
                            <div id="modal-detail-status-pelayanan" class="reservation-modal-value is-status">-</div>
                        </div>

                        <div class="reservation-modal-item">
                            <div class="reservation-modal-label">No. Registrasi</div>
                            <div id="modal-detail-noregistrasi" class="reservation-modal-value">-</div>
                        </div>

                        <div class="reservation-modal-item">
                            <div class="reservation-modal-label">No. Antrean Poli</div>
                            <div id="modal-detail-noantrian" class="reservation-modal-value">-</div>
                        </div>

                        <div class="reservation-modal-item is-wide">
                            <div class="reservation-modal-label">No. Rujukan</div>
                            <div id="modal-detail-norujukan" class="reservation-modal-value">-</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- =========================================================
             MODAL CEK SURAT KONTROL BPJS
        ========================================================== --}}
        <div
            id="bpjs-control-backdrop"
            class="reservation-modal-backdrop"
            role="dialog"
            aria-modal="true"
            aria-labelledby="bpjs-control-title"
        >
            <div class="reservation-modal">
                <div class="reservation-modal-head">
                    <div>
                        <div class="reservation-modal-kicker">
                            BPJS KESEHATAN
                        </div>
                        <h3
                            id="bpjs-control-title"
                            class="reservation-modal-title"
                        >
                            Cek Surat Kontrol
                        </h3>
                    </div>

                    <button
                        id="bpjs-control-close"
                        class="reservation-modal-close"
                        type="button"
                        aria-label="Tutup hasil Surat Kontrol BPJS"
                    >
                        <svg width="19" height="19" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M6 6L18 18M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </button>
                </div>

                <div class="reservation-modal-body">
                    <div
                        id="bpjs-control-loading"
                        class="bpjs-modal-loading"
                    >
                        <div class="bpjs-modal-spinner"></div>
                        <div class="bpjs-modal-loading-title">
                            Memeriksa Surat Kontrol BPJS
                        </div>
                        <div class="bpjs-modal-loading-text">
                            Data dicocokkan menggunakan nomor Surat Kontrol
                            atau No. Kartu BPJS + tanggal reservasi +
                            kode poli subspesialis.
                        </div>
                    </div>

                    <div
                        id="bpjs-control-status"
                        class="bpjs-modal-status"
                    >
                        <div
                            id="bpjs-control-status-icon"
                            class="bpjs-modal-status-icon"
                        >
                            ✓
                        </div>
                        <div>
                            <div
                                id="bpjs-control-status-title"
                                class="bpjs-modal-status-title"
                            >
                                -
                            </div>
                            <div
                                id="bpjs-control-status-message"
                                class="bpjs-modal-status-message"
                            >
                                -
                            </div>
                        </div>
                    </div>

                    <div
                        id="bpjs-control-result"
                        class="bpjs-modal-result"
                    >
                        <div class="reservation-modal-highlight">
                            <div class="reservation-modal-highlight-label">
                                NOMOR SURAT KONTROL
                            </div>
                            <div
                                id="bpjs-result-no-surat"
                                class="reservation-modal-highlight-value"
                            >
                                -
                            </div>
                        </div>

                        <div class="reservation-modal-grid">
                            <div class="reservation-modal-item">
                                <div class="reservation-modal-label">
                                    No. Kartu BPJS
                                </div>
                                <div
                                    id="bpjs-result-no-kartu"
                                    class="reservation-modal-value"
                                >
                                    -
                                </div>
                            </div>

                            <div class="reservation-modal-item">
                                <div class="reservation-modal-label">
                                    Tanggal Rencana Kontrol
                                </div>
                                <div
                                    id="bpjs-result-tanggal"
                                    class="reservation-modal-value"
                                >
                                    -
                                </div>
                            </div>

                            <div class="reservation-modal-item is-wide">
                                <div class="reservation-modal-label">
                                    Poli Tujuan
                                </div>
                                <div
                                    id="bpjs-result-poli"
                                    class="reservation-modal-value"
                                >
                                    -
                                </div>
                            </div>

                            <div class="reservation-modal-item is-wide">
                                <div class="reservation-modal-label">
                                    Dokter
                                </div>
                                <div
                                    id="bpjs-result-dokter"
                                    class="reservation-modal-value"
                                >
                                    -
                                </div>
                            </div>

                            <div class="reservation-modal-item">
                                <div class="reservation-modal-label">
                                    Jenis Kontrol
                                </div>
                                <div
                                    id="bpjs-result-jenis"
                                    class="reservation-modal-value"
                                >
                                    -
                                </div>
                            </div>

                            <div class="reservation-modal-item">
                                <div class="reservation-modal-label">
                                    Tanggal Terbit
                                </div>
                                <div
                                    id="bpjs-result-terbit"
                                    class="reservation-modal-value"
                                >
                                    -
                                </div>
                            </div>

                            <div class="reservation-modal-item is-wide">
                                <div class="reservation-modal-label">
                                    SEP Asal Kontrol
                                </div>
                                <div
                                    id="bpjs-result-sep"
                                    class="reservation-modal-value"
                                >
                                    -
                                </div>
                            </div>

                            <div class="reservation-modal-item is-wide">
                                <div class="reservation-modal-label">
                                    Metode Pencarian
                                </div>
                                <div
                                    id="bpjs-result-metode"
                                    class="reservation-modal-value"
                                >
                                    -
                                </div>
                            </div>
                        </div>

                        <div
                            id="bpjs-result-note"
                            class="bpjs-modal-note"
                        ></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="reservation-bottom-actions">
            <a
                href="{{ $mainMenuUrl }}"
                class="reservation-nav-button is-menu"
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
                <span>Kembali ke Menu Utama</span>
            </a>

            @if($logoutRouteName)
                <form
                    method="POST"
                    action="{{ route($logoutRouteName) }}"
                    class="reservation-logout-form"
                >
                    @csrf

                    <button
                        type="submit"
                        class="reservation-nav-button is-logout"
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
</section>


<script>
/*
|--------------------------------------------------------------------------
| NADI QR Code Generator - Self Contained
|--------------------------------------------------------------------------
| QR Version 1-L, byte mode.
| Cocok untuk noreservasi pendek seperti: 347e71c.
| Tidak membutuhkan CDN / internet / file JS tambahan.
|--------------------------------------------------------------------------
*/
(function (global) {
    'use strict';

    const SIZE = 21;
    const DATA_CODEWORDS = 19;
    const ECC_CODEWORDS = 7;

    function utf8Bytes(text) {
        if (typeof TextEncoder !== 'undefined') {
            return Array.from(
                new TextEncoder().encode(
                    String(text)
                )
            );
        }

        const encoded = unescape(
            encodeURIComponent(
                String(text)
            )
        );

        const bytes = [];

        for (
            let i = 0;
            i < encoded.length;
            i++
        ) {
            bytes.push(
                encoded.charCodeAt(i) & 0xff
            );
        }

        return bytes;
    }

    function createGfTables() {
        const exp = new Array(512).fill(0);
        const log = new Array(256).fill(0);

        let x = 1;

        for (let i = 0; i < 255; i++) {
            exp[i] = x;
            log[x] = i;

            x <<= 1;

            if (x & 0x100) {
                x ^= 0x11d;
            }
        }

        for (let i = 255; i < 512; i++) {
            exp[i] = exp[i - 255];
        }

        return {
            exp: exp,
            log: log
        };
    }

    const GF = createGfTables();

    function gfMul(a, b) {
        if (a === 0 || b === 0) {
            return 0;
        }

        return GF.exp[
            GF.log[a] + GF.log[b]
        ];
    }

    function polyMul(a, b) {
        const out = new Array(
            a.length + b.length - 1
        ).fill(0);

        for (
            let i = 0;
            i < a.length;
            i++
        ) {
            for (
                let j = 0;
                j < b.length;
                j++
            ) {
                out[i + j] ^=
                    gfMul(
                        a[i],
                        b[j]
                    );
            }
        }

        return out;
    }

    function generatorPolynomial(ecCount) {
        let generator = [1];

        for (
            let i = 0;
            i < ecCount;
            i++
        ) {
            generator = polyMul(
                generator,
                [1, GF.exp[i]]
            );
        }

        return generator;
    }

    function reedSolomon(
        data,
        ecCount
    ) {
        const generator =
            generatorPolynomial(ecCount);

        let ecc =
            new Array(ecCount).fill(0);

        data.forEach(
            function (byte) {
                const factor =
                    byte ^ ecc[0];

                ecc = ecc.slice(1);
                ecc.push(0);

                for (
                    let i = 0;
                    i < ecCount;
                    i++
                ) {
                    ecc[i] ^=
                        gfMul(
                            generator[i + 1],
                            factor
                        );
                }
            }
        );

        return ecc;
    }

    function pushBits(
        bits,
        value,
        length
    ) {
        for (
            let i = length - 1;
            i >= 0;
            i--
        ) {
            bits.push(
                (value >> i) & 1
            );
        }
    }

    function createCodewords(text) {
        const bytes =
            utf8Bytes(text);

        /*
        | Version 1-L byte mode maksimal 17 byte.
        | noreservasi SIMRS umumnya pendek.
        */
        if (bytes.length > 17) {
            throw new Error(
                'No. Reservasi terlalu panjang untuk QR lokal.'
            );
        }

        const bits = [];

        // Mode byte = 0100
        pushBits(
            bits,
            0x4,
            4
        );

        // Character count untuk Version 1-9 = 8 bit
        pushBits(
            bits,
            bytes.length,
            8
        );

        bytes.forEach(
            function (byte) {
                pushBits(
                    bits,
                    byte,
                    8
                );
            }
        );

        const bitLimit =
            DATA_CODEWORDS * 8;

        const terminator =
            Math.min(
                4,
                bitLimit - bits.length
            );

        for (
            let i = 0;
            i < terminator;
            i++
        ) {
            bits.push(0);
        }

        while (
            bits.length % 8 !== 0
        ) {
            bits.push(0);
        }

        const data = [];

        for (
            let i = 0;
            i < bits.length;
            i += 8
        ) {
            let value = 0;

            for (
                let j = 0;
                j < 8;
                j++
            ) {
                value =
                    (value << 1)
                    | bits[i + j];
            }

            data.push(value);
        }

        const pads = [
            0xec,
            0x11
        ];

        let padIndex = 0;

        while (
            data.length <
            DATA_CODEWORDS
        ) {
            data.push(
                pads[
                    padIndex % 2
                ]
            );

            padIndex++;
        }

        return data.concat(
            reedSolomon(
                data,
                ECC_CODEWORDS
            )
        );
    }

    function bchDigit(value) {
        let digit = 0;

        while (value !== 0) {
            digit++;
            value >>>= 1;
        }

        return digit;
    }

    function bchTypeInfo(data) {
        const G15 = 0x537;
        const G15_MASK = 0x5412;

        let d = data << 10;

        while (
            bchDigit(d)
            - bchDigit(G15)
            >= 0
        ) {
            d ^=
                G15 << (
                    bchDigit(d)
                    - bchDigit(G15)
                );
        }

        return (
            ((data << 10) | d)
            ^ G15_MASK
        );
    }

    function setupFinder(
        matrix,
        row,
        col
    ) {
        for (
            let r = -1;
            r <= 7;
            r++
        ) {
            if (
                row + r < 0
                || row + r >= SIZE
            ) {
                continue;
            }

            for (
                let c = -1;
                c <= 7;
                c++
            ) {
                if (
                    col + c < 0
                    || col + c >= SIZE
                ) {
                    continue;
                }

                const dark =
                    (
                        r >= 0
                        && r <= 6
                        && (
                            c === 0
                            || c === 6
                        )
                    )
                    ||
                    (
                        c >= 0
                        && c <= 6
                        && (
                            r === 0
                            || r === 6
                        )
                    )
                    ||
                    (
                        r >= 2
                        && r <= 4
                        && c >= 2
                        && c <= 4
                    );

                matrix[
                    row + r
                ][
                    col + c
                ] = Boolean(dark);
            }
        }
    }

    function createMatrix(text) {
        const matrix =
            Array.from(
                {
                    length: SIZE
                },
                function () {
                    return new Array(
                        SIZE
                    ).fill(null);
                }
            );

        setupFinder(
            matrix,
            0,
            0
        );

        setupFinder(
            matrix,
            SIZE - 7,
            0
        );

        setupFinder(
            matrix,
            0,
            SIZE - 7
        );

        // Timing pattern
        for (
            let r = 8;
            r < SIZE - 8;
            r++
        ) {
            if (
                matrix[r][6] === null
            ) {
                matrix[r][6] =
                    r % 2 === 0;
            }
        }

        for (
            let c = 8;
            c < SIZE - 8;
            c++
        ) {
            if (
                matrix[6][c] === null
            ) {
                matrix[6][c] =
                    c % 2 === 0;
            }
        }

        /*
        | Error correction L = 01
        | Mask pattern = 000
        */
        const formatBits =
            bchTypeInfo(
                (1 << 3) | 0
            );

        for (
            let i = 0;
            i < 15;
            i++
        ) {
            const dark =
                (
                    (formatBits >> i)
                    & 1
                ) === 1;

            if (i < 6) {
                matrix[i][8] =
                    dark;
            } else if (i < 8) {
                matrix[i + 1][8] =
                    dark;
            } else {
                matrix[
                    SIZE - 15 + i
                ][8] = dark;
            }
        }

        for (
            let i = 0;
            i < 15;
            i++
        ) {
            const dark =
                (
                    (formatBits >> i)
                    & 1
                ) === 1;

            if (i < 8) {
                matrix[8][
                    SIZE - i - 1
                ] = dark;
            } else if (i < 9) {
                matrix[8][
                    15 - i
                ] = dark;
            } else {
                matrix[8][
                    15 - i - 1
                ] = dark;
            }
        }

        // Fixed dark module
        matrix[
            SIZE - 8
        ][8] = true;

        const codewords =
            createCodewords(text);

        let row = SIZE - 1;
        let inc = -1;
        let bitIndex = 7;
        let byteIndex = 0;

        for (
            let originalCol =
                SIZE - 1;
            originalCol > 0;
            originalCol -= 2
        ) {
            let col =
                originalCol;

            if (col <= 6) {
                col--;
            }

            while (true) {
                [
                    col,
                    col - 1
                ].forEach(
                    function (c) {
                        if (
                            matrix[row][c]
                            !== null
                        ) {
                            return;
                        }

                        let dark = false;

                        if (
                            byteIndex
                            < codewords.length
                        ) {
                            dark =
                                (
                                    (
                                        codewords[
                                            byteIndex
                                        ]
                                        >> bitIndex
                                    )
                                    & 1
                                ) === 1;
                        }

                        // Mask 0
                        if (
                            (row + c)
                            % 2 === 0
                        ) {
                            dark = !dark;
                        }

                        matrix[row][c] =
                            dark;

                        bitIndex--;

                        if (
                            bitIndex === -1
                        ) {
                            byteIndex++;
                            bitIndex = 7;
                        }
                    }
                );

                row += inc;

                if (
                    row < 0
                    || row >= SIZE
                ) {
                    row -= inc;
                    inc = -inc;
                    break;
                }
            }
        }

        return matrix;
    }

    function svgForMatrix(
        matrix,
        pixelSize
    ) {
        const quiet = 4;
        const totalModules =
            SIZE + quiet * 2;

        const rects = [];

        for (
            let r = 0;
            r < SIZE;
            r++
        ) {
            for (
                let c = 0;
                c < SIZE;
                c++
            ) {
                if (
                    matrix[r][c]
                ) {
                    rects.push(
                        '<rect x="'
                        + (c + quiet)
                        + '" y="'
                        + (r + quiet)
                        + '" width="1" height="1"/>'
                    );
                }
            }
        }

        return (
            '<svg '
            + 'xmlns="http://www.w3.org/2000/svg" '
            + 'viewBox="0 0 '
            + totalModules
            + ' '
            + totalModules
            + '" '
            + 'width="'
            + pixelSize
            + '" '
            + 'height="'
            + pixelSize
            + '" '
            + 'role="img" '
            + 'aria-label="QR Code Nomor Reservasi" '
            + 'shape-rendering="crispEdges">'
            + '<rect width="100%" height="100%" fill="#ffffff"/>'
            + '<g fill="#000000">'
            + rects.join('')
            + '</g>'
            + '</svg>'
        );
    }

    function render(
        container,
        text,
        size
    ) {
        if (!container) {
            throw new Error(
                'Container QR tidak tersedia.'
            );
        }

        const value =
            String(
                text || ''
            ).trim();

        container.innerHTML = '';

        if (!value) {
            return false;
        }

        const matrix =
            createMatrix(value);

        container.innerHTML =
            svgForMatrix(
                matrix,
                Number(size) || 156
            );

        return true;
    }

    global.NadiQRCode = {
        render: render
    };
})(window);
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    /*
    |--------------------------------------------------------------------------
    | Helper umum modal
    |--------------------------------------------------------------------------
    */
    function setText(element, value) {
        if (!element) return;

        element.textContent =
            value === null
            || value === undefined
            || String(value).trim() === ''
                ? '-'
                : String(value);
    }

    function formatDate(value) {
        if (!value) return '-';

        const raw = String(value).substring(0, 10);
        const date = new Date(raw + 'T00:00:00');

        if (Number.isNaN(date.getTime())) {
            return raw;
        }

        return new Intl.DateTimeFormat(
            'id-ID',
            {
                weekday: 'long',
                day: '2-digit',
                month: 'long',
                year: 'numeric'
            }
        ).format(date);
    }

    function formatSimpleDate(value) {
        if (!value) return '-';

        const raw = String(value).substring(0, 10);
        const date = new Date(raw + 'T00:00:00');

        if (Number.isNaN(date.getTime())) {
            return raw;
        }

        return new Intl.DateTimeFormat(
            'id-ID',
            {
                day: '2-digit',
                month: 'long',
                year: 'numeric'
            }
        ).format(date);
    }

    function openBackdrop(backdrop) {
        if (!backdrop) return;
        backdrop.classList.add('is-open');
        document.body.style.overflow = 'hidden';
    }

    function closeBackdrop(backdrop) {
        if (!backdrop) return;
        backdrop.classList.remove('is-open');

        const anyOpen = document.querySelector(
            '.reservation-modal-backdrop.is-open'
        );

        if (!anyOpen) {
            document.body.style.overflow = '';
        }
    }

    /*
    |--------------------------------------------------------------------------
    | MODAL DETAIL RESERVASI
    |--------------------------------------------------------------------------
    */
    const detailBackdrop =
        document.getElementById('reservation-detail-backdrop');

    const detailClose =
        document.getElementById('reservation-detail-close');

    let lastDetailButton = null;

    const detailQrSection =
        document.getElementById(
            'reservation-detail-qr-section'
        );

    const detailQrCode =
        document.getElementById(
            'reservation-detail-qr-code'
        );

    const detailQrNumber =
        document.getElementById(
            'reservation-detail-qr-number'
        );

    const detailFields = {
        booking:
            document.getElementById('modal-detail-booking'),
        dokter:
            document.getElementById('modal-detail-dokter'),
        poli:
            document.getElementById('modal-detail-poli'),
        kodepoli:
            document.getElementById('modal-detail-kodepoli'),
        tanggal:
            document.getElementById('modal-detail-tanggal'),
        jam:
            document.getElementById('modal-detail-jam'),
        penjamin:
            document.getElementById('modal-detail-penjamin'),
        nobpjs:
            document.getElementById('modal-detail-nobpjs'),
        nosurat:
            document.getElementById('modal-detail-nosurat'),
        statusRegistrasi:
            document.getElementById('modal-detail-status-registrasi'),
        statusPelayanan:
            document.getElementById('modal-detail-status-pelayanan'),
        noregistrasi:
            document.getElementById('modal-detail-noregistrasi'),
        noantrian:
            document.getElementById('modal-detail-noantrian'),
        norujukan:
            document.getElementById('modal-detail-norujukan')
    };

    function renderReservationQr(value) {
        const booking =
            value
            && value !== '-'
                ? String(value).trim()
                : '';

        if (
            !detailQrSection
            || !detailQrCode
        ) {
            return;
        }

        detailQrCode.innerHTML = '';

        if (!booking) {
            detailQrSection.classList.remove(
                'is-visible'
            );

            if (detailQrNumber) {
                detailQrNumber.textContent = '-';
            }

            return;
        }

        detailQrSection.classList.add(
            'is-visible'
        );

        if (detailQrNumber) {
            detailQrNumber.textContent =
                booking;
        }

        try {
            if (
                !window.NadiQRCode
                || typeof window.NadiQRCode.render
                    !== 'function'
            ) {
                throw new Error(
                    'Generator QR belum tersedia.'
                );
            }

            window.NadiQRCode.render(
                detailQrCode,
                booking,
                156
            );
        } catch (error) {
            console.error(
                'QR Code reservasi:',
                error
            );

            detailQrCode.innerHTML =
                '<div class="reservation-detail-qr-error">'
                + 'QR Code belum dapat dibuat.<br>'
                + 'No. Reservasi: '
                + booking
                + '</div>';
        }
    }

    document.querySelectorAll(
        '.js-reservation-detail'
    ).forEach(function (button) {
        button.addEventListener('click', function () {
            lastDetailButton = button;

            /*
             * QR Code dibuat dari nomor reservasi
             * sama seperti modal pada menu.blade.
             */
            renderReservationQr(
                button.dataset.noreservasi
            );

            setText(
                detailFields.booking,
                button.dataset.noreservasi
            );

            setText(
                detailFields.dokter,
                button.dataset.dokter
            );

            setText(
                detailFields.poli,
                button.dataset.poli
            );

            setText(
                detailFields.kodepoli,
                button.dataset.kodepoli
            );

            setText(
                detailFields.tanggal,
                formatDate(button.dataset.tanggal)
            );

            setText(
                detailFields.jam,
                button.dataset.jam
            );

            setText(
                detailFields.penjamin,
                button.dataset.penjamin
            );

            setText(
                detailFields.nobpjs,
                button.dataset.nobpjs
            );

            setText(
                detailFields.nosurat,
                button.dataset.nosuratkontrol
            );

            setText(
                detailFields.statusRegistrasi,
                button.dataset.statusRegistrasi
            );

            setText(
                detailFields.statusPelayanan,
                button.dataset.statusPelayanan
            );

            setText(
                detailFields.noregistrasi,
                button.dataset.noregistrasi
            );

            setText(
                detailFields.noantrian,
                button.dataset.noantrian
            );

            setText(
                detailFields.norujukan,
                button.dataset.norujukan
            );

            openBackdrop(detailBackdrop);

            if (detailClose) {
                detailClose.focus();
            }
        });
    });

    function closeDetailModal() {
        closeBackdrop(detailBackdrop);

        if (lastDetailButton) {
            lastDetailButton.focus();
        }
    }

    detailClose?.addEventListener(
        'click',
        closeDetailModal
    );

    detailBackdrop?.addEventListener(
        'click',
        function (event) {
            if (event.target === detailBackdrop) {
                closeDetailModal();
            }
        }
    );

    /*
    |--------------------------------------------------------------------------
    | MODAL CEK SURAT KONTROL BPJS
    |--------------------------------------------------------------------------
    */
    const bpjsBackdrop =
        document.getElementById('bpjs-control-backdrop');

    const bpjsClose =
        document.getElementById('bpjs-control-close');

    const bpjsLoading =
        document.getElementById('bpjs-control-loading');

    const bpjsStatus =
        document.getElementById('bpjs-control-status');

    const bpjsStatusIcon =
        document.getElementById('bpjs-control-status-icon');

    const bpjsStatusTitle =
        document.getElementById('bpjs-control-status-title');

    const bpjsStatusMessage =
        document.getElementById('bpjs-control-status-message');

    const bpjsResult =
        document.getElementById('bpjs-control-result');

    const bpjsNote =
        document.getElementById('bpjs-result-note');

    let activeBpjsButton = null;

    const bpjsFields = {
        noSurat:
            document.getElementById('bpjs-result-no-surat'),
        noKartu:
            document.getElementById('bpjs-result-no-kartu'),
        tanggal:
            document.getElementById('bpjs-result-tanggal'),
        poli:
            document.getElementById('bpjs-result-poli'),
        dokter:
            document.getElementById('bpjs-result-dokter'),
        jenis:
            document.getElementById('bpjs-result-jenis'),
        terbit:
            document.getElementById('bpjs-result-terbit'),
        sep:
            document.getElementById('bpjs-result-sep'),
        metode:
            document.getElementById('bpjs-result-metode')
    };

    function resetBpjsModal() {
        bpjsLoading?.classList.remove('is-visible');

        bpjsStatus?.classList.remove(
            'is-visible',
            'is-success',
            'is-warning',
            'is-danger'
        );

        bpjsResult?.classList.remove('is-visible');

        if (bpjsNote) {
            bpjsNote.classList.remove('is-visible');
            bpjsNote.textContent = '';
        }

        Object.values(bpjsFields).forEach(
            function (element) {
                setText(element, '-');
            }
        );
    }

    function showBpjsStatus(type, title, message) {
        if (!bpjsStatus) return;

        bpjsStatus.classList.remove(
            'is-success',
            'is-warning',
            'is-danger'
        );

        const normalizedType =
            ['success', 'warning', 'danger'].includes(type)
                ? type
                : 'warning';

        bpjsStatus.classList.add(
            'is-visible',
            'is-' + normalizedType
        );

        if (bpjsStatusIcon) {
            bpjsStatusIcon.textContent =
                normalizedType === 'success'
                    ? '✓'
                    : (
                        normalizedType === 'danger'
                            ? '×'
                            : '!'
                    );
        }

        setText(bpjsStatusTitle, title);
        setText(bpjsStatusMessage, message);
    }

    function showBpjsResponse(payload) {
        bpjsLoading?.classList.remove('is-visible');

        const success = payload?.success === true;
        const found = payload?.found === true;
        const match = payload?.match === true;
        const data = payload?.data || {};

        if (!success || !found) {
            showBpjsStatus(
                'danger',
                'Surat kontrol belum ditemukan',
                payload?.message
                    || 'Data Surat Kontrol BPJS tidak ditemukan.'
            );

            bpjsResult?.classList.remove(
                'is-visible'
            );

            return;
        }

        showBpjsStatus(
            match ? 'success' : 'warning',
            match
                ? 'Surat kontrol sesuai'
                : 'Surat kontrol ditemukan',
            payload?.message
                || (
                    match
                        ? 'Data Surat Kontrol BPJS sesuai dengan reservasi.'
                        : 'Surat kontrol ditemukan, tetapi ada data yang belum sesuai dengan reservasi.'
                )
        );

        const kodePoli =
            data.kodePoli
            || data.poliTujuan
            || data.kodePoliTujuan
            || '';

        const namaPoli =
            data.namaPoli
            || data.namaPoliTujuan
            || data.namaPoliKontrol
            || '';

        const kodeDokter =
            data.kodeDokter
            || data.kodeDokterKontrol
            || '';

        const namaDokter =
            data.namaDokter
            || data.namaDokterKontrol
            || '';

        setText(
            bpjsFields.noSurat,
            data.noSuratKontrol
                || data.nosuratkontrol
        );

        setText(
            bpjsFields.noKartu,
            data.noKartu
                || data.nokartu
        );

        setText(
            bpjsFields.tanggal,
            formatSimpleDate(
                data.tglRencanaKontrol
                    || data.tglKontrol
            )
        );

        setText(
            bpjsFields.poli,
            [kodePoli, namaPoli]
                .filter(Boolean)
                .join(' - ')
        );

        setText(
            bpjsFields.dokter,
            [kodeDokter, namaDokter]
                .filter(Boolean)
                .join(' - ')
        );

        setText(
            bpjsFields.jenis,
            data.jenisKontrolLabel
                || data.jnsKontrol
        );

        setText(
            bpjsFields.terbit,
            formatSimpleDate(
                data.tglTerbitKontrol
                    || data.tglTerbit
            )
        );

        setText(
            bpjsFields.sep,
            data.noSepAsalKontrol
                || data.noSEP
        );

        setText(
            bpjsFields.metode,
            payload?.metode_label
                || payload?.source
                || '-'
        );

        if (bpjsNote) {
            const note =
                payload?.warning
                || payload?.note
                || '';

            bpjsNote.textContent = note;

            bpjsNote.classList.toggle(
                'is-visible',
                Boolean(note)
            );
        }

        bpjsResult?.classList.add(
            'is-visible'
        );
    }

    async function checkSuratKontrol(button) {
        activeBpjsButton = button;

        const url =
            button.dataset.checkUrl || '';

        openBackdrop(bpjsBackdrop);
        resetBpjsModal();

        /*
         * Tombol tetap dibuka walaupun URL belum lengkap,
         * supaya pasien mendapat informasi field mana yang kurang.
         */
        if (!url) {
            const missing = [];

            if (!button.dataset.nokartu
                && !button.dataset.nosuratkontrol) {
                missing.push('No. Kartu/No. Surat Kontrol');
            }

            if (!button.dataset.nosuratkontrol) {
                if (!button.dataset.tanggal) {
                    missing.push('tanggal reservasi');
                }

                if (!button.dataset.kodepoli) {
                    missing.push(
                        'kode poli subspesialis BPJS'
                    );
                }
            }

            showBpjsStatus(
                'warning',
                'Data kontrol belum lengkap',
                missing.length
                    ? (
                        'Belum dapat melakukan pengecekan karena '
                        + missing.join(', ')
                        + ' belum tersedia.'
                    )
                    : 'Endpoint cek Surat Kontrol BPJS belum tersedia.'
            );

            return;
        }

        bpjsLoading?.classList.add(
            'is-visible'
        );

        button.disabled = true;

        try {
            const response = await fetch(
                url,
                {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin',
                    cache: 'no-store'
                }
            );

            let payload = null;

            try {
                payload = await response.json();
            } catch (parseError) {
                throw new Error(
                    'Respons cek Surat Kontrol bukan JSON.'
                );
            }

            if (!response.ok) {
                throw new Error(
                    payload?.message
                        || 'Gagal memeriksa Surat Kontrol BPJS.'
                );
            }

            showBpjsResponse(
                payload || {}
            );
        } catch (error) {
            bpjsLoading?.classList.remove(
                'is-visible'
            );

            showBpjsStatus(
                'danger',
                'Gagal memeriksa BPJS',
                error?.message
                    || 'Terjadi gangguan saat menghubungi layanan BPJS.'
            );
        } finally {
            button.disabled = false;
        }
    }

    document.querySelectorAll(
        '.js-bpjs-control-check'
    ).forEach(function (button) {
        button.addEventListener(
            'click',
            function () {
                checkSuratKontrol(button);
            }
        );
    });

    function closeBpjsModal() {
        closeBackdrop(bpjsBackdrop);

        if (activeBpjsButton) {
            activeBpjsButton.focus();
        }
    }

    bpjsClose?.addEventListener(
        'click',
        closeBpjsModal
    );

    bpjsBackdrop?.addEventListener(
        'click',
        function (event) {
            if (event.target === bpjsBackdrop) {
                closeBpjsModal();
            }
        }
    );

    /*
    |--------------------------------------------------------------------------
    | Escape menutup modal yang sedang terbuka
    |--------------------------------------------------------------------------
    */
    document.addEventListener(
        'keydown',
        function (event) {
            if (event.key !== 'Escape') {
                return;
            }

            if (
                detailBackdrop
                && detailBackdrop.classList.contains(
                    'is-open'
                )
            ) {
                closeDetailModal();
            }

            if (
                bpjsBackdrop
                && bpjsBackdrop.classList.contains(
                    'is-open'
                )
            ) {
                closeBpjsModal();
            }
        }
    );
});
</script>

@endsection