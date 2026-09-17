@extends('layouts.app', ['title' => 'NADI RSBM | RSUD Bali Mandara Provinsi Bali'])

@push('head')
<meta name="theme-color" content="#163a93">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="apple-mobile-web-app-title" content="NADI RSBM">
<link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
<link rel="apple-touch-icon" href="{{ asset('images/pwa/apple-touch-icon.png') }}">
@endpush

@section('content')
@php
    $oldTanggalLahir = old('tanggal_lahir');
    $tanggalLahirDisplay = '';

    if ($oldTanggalLahir && preg_match('/^\d{4}-\d{2}-\d{2}$/', $oldTanggalLahir)) {
        try {
            $tanggalLahirDisplay = \Carbon\Carbon::createFromFormat(
                'Y-m-d',
                $oldTanggalLahir
            )->format('d-m-Y');
        } catch (\Throwable $e) {
            $tanggalLahirDisplay = $oldTanggalLahir;
        }
    }
@endphp

<style>
    :root {
        --nadi-blue: #163a93;
        --nadi-blue-2: #1477ee;
        --nadi-blue-dark: #0f2d73;
        --nadi-blue-soft: #edf6ff;
        --nadi-green: #18aa61;
        --nadi-green-dark: #118148;
        --nadi-green-soft: #eafaf1;
        --nadi-text: #183153;
        --nadi-muted: #66758e;
        --nadi-line: #dfe8f2;
        --nadi-bg: #f7fbff;
        --nadi-danger: #dc2626;
    }

    html,
    body {
        margin: 0;
        padding: 0;
        background: var(--nadi-bg);
    }

    #app > nav.navbar,
    #app > .navbar,
    body > nav.navbar,
    nav.navbar.navbar-expand-md,
    .navbar.navbar-expand-md {
        display: none !important;
    }

    #app,
    #app > main,
    main.py-4,
    .py-4 {
        margin-top: 0 !important;
        padding-top: 0 !important;
    }

    .nadi-login-page,
    .nadi-login-page * {
        box-sizing: border-box;
    }

    .nadi-login-page {
        min-height: 100svh;
        padding:
            calc(14px + env(safe-area-inset-top))
            14px
            calc(26px + env(safe-area-inset-bottom));
        background:
            radial-gradient(circle at 92% 2%, rgba(20,119,238,.08), transparent 22%),
            linear-gradient(180deg, #fff 0%, var(--nadi-bg) 100%);
        color: var(--nadi-text);
        font-family: Inter, Arial, Helvetica, sans-serif;
    }

    .nadi-login-shell {
        width: 100%;
        max-width: 1320px;
        margin: 0 auto;
    }

    .nadi-login-layout {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    /* =========================
       BRAND / HERO
       ========================= */
    .nadi-brand-block {
        min-width: 0;
    }

    .nadi-brand-row {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 10px;
    }

    .nadi-logo-box {
        display: grid;
        width: 46px;
        height: 46px;
        flex: 0 0 46px;
        place-items: center;
        overflow: hidden;
        border: 1px solid rgba(22,58,147,.10);
        border-radius: 13px;
        background: #fff;
        box-shadow: 0 7px 18px rgba(15,45,115,.07);
    }

    .nadi-logo {
        width: 40px;
        height: 40px;
        object-fit: contain;
    }

    .nadi-logo-fallback {
        display: none;
        place-items: center;
        width: 38px;
        height: 38px;
        border-radius: 11px;
        background: linear-gradient(145deg, var(--nadi-blue), var(--nadi-blue-dark));
        color: #fff;
        font-size: 17px;
        font-weight: 900;
    }

    .nadi-gov {
        color: #7a8aa1;
        font-size: 8px;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .nadi-hospital {
        margin-top: 1px;
        color: var(--nadi-blue);
        font-size: 14px;
        font-weight: 900;
        line-height: 1.25;
    }

    .nadi-location {
        margin-top: 1px;
        color: #91a0b2;
        font-size: 9px;
        font-weight: 600;
    }

    .nadi-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        margin-bottom: 7px;
        padding: 5px 8px;
        border: 1px solid rgba(22,58,147,.08);
        border-radius: 999px;
        background: rgba(255,255,255,.88);
        color: var(--nadi-blue);
        font-size: 8px;
        font-weight: 850;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .nadi-eyebrow-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: var(--nadi-green);
        box-shadow: 0 0 0 4px rgba(24,170,97,.10);
    }

    .nadi-login-title {
        margin: 0;
        color: var(--nadi-text);
        font-size: clamp(27px, 8vw, 34px);
        font-weight: 900;
        line-height: 1.06;
        letter-spacing: -.035em;
    }

    .nadi-login-title span {
        color: var(--nadi-blue);
    }

    .nadi-login-lead {
        display: -webkit-box;
        margin: 8px 0 0;
        overflow: hidden;
        color: var(--nadi-muted);
        font-size: 11.5px;
        line-height: 1.5;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
    }

    .nadi-login-lead strong {
        color: var(--nadi-blue);
        font-weight: 800;
    }

    .nadi-trust-row {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 6px;
        margin-top: 10px;
    }

    .nadi-trust-item {
        display: flex;
        min-width: 0;
        align-items: center;
        gap: 5px;
        padding: 7px 6px;
        border: 1px solid #e7eef6;
        border-radius: 10px;
        background: rgba(255,255,255,.82);
        color: #60738d;
        font-size: 9.5px;
        font-weight: 700;
        line-height: 1.25;
    }

    .nadi-trust-icon {
        display: grid;
        width: 18px;
        height: 18px;
        flex: 0 0 18px;
        place-items: center;
        border-radius: 6px;
        background: var(--nadi-green-soft);
        color: var(--nadi-green-dark);
    }

    /* =========================
       LOGIN CARD
       ========================= */
    .nadi-login-card {
        position: relative;
        width: 100%;
        overflow: hidden;
        padding: 16px 14px 14px;
        border: 1px solid var(--nadi-line);
        border-radius: 18px;
        background: #fff;
        box-shadow: 0 10px 28px rgba(15,45,115,.07);
    }

    .nadi-login-card::before {
        position: absolute;
        top: 0;
        right: 0;
        left: 0;
        height: 4px;
        background: linear-gradient(
            90deg,
            var(--nadi-blue) 0 78%,
            var(--nadi-green) 78% 100%
        );
        content: "";
    }

    .nadi-card-head {
        display: flex;
        min-width: 0;
        align-items: center;
        gap: 10px;
        margin-bottom: 14px;
    }

    .nadi-card-icon {
        display: grid;
        width: 40px;
        height: 40px;
        flex: 0 0 40px;
        place-items: center;
        border-radius: 12px;
        background: var(--nadi-blue-soft);
        color: var(--nadi-blue);
    }

    .nadi-card-title-wrap {
        min-width: 0;
    }

    .nadi-card-title {
        margin: 0;
        color: var(--nadi-text);
        font-size: 18px;
        font-weight: 850;
        line-height: 1.25;
    }

    .nadi-card-subtitle {
        margin: 3px 0 0;
        color: var(--nadi-muted);
        font-size: 10.5px;
        line-height: 1.4;
    }

    /* =========================
       FORM
       ========================= */
    .queue-form {
        display: grid;
        width: 100%;
        gap: 11px;
    }

    .queue-field {
        display: grid;
        gap: 5px;
    }

    .queue-field label {
        color: #405775;
        font-size: 12px;
        font-weight: 700;
        line-height: 1.3;
    }

    .queue-input-wrapper {
        position: relative;
        min-width: 0;
    }

    .queue-input-icon {
        position: absolute;
        top: 50%;
        left: 13px;
        display: grid;
        width: 20px;
        height: 20px;
        place-items: center;
        color: #8394aa;
        pointer-events: none;
        transform: translateY(-50%);
    }

    .queue-input {
        display: block;
        width: 100%;
        min-width: 0;
        height: 50px;
        padding: 0 12px 0 42px;
        border: 1.4px solid #d8e4ef;
        border-radius: 12px;
        outline: none;
        background: #fbfdff;
        color: var(--nadi-text);
        font: inherit;
        font-size: 15px;
        font-weight: 600;
        transition: border-color .18s ease, box-shadow .18s ease, background .18s ease;
    }

    .queue-input::placeholder {
        color: #a8b4c2;
        font-weight: 500;
    }

    .queue-input:focus {
        border-color: #8ab9ef;
        background: #fff;
        box-shadow: 0 0 0 4px rgba(20,119,238,.08);
    }

    .queue-field.has-error .queue-input {
        border-color: #ef4444;
        background: #fffafa;
    }

    .field-description,
    .field-error {
        margin: 0;
        font-size: 10px;
        line-height: 1.4;
    }

    .field-description {
        color: #8797aa;
    }

    .field-error {
        color: var(--nadi-danger);
        font-weight: 700;
    }

    .validation-alert {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        padding: 10px 11px;
        border: 1px solid #fecaca;
        border-radius: 11px;
        background: #fff4f4;
        color: #b91c1c;
        font-size: 10.5px;
        font-weight: 650;
        line-height: 1.45;
    }

    /* CAPTCHA dibuat 1 baris, hanya ditumpuk di layar sangat kecil */
    .captcha-box {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 108px;
        gap: 7px;
        align-items: stretch;
    }

    .captcha-question {
        display: flex;
        min-width: 0;
        min-height: 50px;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        padding: 0 10px;
        border: 1.4px solid #d2e3f6;
        border-radius: 12px;
        background: linear-gradient(135deg, var(--nadi-blue-soft), #f8fbff);
    }

    .captcha-label {
        color: #75879e;
        font-size: 9.5px;
        font-weight: 700;
    }

    .captcha-question strong {
        color: var(--nadi-blue);
        font-size: 15px;
        font-weight: 850;
        white-space: nowrap;
    }

    .captcha-input-wrapper .queue-input {
        padding-left: 38px;
        padding-right: 8px;
        text-align: center;
        font-size: 16px;
        font-weight: 700;
    }

    .captcha-input-wrapper .queue-input-icon {
        left: 10px;
    }

    .queue-submit,
    .install-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border: 0;
        font-family: inherit;
        cursor: pointer;
        transition: transform .18s ease, box-shadow .18s ease, opacity .18s ease;
    }

    .queue-submit {
        width: 100%;
        min-height: 52px;
        margin-top: 1px;
        padding: 0 14px;
        border-radius: 12px;
        background: linear-gradient(135deg, var(--nadi-blue-2), var(--nadi-blue));
        color: #fff;
        font-size: 13.5px;
        font-weight: 800;
        box-shadow: 0 12px 24px rgba(20,119,238,.20);
    }

    .queue-submit:hover {
        transform: translateY(-1px);
        box-shadow: 0 14px 28px rgba(20,119,238,.25);
    }

    .queue-submit:disabled {
        cursor: wait;
        opacity: .7;
        transform: none;
    }

    .nadi-security-line {
        display: flex;
        align-items: flex-start;
        gap: 6px;
        padding-top: 1px;
        color: #6b7d93;
        font-size: 10px;
        line-height: 1.45;
    }

    .nadi-security-line svg {
        flex: 0 0 auto;
        margin-top: 1px;
        color: var(--nadi-green-dark);
    }

    .nadi-card-footer {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .install-button {
        display: none;
        width: 100%;
        min-height: 40px;
        padding: 0 11px;
        border: 1px solid #cfe2f7;
        border-radius: 10px;
        background: var(--nadi-blue-soft);
        color: var(--nadi-blue);
        font-size: 10.5px;
        font-weight: 750;
    }

    .install-button.is-visible {
        display: inline-flex;
    }

    .install-button:disabled {
        opacity: .6;
        cursor: default;
    }

    .queue-input:focus-visible,
    .queue-submit:focus-visible,
    .install-button:focus-visible {
        outline: 3px solid rgba(20,119,238,.20);
        outline-offset: 2px;
    }

    /* =========================
       VERY SMALL MOBILE
       ========================= */
    @media (max-width: 360px) {
        .nadi-login-page {
            padding-right: 10px;
            padding-left: 10px;
        }

        .nadi-trust-row {
            grid-template-columns: 1fr;
        }

        .captcha-box {
            grid-template-columns: 1fr;
        }

        .captcha-input-wrapper .queue-input {
            text-align: left;
            padding-left: 42px;
        }
    }

    /* =========================
       TABLET
       ========================= */
    @media (min-width: 601px) {
        .nadi-login-page {
            padding: 28px 24px 36px;
        }

        .nadi-login-layout {
            gap: 24px;
        }

        .nadi-brand-block {
            max-width: 650px;
            margin: 0 auto;
            text-align: center;
        }

        .nadi-brand-row {
            justify-content: center;
            text-align: left;
        }

        .nadi-login-title {
            font-size: clamp(36px, 6vw, 48px);
        }

        .nadi-login-lead {
            display: block;
            max-width: 610px;
            margin: 12px auto 0;
            overflow: visible;
            font-size: 14px;
            line-height: 1.65;
        }

        .nadi-trust-row {
            display: flex;
            justify-content: center;
            gap: 12px 18px;
            margin-top: 16px;
        }

        .nadi-trust-item {
            padding: 0;
            border: 0;
            background: transparent;
            font-size: 11px;
        }

        .nadi-trust-icon {
            width: 22px;
            height: 22px;
            flex-basis: 22px;
        }

        .nadi-login-card {
            width: 100%;
            max-width: 520px;
            margin: 0 auto;
            padding: 24px;
            border-radius: 22px;
        }

        .nadi-card-icon {
            width: 46px;
            height: 46px;
            flex-basis: 46px;
        }

        .nadi-card-title {
            font-size: 21px;
        }

        .nadi-card-subtitle {
            font-size: 11.5px;
        }

        .queue-form {
            gap: 13px;
        }

        .queue-field label {
            font-size: 12.5px;
        }

        .field-description,
        .field-error {
            font-size: 10.5px;
        }

        .captcha-box {
            grid-template-columns: minmax(0, 1fr) 132px;
        }

        .captcha-question strong {
            font-size: 17px;
        }

        .nadi-card-footer {
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
        }

        .install-button.is-visible {
            width: auto;
            min-height: 34px;
        }
    }

    /* =========================
       DESKTOP / LAPTOP
       Full-bleed agar tidak terikat .container dari layouts.app
       ========================= */
    @media (min-width: 901px) {
        .nadi-login-page {
            position: relative;
            left: 50%;
            width: 100vw;
            max-width: 100vw;
            min-height: 100svh;
            margin-left: -50vw;
            display: flex;
            align-items: center;
            padding: 34px clamp(28px, 4vw, 60px);
            overflow-x: clip;
            background:
                radial-gradient(circle at 8% 8%, rgba(24,170,97,.10), transparent 28%),
                radial-gradient(circle at 94% 10%, rgba(20,119,238,.10), transparent 30%),
                linear-gradient(180deg, #fff 0%, var(--nadi-bg) 100%);
        }

        .nadi-login-shell {
            width: 100%;
            max-width: 1280px;
            margin: 0 auto;
        }

        .nadi-login-layout {
            display: grid;
            width: 100%;
            grid-template-columns: minmax(0, 1fr) minmax(390px, 460px);
            gap: clamp(30px, 4.5vw, 72px);
            align-items: center;
        }

        .nadi-brand-block {
            width: 100%;
            min-width: 0;
            max-width: 650px;
            margin: 0;
            text-align: left;
        }

        .nadi-brand-row {
            justify-content: flex-start;
            margin-bottom: 16px;
        }

        .nadi-logo-box {
            width: 56px;
            height: 56px;
            flex-basis: 56px;
            border-radius: 15px;
        }

        .nadi-logo {
            width: 47px;
            height: 47px;
        }

        .nadi-gov {
            font-size: 9px;
        }

        .nadi-hospital {
            font-size: 16px;
        }

        .nadi-location {
            font-size: 10px;
        }

        .nadi-eyebrow {
            margin-bottom: 11px;
            padding: 7px 11px;
            font-size: 9.5px;
        }

        .nadi-login-title {
            width: 100%;
            max-width: 610px;
            font-size: clamp(40px, 4vw, 50px);
            line-height: 1.03;
            overflow-wrap: normal;
            word-break: normal;
        }

        .nadi-login-lead {
            display: block;
            width: 100%;
            max-width: 590px;
            margin: 14px 0 0;
            overflow: visible;
            font-size: 14px;
            line-height: 1.65;
        }

        .nadi-trust-row {
            display: flex;
            justify-content: flex-start;
            flex-wrap: wrap;
            gap: 10px 16px;
            margin-top: 18px;
        }

        .nadi-trust-item {
            padding: 0;
            border: 0;
            background: transparent;
            font-size: 11px;
        }

        .nadi-trust-icon {
            width: 22px;
            height: 22px;
            flex-basis: 22px;
        }

        .nadi-login-card {
            width: 100%;
            min-width: 0;
            max-width: 460px;
            justify-self: end;
            margin: 0;
            padding: 26px;
            border-radius: 23px;
            box-shadow: 0 22px 54px rgba(15,45,115,.11);
        }

        .nadi-card-head {
            margin-bottom: 19px;
        }

        .nadi-card-icon {
            width: 46px;
            height: 46px;
            flex-basis: 46px;
            border-radius: 14px;
        }

        .nadi-card-title {
            font-size: 21px;
        }

        .nadi-card-subtitle {
            font-size: 11.5px;
        }

        .queue-input {
            height: 52px;
        }

        .queue-submit {
            min-height: 54px;
            font-size: 14px;
        }
    }

    /* Desktop lebar: beri ruang lebih lega tanpa membuat hero berlebihan */
    @media (min-width: 1200px) {
        .nadi-login-page {
            padding-right: clamp(44px, 5vw, 76px);
            padding-left: clamp(44px, 5vw, 76px);
        }

        .nadi-login-layout {
            grid-template-columns: minmax(0, 1fr) 470px;
            gap: clamp(56px, 6vw, 92px);
        }

        .nadi-brand-block {
            max-width: 680px;
        }

        .nadi-login-title {
            max-width: 650px;
            font-size: clamp(44px, 4vw, 52px);
        }

        .nadi-login-lead {
            max-width: 620px;
            font-size: 15px;
        }

        .nadi-login-card {
            max-width: 470px;
            padding: 28px;
        }
    }
</style>

<section class="nadi-login-page">
    <div class="nadi-login-shell">
        <div class="nadi-login-layout">

            {{-- Branding --}}
            <section class="nadi-brand-block">
                <div class="nadi-brand-row">
                    <div class="nadi-logo-box">
                        <img
                            class="nadi-logo"
                            src="{{ asset('images/logo-rsbm.png') }}"
                            alt="Logo RSUD Bali Mandara"
                            onerror="this.style.display='none'; this.nextElementSibling.style.display='grid';"
                        >
                        <span class="nadi-logo-fallback" aria-hidden="true">+</span>
                    </div>

                    <div>
                        <div class="nadi-gov">Pemerintah Provinsi Bali</div>
                        <div class="nadi-hospital">RSUD Bali Mandara</div>
                        <div class="nadi-location">Sanur · Denpasar · Bali</div>
                    </div>
                </div>

                <div class="nadi-eyebrow">
                    <span class="nadi-eyebrow-dot"></span>
                    NADI RSBM
                </div>

                <h1 class="nadi-login-title">
                    <span>NADI RSBM</span><br>
                    Navigasi Akses Digital Pasien
                </h1>

                <p class="nadi-login-lead">
                    Akses reservasi, hasil pemeriksaan, antrean, dan informasi
                    pelayanan pasien RSUD Bali Mandara dalam satu tempat.
                </p>

                <div class="nadi-trust-row">
                    <span class="nadi-trust-item">
                        <span class="nadi-trust-icon">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M5 12L10 17L19 7" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        Satu pintu layanan
                    </span>

                    <span class="nadi-trust-item">
                        <span class="nadi-trust-icon">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M12 3L20 7V12C20 17 16.5 20.5 12 22C7.5 20.5 4 17 4 12V7L12 3Z" stroke="currentColor" stroke-width="2"/>
                            </svg>
                        </span>
                        Akses lebih aman
                    </span>

                    <span class="nadi-trust-item">
                        <span class="nadi-trust-icon">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/>
                                <path d="M12 7V12L15 14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </span>
                        Cepat dan praktis
                    </span>
                </div>
            </section>

            {{-- Login --}}
            <section class="nadi-login-card">
                <div class="nadi-card-head">
                    <div class="nadi-card-icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <rect x="4" y="7" width="16" height="14" rx="3" stroke="currentColor" stroke-width="2"/>
                            <path d="M8 7V5C8 3.9 8.9 3 10 3H14C15.1 3 16 3.9 16 5V7M9 12H15M12 9V15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </div>

                    <div class="nadi-card-title-wrap">
                        <h2 class="nadi-card-title">Masuk ke NADI</h2>
                        <p class="nadi-card-subtitle">
                            Gunakan No. RM dan tanggal lahir Anda.
                        </p>
                    </div>
                </div>

                <form
                    id="queue-check-form"
                    class="queue-form"
                    method="POST"
                    action="{{ route('layanan.masuk') }}"
                >
                    @csrf

                    @error('validasi')
                        <div class="validation-alert" role="alert">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/>
                                <path d="M12 7V13M12 17H12.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                            <span>{{ $message }}</span>
                        </div>
                    @enderror

                    <div class="queue-field @error('rm') has-error @enderror">
                        <label for="rm">Nomor Rekam Medis</label>

                        <div class="queue-input-wrapper">
                            <span class="queue-input-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M7 3H17C18.1 3 19 3.9 19 5V21L12 17.5L5 21V5C5 3.9 5.9 3 7 3Z" stroke="currentColor" stroke-width="2"/>
                                    <path d="M9 8H15M9 12H13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </span>

                            <input
                                id="rm"
                                class="queue-input"
                                name="rm"
                                type="text"
                                value="{{ old('rm') }}"
                                placeholder="00.00.00"
                                inputmode="numeric"
                                autocomplete="off"
                                maxlength="8"
                                pattern="[0-9.]*"
                                autofocus
                                required
                            >
                        </div>

                        @error('rm')
                            <p class="field-error">{{ $message }}</p>
                        @else
                            <p class="field-description">
                                Masukkan 6 angka. Titik ditambahkan otomatis.
                            </p>
                        @enderror
                    </div>

                    <div class="queue-field @error('tanggal_lahir') has-error @enderror">
                        <label for="tanggal_lahir_display">Tanggal Lahir</label>

                        <div class="queue-input-wrapper">
                            <span class="queue-input-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <rect x="3" y="5" width="18" height="16" rx="3" stroke="currentColor" stroke-width="2"/>
                                    <path d="M8 3V7M16 3V7M3 10H21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </span>

                            <input
                                id="tanggal_lahir_display"
                                class="queue-input"
                                type="text"
                                value="{{ $tanggalLahirDisplay }}"
                                placeholder="DD-MM-YYYY"
                                inputmode="numeric"
                                autocomplete="bday"
                                maxlength="10"
                                pattern="[0-9-]*"
                                required
                            >

                            <input
                                id="tanggal_lahir"
                                name="tanggal_lahir"
                                type="hidden"
                                value="{{ old('tanggal_lahir') }}"
                            >
                        </div>

                        @error('tanggal_lahir')
                            <p class="field-error">{{ $message }}</p>
                        @else
                            <p class="field-description">
                                Contoh: 27-12-1968.
                            </p>
                        @enderror
                    </div>

                    <div class="queue-field @error('captcha') has-error @enderror">
                        <label for="captcha">Verifikasi Keamanan</label>

                        <div class="captcha-box">
                            <div class="captcha-question">
                                <span class="captcha-label">Hitung</span>
                                <strong>{{ $captchaQuestion }} = ?</strong>
                            </div>

                            <div class="queue-input-wrapper captcha-input-wrapper">
                                <span class="queue-input-icon">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M12 3L20 7V12C20 17 16.5 20.5 12 22C7.5 20.5 4 17 4 12V7L12 3Z" stroke="currentColor" stroke-width="2"/>
                                    </svg>
                                </span>

                                <input
                                    id="captcha"
                                    class="queue-input"
                                    name="captcha"
                                    type="text"
                                    placeholder="Hasil"
                                    inputmode="numeric"
                                    pattern="[0-9]*"
                                    autocomplete="off"
                                    maxlength="2"
                                    required
                                >

                                <input
                                    type="hidden"
                                    name="captcha_token"
                                    value="{{ $captchaToken }}"
                                >
                            </div>
                        </div>

                        @error('captcha')
                            <p class="field-error">{{ $message }}</p>
                        @else
                            <p class="field-description">
                                Masukkan hasil perhitungan untuk melanjutkan.
                            </p>
                        @enderror
                    </div>

                    <button
                        id="submit-button"
                        class="queue-submit"
                        type="submit"
                    >
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M5 12H19M14 7L19 12L14 17" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span>Masuk ke NADI</span>
                    </button>

                    <div class="nadi-card-footer">
                        <div class="nadi-security-line">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <rect x="5" y="10" width="14" height="11" rx="2" stroke="currentColor" stroke-width="2"/>
                                <path d="M8 10V7C8 4.8 9.8 3 12 3C14.2 3 16 4.8 16 7V10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                            <span>
                                Data digunakan hanya untuk verifikasi akses pasien.
                            </span>
                        </div>

                        <button
                            id="install-pwa-button"
                            class="install-button"
                            type="button"
                        >
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M12 3V15M8 11L12 15L16 11M5 20H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span>Pasang App</span>
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </div>
</section>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('queue-check-form');
        const rmInput = document.getElementById('rm');
        const dateDisplayInput = document.getElementById('tanggal_lahir_display');
        const dateHiddenInput = document.getElementById('tanggal_lahir');
        const submitButton = document.getElementById('submit-button');
        const installButton = document.getElementById('install-pwa-button');
        const captchaInput = document.getElementById('captcha');
        let deferredInstallPrompt = null;

        function formatMedicalRecord(value) {
            const numbers = String(value || '')
                .replace(/\D/g, '')
                .slice(0, 6);

            if (numbers.length <= 2) {
                return numbers;
            }

            if (numbers.length <= 4) {
                return numbers.slice(0, 2) + '.' + numbers.slice(2);
            }

            return (
                numbers.slice(0, 2) + '.' +
                numbers.slice(2, 4) + '.' +
                numbers.slice(4, 6)
            );
        }

        function formatBirthDate(value) {
            const numbers = String(value || '')
                .replace(/\D/g, '')
                .slice(0, 8);

            if (numbers.length <= 2) {
                return numbers;
            }

            if (numbers.length <= 4) {
                return numbers.slice(0, 2) + '-' + numbers.slice(2);
            }

            return (
                numbers.slice(0, 2) + '-' +
                numbers.slice(2, 4) + '-' +
                numbers.slice(4, 8)
            );
        }

        function convertBirthDateToIso(value) {
            const match = value.match(/^(\d{2})-(\d{2})-(\d{4})$/);

            if (!match) {
                return null;
            }

            const day = Number(match[1]);
            const month = Number(match[2]);
            const year = Number(match[3]);
            const birthDate = new Date(year, month - 1, day);
            const today = new Date();

            today.setHours(0, 0, 0, 0);

            const isValidDate =
                birthDate.getFullYear() === year &&
                birthDate.getMonth() === month - 1 &&
                birthDate.getDate() === day;

            if (!isValidDate || birthDate > today) {
                return null;
            }

            return [
                String(year).padStart(4, '0'),
                String(month).padStart(2, '0'),
                String(day).padStart(2, '0')
            ].join('-');
        }

        // No. RM: pengguna cukup mengetik 6 angka.
        // Contoh 123456 otomatis menjadi 12.34.56.
        rmInput.value = formatMedicalRecord(rmInput.value);

        rmInput.addEventListener('input', function() {
            this.value = formatMedicalRecord(this.value);
            this.setCustomValidity('');
        });

        // Pastikan paste/autofill juga selalu dibersihkan dan diformat.
        rmInput.addEventListener('change', function() {
            this.value = formatMedicalRecord(this.value);
            this.setCustomValidity('');
        });

        rmInput.addEventListener('blur', function() {
            this.value = formatMedicalRecord(this.value);

            const numbers = this.value.replace(/\D/g, '');
            this.setCustomValidity(
                numbers.length === 6 ?
                '' :
                'Nomor rekam medis harus terdiri dari 6 angka.'
            );
        });

        // Tanggal lahir: pengguna cukup mengetik 8 angka.
        // Contoh 27121968 otomatis menjadi 27-12-1968.
        dateDisplayInput.value = formatBirthDate(dateDisplayInput.value);

        function syncBirthDate() {
            const formattedValue = formatBirthDate(dateDisplayInput.value);

            dateDisplayInput.value = formattedValue;
            dateDisplayInput.setCustomValidity('');

            const isoDate = convertBirthDateToIso(formattedValue);
            dateHiddenInput.value = isoDate ? isoDate : '';
        }

        dateDisplayInput.addEventListener('input', syncBirthDate);

        // Paste/autofill ikut dibersihkan. Huruf dan simbol selain
        // pemisah yang dibentuk otomatis tidak akan dipertahankan.
        dateDisplayInput.addEventListener('change', syncBirthDate);

        dateDisplayInput.addEventListener('blur', function() {
            syncBirthDate();

            const isoDate = convertBirthDateToIso(this.value);

            if (!isoDate) {
                this.setCustomValidity(
                    'Masukkan tanggal lahir yang valid dengan format DD-MM-YYYY.'
                );
                dateHiddenInput.value = '';
            } else {
                this.setCustomValidity('');
                dateHiddenInput.value = isoDate;
            }
        });

        // CAPTCHA hanya menerima angka.
        if (captchaInput) {
            captchaInput.addEventListener('input', function() {
                this.value = String(this.value || '')
                    .replace(/\D/g, '')
                    .slice(0, 2);

                this.setCustomValidity('');
            });
        }

        form.addEventListener('submit', function(event) {
            rmInput.value = formatMedicalRecord(rmInput.value);

            if (captchaInput) {
                captchaInput.value = String(captchaInput.value || '')
                    .replace(/\D/g, '')
                    .slice(0, 2);
            }

            const rmNumbers = rmInput.value.replace(/\D/g, '');
            const isoDate = convertBirthDateToIso(dateDisplayInput.value);

            rmInput.setCustomValidity(
                rmNumbers.length === 6 ? '' : 'Nomor rekam medis harus terdiri dari 6 angka.'
            );

            if (!isoDate) {
                dateDisplayInput.setCustomValidity('Masukkan tanggal lahir yang valid dengan format DD-MM-YYYY.');
                dateHiddenInput.value = '';
            } else {
                dateDisplayInput.setCustomValidity('');
                dateHiddenInput.value = isoDate;
            }

            if (!form.checkValidity()) {
                event.preventDefault();
                form.reportValidity();
                return;
            }

            submitButton.disabled = true;
            submitButton.querySelector('span').textContent = 'Mengakses NADI RSBM...';
        });

        // ---------------------------------------------------------
        // PWA
        // URL aset PWA dibuat langsung oleh helper asset() Laravel.
        // Sebelum register Service Worker, cek dulu apakah URL benar-
        // benar mengembalikan JavaScript dan bukan halaman HTML/404.
        // ---------------------------------------------------------
        const manifestUrl = "{{ asset('manifest.webmanifest') }}";
        const serviceWorkerUrl = "{{ asset('service-worker.js') }}";

        if (!document.querySelector('link[rel="manifest"]')) {
            const manifestLink = document.createElement('link');
            manifestLink.rel = 'manifest';
            manifestLink.href = manifestUrl;
            document.head.appendChild(manifestLink);
        }

        if (!document.querySelector('meta[name="theme-color"]')) {
            const themeMeta = document.createElement('meta');
            themeMeta.name = 'theme-color';
            themeMeta.content = '#163a93';
            document.head.appendChild(themeMeta);
        }

        async function registerServiceWorkerSafely() {
            if (!('serviceWorker' in navigator)) {
                return;
            }

            try {
                const response = await fetch(serviceWorkerUrl, {
                    method: 'GET',
                    cache: 'no-store',
                    credentials: 'same-origin'
                });

                if (!response.ok) {
                    console.warn(
                        'Service worker tidak ditemukan. HTTP status:',
                        response.status,
                        serviceWorkerUrl
                    );
                    return;
                }

                const contentType = (response.headers.get('content-type') || '')
                    .toLowerCase();
                const body = await response.clone().text();
                const firstCharacter = body.trimStart().charAt(0);

                if (
                    contentType.includes('text/html') ||
                    firstCharacter === '<'
                ) {
                    console.warn(
                        'Service worker URL mengembalikan HTML, bukan JavaScript:',
                        serviceWorkerUrl
                    );
                    return;
                }

                await navigator.serviceWorker.register(serviceWorkerUrl, {
                    scope: '/'
                });
            } catch (error) {
                console.warn('Service worker gagal didaftarkan:', error);
            }
        }

        registerServiceWorkerSafely();

        window.addEventListener('beforeinstallprompt', function(event) {
            event.preventDefault();
            deferredInstallPrompt = event;
            installButton.classList.add('is-visible');
        });

        installButton.addEventListener('click', async function() {
            if (!deferredInstallPrompt) {
                return;
            }

            deferredInstallPrompt.prompt();
            const choice = await deferredInstallPrompt.userChoice;

            if (choice.outcome === 'accepted') {
                installButton.disabled = true;
                installButton.querySelector('span').textContent = 'Terpasang';
            }

            deferredInstallPrompt = null;
            installButton.classList.remove('is-visible');
        });

        window.addEventListener('appinstalled', function() {
            deferredInstallPrompt = null;
            installButton.classList.remove('is-visible');
        });
    });
</script>
@endsection
