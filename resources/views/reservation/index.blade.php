@extends('layouts.app', ['title' => 'Cek Reservasi | NADI RSBM'])

@section('content')

{{-- Samakan perilaku responsive dengan Blade menu utama NADI. --}}
<script>
(function () {
    var viewport = document.querySelector('meta[name="viewport"]');
    if (!viewport) {
        viewport = document.createElement('meta');
        viewport.name = 'viewport';
        viewport.content = 'width=device-width, initial-scale=1, viewport-fit=cover';
        document.head.appendChild(viewport);
    }
})();
</script>

@php
    $mainMenuUrl = Route::has('layanan.menu')
        ? route('layanan.menu')
        : url('/layanan/menu');

    $logoutRouteName = Route::has('layanan.logout')
        ? 'layanan.logout'
        : (Route::has('logout') ? 'logout' : null);

    $routeOrUrl = static function ($routeName, $fallback) {
        return Route::has($routeName)
            ? route($routeName)
            : url($fallback);
    };

    $patientData = $patientInfo ?? [];
    $patientName = data_get($patientData, 'nama', data_get($patientData, 'name', 'Pasien'));
    $patientRm = data_get($patientData, 'medical_record', data_get($patientData, 'rm', '-'));

    /* Endpoint internal cek Surat Kontrol BPJS */
    $bpjsCheckBaseUrl = Route::has('bpjs.surat-kontrol.check')
        ? route('bpjs.surat-kontrol.check')
        : (
            Route::has('bpjs.surat-kontrol.index')
                ? route('bpjs.surat-kontrol.index')
                : null
        );
@endphp

