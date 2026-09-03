@extends('layouts.app', ['title' => 'NADI RSBM | RSUD Bali Mandara Provinsi Bali'])

@push('head')
<meta name="theme-color" content="#26358f">
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

    .sapa-page,
    .sapa-page * {
        box-sizing: border-box;
    }

    .sapa-page {
        position: relative;
        overflow: hidden;
        min-height: calc(100svh - 72px);
        display: flex;
        align-items: center;
        padding: 12px 0 18px;
        background:
            radial-gradient(circle at 9% 10%, rgba(38, 53, 143, .09), transparent 26%),
            radial-gradient(circle at 92% 88%, rgba(25, 200, 61, .08), transparent 26%),
            linear-gradient(180deg, #fff 0%, var(--rsbm-bg) 100%);
    }

    .sapa-page::before,
    .sapa-page::after {
        position: absolute;
        border-radius: 999px;
        content: "";
        pointer-events: none;
    }

    .sapa-page::before {
        top: -140px;
        right: -120px;
        width: 310px;
        height: 310px;
        border: 44px solid rgba(38, 53, 143, .025);
    }

    .sapa-page::after {
        bottom: -150px;
        left: -130px;
        width: 320px;
        height: 320px;
        border: 48px solid rgba(25, 200, 61, .025);
    }

    .sapa-shell {
        position: relative;
        z-index: 1;
        width: min(1060px, calc(100% - 28px));
        margin: 0 auto;
    }

    .sapa-layout {
        display: grid;
        grid-template-columns: minmax(0, .98fr) minmax(380px, .82fr);
        gap: clamp(22px, 3vw, 34px);
        align-items: center;
    }

    .sapa-intro {
        min-width: 0;
        padding: 0;
    }

    .brand-row {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 9px;
    }

    .brand-logo-shell {
        display: grid;
        width: 50px;
        height: 50px;
        flex: 0 0 50px;
        place-items: center;
        overflow: hidden;
        border: 1px solid rgba(38, 53, 143, .11);
        border-radius: 12px;
        background: #fff;
        box-shadow: 0 10px 24px rgba(38, 53, 143, .09);
    }

    .brand-logo {
        width: 43px;
        height: 43px;
        object-fit: contain;
    }

    .brand-logo-fallback {
        display: none;
        width: 39px;
        height: 39px;
        place-items: center;
        border-radius: 13px;
        background: linear-gradient(145deg, var(--rsbm-blue), var(--rsbm-blue-dark));
        color: #fff;
        font-size: 18px;
        font-weight: 950;
    }

    .brand-government {
        color: #788397;
        font-size: 9px;
        font-weight: 850;
        letter-spacing: .12em;
        text-transform: uppercase;
    }

    .brand-hospital {
        margin-top: 1px;
        color: var(--rsbm-blue-dark);
        font-size: 16px;
        font-weight: 950;
        letter-spacing: -.015em;
    }

    .brand-location {
        margin-top: 1px;
        color: #8a95a6;
        font-size: 10px;
        font-weight: 650;
    }

    .eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 9px;
        padding: 6px 10px;
        border: 1px solid rgba(38, 53, 143, .08);
        border-radius: 999px;
        background: rgba(255, 255, 255, .8);
        color: var(--rsbm-blue-dark);
        font-size: 10px;
        font-weight: 900;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .eyebrow-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: var(--rsbm-green);
        box-shadow: 0 0 0 4px rgba(25, 200, 61, .11);
    }

    .sapa-title {
        max-width: 560px;
        margin: 0;
        color: var(--rsbm-text);
        font-size: clamp(34px, 4.15vw, 52px);
        font-weight: 950;
        line-height: 1.06;
        letter-spacing: -.045em;
    }

    .sapa-title span {
        color: var(--rsbm-blue);
    }

    .sapa-lead {
        max-width: 540px;
        margin: 11px 0 0;
        color: var(--rsbm-muted);
        font-size: clamp(13px, 1.35vw, 15.5px);
        line-height: 1.55;
    }

    .sapa-lead strong {
        color: var(--rsbm-blue-dark);
        font-weight: 900;
    }

    .value-row {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
        margin-top: 18px;
    }

    .value-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 10px;
        border: 1px solid rgba(38, 53, 143, .07);
        border-radius: 11px;
        background: rgba(255, 255, 255, .78);
        color: #4d596b;
        font-size: 10.5px;
        font-weight: 750;
    }

    .value-chip strong {
        display: grid;
        width: 21px;
        height: 21px;
        place-items: center;
        border-radius: 6px;
        background: var(--rsbm-blue-soft);
        color: var(--rsbm-blue);
        font-size: 10px;
        font-weight: 950;
    }

    .value-chip:nth-child(2n) strong {
        background: var(--rsbm-green-soft);
        color: var(--rsbm-green-dark);
    }

    .trust-row {
        display: flex;
        flex-wrap: wrap;
        gap: 7px 11px;
        margin-top: 13px;
        color: #6c7889;
        font-size: 10.5px;
        font-weight: 700;
    }

    .trust-item {
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .trust-icon {
        display: grid;
        width: 20px;
        height: 20px;
        place-items: center;
        border-radius: 6px;
        background: var(--rsbm-green-soft);
        color: var(--rsbm-green-dark);
    }

    .sapa-card {
        position: relative;
        overflow: hidden;
        width: 100%;
        max-width: 440px;
        justify-self: end;
        padding: 22px;
        border: 1px solid rgba(217, 224, 234, .95);
        border-radius: 20px;
        background: rgba(255, 255, 255, .97);
        box-shadow: 0 18px 42px rgba(28, 39, 90, .10);
        backdrop-filter: blur(10px);
    }

    .sapa-card::before {
        position: absolute;
        top: 0;
        right: 0;
        left: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--rsbm-blue) 0 72%, var(--rsbm-green) 72% 100%);
        content: "";
    }

    .card-head {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 15px;
    }

    .card-head-icon {
        display: grid;
        width: 42px;
        height: 42px;
        flex: 0 0 42px;
        place-items: center;
        border-radius: 12px;
        background: var(--rsbm-blue-soft);
        color: var(--rsbm-blue);
    }

    .card-kicker {
        color: var(--rsbm-green-dark);
        font-size: 9px;
        font-weight: 900;
        letter-spacing: .11em;
        text-transform: uppercase;
    }

    .card-head h2 {
        margin: 2px 0 0;
        color: var(--rsbm-text);
        font-size: 19px;
        font-weight: 950;
        letter-spacing: -.02em;
    }

    .card-head p {
        margin: 3px 0 0;
        color: #7b8797;
        font-size: 10.5px;
        line-height: 1.45;
    }

    .queue-form {
        display: grid;
        gap: 10px;
    }

    .queue-field {
        display: grid;
        gap: 5px;
    }

    .queue-field label {
        color: #344052;
        font-size: 12px;
        font-weight: 850;
    }

    .queue-input-wrapper {
        position: relative;
    }

    .queue-input-icon {
        position: absolute;
        top: 50%;
        left: 14px;
        display: grid;
        width: 20px;
        height: 20px;
        place-items: center;
        color: #8b96a8;
        pointer-events: none;
        transform: translateY(-50%);
    }

    .queue-input {
        width: 100%;
        height: 50px;
        padding: 0 13px 0 43px;
        border: 1.4px solid var(--rsbm-line);
        border-radius: 12px;
        outline: none;
        background: #fbfcfe;
        color: var(--rsbm-text);
        font: inherit;
        font-size: 13.5px;
        font-weight: 750;
        transition: border-color .2s ease, box-shadow .2s ease, background .2s ease;
    }

    .queue-input::placeholder {
        color: #a9b2c0;
        font-weight: 500;
    }

    .queue-input:focus {
        border-color: var(--rsbm-blue);
        background: #fff;
        box-shadow: 0 0 0 3px rgba(38, 53, 143, .09);
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
        color: #929cad;
    }

    .field-error {
        color: #dc2626;
        font-weight: 700;
    }

    .validation-alert {
        display: flex;
        align-items: flex-start;
        gap: 9px;
        padding: 10px 12px;
        border: 1px solid #fecaca;
        border-radius: 12px;
        background: #fef2f2;
        color: #b91c1c;
        font-size: 10.5px;
        font-weight: 700;
        line-height: 1.45;
    }

    .queue-submit,
    .install-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border: 0;
        font-family: inherit;
        font-weight: 900;
        cursor: pointer;
        transition: transform .2s ease, box-shadow .2s ease, opacity .2s ease;
    }

    .queue-submit {
        width: 100%;
        min-height: 50px;
        margin-top: 1px;
        border-radius: 12px;
        background: linear-gradient(135deg, var(--rsbm-blue), var(--rsbm-blue-dark));
        color: #fff;
        font-size: 12.5px;
        box-shadow: 0 11px 23px rgba(38, 53, 143, .22);
    }

    .queue-submit:hover,
    .install-button:hover {
        transform: translateY(-1px);
    }

    .queue-submit:disabled {
        cursor: wait;
        opacity: .72;
        transform: none;
    }

    .secure-note {
        display: flex;
        align-items: flex-start;
        gap: 5px;
        padding: 8px 9px;
        border-radius: 11px;
        background: var(--rsbm-green-soft);
        color: #52735b;
        font-size: 10px;
        font-weight: 650;
        line-height: 1.45;
    }

    .secure-note svg {
        flex: 0 0 auto;
        color: var(--rsbm-green-dark);
    }

    .card-footer {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        gap: 8px;
        padding-top: 1px;
    }

    .privacy-text {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        min-width: 0;
        color: #98a1af;
        font-size: 9px;
        line-height: 1.35;
    }

    .install-button {
        display: none;
        min-height: 32px;
        flex: 0 0 auto;
        padding: 0 10px;
        border: 1px solid rgba(38, 53, 143, .12);
        border-radius: 10px;
        background: var(--rsbm-blue-soft);
        color: var(--rsbm-blue);
        font-size: 10px;
    }

    .install-button.is-visible {
        display: inline-flex;
    }

    .install-button:disabled {
        opacity: .6;
        cursor: default;
    }

    .captcha-box {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 132px;
        gap: 8px;
        align-items: stretch;
    }

    .captcha-question {
        display: flex;
        min-width: 0;
        min-height: 50px;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 0 13px;
        border: 1.4px solid rgba(38, 53, 143, .12);
        border-radius: 12px;
        background: linear-gradient(135deg, var(--rsbm-blue-soft), #f8faff);
    }

    .captcha-label {
        flex: 0 0 auto;
        color: #7b8797;
        font-size: 9.5px;
        font-weight: 800;
        letter-spacing: .02em;
    }

    .captcha-question strong {
        display: inline-flex;
        min-width: 0;
        align-items: center;
        gap: 5px;
        color: var(--rsbm-blue);
        font-size: 16px;
        font-weight: 950;
        letter-spacing: .02em;
        white-space: nowrap;
    }

    .captcha-question strong span {
        color: #8791a2;
        font-size: 13px;
        font-weight: 800;
    }

    .captcha-input-wrapper .queue-input {
        padding-right: 10px;
        padding-left: 38px;
        text-align: center;
        font-size: 16px;
        letter-spacing: .04em;
    }

    .captcha-input-wrapper .queue-input-icon {
        left: 11px;
        width: 18px;
        height: 18px;
    }

    @media (max-width: 420px) {
        .captcha-box {
            grid-template-columns: minmax(0, 1fr) 108px;
            gap: 6px;
        }

        .captcha-question {
            gap: 6px;
            padding: 0 9px;
        }

        .captcha-label {
            font-size: 9px;
        }

        .captcha-question strong {
            gap: 3px;
            font-size: 14px;
        }

        .captcha-input-wrapper .queue-input {
            padding-left: 33px;
            font-size: 15px;
        }

        .captcha-input-wrapper .queue-input-icon {
            left: 9px;
        }
    }

    @media (min-width: 901px) and (max-height: 760px) {
        .sapa-page {
            align-items: flex-start;
            padding-top: 14px;
        }

        .sapa-title {
            font-size: clamp(32px, 4vw, 46px);
        }

        .sapa-lead {
            margin-top: 8px;
        }

        .trust-row {
            margin-top: 10px;
        }

        .sapa-card {
            padding: 19px 20px;
        }
    }

    @media (max-width: 900px) {
        .sapa-page {
            min-height: auto;
            align-items: flex-start;
            padding: 18px 0 28px;
        }

        .sapa-layout {
            grid-template-columns: 1fr;
            gap: 22px;
        }

        .sapa-intro {
            max-width: 700px;
            margin: 0 auto;
            text-align: center;
        }

        .brand-row {
            justify-content: center;
            text-align: left;
        }

        .sapa-title,
        .sapa-lead {
            margin-right: auto;
            margin-left: auto;
        }

        .value-row,
        .trust-row {
            justify-content: center;
        }

        .sapa-card {
            max-width: 520px;
            justify-self: center;
        }
    }

    @media (max-width: 560px) {
        .sapa-shell {
            width: min(100% - 18px, 520px);
        }

        .sapa-page {
            padding-top: 8px;
        }

        .brand-row {
            margin-bottom: 9px;
        }

        .brand-logo-shell {
            width: 49px;
            height: 49px;
            flex-basis: 49px;
            border-radius: 12px;
        }

        .brand-logo {
            width: 42px;
            height: 42px;
        }

        .brand-hospital {
            font-size: 15px;
        }

        .eyebrow {
            margin-bottom: 9px;
        }

        .sapa-title {
            font-size: clamp(29px, 9vw, 38px);
        }

        .sapa-lead {
            margin-top: 8px;
            font-size: 12.5px;
            line-height: 1.55;
        }

        .value-row {
            margin-top: 14px;
            gap: 6px;
        }

        .value-chip {
            padding: 6px 8px;
            font-size: 10px;
        }

        .trust-row {
            margin-top: 10px;
            gap: 7px 12px;
            font-size: 10px;
        }

        .sapa-card {
            padding: 17px 14px 15px;
            border-radius: 20px;
        }

        .card-head {
            margin-bottom: 13px;
        }

        .card-head-icon {
            width: 41px;
            height: 41px;
            flex-basis: 41px;
        }

        .card-head h2 {
            font-size: 19px;
        }

        .queue-form {
            gap: 10px;
        }

        .queue-input,
        .queue-submit {
            min-height: 50px;
            height: 50px;
        }

        .card-footer {
            align-items: center;
            flex-wrap: wrap;
        }
    }
</style>

<section class="sapa-page">
    <div class="sapa-shell">
        <div class="sapa-layout">
            <div class="sapa-intro">
                <div class="brand-row">
                    <div class="brand-logo-shell">
                        <img
                            class="brand-logo"
                            src="{{ asset('images/logo-rsbm.png') }}"
                            alt="Logo RSUD Bali Mandara"
                            onerror="this.style.display='none'; this.nextElementSibling.style.display='grid';">
                        <span class="brand-logo-fallback" aria-hidden="true">+</span>
                    </div>

                    <div>
                        <div class="brand-government">Pemerintah Provinsi Bali</div>
                        <div class="brand-hospital">RSUD Bali Mandara</div>
                        <div class="brand-location">Sanur · Denpasar · Bali</div>
                    </div>
                </div>

                <div class="eyebrow">
                    <span class="eyebrow-dot"></span>
                    NADI RSBM
                </div>

                <h1 class="sapa-title">
                    <span>NADI RSBM</span><br>
                    Navigasi Akses Digital Informasi Pasien
                </h1>

                <p class="sapa-lead">
                    <strong>NADI RSBM</strong> adalah
                    <strong>Navigasi Akses Digital Informasi Pasien</strong>
                    dengan konsep <strong>Satu Akses Pelayanan Pasien RSUD Bali Mandara</strong>
                    yang memudahkan pasien mengakses layanan digital rumah sakit
                    secara cepat, aman, dan terintegrasi.
                </p>



                <div class="trust-row">
                    <span class="trust-item">
                        <span class="trust-icon">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M5 12L10 17L19 7" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                        Satu pintu layanan
                    </span>

                    <span class="trust-item">
                        <span class="trust-icon">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M12 3L20 7V12C20 17 16.5 20.5 12 22C7.5 20.5 4 17 4 12V7L12 3Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round" />
                            </svg>
                        </span>
                        Akses lebih aman
                    </span>

                    <span class="trust-item">
                        <span class="trust-icon">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2" />
                                <path d="M12 7V12L15 14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                        Cepat dan praktis
                    </span>
                </div>
            </div>

            <div class="sapa-card">
                <div class="card-head">
                    <div class="card-head-icon">
                        <svg width="23" height="23" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <rect x="4" y="7" width="16" height="14" rx="3" stroke="currentColor" stroke-width="2" />
                            <path d="M8 7V5C8 3.9 8.9 3 10 3H14C15.1 3 16 3.9 16 5V7M9 12H15M12 9V15" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        </svg>
                    </div>

                    <div>
                        <div class="card-kicker">
                            Navigasi Akses Digital Informasi Pasien
                        </div>
                        <h2>Masuk ke NADI RSBM</h2>
                        <p>
                            Gunakan identitas pasien yang terdaftar
                            di RSUD Bali Mandara.
                        </p>
                    </div>
                </div>

                <form id="queue-check-form" class="queue-form" method="POST" action="{{ route('layanan.masuk') }}">
                    @csrf

                    @error('validasi')
                    <div class="validation-alert" role="alert">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2" />
                            <path d="M12 7V13M12 17H12.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        </svg>
                        <span>{{ $message }}</span>
                    </div>
                    @enderror

                    <div class="queue-field @error('rm') has-error @enderror">
                        <label for="rm">Nomor Rekam Medis</label>
                        <div class="queue-input-wrapper">
                            <span class="queue-input-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M7 3H17C18.1 3 19 3.9 19 5V21L12 17.5L5 21V5C5 3.9 5.9 3 7 3Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round" />
                                    <path d="M9 8H15M9 12H13" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
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
                                required>
                        </div>

                        @error('rm')
                        <p class="field-error">{{ $message }}</p>
                        @else
                        <p class="field-description">Masukkan 6 angka. Titik ditambahkan otomatis.</p>
                        @enderror
                    </div>

                    <div class="queue-field @error('tanggal_lahir') has-error @enderror">
                        <label for="tanggal_lahir_display">Tanggal Lahir</label>
                        <div class="queue-input-wrapper">
                            <span class="queue-input-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <rect x="3" y="5" width="18" height="16" rx="3" stroke="currentColor" stroke-width="2" />
                                    <path d="M8 3V7M16 3V7M3 10H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
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
                                required>
                            <input id="tanggal_lahir" name="tanggal_lahir" type="hidden" value="{{ old('tanggal_lahir') }}">
                        </div>

                        @error('tanggal_lahir')
                        <p class="field-error">{{ $message }}</p>
                        @else
                        <p class="field-description">Contoh: 27-12-1968.</p>
                        @enderror
                    </div>
                    <div class="queue-field @error('captcha') has-error @enderror">
                        <label for="captcha">
                            Verifikasi Keamanan
                        </label>

                        <div class="captcha-box">

                            <div class="captcha-question">
                                <span class="captcha-label">
                                    Hitung
                                </span>

                                <strong>
                                    {{ $captchaQuestion }} = ?
                                </strong>
                            </div>

                            <div class="queue-input-wrapper captcha-input-wrapper">

                                <span class="queue-input-icon">
                                    <svg
                                        width="18"
                                        height="18"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        aria-hidden="true">
                                        <path
                                            d="M12 3L20 7V12C20 17 16.5 20.5 12 22C7.5 20.5 4 17 4 12V7L12 3Z"
                                            stroke="currentColor"
                                            stroke-width="2" />
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
                                    required>

                                <input
                                    type="hidden"
                                    name="captcha_token"
                                    value="{{ $captchaToken }}">

                            </div>
                        </div>

                        @error('captcha')
                        <p class="field-error">
                            {{ $message }}
                        </p>
                        @else
                        <p class="field-description">
                            Masukkan hasil perhitungan untuk melanjutkan.
                        </p>
                        @enderror
                    </div>

                    <button id="submit-button" class="queue-submit" type="submit">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M5 12H19M14 7L19 12L14 17" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <span>Masuk ke NADI RSBM</span>
                    </button>

                    <div class="secure-note">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M12 3L20 7V12C20 17 16.5 20.5 12 22C7.5 20.5 4 17 4 12V7L12 3Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round" />
                            <path d="M9 12L11 14L15.5 9.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <span>Pastikan data sesuai dengan identitas pasien yang terdaftar di rumah sakit.</span>
                    </div>

                    <div class="card-footer">
                        <span class="privacy-text">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <rect x="5" y="10" width="14" height="11" rx="2" stroke="currentColor" stroke-width="2" />
                                <path d="M8 10V7C8 4.8 9.8 3 12 3C14.2 3 16 4.8 16 7V10" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            </svg>
                            Data pasien tidak disimpan untuk penggunaan offline.
                        </span>

                        <button id="install-pwa-button" class="install-button" type="button">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M12 3V15M8 11L12 15L16 11M5 20H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <span>Pasang App</span>
                        </button>
                    </div>
                </form>
            </div>
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
            themeMeta.content = '#26358f';
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