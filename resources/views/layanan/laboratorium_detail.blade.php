@extends('layouts.app', ['title' => 'Detail Laboratorium | NADI RSBM'])

@section('content')

<style>
    /* =========================================================
       RESET LAYOUT.APP
       Laravel default sering memakai <main class="py-4">.
       Padding Bootstrap py-4 inilah yang menampilkan strip/ruang
       background layout di bagian paling atas halaman.
       ========================================================= */
    html,
    body {
        margin: 0 !important;
        padding: 0 !important;
    }

    /*
     * HILANGKAN BAR / NAVBAR BAWAAN layouts.app DI BAGIAN ATAS.
     * Area inilah yang pada desktop terlihat sebagai ruang hitam/orange
     * sebelum halaman NADI dimulai. Bottom navigation NADI tidak terkena
     * karena class-nya .nadi-bottom-nav, bukan .navbar.
     */
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

    /* Hapus seluruh offset/padding dari wrapper layout Laravel */
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

    /* Fallback untuk template AdminLTE / layout lain */
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

    .lab-page {
        width: min(100%, 1080px);
        margin: 0 auto;
        padding: 10px 0 40px;
    }

    .lab-header {
        margin-bottom: 22px;
        text-align: center;
    }

    .lab-header h1 {
        margin: 0 0 8px;
        color: #0f172a;
        font-size: clamp(28px, 5vw, 42px);
        font-weight: 900;
        letter-spacing: -0.04em;
    }

    .lab-header p {
        max-width: 680px;
        margin: 0 auto;
        color: #64748b;
        font-size: 14px;
        line-height: 1.7;
    }

    .lab-card {
        margin-bottom: 20px;
        padding: clamp(18px, 4vw, 28px);
        border: 1px solid #e2e8f0;
        border-radius: 26px;
        background: #fff;
        box-shadow: 0 14px 40px rgba(15, 23, 42, .06);
    }

    .result-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 18px;
    }

    .result-title {
        color: #0f172a;
        font-size: 21px;
        font-weight: 900;
    }

    .lab-meta {
        margin-top: 7px;
        color: #94a3b8;
        font-size: 11px;
        line-height: 1.55;
    }

    .badge-row {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-end;
        gap: 8px;
    }

    .badge {
        display: inline-flex;
        align-items: center;
        padding: 8px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 900;
    }

    .badge.is-type {
        background: #f1f5f9;
        color: #475569;
    }

    .badge.is-micro {
        background: #f3e8ff;
        color: #7e22ce;
    }

    .badge.is-pa {
        background: #ffedd5;
        color: #9a3412;
    }

    .badge.is-order {
        background: #dcfce7;
        color: #166534;
    }

    .badge.is-billing {
        background: #e0f2fe;
        color: #0369a1;
    }

    .lab-alert {
        padding: 15px 17px;
        border-radius: 15px;
        font-size: 14px;
        font-weight: 700;
        line-height: 1.55;
    }

    .lab-alert.is-success {
        border: 1px solid #bbf7d0;
        background: #f0fdf4;
        color: #166534;
    }

    .lab-alert.is-error {
        border: 1px solid #fecaca;
        background: #fef2f2;
        color: #b91c1c;
    }

    .lab-alert.is-warning {
        border: 1px solid #fde68a;
        background: #fffbeb;
        color: #92400e;
    }

    /* =========================================================
       PATOLOGI KLINIK
       ========================================================= */

    .lab-group {
        margin-top: 18px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        background: #fff;
    }

    .lab-group-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 16px 18px;
        border-bottom: 1px solid #e2e8f0;
        background: #f8fafc;
    }

    .lab-group-title {
        color: #0f172a;
        font-size: 16px;
        font-weight: 900;
    }

    .lab-group-count {
        padding: 5px 9px;
        border-radius: 999px;
        background: #e2e8f0;
        color: #475569;
        font-size: 11px;
        font-weight: 900;
    }

    .group-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 10px 24px;
        padding: 12px 18px;
        border-bottom: 1px solid #e2e8f0;
        background: #fff;
    }

    .lab-table-wrapper {
        overflow-x: auto;
    }

    .lab-table {
        width: 100%;
        min-width: 720px;
        border-collapse: collapse;
    }

    .lab-table th {
        padding: 12px 14px;
        border-bottom: 1px solid #e2e8f0;
        background: #fff;
        color: #64748b;
        font-size: 11px;
        font-weight: 900;
        text-align: left;
        text-transform: uppercase;
        letter-spacing: .05em;
    }

    .lab-table td {
        padding: 14px;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
        font-size: 13px;
        vertical-align: middle;
    }

    .lab-table tr:last-child td {
        border-bottom: 0;
    }

    .test-name {
        color: #0f172a;
        font-weight: 800;
    }

    .result-value {
        color: #0f172a;
        font-size: 15px;
        font-weight: 900;
    }

    .result-value.is-high {
        color: #dc2626;
    }

    .result-value.is-low {
        color: #d97706;
    }

    .flag {
        display: inline-flex;
        min-width: 28px;
        align-items: center;
        justify-content: center;
        padding: 5px 8px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 900;
    }

    .flag.is-normal {
        background: #dcfce7;
        color: #166534;
    }

    .flag.is-high {
        background: #fee2e2;
        color: #b91c1c;
    }

    .flag.is-low {
        background: #fef3c7;
        color: #92400e;
    }

    .flag.is-other {
        background: #e2e8f0;
        color: #475569;
    }

    /* =========================================================
       MIKROBIOLOGI
       ========================================================= */

    .micro-report-title {
        margin-top: 18px;
        padding: 15px 18px;
        border: 1px solid #cbd5e1;
        border-radius: 16px 16px 0 0;
        background: #f8fafc;
        color: #0f172a;
        text-align: center;
        font-size: 17px;
        font-weight: 900;
    }

    .micro-info-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        border-right: 1px solid #cbd5e1;
        border-bottom: 1px solid #cbd5e1;
        border-left: 1px solid #cbd5e1;
    }

    .micro-panel {
        padding: 18px;
    }

    .micro-panel + .micro-panel {
        border-left: 1px solid #cbd5e1;
    }

    .section-title {
        margin-bottom: 13px;
        color: #0f172a;
        font-size: 14px;
        font-weight: 900;
        text-align: center;
    }

    .info-list {
        display: grid;
        gap: 8px;
    }

    .info-row {
        display: grid;
        grid-template-columns: 150px 12px minmax(0, 1fr);
        gap: 4px;
        color: #334155;
        font-size: 13px;
        line-height: 1.45;
    }

    .info-label {
        color: #64748b;
        font-weight: 700;
    }

    .micro-result {
        padding: 18px;
        border-right: 1px solid #cbd5e1;
        border-bottom: 1px solid #cbd5e1;
        border-left: 1px solid #cbd5e1;
    }

    .micro-result-grid {
        display: grid;
        grid-template-columns: 170px 12px minmax(0, 1fr);
        gap: 8px 4px;
        color: #334155;
        font-size: 14px;
        line-height: 1.55;
    }

    .micro-result-value {
        color: #0f172a;
        font-size: 16px;
        font-weight: 900;
    }

    .micro-comment {
        margin-top: 16px;
        padding: 15px 17px;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        background: #f8fafc;
        color: #475569;
        font-size: 13px;
        line-height: 1.7;
        white-space: pre-line;
    }

    .micro-culture-card {
        padding: 18px;
        border-bottom: 1px solid #e2e8f0;
    }

    .micro-culture-card:last-child {
        border-bottom: 0;
    }

    .micro-culture-heading {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
    }

    .micro-culture-product {
        color: #0f172a;
        font-size: 17px;
        font-weight: 900;
    }

    .micro-culture-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 10px 24px;
        margin-top: 14px;
        padding: 12px 14px;
        border-radius: 12px;
        background: #f8fafc;
    }

    .micro-culture-meta > div {
        display: flex;
        flex-direction: column;
        gap: 2px;
        color: #334155;
        font-size: 12px;
    }

    .micro-culture-meta span {
        color: #94a3b8;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .04em;
    }

    .micro-culture-section {
        margin-top: 16px;
    }

    .micro-culture-report {
        margin: 0;
        padding: 16px;
        overflow-x: auto;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        background: #0f172a;
        color: #e2e8f0;
        font-family: Consolas, Monaco, monospace;
        font-size: 12px;
        line-height: 1.65;
        white-space: pre-wrap;
        word-break: break-word;
    }

    /* =========================================================
       WATERMARK MDRO
       Tampil hanya jika commentheader mengandung "MDRO".
       ========================================================= */

    .mdro-watermark {
        position: relative;
        overflow: hidden;
        isolation: isolate;
    }

    .mdro-watermark::after {
        content: "MDRO";
        position: absolute;
        top: 50%;
        left: 50%;
        z-index: 20;
        transform: translate(-50%, -50%) rotate(-28deg);
        color: rgba(220, 38, 38, .16);
        font-size: clamp(72px, 13vw, 150px);
        font-weight: 1000;
        line-height: 1;
        letter-spacing: .08em;
        white-space: nowrap;
        pointer-events: none;
        user-select: none;
    }

    .signature-wrap {
        display: flex;
        justify-content: flex-end;
        margin-top: 24px;
    }

    .signature-box {
        width: min(100%, 430px);
        color: #334155;
        font-size: 13px;
        line-height: 1.7;
    }

    .signature-space {
        height: 56px;
    }

    .signature-name {
        color: #0f172a;
        font-weight: 800;
        text-decoration: underline;
    }

    /* =========================================================
       PATOLOGI ANATOMI
       ========================================================= */

    .pa-box {
        margin-top: 18px;
        overflow: hidden;
        border: 1px solid #fed7aa;
        border-radius: 18px;
        background: #fff;
    }

    .pa-title {
        padding: 15px 18px;
        border-bottom: 1px solid #fed7aa;
        background: #fff7ed;
        color: #9a3412;
        font-size: 16px;
        font-weight: 900;
    }

    .pa-content {
        padding: 18px;
    }

    .pa-field {
        margin-bottom: 16px;
    }

    .pa-field:last-child {
        margin-bottom: 0;
    }

    .pa-label {
        margin-bottom: 5px;
        color: #64748b;
        font-size: 12px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .04em;
    }

    .pa-value {
        color: #0f172a;
        font-size: 14px;
        line-height: 1.7;
        white-space: pre-line;
    }

    /* =========================================================
       NAVIGASI SAPA RSBM
       ========================================================= */

    .lab-navigation {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 18px;
        padding: 10px;
        border: 1px solid rgba(203, 213, 225, .85);
        border-radius: 18px;
        background: rgba(255, 255, 255, .96);
        box-shadow: 0 10px 28px rgba(15, 23, 42, .055);
    }

    .lab-navigation-left,
    .lab-navigation-right,
    .lab-bottom-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .lab-navigation-right {
        justify-content: flex-end;
    }

    .lab-nav-button {
        display: inline-flex;
        min-height: 40px;
        align-items: center;
        justify-content: center;
        gap: 7px;
        padding: 0 13px;
        border: 1px solid transparent;
        border-radius: 12px;
        font-family: inherit;
        font-size: 11px;
        font-weight: 900;
        line-height: 1;
        text-decoration: none;
        white-space: nowrap;
        cursor: pointer;
        transition:
            transform .18s ease,
            box-shadow .18s ease,
            border-color .18s ease,
            background .18s ease;
    }

    .lab-nav-button:hover {
        transform: translateY(-1px);
        text-decoration: none;
    }

    .lab-nav-button.is-lab {
        border-color: #bbf7d0;
        background: #f0fdf4;
        color: #15803d;
    }

    .lab-nav-button.is-lab:hover {
        border-color: #86efac;
        background: #dcfce7;
        color: #166534;
    }

    .lab-nav-button.is-menu {
        border-color: #c7d2fe;
        background: #eef1ff;
        color: #26358f;
    }

    .lab-nav-button.is-menu:hover {
        border-color: #a5b4fc;
        background: #e0e7ff;
        color: #1d286d;
    }

    .lab-nav-button.is-logout {
        border-color: #fecaca;
        background: #fff5f5;
        color: #b91c1c;
    }

    .lab-nav-button.is-logout:hover {
        border-color: #fca5a5;
        background: #fee2e2;
        color: #991b1b;
    }

    .lab-nav-button svg {
        flex: 0 0 auto;
    }

    .lab-bottom-actions {
        justify-content: center;
        margin-top: 22px;
        padding-top: 18px;
        border-top: 1px solid #e2e8f0;
    }

    .lab-logout-form {
        margin: 0;
    }

    @media (max-width: 720px) {
        .lab-navigation {
            align-items: stretch;
            flex-direction: column;
            padding: 9px;
        }

        .lab-navigation-left,
        .lab-navigation-right {
            display: grid;
            width: 100%;
            grid-template-columns: 1fr;
        }

        .lab-nav-button {
            width: 100%;
            min-height: 42px;
        }

        .lab-bottom-actions {
            display: grid;
            grid-template-columns: 1fr;
        }

        .lab-bottom-actions .lab-logout-form,
        .lab-bottom-actions .lab-nav-button {
            width: 100%;
        }

        .result-header {
            flex-direction: column;
        }

        .badge-row {
            justify-content: flex-start;
        }

        .micro-info-grid {
            grid-template-columns: 1fr;
        }

        .micro-panel + .micro-panel {
            border-top: 1px solid #cbd5e1;
            border-left: 0;
        }

        .info-row {
            grid-template-columns: 115px 10px minmax(0, 1fr);
            font-size: 12px;
        }

        .micro-result-grid {
            grid-template-columns: 120px 10px minmax(0, 1fr);
            font-size: 13px;
        }
    }


    /* =========================================================
       NADI MOBILE DETAIL LABORATORIUM
       ========================================================= */
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
        box-shadow: 0 26px 70px rgba(15, 45, 115, .11), 0 2px 10px rgba(15, 45, 115, .04);
    }

    .nadi-main {
        padding: 22px 18px 108px;
    }

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

    .nadi-greeting { min-width: 0; }

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

    .nadi-logout-form { margin: 0; }

    .detail-intro {
        position: relative;
        margin-bottom: 13px;
        padding: 15px;
        overflow: hidden;
        border: 1px solid #d8efdf;
        border-radius: 20px;
        background: linear-gradient(135deg, rgba(237, 252, 244, .98), rgba(248, 253, 255, .98));
        box-shadow: 0 10px 26px rgba(24, 170, 97, .06);
    }

    .detail-intro::after {
        position: absolute;
        top: -30px;
        right: -28px;
        width: 92px;
        height: 92px;
        border: 18px solid rgba(24, 170, 97, .055);
        border-radius: 50%;
        content: '';
    }

    .detail-intro-head {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .detail-intro-icon {
        display: grid;
        width: 45px;
        height: 45px;
        flex: 0 0 45px;
        place-items: center;
        border-radius: 13px;
        background: var(--nadi-green-soft);
        color: var(--nadi-green);
    }

    .detail-intro-title {
        margin: 0;
        color: var(--nadi-blue);
        font-size: 18px;
        font-weight: 950;
        line-height: 1.2;
        letter-spacing: -.025em;
    }

    .detail-intro-text {
        margin: 4px 0 0;
        color: var(--nadi-muted);
        font-size: 10.5px;
        font-weight: 650;
        line-height: 1.5;
    }

    .detail-patient {
        display: grid;
        grid-template-columns: 1.15fr .85fr;
        gap: 8px;
        margin-bottom: 12px;
    }

    .detail-patient-item {
        min-width: 0;
        padding: 11px 12px;
        border: 1px solid var(--nadi-line);
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 6px 16px rgba(20, 70, 130, .035);
    }

    .detail-patient-item.is-wide { grid-column: 1 / -1; }

    .detail-label {
        margin-bottom: 4px;
        color: #8795a9;
        font-size: 8.5px;
        font-weight: 900;
        letter-spacing: .04em;
        text-transform: uppercase;
    }

    .detail-value {
        min-width: 0;
        overflow-wrap: anywhere;
        color: var(--nadi-dark);
        font-size: 11.5px;
        font-weight: 900;
        line-height: 1.35;
    }

    .detail-back {
        display: inline-flex;
        min-height: 36px;
        align-items: center;
        justify-content: center;
        gap: 6px;
        margin-bottom: 12px;
        padding: 0 11px;
        border: 1px solid #cfe3fa;
        border-radius: 10px;
        background: var(--nadi-blue-soft);
        color: var(--nadi-blue-2);
        font-size: 9.5px;
        font-weight: 900;
        text-decoration: none;
    }

    .detail-back:hover {
        background: #e4f2ff;
        color: var(--nadi-blue);
        text-decoration: none;
    }

    /* Override komponen detail lama agar selaras dengan layout NADI */
    .lab-card {
        margin-bottom: 12px;
        padding: 13px;
        border-color: var(--nadi-line);
        border-radius: 18px;
        box-shadow: 0 8px 22px rgba(20, 70, 130, .045);
    }

    .result-header {
        gap: 10px;
        margin-bottom: 11px;
    }

    .result-title {
        color: var(--nadi-blue);
        font-size: 15px;
        line-height: 1.35;
    }

    .lab-meta {
        margin-top: 4px;
        color: #7c8da5;
        font-size: 9px;
        line-height: 1.45;
    }

    .badge-row { gap: 5px; }

    .badge {
        padding: 5px 7px;
        font-size: 8px;
    }

    .lab-alert {
        padding: 11px 12px;
        border-radius: 12px;
        font-size: 10px;
        line-height: 1.5;
    }

    .lab-group {
        margin-top: 12px;
        border-color: var(--nadi-line);
        border-radius: 15px;
    }

    .lab-group-header {
        gap: 8px;
        padding: 11px 12px;
        border-bottom-color: var(--nadi-line);
        background: #f8fbff;
    }

    .lab-group-title {
        color: var(--nadi-dark);
        font-size: 12px;
    }

    .lab-group-count {
        padding: 4px 7px;
        background: var(--nadi-blue-soft);
        color: var(--nadi-blue-2);
        font-size: 8px;
        white-space: nowrap;
    }

    .group-meta {
        gap: 7px 14px;
        padding: 9px 11px;
        border-bottom-color: var(--nadi-line);
    }

    .lab-table-wrapper {
        margin: 0;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .lab-table { min-width: 650px; }

    .lab-table th {
        padding: 9px 10px;
        color: #78889f;
        font-size: 8px;
    }

    .lab-table td {
        padding: 10px;
        color: #40536f;
        font-size: 9.5px;
    }

    .test-name {
        color: var(--nadi-dark);
        font-size: 10px;
    }

    .result-value { font-size: 11px; }

    .flag {
        min-width: 25px;
        padding: 4px 6px;
        font-size: 8px;
    }

    .micro-report-title {
        margin-top: 12px;
        padding: 11px 12px;
        border-color: var(--nadi-line);
        border-radius: 14px 14px 0 0;
        background: #f8fbff;
        color: var(--nadi-blue);
        font-size: 12px;
    }

    .micro-info-grid {
        grid-template-columns: 1fr;
        border-color: var(--nadi-line);
    }

    .micro-panel {
        padding: 12px;
    }

    .micro-panel + .micro-panel {
        border-top: 1px solid var(--nadi-line);
        border-left: 0;
    }

    .section-title {
        margin-bottom: 9px;
        color: var(--nadi-blue);
        font-size: 11px;
    }

    .info-list { gap: 6px; }

    .info-row {
        grid-template-columns: 105px 8px minmax(0, 1fr);
        color: #40536f;
        font-size: 9.5px;
    }

    .info-label {
        color: #7c8da5;
        font-size: 9px;
    }

    .micro-result {
        padding: 12px;
        border-color: var(--nadi-line);
    }

    .micro-result-grid {
        grid-template-columns: 110px 8px minmax(0, 1fr);
        color: #40536f;
        font-size: 10px;
    }

    .micro-result-value {
        color: var(--nadi-dark);
        font-size: 12px;
    }

    .micro-comment,
    .micro-culture-meta,
    .micro-culture-report {
        border-radius: 11px;
    }

    .micro-comment {
        margin-top: 10px;
        padding: 11px;
        font-size: 9.5px;
        line-height: 1.6;
    }

    .micro-culture-card { padding: 12px; }

    .micro-culture-product {
        color: var(--nadi-dark);
        font-size: 12px;
    }

    .micro-culture-meta {
        gap: 8px 14px;
        margin-top: 10px;
        padding: 9px 10px;
    }

    .micro-culture-meta > div { font-size: 9px; }
    .micro-culture-meta span { font-size: 7.5px; }

    .micro-culture-section { margin-top: 11px; }

    .micro-culture-report {
        padding: 11px;
        font-size: 9px;
        line-height: 1.55;
    }

    .mdro-watermark::after {
        font-size: clamp(60px, 18vw, 105px);
    }

    .signature-wrap { margin-top: 16px; }
    .signature-box { font-size: 9.5px; }
    .signature-space { height: 42px; }

    .pa-box {
        margin-top: 12px;
        border-radius: 15px;
    }

    .pa-title {
        padding: 11px 12px;
        font-size: 12px;
    }

    .pa-content { padding: 12px; }
    .pa-field { margin-bottom: 11px; }
    .pa-label { font-size: 8.5px; }
    .pa-value { font-size: 10px; line-height: 1.55; }

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
       Pada layar besar, jangan tampil seperti card/phone 520px.
       Konten memakai lebar halaman agar tabel hasil lebih nyaman.
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

        .detail-intro,
        .detail-patient,
        .detail-card,
        .lab-card {
            width: 100%;
        }

        .nadi-bottom-nav {
            width: 100vw;
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

        .nadi-main { padding: 18px 14px 100px; }
        .nadi-greeting-name { max-width: 205px; font-size: 22px; }
        .nadi-user-icon { width: 43px; height: 43px; flex-basis: 43px; }
        .result-header { flex-direction: column; }
        .badge-row { justify-content: flex-start; }
        .nadi-bottom-link { min-height: 68px; font-size: 8.5px; }
    }

    @media (max-width: 350px) {
        .nadi-main { padding-right: 10px; padding-left: 10px; }
        .nadi-greeting-name { max-width: 150px; }
        .detail-patient { grid-template-columns: 1fr; }
        .detail-patient-item.is-wide { grid-column: auto; }
        .info-row,
        .micro-result-grid { grid-template-columns: 88px 7px minmax(0, 1fr); }
    }


    /* =========================================================
       KONSISTENSI DENGAN MENU UTAMA NADI
       - desktop full width seperti dashboard menu
       - typography lebih nyaman untuk pasien
       - identitas pasien lebih ringkas
       - bottom navigation tampil di desktop + mobile
       ========================================================= */

    @media (min-width: 768px) {
        .nadi-page {
            padding: 26px clamp(24px, 3vw, 48px) 108px;
        }

        .nadi-header {
            margin-bottom: 18px;
            padding: 0 2px 18px;
            border-bottom: 1px solid rgba(22, 58, 147, .08);
        }

        .nadi-greeting-small {
            font-size: 13px;
        }

        .nadi-greeting-name {
            max-width: min(560px, 48vw);
            font-size: clamp(24px, 2.4vw, 32px);
        }

        .detail-intro {
            padding: 18px 20px;
            border-radius: 20px;
        }

        .detail-intro-icon {
            width: 50px;
            height: 50px;
            flex-basis: 50px;
            border-radius: 14px;
        }

        .detail-intro-title {
            font-size: 20px;
        }

        .detail-intro-text {
            font-size: 12px;
        }

        .detail-patient {
            grid-template-columns: minmax(260px, 1.5fr) minmax(180px, .7fr) minmax(180px, .7fr);
            gap: 10px;
        }

        .detail-patient-item,
        .detail-patient-item.is-wide {
            grid-column: auto;
            padding: 14px 16px;
        }

        .detail-label {
            font-size: 10.5px;
        }

        .detail-value {
            font-size: 13px;
        }

        .detail-back {
            min-height: 40px;
            padding: 0 14px;
            font-size: 11px;
        }

        .lab-card {
            padding: 18px 20px;
            border-radius: 20px;
        }

        .result-header {
            margin-bottom: 14px;
        }

        .result-title {
            font-size: 18px;
        }

        .lab-meta {
            font-size: 11.5px;
        }

        .badge {
            padding: 6px 9px;
            font-size: 10px;
        }

        .lab-alert {
            padding: 13px 14px;
            font-size: 12px;
        }

        .lab-group {
            margin-top: 14px;
            border-radius: 16px;
        }

        .lab-group-header {
            padding: 13px 15px;
        }

        .lab-group-title {
            font-size: 14px;
        }

        .lab-group-count {
            font-size: 10px;
        }

        .group-meta {
            padding: 10px 14px;
        }

        .lab-table {
            min-width: 760px;
        }

        .lab-table th {
            padding: 11px 12px;
            font-size: 10px;
        }

        .lab-table td {
            padding: 12px;
            font-size: 12px;
        }

        .test-name {
            font-size: 12px;
        }

        .result-value {
            font-size: 13px;
        }

        .flag {
            min-width: 28px;
            padding: 5px 7px;
            font-size: 9.5px;
        }

        .micro-report-title,
        .pa-title {
            font-size: 14px;
        }

        .micro-panel,
        .micro-result,
        .micro-culture-card,
        .pa-content {
            padding: 15px;
        }

        .section-title {
            font-size: 13px;
        }

        .info-row {
            grid-template-columns: 145px 10px minmax(0, 1fr);
            font-size: 12px;
        }

        .info-label {
            font-size: 11px;
        }

        .micro-result-grid {
            grid-template-columns: 155px 10px minmax(0, 1fr);
            font-size: 12px;
        }

        .micro-result-value {
            font-size: 14px;
        }

        .micro-comment,
        .micro-culture-meta > div,
        .pa-value,
        .signature-box {
            font-size: 11.5px;
        }

        .micro-culture-meta span,
        .pa-label {
            font-size: 10px;
        }

        .micro-culture-product {
            font-size: 14px;
        }

        .micro-culture-report {
            font-size: 11px;
        }
    }

    @media (min-width: 1024px) {
        .nadi-page {
            padding: 28px clamp(28px, 3vw, 56px) 104px;
        }

        .nadi-main {
            width: 100%;
            padding: 0 0 88px;
        }

        .detail-intro,
        .detail-patient,
        .lab-card {
            width: 100%;
        }

        .micro-info-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .micro-panel + .micro-panel {
            border-top: 0;
            border-left: 1px solid var(--nadi-line);
        }

        /* Footer utama mengikuti menu NADI pada desktop. */
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

    /* Footer mobile/tablet selalu terlihat seperti menu utama NADI. */
    @media (max-width: 1023px), (hover: none) and (pointer: coarse) {
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
            padding-bottom: max(6px, env(safe-area-inset-bottom)) !important;
            background: rgba(255, 255, 255, .98) !important;
            box-shadow: 0 -8px 24px rgba(15, 45, 115, .12) !important;
            visibility: visible !important;
            opacity: 1 !important;
            -webkit-transform: translateZ(0);
            transform: translateZ(0);
        }

        .nadi-main {
            padding-bottom: calc(100px + env(safe-area-inset-bottom)) !important;
        }

        .nadi-bottom-link {
            min-width: 0;
            min-height: 68px;
        }
    }

    @media (max-width: 767px) {
        .detail-intro-text {
            font-size: 11px;
        }

        .detail-label {
            font-size: 9.5px;
        }

        .detail-value {
            font-size: 12px;
        }

        .lab-meta {
            font-size: 10px;
        }

        .badge {
            font-size: 9px;
        }

        .lab-alert {
            font-size: 11px;
        }

        .lab-table th {
            font-size: 9px;
        }

        .lab-table td,
        .test-name {
            font-size: 10.5px;
        }

        .result-value {
            font-size: 12px;
        }
    }

</style>

@php
    /*
     * =========================================================
     * JENIS LAB BERASAL DARI PARAMETER ROUTE
     * =========================================================
     *
     * Nilai yang diharapkan:
     * - LAB - PATOLOGI KLINIK
     * - LAB - MIKROBIOLOGI KLINIK
     * - LAB - PATOLOGI ANATOMI
     */

    $labName = strtoupper(
        trim((string) ($lab ?? ''))
    );

    $isMikrobiologi = str_contains(
        $labName,
        'MIKROBIOLOGI'
    );

    $isPatologiAnatomi = str_contains(
        $labName,
        'PATOLOGI ANATOMI'
    );

    $isPatologiKlinik =
        str_contains(
            $labName,
            'PATOLOGI KLINIK'
        )
        && ! $isMikrobiologi
        && ! $isPatologiAnatomi;

    $success = (bool) data_get(
        $result,
        'success',
        false
    );

    $payload = data_get(
        $result,
        'data',
        []
    );

    $responseNoOrder = data_get(
        $payload,
        'noorder',
        $noOrder ?? '-'
    );

    $noBilling = data_get(
        $payload,
        'nobilling',
        '-'
    );

    /*
     * Patologi Klinik:
     * data.hasil = [
     *   [
     *     'group' => 'HEMATOLOGI',
     *     'items' => [...]
     *   ]
     * ]
     */
    $clinicalGroups = data_get(
        $payload,
        'hasil',
        []
    );

    /*
     * Mikrobiologi:
     * mendukung response langsung pada data
     * maupun data.mikrobiologi.
     */
    $microData = data_get(
        $payload,
        'mikrobiologi',
        $payload
    );

    /*
     * Patologi Anatomi:
     * sementara mendukung response langsung pada data
     * maupun data.patologi_anatomi.
     */
    $paData = data_get(
        $payload,
        'patologi_anatomi',
        $payload
    );

    /*
     * Navigasi halaman.
     * Menggunakan fallback URL agar Blade tidak error bila nama route
     * berbeda pada environment tertentu.
     */
    $laboratoryHistoryUrl = Route::has('laboratory.index')
        ? route('laboratory.index')
        : url('/cek-hasil-laboratorium');

    $mainMenuUrl = Route::has('layanan.menu')
        ? route('layanan.menu')
        : url('/layanan/menu');

    $logoutRouteName = Route::has('layanan.logout')
        ? 'layanan.logout'
        : (Route::has('logout') ? 'logout' : null);

    /*
     * Identitas pasien untuk header NADI.
     * Detail laboratorium tidak perlu bergantung pada controller untuk nama pasien;
     * gunakan session sebagai sumber utama dengan beberapa fallback field.
     */
    $sessionPatient = session('pasien', []);

    $patientName = trim((string) (
        data_get($sessionPatient, 'name')
        ?: data_get($sessionPatient, 'namapasien')
        ?: data_get($sessionPatient, 'patient_name')
        ?: data_get($sessionPatient, 'nama_pasien')
        ?: data_get($payload, 'namapasien')
        ?: data_get($microData, 'namapasien')
        ?: 'Pasien'
    ));

    $medicalRecord = trim((string) (
        data_get($sessionPatient, 'medical_record')
        ?: data_get($sessionPatient, 'rm')
        ?: data_get($sessionPatient, 'nocm')
        ?: data_get($payload, 'nocm')
        ?: data_get($microData, 'nocm')
        ?: '-'
    ));

    $birthDate = (
        data_get($sessionPatient, 'birth_date')
        ?: data_get($sessionPatient, 'tanggal_lahir')
        ?: data_get($sessionPatient, 'tgllahir')
        ?: data_get($payload, 'tgllahir')
        ?: data_get($microData, 'tgllahir')
        ?: null
    );

    $birthDateLabel = '-';

    if ($birthDate) {
        try {
            $birthDateLabel = \Carbon\Carbon::parse($birthDate)->format('d-m-Y');
        } catch (\Throwable $e) {
            $birthDateLabel = (string) $birthDate;
        }
    }

    $routeOrUrl = static function ($routeName, $fallback) {
        return Route::has($routeName)
            ? route($routeName)
            : url($fallback);
    };

    $labText = static function ($value, $default = '-') {
        if ($value === null) {
            return $default;
        }

        if ($value instanceof \Illuminate\Support\Collection) {
            $value = $value->all();
        }

        if (is_object($value)) {
            $value = (array) $value;
        }

        if (is_array($value)) {
            $parts = [];

            array_walk_recursive(
                $value,
                static function ($item) use (&$parts) {
                    if (
                        is_scalar($item)
                        && trim((string) $item) !== ''
                    ) {
                        $parts[] = trim((string) $item);
                    }
                }
            );

            $parts = array_values(
                array_unique($parts)
            );

            return count($parts)
                ? implode(', ', $parts)
                : $default;
        }

        if (is_bool($value)) {
            return $value ? 'Ya' : 'Tidak';
        }

        if (! is_scalar($value)) {
            return $default;
        }

        $value = trim((string) $value);

        return $value !== ''
            ? $value
            : $default;
    };
@endphp

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
                        href="{{ $laboratoryHistoryUrl }}"
                        class="nadi-icon-button"
                        aria-label="Kembali ke hasil laboratorium"
                        title="Kembali ke hasil laboratorium"
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

            {{-- Intro Detail Laboratorium --}}
            <section class="detail-intro">
                <div class="detail-intro-head">
                    <div class="detail-intro-icon" aria-hidden="true">
                        <svg width="27" height="27" viewBox="0 0 24 24" fill="none">
                            <path d="M9 3V8L5.5 15.2C4.2 17.9 6.2 21 9.2 21H14.8C17.8 21 19.8 17.9 18.5 15.2L15 8V3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M8 3H16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <path d="M7.5 14H16.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <circle cx="10" cy="17" r="1" fill="currentColor"/>
                            <circle cx="14" cy="16" r="1" fill="currentColor"/>
                        </svg>
                    </div>

                    <div>
                        <h1 class="detail-intro-title">Detail Hasil Laboratorium</h1>
                        <p class="detail-intro-text">
                            Detail pemeriksaan berdasarkan nomor order yang dipilih.
                        </p>
                    </div>
                </div>
            </section>

            {{-- Identitas pasien --}}
            <section class="detail-patient" aria-label="Identitas pasien">
                <div class="detail-patient-item is-wide">
                    <div class="detail-label">Nama Pasien</div>
                    <div class="detail-value">{{ $patientName ?: '-' }}</div>
                </div>

                <div class="detail-patient-item">
                    <div class="detail-label">No. Rekam Medis</div>
                    <div class="detail-value">{{ $medicalRecord ?: '-' }}</div>
                </div>

                <div class="detail-patient-item">
                    <div class="detail-label">Tanggal Lahir</div>
                    <div class="detail-value">{{ $birthDateLabel }}</div>
                </div>
            </section>

            <a href="{{ $laboratoryHistoryUrl }}" class="detail-back">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span>Kembali ke Hasil Laboratorium</span>
            </a>

    @if($result === null)

        <div class="lab-card">
            <div class="lab-alert is-error">
                Data hasil laboratorium belum tersedia.
            </div>
        </div>

    @elseif(! $success)

        <div class="lab-card">
            <div class="lab-alert is-error">
                {{ data_get(
                    $result,
                    'message',
                    'Hasil laboratorium tidak ditemukan.'
                ) }}
            </div>
        </div>

    @else

        <div class="lab-card">

            <div class="result-header">

                <div>

                    <div class="result-title">
                        @if($isMikrobiologi)
                            Hasil Pemeriksaan Mikrobiologi Klinik
                        @elseif($isPatologiAnatomi)
                            Hasil Pemeriksaan Patologi Anatomi
                        @elseif($isPatologiKlinik)
                            Hasil Pemeriksaan Patologi Klinik
                        @else
                            Hasil Pemeriksaan Laboratorium
                        @endif
                    </div>

                    <div class="lab-meta">
                        {{ $lab ?: 'Laboratorium RS Bali Mandara' }}
                    </div>

                </div>

                <div class="badge-row">

                    @if($isMikrobiologi)
                        <span class="badge is-type is-micro">
                            Mikrobiologi Klinik
                        </span>
                    @elseif($isPatologiAnatomi)
                        <span class="badge is-type is-pa">
                            Patologi Anatomi
                        </span>
                    @elseif($isPatologiKlinik)
                        <span class="badge is-type">
                            Patologi Klinik
                        </span>
                    @endif

                    @if(
                        $responseNoOrder
                        && $responseNoOrder !== '-'
                    )
                        <span class="badge is-order">
                            Order: {{ $labText($responseNoOrder) }}
                        </span>
                    @endif

                    @if(
                        $noBilling
                        && $noBilling !== '-'
                    )
                        <span class="badge is-billing">
                            Billing: {{ $labText($noBilling) }}
                        </span>
                    @endif

                </div>

            </div>

            <div class="lab-alert is-success">
                {{ data_get(
                    $result,
                    'message',
                    'Data hasil laboratorium berhasil diambil.'
                ) }}
            </div>

            {{-- =====================================================
                 LAB - PATOLOGI KLINIK
                 ===================================================== --}}
            @if($isPatologiKlinik)

                <div
                    class="lab-meta"
                    style="margin-top:10px;"
                >
                    Keterangan:
                    <strong>N</strong> = Normal,
                    <strong>H/HH</strong> = Tinggi,
                    <strong>L/LL</strong> = Rendah.
                </div>

                @forelse($clinicalGroups as $group)

                    @php
                        $items = data_get(
                            $group,
                            'items',
                            []
                        );
                    @endphp

                    <div class="lab-group">

                        <div class="lab-group-header">

                            <div class="lab-group-title">
                                {{ data_get(
                                    $group,
                                    'group',
                                    'Pemeriksaan'
                                ) }}
                            </div>

                            <div class="lab-group-count">
                                {{ count($items) }}
                                pemeriksaan
                            </div>

                        </div>

                        @if(
                            data_get($group, 'diotorisasi')
                            || data_get(
                                $group,
                                'dokterdiperiksa'
                            )
                        )

                            <div class="group-meta">

                                @if(
                                    data_get(
                                        $group,
                                        'diotorisasi'
                                    )
                                )
                                    <div
                                        class="lab-meta"
                                        style="margin-top:0;"
                                    >
                                        <strong
                                            style="color:#475569;"
                                        >
                                            Diotorisasi:
                                        </strong>

                                        {{ data_get(
                                            $group,
                                            'diotorisasi'
                                        ) }}
                                    </div>
                                @endif

                                @if(
                                    data_get(
                                        $group,
                                        'dokterdiperiksa'
                                    )
                                )
                                    <div
                                        class="lab-meta"
                                        style="margin-top:0;"
                                    >
                                        <strong
                                            style="color:#475569;"
                                        >
                                            Pemeriksa:
                                        </strong>

                                        {{ data_get(
                                            $group,
                                            'dokterdiperiksa'
                                        ) }}
                                    </div>
                                @endif

                            </div>

                        @endif

                        <div class="lab-table-wrapper">

                            <table class="lab-table">

                                <thead>
                                    <tr>
                                        <th>Pemeriksaan</th>
                                        <th>Hasil</th>
                                        <th>Flag</th>
                                        <th>Satuan</th>
                                        <th>Nilai Rujukan</th>
                                    </tr>
                                </thead>

                                <tbody>

                                @foreach($items as $item)

                                    @php
                                        $flag = strtoupper(
                                            trim(
                                                (string) data_get(
                                                    $item,
                                                    'flag',
                                                    ''
                                                )
                                            )
                                        );

                                        $flagClass = 'is-other';
                                        $resultClass = '';

                                        if (
                                            $flag === 'N'
                                            || $flag === ''
                                        ) {
                                            $flagClass =
                                                'is-normal';
                                        } elseif (
                                            str_contains(
                                                $flag,
                                                'H'
                                            )
                                        ) {
                                            $flagClass =
                                                'is-high';

                                            $resultClass =
                                                'is-high';
                                        } elseif (
                                            str_contains(
                                                $flag,
                                                'L'
                                            )
                                        ) {
                                            $flagClass =
                                                'is-low';

                                            $resultClass =
                                                'is-low';
                                        }

                                        $method = trim(
                                            (string) data_get(
                                                $item,
                                                'metode',
                                                ''
                                            )
                                        );
                                    @endphp

                                    <tr>

                                        <td>

                                            <div class="test-name">
                                                {{ data_get(
                                                    $item,
                                                    'detailpemeriksaan',
                                                    '-'
                                                ) }}
                                            </div>

                                            @if(
                                                data_get(
                                                    $item,
                                                    'namaproduk'
                                                )
                                            )
                                                <div class="lab-meta">
                                                    {{ data_get(
                                                        $item,
                                                        'namaproduk'
                                                    ) }}
                                                </div>
                                            @endif

                                            @if(
                                                data_get(
                                                    $item,
                                                    'tglhasil'
                                                )
                                            )
                                                <div class="lab-meta">
                                                    Hasil:
                                                    {{ data_get(
                                                        $item,
                                                        'tglhasil'
                                                    ) }}
                                                </div>
                                            @endif

                                            @if(
                                                $method !== ''
                                                && $method !== '-'
                                            )
                                                <div class="lab-meta">
                                                    Metode:
                                                    {{ $method }}
                                                </div>
                                            @endif

                                        </td>

                                        <td>
                                            <span
                                                class="result-value {{ $resultClass }}"
                                            >
                                                {{ data_get(
                                                    $item,
                                                    'hasil',
                                                    '-'
                                                ) }}
                                            </span>
                                        </td>

                                        <td>
                                            <span
                                                class="flag {{ $flagClass }}"
                                            >
                                                {{ $flag !== ''
                                                    ? $flag
                                                    : 'N' }}
                                            </span>
                                        </td>

                                        <td>
                                            {{ data_get(
                                                $item,
                                                'satuanstandar',
                                                '-'
                                            ) ?: '-' }}
                                        </td>

                                        <td>
                                            {{ data_get(
                                                $item,
                                                'nilaitext',
                                                '-'
                                            ) ?: '-' }}
                                        </td>

                                    </tr>

                                @endforeach

                                </tbody>

                            </table>

                        </div>

                    </div>

                @empty

                    <div
                        class="lab-alert is-error"
                        style="margin-top:18px;"
                    >
                        Data hasil pemeriksaan
                        Patologi Klinik belum tersedia.
                    </div>

                @endforelse

            {{-- =====================================================
                 LAB - MIKROBIOLOGI KLINIK
                 ===================================================== --}}
            @elseif($isMikrobiologi)

                @php
                    /*
                     * Struktur API Mikrobiologi terbaru:
                     * data.hasil = [
                     *   [
                     *     'group' => 'MIKROBIOLOGI',
                     *     'items' => [...],
                     *     'diotorisasi' => '...',
                     *     'dokterdiperiksa' => '...'
                     *   ]
                     * ]
                     *
                     * result_ft berisi laporan kultur/antibiogram
                     * dengan karakter ~ sebagai pemisah baris.
                     */
                    $microGroups = data_get(
                        $payload,
                        'hasil',
                        []
                    );

                    /*
                     * Jika ada items di data.hasil, gunakan format baru.
                     * Jika tidak, tampilan mikrobiologi lama di bawah
                     * tetap digunakan sebagai fallback.
                     */
                    $hasGroupedMicroResult =
                        is_array($microGroups)
                        && count($microGroups) > 0
                        && is_array(
                            data_get(
                                $microGroups,
                                '0.items'
                            )
                        );
                @endphp

                @if($hasGroupedMicroResult)

                    @forelse($microGroups as $group)

                        @php
                            $items = data_get(
                                $group,
                                'items',
                                []
                            );

                            $groupName = $labText(
                                data_get(
                                    $group,
                                    'group',
                                    'MIKROBIOLOGI'
                                )
                            );

                            $authorizedBy = trim(
                                (string) data_get(
                                    $group,
                                    'diotorisasi',
                                    ''
                                )
                            );

                            $examinedBy = trim(
                                (string) data_get(
                                    $group,
                                    'dokterdiperiksa',
                                    ''
                                )
                            );
                        @endphp

                        <div class="micro-report-title">
                            HASIL PEMERIKSAAN {{ strtoupper($groupName) }}
                        </div>

                        <div
                            class="lab-group"
                            style="margin-top:0; border-radius:0 0 20px 20px;"
                        >

                            <div class="lab-group-header">
                                <div class="lab-group-title">
                                    {{ $groupName }}
                                </div>

                                <div class="lab-group-count">
                                    {{ count($items) }} pemeriksaan
                                </div>
                            </div>

                            @if($authorizedBy !== '' || $examinedBy !== '')
                                <div class="group-meta">
                                    @if($examinedBy !== '')
                                        <div
                                            class="lab-meta"
                                            style="margin-top:0;"
                                        >
                                            <strong style="color:#475569;">
                                                Pemeriksa:
                                            </strong>
                                            {{ $examinedBy }}
                                        </div>
                                    @endif

                                    @if($authorizedBy !== '')
                                        <div
                                            class="lab-meta"
                                            style="margin-top:0;"
                                        >
                                            <strong style="color:#475569;">
                                                Diotorisasi:
                                            </strong>
                                            {{ $authorizedBy }}
                                        </div>
                                    @endif
                                </div>
                            @endif

                            @forelse($items as $item)

                                @php
                                    $productName = $labText(
                                        data_get(
                                            $item,
                                            'namaproduk',
                                            '-'
                                        )
                                    );

                                    $detailName = $labText(
                                        data_get(
                                            $item,
                                            'detailpemeriksaan',
                                            '-'
                                        )
                                    );

                                    $rawResultFt = data_get(
                                        $item,
                                        'result_ft',
                                        ''
                                    );

                                    $rawResult = data_get(
                                        $item,
                                        'hasil',
                                        ''
                                    );

                                    $formattedResultFt = trim(
                                        str_replace(
                                            '~',
                                            "\n",
                                            is_scalar($rawResultFt)
                                                ? (string) $rawResultFt
                                                : ''
                                        )
                                    );

                                    $formattedComment = trim(
                                        str_replace(
                                            '~',
                                            "\n",
                                            (string) data_get(
                                                $item,
                                                'comment',
                                                ''
                                            )
                                        )
                                    );

                                    $method = trim(
                                        (string) data_get(
                                            $item,
                                            'metode',
                                            ''
                                        )
                                    );

                                    $flag = strtoupper(
                                        trim(
                                            (string) data_get(
                                                $item,
                                                'flag',
                                                ''
                                            )
                                        )
                                    );

                                    $reportDate = trim(
                                        (string) data_get(
                                            $item,
                                            'tglhasil',
                                            ''
                                        )
                                    );

                                    /*
                                     * Watermark MDRO hanya untuk item
                                     * mikrobiologi yang commentheader-nya
                                     * mengandung kata MDRO.
                                     */
                                    $commentHeader = strtoupper(
                                        trim(
                                            (string) data_get(
                                                $item,
                                                'commentheader',
                                                ''
                                            )
                                        )
                                    );

                                    $isMdro = str_contains(
                                        $commentHeader,
                                        'MDRO'
                                    );
                                @endphp

                                <div class="micro-culture-card {{ $isMdro ? 'mdro-watermark' : '' }}">

                                    <div class="micro-culture-heading">
                                        <div>
                                            <div class="micro-culture-product">
                                                {{ $productName }}
                                            </div>

                                            <div class="lab-meta">
                                                Detail pemeriksaan:
                                                <strong style="color:#475569;">
                                                    {{ $detailName }}
                                                </strong>
                                            </div>
                                        </div>

                                        @if($flag !== '')
                                            <span class="flag is-other">
                                                {{ $flag }}
                                            </span>
                                        @endif
                                    </div>

                                    <div class="micro-culture-meta">
                                        @if($reportDate !== '')
                                            <div>
                                                <span>Tanggal hasil</span>
                                                <strong>
                                                    {{ $reportDate }}
                                                </strong>
                                            </div>
                                        @endif

                                        @if($method !== '' && $method !== '-')
                                            <div>
                                                <span>Metode</span>
                                                <strong>{{ $method }}</strong>
                                            </div>
                                        @endif

                                        @if(data_get($item, 'satuanstandar'))
                                            <div>
                                                <span>Satuan</span>
                                                <strong>
                                                    {{ data_get(
                                                        $item,
                                                        'satuanstandar'
                                                    ) }}
                                                </strong>
                                            </div>
                                        @endif
                                    </div>

                                    @if($formattedResultFt !== '')
                                        <div class="micro-culture-section">
                                            <div
                                                class="section-title"
                                                style="text-align:left; margin-bottom:8px;"
                                            >
                                                Hasil Kultur / Antibiogram
                                            </div>

                                            <pre class="micro-culture-report">{{ $formattedResultFt }}</pre>
                                        </div>
                                    @elseif(
                                        is_scalar($rawResult)
                                        && trim((string) $rawResult) !== ''
                                    )
                                        <div class="micro-culture-section">
                                            <div
                                                class="section-title"
                                                style="text-align:left; margin-bottom:8px;"
                                            >
                                                Hasil
                                            </div>

                                            <div class="micro-result-value">
                                                {{ trim((string) $rawResult) }}
                                            </div>
                                        </div>
                                    @else
                                        <div
                                            class="lab-alert is-warning"
                                            style="margin-top:16px;"
                                        >
                                            Hasil pemeriksaan belum tersedia.
                                        </div>
                                    @endif

                                    @if($formattedComment !== '')
                                        <div class="micro-comment">
                                            <strong>Komentar:</strong>
                                            {{ $formattedComment }}
                                        </div>
                                    @endif

                                </div>

                            @empty

                                <div
                                    class="lab-alert is-warning"
                                    style="margin:18px;"
                                >
                                    Item hasil mikrobiologi belum tersedia.
                                </div>

                            @endforelse

                        </div>

                    @empty

                        <div
                            class="lab-alert is-warning"
                            style="margin-top:18px;"
                        >
                            Data hasil pemeriksaan Mikrobiologi
                            belum tersedia.
                        </div>

                    @endforelse

                @else

                    {{-- Fallback struktur mikrobiologi lama --}}

                @php
                    $namaProduk = $labText(
                        data_get(
                            $microData,
                            'namaproduk',
                            '-'
                        )
                    );

                    $rawHasilSpesimen = data_get(
                        $microData,
                        'hasilspesimen'
                    );

                    if ($rawHasilSpesimen === null) {
                        $candidateHasil = data_get(
                            $microData,
                            'hasil'
                        );

                        $rawHasilSpesimen =
                            is_scalar($candidateHasil)
                                ? $candidateHasil
                                : '-';
                    }

                    $hasilSpesimen = $labText(
                        $rawHasilSpesimen
                    );

                    $microComment = $labText(
                        data_get(
                            $microData,
                            'comment',
                            data_get(
                                $microData,
                                'komentar',
                                ''
                            )
                        ),
                        ''
                    );

                    $microCommentHeader = strtoupper(
                        trim(
                            (string) data_get(
                                $microData,
                                'commentheader',
                                ''
                            )
                        )
                    );

                    $isMdroFallback = str_contains(
                        $microCommentHeader,
                        'MDRO'
                    );

                    $upperProduct = strtoupper(
                        $namaProduk
                    );

                    $isCovid =
                        str_contains(
                            $upperProduct,
                            'COVID'
                        )
                        || str_contains(
                            $upperProduct,
                            'SARS'
                        );

                    $rawReportDate = data_get(
                        $microData,
                        'tglkeluarhasil',
                        data_get(
                            $microData,
                            'tglhasil'
                        )
                    );

                    $reportDate =
                        is_scalar($rawReportDate)
                            ? trim(
                                (string) $rawReportDate
                            )
                            : '';

                    $reportDateText = '-';

                    if ($reportDate !== '') {
                        try {
                            $reportDateText =
                                \Carbon\Carbon::parse(
                                    $reportDate
                                )->isoFormat(
                                    'DD MMMM Y'
                                );
                        } catch (\Throwable $e) {
                            $reportDateText =
                                $reportDate;
                        }
                    }
                @endphp

                <div class="micro-report-title">
                    {{ $isCovid
                        ? 'HASIL PEMERIKSAAN COVID-19'
                        : 'HASIL PEMERIKSAAN MIKROBIOLOGI' }}
                </div>

                <div class="micro-info-grid">

                    <div class="micro-panel">

                        <div class="section-title">
                            Informasi Pasien
                        </div>

                        <div class="info-list">

                            <div class="info-row">
                                <div class="info-label">
                                    Nama
                                </div>
                                <div>:</div>
                                <div>
                                    {{ $labText(
                                        data_get(
                                            $microData,
                                            'namapasien',
                                            '-'
                                        )
                                    ) }}
                                </div>
                            </div>

                            <div class="info-row">
                                <div class="info-label">
                                    Tanggal Lahir
                                </div>
                                <div>:</div>
                                <div>
                                    {{ $labText(
                                        data_get(
                                            $microData,
                                            'tgllahir',
                                            '-'
                                        )
                                    ) }}

                                    @if(
                                        data_get(
                                            $microData,
                                            'umur'
                                        )
                                    )
                                        /
                                        {{ $labText(
                                            data_get(
                                                $microData,
                                                'umur'
                                            )
                                        ) }}
                                    @endif
                                </div>
                            </div>

                            <div class="info-row">
                                <div class="info-label">
                                    Jenis Kelamin
                                </div>
                                <div>:</div>
                                <div>
                                    {{ $labText(
                                        data_get(
                                            $microData,
                                            'jeniskelamin',
                                            '-'
                                        )
                                    ) }}
                                </div>
                            </div>

                            <div class="info-row">
                                <div class="info-label">
                                    No. RM
                                </div>
                                <div>:</div>
                                <div>
                                    {{ $labText(
                                        data_get(
                                            $microData,
                                            'nocm',
                                            '-'
                                        )
                                    ) }}
                                </div>
                            </div>

                            @if(
                                data_get(
                                    $microData,
                                    'noidentitas'
                                )
                            )
                                <div class="info-row">
                                    <div class="info-label">
                                        No. Identitas
                                    </div>
                                    <div>:</div>
                                    <div>
                                        {{ $labText(
                                            data_get(
                                                $microData,
                                                'noidentitas'
                                            )
                                        ) }}
                                    </div>
                                </div>
                            @endif

                            @if(
                                data_get(
                                    $microData,
                                    'nohp'
                                )
                            )
                                <div class="info-row">
                                    <div class="info-label">
                                        No. Telp/HP
                                    </div>
                                    <div>:</div>
                                    <div>
                                        {{ $labText(
                                            data_get(
                                                $microData,
                                                'nohp'
                                            )
                                        ) }}
                                    </div>
                                </div>
                            @endif

                            @if(
                                data_get(
                                    $microData,
                                    'alamatlengkap'
                                )
                            )
                                <div class="info-row">
                                    <div class="info-label">
                                        Alamat
                                    </div>
                                    <div>:</div>
                                    <div>
                                        {{ $labText(
                                            data_get(
                                                $microData,
                                                'alamatlengkap'
                                            )
                                        ) }}
                                    </div>
                                </div>
                            @endif

                        </div>

                    </div>

                    <div class="micro-panel">

                        <div class="section-title">
                            Informasi Spesimen
                        </div>

                        <div class="info-list">

                            <div class="info-row">
                                <div class="info-label">
                                    Jenis Spesimen
                                </div>
                                <div>:</div>
                                <div>
                                    {{ $labText(
                                        data_get(
                                            $microData,
                                            'jenisspesimen',
                                            '-'
                                        )
                                    ) }}
                                </div>
                            </div>

                            <div class="info-row">
                                <div class="info-label">
                                    Kode Spesimen
                                </div>
                                <div>:</div>
                                <div>
                                    {{ $labText(
                                        data_get(
                                            $microData,
                                            'kodespesimen',
                                            '-'
                                        )
                                    ) }}
                                </div>
                            </div>

                            <div class="info-row">
                                <div class="info-label">
                                    Spesimen Ke-
                                </div>
                                <div>:</div>
                                <div>
                                    {{ $labText(
                                        data_get(
                                            $microData,
                                            'spesimenke',
                                            '-'
                                        )
                                    ) }}
                                </div>
                            </div>

                            <div class="info-row">
                                <div class="info-label">
                                    Asal Spesimen
                                </div>
                                <div>:</div>
                                <div>
                                    {{ $labText(
                                        data_get(
                                            $microData,
                                            'asalspesimen',
                                            '-'
                                        )
                                    ) }}
                                </div>
                            </div>

                            <div class="info-row">
                                <div class="info-label">
                                    Tgl & Jam Pengambilan
                                </div>
                                <div>:</div>
                                <div>
                                    {{ $labText(
                                        data_get(
                                            $microData,
                                            'tglterimaspesimen',
                                            '-'
                                        )
                                    ) }}
                                </div>
                            </div>

                            <div class="info-row">
                                <div class="info-label">
                                    Tgl Diproses
                                </div>
                                <div>:</div>
                                <div>
                                    {{ $labText(
                                        data_get(
                                            $microData,
                                            'tgldikerjakanspesimen',
                                            '-'
                                        )
                                    ) }}
                                </div>
                            </div>

                            <div class="info-row">
                                <div class="info-label">
                                    Tgl Pelaporan
                                </div>
                                <div>:</div>
                                <div>
                                    {{ $labText(
                                        $reportDate,
                                        '-'
                                    ) }}
                                </div>
                            </div>

                        </div>

                    </div>

                </div>

                <div class="micro-result {{ $isMdroFallback ? 'mdro-watermark' : '' }}">

                    <div class="micro-result-grid">

                        <div class="info-label">
                            Jenis Pemeriksaan
                        </div>
                        <div>:</div>
                        <div>
                            {{ $namaProduk }}
                        </div>

                        <div class="info-label">
                            Hasil
                        </div>
                        <div>:</div>
                        <div class="micro-result-value">
                            {{ $hasilSpesimen }}

                            @if($isCovid)
                                SARS-CoV2
                            @endif
                        </div>

                    </div>

                    @if($microComment !== '')

                        <div class="micro-comment">
                            <strong>Komentar:</strong>
                            {{ $microComment }}
                        </div>

                    @elseif($isCovid)

                        <div class="micro-comment"><strong>Komentar:</strong>
1. SARS-CoV2 adalah virus penyebab COVID-19.
2. Hasil AG-RDT negatif berarti komponen protein virus SARS-CoV2 tidak terdeteksi di atas ambang batas kit deteksi.
3. Hasil negatif belum dapat menyingkirkan COVID-19. Bila secara klinis masih mengarah COVID-19, pemeriksaan lanjutan dapat dipertimbangkan sesuai penilaian tenaga kesehatan.
4. Hubungi layanan kesehatan untuk informasi lebih lanjut dan ikuti anjuran kesehatan yang berlaku.</div>

                    @endif

                </div>

                @if($reportDate)

                    <div class="signature-wrap">

                        <div class="signature-box">

                            <div>
                                Denpasar,
                                {{ $reportDateText }}
                            </div>

                            <div>
                                <strong>
                                    Kepala Laboratorium
                                    Mikrobiologi Klinik
                                </strong>
                            </div>

                            <div class="signature-space"></div>

                            <div class="signature-name">
                                dr I Wy Agus Gede Manik S,
                                M.Ked.Klin, Sp.MK
                            </div>

                            <div>
                                NIP. 198308142009021004
                            </div>

                        </div>

                    </div>

                @endif

                @endif

            {{-- =====================================================
                 LAB - PATOLOGI ANATOMI
                 ===================================================== --}}
            @elseif($isPatologiAnatomi)

                @php
                    /*
                     * Struktur final API Patologi Anatomi
                     * belum diberikan.
                     *
                     * Field di bawah dibuat tolerant:
                     * bila field tersedia akan tampil,
                     * bila tidak tersedia tidak menyebabkan error.
                     */

                    $paNamaProduk = data_get(
                        $paData,
                        'namaproduk',
                        data_get(
                            $paData,
                            'pemeriksaan',
                            '-'
                        )
                    );

                    $paHasil = data_get(
                        $paData,
                        'hasil',
                        data_get(
                            $paData,
                            'hasilpemeriksaan'
                        )
                    );

                    $paMakroskopik = data_get(
                        $paData,
                        'makroskopik'
                    );

                    $paMikroskopik = data_get(
                        $paData,
                        'mikroskopik'
                    );

                    $paKesimpulan = data_get(
                        $paData,
                        'kesimpulan',
                        data_get(
                            $paData,
                            'diagnosa'
                        )
                    );

                    $paCatatan = data_get(
                        $paData,
                        'catatan',
                        data_get(
                            $paData,
                            'comment'
                        )
                    );

                    $paTanggal = data_get(
                        $paData,
                        'tglhasil',
                        data_get(
                            $paData,
                            'tglkeluarhasil'
                        )
                    );
                @endphp

                <div class="pa-box">

                    <div class="pa-title">
                        HASIL PEMERIKSAAN PATOLOGI ANATOMI
                    </div>

                    <div class="pa-content">

                        <div class="pa-field">
                            <div class="pa-label">
                                Pemeriksaan
                            </div>
                            <div class="pa-value">
                                {{ $labText($paNamaProduk) }}
                            </div>
                        </div>

                        @if($paMakroskopik)
                            <div class="pa-field">
                                <div class="pa-label">
                                    Makroskopik
                                </div>
                                <div class="pa-value">
                                    {{ $labText($paMakroskopik) }}
                                </div>
                            </div>
                        @endif

                        @if($paMikroskopik)
                            <div class="pa-field">
                                <div class="pa-label">
                                    Mikroskopik
                                </div>
                                <div class="pa-value">
                                    {{ $labText($paMikroskopik) }}
                                </div>
                            </div>
                        @endif

                        @if($paHasil)
                            <div class="pa-field">
                                <div class="pa-label">
                                    Hasil Pemeriksaan
                                </div>
                                <div class="pa-value">
                                    {{ $labText($paHasil) }}
                                </div>
                            </div>
                        @endif

                        @if($paKesimpulan)
                            <div class="pa-field">
                                <div class="pa-label">
                                    Kesimpulan / Diagnosis
                                </div>
                                <div class="pa-value">
                                    {{ $labText($paKesimpulan) }}
                                </div>
                            </div>
                        @endif

                        @if($paCatatan)
                            <div class="pa-field">
                                <div class="pa-label">
                                    Catatan
                                </div>
                                <div class="pa-value">
                                    {{ $labText($paCatatan) }}
                                </div>
                            </div>
                        @endif

                        @if($paTanggal)
                            <div class="pa-field">
                                <div class="pa-label">
                                    Tanggal Hasil
                                </div>
                                <div class="pa-value">
                                    {{ $labText($paTanggal) }}
                                </div>
                            </div>
                        @endif

                        @if(
                            ! $paHasil
                            && ! $paMakroskopik
                            && ! $paMikroskopik
                            && ! $paKesimpulan
                        )
                            <div class="lab-alert is-warning">
                                Data Patologi Anatomi ditemukan,
                                tetapi struktur detail hasil belum
                                sesuai dengan field yang tersedia
                                pada tampilan ini.
                            </div>
                        @endif

                    </div>

                </div>

            {{-- =====================================================
                 JENIS LAB TIDAK DIKENALI
                 ===================================================== --}}
            @else

                <div
                    class="lab-alert is-error"
                    style="margin-top:18px;"
                >
                    Jenis laboratorium tidak dikenali:
                    <strong>
                        {{ $lab ?: '-' }}
                    </strong>
                </div>

            @endif

        </div>

    @endif

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