<style>
    /* =========================================================
       RESET LAYOUT.APP
       ========================================================= */
    html, body {
        margin: 0 !important;
        padding: 0 !important;
        background: #f6f9fd;
    }

    #app > nav.navbar,
    #app > .navbar,
    body > nav.navbar,
    nav.navbar.navbar-expand-md,
    .navbar.navbar-expand-md,
    .content-header {
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
    .py-4,
    .content-wrapper,
    .content,
    .app-content,
    .page-content,
    .main-content {
        margin-top: 0 !important;
        padding-top: 0 !important;
    }

    :root {
        --nadi-blue: #163a93;
        --nadi-blue-2: #1477ee;
        --nadi-blue-soft: #edf6ff;
        --nadi-green: #18aa61;
        --nadi-green-dark: #118148;
        --nadi-green-soft: #eafaf1;
        --nadi-text: #17335f;
        --nadi-dark: #172b4d;
        --nadi-muted: #6f7f95;
        --nadi-line: #dfe8f2;
        --nadi-bg: #f6f9fd;
        --nadi-danger: #d92d20;
        --nadi-warning: #a16207;
    }

    .reservation-page,
    .reservation-page * {
        box-sizing: border-box;
    }

    .reservation-page {
        min-height: 100svh;
        padding: 26px clamp(16px, 3vw, 42px) 110px;
        background:
            radial-gradient(circle at 7% 4%, rgba(24, 170, 97, .08), transparent 25%),
            radial-gradient(circle at 94% 7%, rgba(20, 119, 238, .08), transparent 28%),
            linear-gradient(180deg, #fff 0%, var(--nadi-bg) 100%);
        color: var(--nadi-dark);
    }

    .reservation-container {
        width: min(100%, 1320px);
        margin: 0 auto;
    }

    /* =========================================================
       HEADER NADI
       ========================================================= */
    .reservation-headerbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        margin-bottom: 24px;
    }

    .reservation-welcome {
        display: flex;
        min-width: 0;
        align-items: center;
        gap: 13px;
    }

    .reservation-user-icon {
        display: grid;
        width: 52px;
        height: 52px;
        flex: 0 0 52px;
        place-items: center;
        border-radius: 50%;
        background: linear-gradient(145deg, #dff8c9, #bfeea7);
        color: var(--nadi-green);
        box-shadow: inset 0 0 0 1px rgba(24, 170, 97, .08);
    }

    .reservation-greeting-small {
        color: var(--nadi-blue);
        font-size: 12px;
        font-weight: 800;
        line-height: 1.2;
    }

    .reservation-greeting-name {
        max-width: 560px;
        overflow: hidden;
        color: var(--nadi-blue);
        font-size: clamp(22px, 2.8vw, 32px);
        font-weight: 950;
        line-height: 1.08;
        text-overflow: ellipsis;
        white-space: nowrap;
        letter-spacing: -.035em;
    }

    .reservation-greeting-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 6px 12px;
        margin-top: 5px;
        color: var(--nadi-muted);
        font-size: 12px;
        font-weight: 700;
    }

    .reservation-header-actions {
        display: flex;
        flex: 0 0 auto;
        align-items: center;
        gap: 8px;
    }

    .reservation-icon-button {
        display: grid;
        width: 42px;
        height: 42px;
        place-items: center;
        border: 1px solid #dce7f2;
        border-radius: 12px;
        background: #fff;
        color: var(--nadi-blue-2);
        cursor: pointer;
        text-decoration: none;
        box-shadow: 0 5px 14px rgba(15, 45, 115, .05);
        transition: .18s ease;
    }

    .reservation-icon-button:hover {
        transform: translateY(-1px);
        border-color: #c4d8ec;
        background: var(--nadi-blue-soft);
        color: var(--nadi-blue);
        text-decoration: none;
    }

    .reservation-logout-form { margin: 0; }
    .reservation-icon-button.is-logout { color: #c03b36; }
    .reservation-icon-button.is-logout:hover { background: #fff3f2; border-color: #f2cfcc; }

    /* =========================================================
       PAGE HEADING
       ========================================================= */
    .reservation-hero {
        display: flex;
        align-items: end;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 18px;
        padding: 20px 22px;
        border: 1px solid #dce8f4;
        border-radius: 22px;
        background: linear-gradient(135deg, rgba(237, 246, 255, .95), rgba(246, 253, 249, .96));
        box-shadow: 0 10px 28px rgba(15, 45, 115, .05);
    }

    .reservation-hero-copy { min-width: 0; }

    .reservation-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        margin-bottom: 7px;
        color: var(--nadi-green-dark);
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .06em;
    }

    .reservation-eyebrow::before {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: var(--nadi-green);
        box-shadow: 0 0 0 4px rgba(24, 170, 97, .10);
        content: '';
    }

    .reservation-title {
        margin: 0;
        color: var(--nadi-blue);
        font-size: clamp(26px, 3.4vw, 38px);
        font-weight: 950;
        line-height: 1.08;
        letter-spacing: -.035em;
    }

    .reservation-description {
        max-width: 720px;
        margin: 7px 0 0;
        color: var(--nadi-muted);
        font-size: 13px;
        font-weight: 650;
        line-height: 1.6;
    }

    .reservation-count {
        display: flex;
        flex: 0 0 auto;
        align-items: baseline;
        gap: 6px;
        padding: 10px 13px;
        border: 1px solid #d4eadc;
        border-radius: 14px;
        background: #fff;
        color: var(--nadi-muted);
        font-size: 12px;
        font-weight: 750;
    }

    .reservation-count strong {
        color: var(--nadi-green-dark);
        font-size: 22px;
        font-weight: 950;
        line-height: 1;
    }

    /* =========================================================
       RESERVATION CARDS
       ========================================================= */
    .reservation-list {
        display: grid;
        gap: 14px;
    }

    .reservation-card {
        position: relative;
        overflow: hidden;
        border: 1px solid #dce5ef;
        border-radius: 20px;
        background: #fff;
        box-shadow: 0 10px 28px rgba(15, 45, 115, .05);
    }

    .reservation-card::before {
        display: block;
        height: 4px;
        background: linear-gradient(90deg, var(--nadi-green) 0 24%, var(--nadi-blue-2) 24% 100%);
        content: '';
    }

    .reservation-card-main {
        display: grid;
        grid-template-columns: 170px minmax(0, 1fr) 150px;
        gap: 20px;
        align-items: center;
        padding: 20px;
    }

    .reservation-datebox {
        display: flex;
        align-items: center;
        gap: 11px;
    }

    .date-icon {
        display: grid;
        width: 46px;
        height: 46px;
        flex: 0 0 46px;
        place-items: center;
        border-radius: 13px;
        background: var(--nadi-blue-soft);
        color: var(--nadi-blue-2);
    }

    .date-content strong {
        display: block;
        color: var(--nadi-dark);
        font-size: 15px;
        font-weight: 950;
        line-height: 1.25;
    }

    .date-content span {
        display: block;
        margin-top: 4px;
        color: var(--nadi-blue-2);
        font-size: 13px;
        font-weight: 850;
    }

    .reservation-service { min-width: 0; }

    .reservation-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-bottom: 7px;
    }

    .reservation-badge {
        display: inline-flex;
        min-height: 25px;
        align-items: center;
        padding: 4px 9px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 850;
        line-height: 1;
    }

    .reservation-badge.is-status {
        background: var(--nadi-green-soft);
        color: var(--nadi-green-dark);
    }

    .reservation-badge.is-bpjs {
        background: #f3f6fa;
        color: #62748d;
    }

    .reservation-service h2 {
        margin: 0;
        color: var(--nadi-blue);
        font-size: clamp(17px, 1.8vw, 21px);
        font-weight: 950;
        line-height: 1.3;
        letter-spacing: -.02em;
    }

    .reservation-doctor {
        display: flex;
        align-items: center;
        gap: 7px;
        margin-top: 6px;
        color: #526982;
        font-size: 13px;
        font-weight: 750;
        line-height: 1.4;
    }

    .reservation-queue {
        text-align: center;
        padding: 12px 14px;
        border: 1px solid #dbe8f5;
        border-radius: 15px;
        background: linear-gradient(180deg, #fbfdff, #f4f9ff);
    }

    .queue-label {
        display: block;
        margin-bottom: 5px;
        color: #72849a;
        font-size: 11px;
        font-weight: 850;
    }

    .queue-number {
        color: var(--nadi-blue);
        font-size: 34px;
        font-weight: 950;
        line-height: 1;
        letter-spacing: -.04em;
    }

    .reservation-card-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 9px;
        padding: 13px 20px 16px;
        border-top: 1px solid #edf2f7;
        background: #fbfdff;
    }

    .reservation-action-button {
        display: inline-flex;
        min-height: 40px;
        align-items: center;
        justify-content: center;
        gap: 7px;
        padding: 0 14px;
        border: 1px solid transparent;
        border-radius: 11px;
        background: #fff;
        font: inherit;
        font-size: 12px;
        font-weight: 900;
        line-height: 1;
        cursor: pointer;
        transition: .16s ease;
    }

    .reservation-action-button:hover { transform: translateY(-1px); }

    .reservation-action-button.is-detail {
        border-color: #cfe2f6;
        background: var(--nadi-blue-soft);
        color: var(--nadi-blue-2);
    }

    .reservation-action-button.is-bpjs {
        border-color: #cfe8d8;
        background: var(--nadi-green-soft);
        color: var(--nadi-green-dark);
    }

    .reservation-action-button[disabled] {
        opacity: .58;
        cursor: wait;
        transform: none;
    }

    /* =========================================================
       EMPTY STATE
       ========================================================= */
    .empty-state {
        padding: 56px 20px;
        border: 1px dashed #cedbea;
        border-radius: 20px;
        background: rgba(255,255,255,.86);
        text-align: center;
    }

    .empty-icon {
        display: grid;
        width: 60px;
        height: 60px;
        margin: 0 auto 14px;
        place-items: center;
        border-radius: 17px;
        background: var(--nadi-blue-soft);
        color: var(--nadi-blue-2);
    }

    .empty-state h2 {
        margin: 0;
        color: var(--nadi-blue);
        font-size: 20px;
        font-weight: 950;
    }

    .empty-state p {
        max-width: 500px;
        margin: 7px auto 0;
        color: var(--nadi-muted);
        font-size: 13px;
        line-height: 1.6;
    }

    /* =========================================================
       MODAL DETAIL / BPJS
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

    .reservation-modal-backdrop.is-open { display: flex; }

    .reservation-modal {
        width: min(100%, 520px);
        max-height: min(88vh, 800px);
        overflow: hidden;
        border: 1px solid rgba(22, 58, 147, .10);
        border-radius: 22px;
        background: #fff;
        box-shadow: 0 28px 80px rgba(15, 45, 115, .24);
    }

    .reservation-modal-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 18px 19px 15px;
        border-bottom: 1px solid #eaf0f7;
        background: linear-gradient(135deg, #edf7ff, #f8fcff);
    }

    .reservation-modal-kicker {
        margin-bottom: 3px;
        color: var(--nadi-green-dark);
        font-size: 11px;
        font-weight: 950;
        text-transform: uppercase;
        letter-spacing: .07em;
    }

    .reservation-modal-title {
        margin: 0;
        color: var(--nadi-blue);
        font-size: 19px;
        font-weight: 950;
        line-height: 1.2;
    }

    .reservation-modal-close {
        display: grid;
        width: 38px;
        height: 38px;
        flex: 0 0 38px;
        place-items: center;
        border: 1px solid #e3eaf2;
        border-radius: 11px;
        background: #fff;
        color: #64748b;
        cursor: pointer;
    }

    .reservation-modal-body {
        max-height: calc(min(88vh, 800px) - 72px);
        overflow-y: auto;
        padding: 18px 19px 22px;
    }

    .reservation-detail-qr {
        display: none;
        margin-bottom: 14px;
        padding: 14px;
        border: 1px solid #dbe9f8;
        border-radius: 16px;
        background: linear-gradient(180deg, #fff 0%, #f8fbff 100%);
        text-align: center;
    }
    .reservation-detail-qr.is-visible { display: block; }

    .reservation-detail-qr-box {
        display: grid;
        width: 174px;
        min-height: 174px;
        margin: 0 auto;
        place-items: center;
        padding: 8px;
        border: 1px solid #e2eaf4;
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 7px 20px rgba(15, 45, 115, .08);
    }
    .reservation-detail-qr-box svg { display: block; width: 156px; height: 156px; }
    .reservation-detail-qr-title { margin-top: 9px; color: var(--nadi-blue); font-size: 13px; font-weight: 950; }
    .reservation-detail-qr-number { margin-top: 3px; overflow-wrap: anywhere; color: #3f5f8c; font-size: 13px; font-weight: 900; letter-spacing: .05em; }
    .reservation-detail-qr-help { margin-top: 5px; color: #7a899f; font-size: 11px; font-weight: 650; line-height: 1.45; }
    .reservation-detail-qr-error { display: flex; min-height: 156px; align-items: center; justify-content: center; padding: 12px; color: #9a5e0b; font-size: 12px; font-weight: 800; line-height: 1.45; text-align: center; }

    .reservation-modal-highlight {
        margin-bottom: 14px;
        padding: 14px 15px;
        border: 1px solid #dbe9f8;
        border-radius: 15px;
        background: #f7fbff;
    }
    .reservation-modal-highlight-label { color: #718096; font-size: 11px; font-weight: 850; text-transform: uppercase; letter-spacing: .04em; }
    .reservation-modal-highlight-value { margin-top: 3px; overflow-wrap: anywhere; color: var(--nadi-blue); font-size: 19px; font-weight: 950; letter-spacing: .025em; }

    .reservation-modal-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
    }
    .reservation-modal-item { min-width: 0; padding: 11px 12px; border: 1px solid #e6edf5; border-radius: 12px; background: #fff; }
    .reservation-modal-item.is-wide { grid-column: 1 / -1; }
    .reservation-modal-label { margin-bottom: 4px; color: #8290a5; font-size: 11px; font-weight: 850; }
    .reservation-modal-value { overflow-wrap: anywhere; color: #19386f; font-size: 13px; font-weight: 850; line-height: 1.45; }
    .reservation-modal-value.is-status { color: var(--nadi-green-dark); }

    .bpjs-modal-loading { display: none; min-height: 220px; align-items: center; justify-content: center; flex-direction: column; gap: 11px; color: #60728c; text-align: center; }
    .bpjs-modal-loading.is-visible { display: flex; }
    .bpjs-modal-spinner { width: 36px; height: 36px; border: 3px solid #dbe8f6; border-top-color: var(--nadi-blue-2); border-radius: 50%; animation: reservationBpjsSpin .8s linear infinite; }
    @keyframes reservationBpjsSpin { to { transform: rotate(360deg); } }
    .bpjs-modal-loading-title { color: var(--nadi-blue); font-size: 13px; font-weight: 950; }
    .bpjs-modal-loading-text { max-width: 320px; font-size: 11.5px; line-height: 1.5; }
    .bpjs-modal-status { display: none; margin-bottom: 14px; padding: 12px 13px; border: 1px solid #dbe9f8; border-radius: 15px; background: #f7fbff; }
    .bpjs-modal-status.is-visible { display: flex; align-items: flex-start; gap: 10px; }
    .bpjs-modal-status.is-success { border-color: #ccebd9; background: #eefbf4; color: #137a48; }
    .bpjs-modal-status.is-warning { border-color: #f2dfad; background: #fff9e9; color: #94600a; }
    .bpjs-modal-status.is-danger { border-color: #f0cccc; background: #fff4f4; color: #b63838; }
    .bpjs-modal-status-icon { display: grid; width: 34px; height: 34px; flex: 0 0 34px; place-items: center; border-radius: 10px; background: rgba(255,255,255,.80); font-size: 16px; font-weight: 950; }
    .bpjs-modal-status-title { font-size: 13px; font-weight: 950; line-height: 1.35; }
    .bpjs-modal-status-message { margin-top: 2px; font-size: 11.5px; font-weight: 650; line-height: 1.45; opacity: .9; }
    .bpjs-modal-result { display: none; }
    .bpjs-modal-result.is-visible { display: block; }
    .bpjs-modal-note { display: none; margin-top: 12px; padding: 11px 12px; border-radius: 12px; background: #fff8e7; color: #8d620e; font-size: 11.5px; font-weight: 700; line-height: 1.5; }
    .bpjs-modal-note.is-visible { display: block; }

    /* =========================================================
       BOTTOM NAVIGATION - SEMUA DEVICE
       ========================================================= */
    .nadi-bottom-nav {
        position: fixed;
        z-index: 1000;
        right: 0;
        bottom: 0;
        left: 0;
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        border-top: 1px solid #e2eaf3;
        background: rgba(255,255,255,.98);
        box-shadow: 0 -8px 24px rgba(15,45,115,.08);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        padding-bottom: env(safe-area-inset-bottom);
    }

    .nadi-bottom-link {
        position: relative;
        display: flex;
        min-height: 68px;
        align-items: center;
        justify-content: center;
        gap: 8px;
        color: #667891;
        font-size: 12px;
        font-weight: 800;
        text-decoration: none;
    }
    .nadi-bottom-link:hover { color: var(--nadi-blue-2); text-decoration: none; background: #f8fbff; }
    .nadi-bottom-link.is-active { color: var(--nadi-blue-2); font-weight: 950; }
    .nadi-bottom-link.is-active::after { position: absolute; right: 30%; bottom: 5px; left: 30%; height: 3px; border-radius: 999px; background: var(--nadi-blue-2); content: ''; }

    @media (max-width: 900px) {
        .reservation-page { padding: 20px 16px 105px; }
        .reservation-card-main { grid-template-columns: 145px minmax(0, 1fr); }
        .reservation-queue { grid-column: 1 / -1; display: flex; align-items: center; justify-content: space-between; text-align: left; }
        .queue-label { margin: 0; font-size: 12px; }
        .queue-number { font-size: 30px; }
    }

    @media (max-width: 640px) {
        .reservation-page { padding: 16px 12px 96px; background: #fff; }
        .reservation-container { width: 100%; }
        .reservation-headerbar { margin-bottom: 16px; gap: 10px; }
        .reservation-user-icon { width: 44px; height: 44px; flex-basis: 44px; }
        .reservation-greeting-name { max-width: 210px; font-size: 22px; }
        .reservation-greeting-small { font-size: 11px; }
        .reservation-greeting-meta { font-size: 11px; }
        .reservation-icon-button { width: 38px; height: 38px; }
        .reservation-hero { align-items: flex-start; flex-direction: column; padding: 16px; border-radius: 18px; }
        .reservation-title { font-size: 26px; }
        .reservation-description { font-size: 12px; }
        .reservation-count { width: 100%; justify-content: center; }
        .reservation-card { border-radius: 17px; }
        .reservation-card-main { grid-template-columns: 1fr; gap: 14px; padding: 16px; }
        .reservation-datebox { padding-bottom: 13px; border-bottom: 1px solid #edf2f7; }
        .reservation-service h2 { font-size: 18px; }
        .reservation-doctor { font-size: 12.5px; }
        .reservation-queue { grid-column: auto; }
        .reservation-card-actions { display: grid; grid-template-columns: 1fr; padding: 12px 16px 15px; }
        .reservation-action-button { width: 100%; min-height: 42px; font-size: 12px; }
        .reservation-modal { border-radius: 18px; }
        .reservation-modal-grid { grid-template-columns: 1fr; }
        .reservation-modal-item.is-wide { grid-column: auto; }
        .nadi-bottom-link { min-height: 66px; flex-direction: column; gap: 4px; font-size: 9px; }
        .nadi-bottom-link svg { width: 21px; height: 21px; }
    }

    @media (max-width: 370px) {
        .reservation-greeting-name { max-width: 160px; }
        .reservation-header-actions { gap: 5px; }
        .reservation-icon-button { width: 36px; height: 36px; }
    }


    /* =========================================================
       KONSISTENSI VISUAL DENGAN BLADE MENU UTAMA NADI
       Header, spacing, panel, typography dan bottom navigation
       mengikuti menu-nadi-dashboard-footer-semua-device.
       ========================================================= */

    /* Warna dan tipografi identik dengan menu utama. */
    :root {
        --nadi-blue: #163a93;
        --nadi-blue-2: #1477ee;
        --nadi-blue-soft: #edf6ff;
        --nadi-green: #18aa61;
        --nadi-green-soft: #eafaf1;
        --nadi-text: #0f2d73;
        --nadi-muted: #66758e;
        --nadi-line: #dfe8f2;
        --nadi-bg: #f7fbff;
        --nadi-danger: #ef4444;
    }

    html,
    body {
        background: #fff !important;
    }

    .reservation-page {
        position: relative;
        min-height: calc(100svh - 72px);
        padding: 22px 12px 110px;
        background:
            radial-gradient(circle at 8% 5%, rgba(24, 170, 97, .10), transparent 28%),
            radial-gradient(circle at 96% 8%, rgba(20, 119, 238, .09), transparent 30%),
            linear-gradient(180deg, #fff 0%, var(--nadi-bg) 100%);
        color: var(--nadi-text);
    }

    .reservation-container {
        width: min(100%, 520px);
        margin: 0 auto;
        overflow: hidden;
        border: 1px solid rgba(22, 58, 147, .10);
        border-radius: 28px;
        background: rgba(255,255,255,.98);
        box-shadow:
            0 26px 70px rgba(15, 45, 115, .11),
            0 2px 10px rgba(15, 45, 115, .04);
        padding: 22px 18px 105px;
    }

    /* Header dibuat sama persis skalanya dengan menu NADI. */
    .reservation-headerbar {
        gap: 14px;
        margin-bottom: 18px;
        padding: 0;
        border: 0;
        background: transparent;
        box-shadow: none;
    }

    .reservation-welcome {
        gap: 12px;
    }

    .reservation-user-icon {
        width: 48px;
        height: 48px;
        flex: 0 0 48px;
        border-radius: 50%;
        background: linear-gradient(145deg, #dff8c9, #bfeea7);
        color: var(--nadi-green);
        box-shadow: none;
    }

    .reservation-user-icon svg {
        width: 24px;
        height: 24px;
    }

    .reservation-greeting-small {
        color: var(--nadi-blue);
        font-size: 13px;
        font-weight: 800;
        line-height: 1.15;
    }

    .reservation-greeting-name {
        max-width: 295px;
        color: var(--nadi-text);
        font-size: clamp(22px, 6vw, 31px);
        font-weight: 950;
        line-height: 1.03;
        letter-spacing: -.035em;
    }

    /* Menu utama tidak menaruh RM di header; detail pasien tetap tersedia di modal/data. */
    .reservation-greeting-meta {
        display: none;
    }

    .reservation-header-actions {
        gap: 7px;
    }

    .reservation-icon-button {
        width: 40px;
        height: 40px;
        border: 0;
        border-radius: 12px;
        background: #fff;
        color: var(--nadi-blue-2);
        box-shadow: none;
    }

    .reservation-icon-button:hover {
        transform: none;
        border-color: transparent;
        background: var(--nadi-blue-soft);
        color: var(--nadi-blue);
    }

    .reservation-icon-button.is-logout {
        color: var(--nadi-blue-2);
    }

    .reservation-icon-button.is-logout:hover {
        border-color: transparent;
        background: var(--nadi-blue-soft);
        color: var(--nadi-blue);
    }

    /* Judul halaman dibuat seperti panel/section pada menu, bukan hero landing page. */
    .reservation-hero {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        margin-bottom: 18px;
        padding: 15px 16px;
        border: 1px solid #d9ebfb;
        border-radius: 20px;
        background: linear-gradient(135deg, rgba(235,247,255,.98), rgba(247,252,255,.98));
        box-shadow: 0 10px 26px rgba(31,110,191,.06);
    }

    .reservation-eyebrow {
        margin-bottom: 4px;
        color: var(--nadi-green);
        font-size: 9px;
        font-weight: 950;
        letter-spacing: .06em;
    }

    .reservation-eyebrow::before {
        width: 6px;
        height: 6px;
    }

    .reservation-title {
        color: var(--nadi-blue);
        font-size: 18px;
        font-weight: 950;
        line-height: 1.2;
        letter-spacing: -.02em;
    }

    .reservation-description {
        max-width: 760px;
        margin-top: 5px;
        color: var(--nadi-muted);
        font-size: 12px;
        font-weight: 650;
        line-height: 1.5;
    }

    .reservation-count {
        min-width: 82px;
        justify-content: center;
        padding: 8px 11px;
        border: 1px solid #d7eee0;
        border-radius: 999px;
        background: var(--nadi-green-soft);
        color: var(--nadi-green);
        font-size: 10px;
        font-weight: 900;
    }

    .reservation-count strong {
        color: var(--nadi-green);
        font-size: 17px;
    }

    /* Kartu reservasi mengikuti language card Kontrol Berikutnya. */
    .reservation-list {
        gap: 12px;
    }

    .reservation-card {
        border: 1px solid rgba(22, 58, 147, .09);
        border-radius: 18px;
        background: #fff;
        box-shadow: 0 9px 24px rgba(15,45,115,.045);
    }

    .reservation-card::before {
        height: 3px;
        background: linear-gradient(90deg, var(--nadi-green) 0 35%, var(--nadi-blue-2) 35% 100%);
    }

    .reservation-card-main {
        gap: 18px;
        padding: 18px;
        background: linear-gradient(90deg, rgba(234,250,241,.34), rgba(237,246,255,.26), #fff);
    }

    .date-icon {
        width: 42px;
        height: 42px;
        flex-basis: 42px;
        border-radius: 11px;
    }

    .date-content strong {
        color: var(--nadi-blue);
        font-size: 14px;
    }

    .date-content span {
        color: var(--nadi-muted);
        font-size: 12px;
    }

    .reservation-badge {
        min-height: 24px;
        padding: 4px 8px;
        font-size: 10px;
    }

    .reservation-service h2 {
        color: var(--nadi-blue);
        font-size: 16px;
    }

    .reservation-doctor {
        color: var(--nadi-muted);
        font-size: 12px;
    }

    .reservation-queue {
        border: 1px solid #e2eaf3;
        border-radius: 14px;
        background: #fbfdff;
    }

    .queue-label {
        color: #8290a5;
        font-size: 10px;
    }

    .queue-number {
        color: var(--nadi-blue-2);
        font-size: 31px;
    }

    .reservation-card-actions {
        gap: 8px;
        padding: 11px 18px 14px;
        border-top: 1px solid #edf2f7;
        background: #fff;
    }

    .reservation-action-button {
        min-height: 38px;
        padding: 0 13px;
        border-radius: 10px;
        font-size: 11px;
    }

    .empty-state {
        border-color: #cddff1;
        border-radius: 16px;
        background: rgba(255,255,255,.76);
        box-shadow: none;
    }

    /* Footer sama dengan menu utama, termasuk desktop. */
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
        border-top: 1px solid #e6edf5 !important;
        background: rgba(255,255,255,.98) !important;
        box-shadow: 0 -8px 24px rgba(15,45,115,.08) !important;
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
        background: transparent;
    }

    .nadi-bottom-link.is-active {
        color: var(--nadi-blue-2);
        font-weight: 900;
    }

    .nadi-bottom-link.is-active::after {
        right: 28%;
        bottom: 7px;
        left: 28%;
        height: 3px;
    }

    /* Tablet / desktop: identik dengan menu utama yang melebar penuh. */
    @media (min-width: 768px) {
        .reservation-page {
            left: 50%;
            width: 100vw;
            min-height: 100vh;
            margin-left: -50vw;
            padding: 26px clamp(24px, 3vw, 48px) 110px;
        }

        .reservation-container {
            width: 100%;
            max-width: none;
            margin: 0;
            overflow: visible;
            border: 0;
            border-radius: 0;
            background: transparent;
            box-shadow: none;
            padding: 0;
        }

        .reservation-headerbar {
            margin-bottom: 20px;
        }

        .reservation-greeting-name {
            max-width: min(520px, 45vw);
        }

        .reservation-hero {
            padding: 18px 20px;
        }

        .reservation-card-main {
            grid-template-columns: 170px minmax(0, 1fr) 150px;
        }

        .nadi-bottom-nav {
            width: 100vw !important;
        }
    }

    /* Desktop besar mengikuti dashboard menu: spacing dan footer horizontal. */
    @media (min-width: 1024px) {
        .reservation-page {
            padding: 28px clamp(28px, 3vw, 56px) 110px;
        }

        .reservation-headerbar {
            margin-bottom: 2px;
            padding: 0 2px 18px;
            border-bottom: 1px solid rgba(22,58,147,.08);
        }

        .reservation-hero {
            margin-top: 20px;
            padding: 20px;
            border-radius: 22px;
        }

        .reservation-title {
            font-size: 18px;
        }

        .reservation-description {
            font-size: 12px;
        }

        .reservation-card-main {
            padding: 20px;
        }

        .reservation-service h2 {
            font-size: 17px;
        }

        .reservation-doctor,
        .date-content span {
            font-size: 12px;
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

    /* Mobile mengikuti shell menu utama: layar penuh, tanpa phone card. */
    @media (max-width: 420px) {
        .reservation-page {
            padding: 0;
            background: #fff;
        }

        .reservation-container {
            width: 100%;
            min-height: calc(100svh - 72px);
            margin: 0;
            overflow: visible;
            border: 0;
            border-radius: 0;
            background: #fff;
            box-shadow: none;
            padding: 18px 14px calc(100px + env(safe-area-inset-bottom));
        }

        .reservation-headerbar {
            margin-bottom: 18px;
        }

        .reservation-user-icon {
            width: 43px;
            height: 43px;
            flex-basis: 43px;
        }

        .reservation-greeting-name {
            max-width: 205px;
            font-size: 22px;
        }

        .reservation-hero {
            align-items: flex-start;
            flex-direction: column;
            padding: 13px;
            border-radius: 17px;
        }

        .reservation-title {
            font-size: 16px;
        }

        .reservation-description {
            font-size: 11px;
        }

        .reservation-count {
            width: auto;
            min-width: 0;
            justify-content: flex-start;
        }

        .reservation-card-main {
            grid-template-columns: 1fr;
            gap: 13px;
            padding: 14px;
        }

        .reservation-datebox {
            padding-bottom: 11px;
            border-bottom: 1px solid #edf2f7;
        }

        .reservation-queue {
            grid-column: auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            text-align: left;
        }

        .queue-label {
            margin-bottom: 0;
        }

        .queue-number {
            font-size: 28px;
        }

        .reservation-card-actions {
            display: grid;
            grid-template-columns: 1fr;
            padding: 11px 14px 14px;
        }

        .reservation-action-button {
            width: 100%;
            min-height: 40px;
        }

        .nadi-bottom-nav {
            padding-bottom: max(6px, env(safe-area-inset-bottom)) !important;
            box-shadow: 0 -8px 24px rgba(15,45,115,.12) !important;
            visibility: visible !important;
            opacity: 1 !important;
        }

        .nadi-bottom-link {
            min-height: 68px;
            font-size: 8.5px;
        }
    }

</style>

<section class="reservation-page">
    <div class="reservation-container">

        {{-- Header pasien / NADI --}}
        <header class="reservation-headerbar">
            <div class="reservation-welcome">
                <div class="reservation-user-icon" aria-hidden="true">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="8" r="4" fill="currentColor"/>
                        <path d="M4 21C4.7 16.4 7.3 14 12 14C16.7 14 19.3 16.4 20 21" fill="currentColor"/>
                    </svg>
                </div>

                <div style="min-width:0;">
                    <div class="reservation-greeting-small">Selamat Datang,</div>
                    <div class="reservation-greeting-name">{{ $patientName ?: 'Pasien' }}</div>
                    <div class="reservation-greeting-meta">
                        <span>No. RM: {{ $patientRm ?: '-' }}</span>
                    </div>
                </div>
            </div>

            <div class="reservation-header-actions">
                <a
                    href="{{ $mainMenuUrl }}"
                    class="reservation-icon-button"
                    title="Kembali ke menu utama"
                    aria-label="Kembali ke menu utama"
                >
                    <svg width="21" height="21" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>

                @if($logoutRouteName)
                    <form method="POST" action="{{ route($logoutRouteName) }}" class="reservation-logout-form">
                        @csrf
                        <button
                            type="submit"
                            class="reservation-icon-button is-logout"
                            title="Logout"
                            aria-label="Logout"
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
        <section class="reservation-hero">
            <div class="reservation-hero-copy">
                <div class="reservation-eyebrow">NADI RSBM · Reservasi</div>
                <h1 class="reservation-title">Riwayat Reservasi</h1>
                <p class="reservation-description">
                    Lihat jadwal kontrol, dokter, poliklinik, nomor antrean,
                    dan detail reservasi pelayanan Anda.
                </p>
            </div>

            <div class="reservation-count">
                <strong>{{ number_format($total) }}</strong>
                <span>reservasi</span>
            </div>
        </section>

        @if($reservations->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">
                    <svg width="29" height="29" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <rect x="3" y="5" width="18" height="16" rx="3" stroke="currentColor" stroke-width="2"/>
                        <path d="M8 3V7M16 3V7M3 10H21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
                <h2>Belum ada reservasi</h2>
                <p>
                    Tidak ditemukan data reservasi pasien pada sistem e-Reservasi
                    RSUD Bali Mandara.
                </p>
            </div>
        @else
            <div class="reservation-list">
                @foreach($reservations as $reservation)
                    @php
                        $tanggalReservasi = data_get($reservation, 'tanggalreservasi');
                        $tanggalDisplay = '-';

                        if ($tanggalReservasi) {
                            try {
                                $tanggalDisplay = \Carbon\Carbon::parse($tanggalReservasi)->format('d-m-Y');
                            } catch (\Throwable $e) {
                                $tanggalDisplay = $tanggalReservasi;
                            }
                        }

                        $noAntrian = data_get($reservation, 'noantrianpoli');
                        $status = data_get($reservation, 'status', 'Reservasi');

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
                                        data_get($reservation, 'kodepoli')
                                    )
                                )
                            )
                        );

                        $tanggalBpjs = null;

                        if ($tanggalReservasi) {
                            try {
                                $tanggalBpjs = \Carbon\Carbon::parse($tanggalReservasi)->format('Y-m-d');
                            } catch (\Throwable $e) {
                                $tanggalBpjs = substr((string) $tanggalReservasi, 0, 10);
                            }
                        }

                        $bpjsCheckUrl = null;

                        if ($bpjsCheckBaseUrl && ($noSuratKontrol || $noKartuBpjs)) {
                            $bpjsCheckParams = array_filter(
                                [
                                    'nosuratkontrol' => $noSuratKontrol,
                                    'nokartu' => $noKartuBpjs,
                                    'tanggalreservasi' => $tanggalBpjs,
                                    'kodepoli' => $kodePoliSubspesialis,
                                ],
                                static function ($value) {
                                    return $value !== null && $value !== '';
                                }
                            );

                            $bpjsCheckUrl = $bpjsCheckBaseUrl . '?' . http_build_query($bpjsCheckParams);
                        }

                        $isBpjsReservation =
                            strtoupper(trim((string) data_get($reservation, 'kelompokpasien', ''))) === 'BPJS'
                            || !empty($noKartuBpjs);
                    @endphp

                    <article class="reservation-card">
                        <div class="reservation-card-main">
                            <div class="reservation-datebox">
                                <div class="date-icon" aria-hidden="true">
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                                        <rect x="3" y="5" width="18" height="16" rx="3" stroke="currentColor" stroke-width="2"/>
                                        <path d="M8 3V7M16 3V7M3 10H21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                </div>
                                <div class="date-content">
                                    <strong>{{ $tanggalDisplay }}</strong>
                                    <span>{{ data_get($reservation, 'jamreservasi', '-') ?: '-' }}</span>
                                </div>
                            </div>

                            <div class="reservation-service">
                                <div class="reservation-badges">
                                    <span class="reservation-badge is-status">{{ $status }}</span>

                                    @if(data_get($reservation, 'kelompokpasien'))
                                        <span class="reservation-badge is-bpjs">
                                            {{ data_get($reservation, 'kelompokpasien') }}
                                        </span>
                                    @endif
                                </div>

                                <h2>{{ data_get($reservation, 'namaruangan', '-') ?: '-' }}</h2>

                                <div class="reservation-doctor">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <circle cx="12" cy="8" r="4" stroke="currentColor" stroke-width="2"/>
                                        <path d="M5 21C5.8 16.9 8.2 15 12 15C15.8 15 18.2 16.9 19 21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                    <span>{{ data_get($reservation, 'dokter', '-') ?: '-' }}</span>
                                </div>
                            </div>

                            <div class="reservation-queue">
                                <span class="queue-label">No. Antrean Poli</span>
                                <div class="queue-number">{{ $noAntrian !== null ? $noAntrian : '-' }}</div>
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
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" aria-hidden="true">
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
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" aria-hidden="true">
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