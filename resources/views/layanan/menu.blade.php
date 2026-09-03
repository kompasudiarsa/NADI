@extends('layouts.app', ['title' => 'NADI RSBM | RSUD Bali Mandara Provinsi Bali'])

@section('content')
@php
$menus = [
[
'title' => 'Cek Reservasi',
'description' => 'Lihat informasi reservasi dan jadwal pelayanan pasien.',
'url' => Route::has('reservation.index')
? route('reservation.index')
: url('/cek-reservasi'),
'class' => 'is-blue',
'icon' => 'calendar',
],
[
'title' => 'Hasil Laboratorium',
'description' => 'Akses hasil pemeriksaan laboratorium yang telah tersedia.',
'url' => Route::has('laboratory.index')
? route('laboratory.index')
: url('/cek-hasil-laboratorium'),
'class' => 'is-green',
'icon' => 'laboratory',
],
[
'title' => 'Hasil Radiologi',
'description' => 'Lihat hasil dan informasi pemeriksaan radiologi pasien.',
'url' => Route::has('radiology.index')
? route('radiology.index')
: url('/cek-hasil-radiologi'),
'class' => 'is-blue',
'icon' => 'radiology',
],
[
'title' => 'Cek Waktu Tunggu',
'description' => 'Pantau status antrean dan perkiraan waktu tunggu pelayanan.',
'url' => Route::has('waktu-tunggu.index')
? route('waktu-tunggu.index')
: url('/cek-waktu-tunggu'),
'class' => 'is-green',
'icon' => 'clock',
],
[
'title' => 'Video Edukasi Pasien',
'description' => 'Tonton video edukasi untuk memahami prosedur medis dan perawatan.',
'url' => Route::has('video-edukasi.index')
? route('video-edukasi.index')
: url('/video-edukasi'),
'class' => 'is-blue',
'icon' => 'circle-play',
],
[
    'title' => 'Jadwal Dokter',

    'description' => 'Lihat jadwal praktik dokter dan layanan poliklinik yang tersedia.',

    'url' => Route::has('jadwal-dokter.index')
        ? route('jadwal-dokter.index')
        : url('/jadwal-dokter'),

    'class' => 'is-green',

    'icon' => 'doctor-calendar',
],
];
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

    .service-page,
    .service-page * {
        box-sizing: border-box;
    }

    .service-page {
        position: relative;
        min-height: calc(100svh - 72px);
        overflow: hidden;
        padding: 18px 0 24px;
        background:
            radial-gradient(circle at 8% 8%, rgba(38, 53, 143, .09), transparent 26%),
            radial-gradient(circle at 94% 92%, rgba(25, 200, 61, .08), transparent 25%),
            linear-gradient(180deg, #fff 0%, var(--rsbm-bg) 100%);
    }

    .service-page::before,
    .service-page::after {
        position: absolute;
        border-radius: 999px;
        content: '';
        pointer-events: none;
    }

    .service-page::before {
        top: -145px;
        right: -115px;
        width: 310px;
        height: 310px;
        border: 44px solid rgba(38, 53, 143, .025);
    }

    .service-page::after {
        bottom: -160px;
        left: -130px;
        width: 320px;
        height: 320px;
        border: 48px solid rgba(25, 200, 61, .025);
    }

    .service-shell {
        position: relative;
        z-index: 1;
        width: min(1060px, calc(100% - 28px));
        margin: 0 auto;
    }

    .service-brand {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        margin-bottom: 18px;
        padding-bottom: 15px;
        border-bottom: 1px solid rgba(227, 232, 239, .88);
    }

    .brand-row {
        display: flex;
        align-items: center;
        gap: 11px;
        min-width: 0;
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
        box-shadow: 0 8px 20px rgba(38, 53, 143, .08);
    }

    .brand-logo {
        width: 43px;
        height: 43px;
        object-fit: contain;
    }

    .brand-logo-fallback {
        display: none;
        width: 38px;
        height: 38px;
        place-items: center;
        border-radius: 11px;
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

    .service-badge {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        flex: 0 0 auto;
        padding: 7px 11px;
        border: 1px solid rgba(38, 53, 143, .08);
        border-radius: 999px;
        background: rgba(255, 255, 255, .86);
        color: var(--rsbm-blue-dark);
        font-size: 10px;
        font-weight: 900;
        letter-spacing: .07em;
        text-transform: uppercase;
    }

    .service-badge-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: var(--rsbm-green);
        box-shadow: 0 0 0 4px rgba(25, 200, 61, .11);
    }

    .service-heading {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 24px;
        align-items: end;
        margin-bottom: 18px;
    }

    .service-heading h1 {
        max-width: 680px;
        margin: 0;
        color: var(--rsbm-text);
        font-size: clamp(30px, 4vw, 48px);
        font-weight: 950;
        line-height: 1.05;
        letter-spacing: -.04em;
    }

    .service-heading h1 span {
        color: var(--rsbm-blue);
    }

    .service-heading p {
        max-width: 650px;
        margin: 9px 0 0;
        color: var(--rsbm-muted);
        font-size: 13.5px;
        line-height: 1.6;
    }

    .service-summary {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 2px;
        padding: 9px 11px;
        border: 1px solid rgba(25, 200, 61, .13);
        border-radius: 11px;
        background: var(--rsbm-green-soft);
        color: #55715d;
        font-size: 10.5px;
        font-weight: 700;
        white-space: nowrap;
    }

    .service-summary svg {
        color: var(--rsbm-green-dark);
    }

    .service-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }

    .service-card {
        --accent: var(--rsbm-blue);
        --accent-dark: var(--rsbm-blue-dark);
        --accent-soft: var(--rsbm-blue-soft);
        --accent-border: rgba(38, 53, 143, .13);

        position: relative;
        display: grid;
        grid-template-columns: 54px minmax(0, 1fr) 34px;
        gap: 14px;
        align-items: center;
        min-height: 126px;
        padding: 17px 17px;
        overflow: hidden;
        border: 1px solid var(--accent-border);
        border-radius: 18px;
        background: rgba(255, 255, 255, .97);
        color: inherit;
        text-decoration: none;
        box-shadow: 0 12px 28px rgba(28, 39, 90, .07);
        transition:
            transform .2s ease,
            box-shadow .2s ease,
            border-color .2s ease;
    }

    .service-card::before {
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: var(--accent);
        content: '';
    }

    .service-card::after {
        position: absolute;
        top: -58px;
        right: -55px;
        width: 135px;
        height: 135px;
        border-radius: 50%;
        background: var(--accent);
        content: '';
        opacity: .045;
    }

    .service-card:hover {
        transform: translateY(-2px);
        border-color: var(--accent);
        color: inherit;
        text-decoration: none;
        box-shadow: 0 18px 38px rgba(28, 39, 90, .11);
    }

    .service-card:focus-visible {
        outline: 3px solid rgba(38, 53, 143, .16);
        outline-offset: 3px;
    }

    .service-card.is-green {
        --accent: var(--rsbm-green-dark);
        --accent-dark: #0c8426;
        --accent-soft: var(--rsbm-green-soft);
        --accent-border: rgba(15, 159, 46, .16);
    }

    .service-icon {
        position: relative;
        z-index: 1;
        display: grid;
        width: 54px;
        height: 54px;
        place-items: center;
        border-radius: 14px;
        background: var(--accent-soft);
        color: var(--accent);
    }

    .service-content {
        position: relative;
        z-index: 1;
        min-width: 0;
    }

    .service-label {
        display: inline-flex;
        align-items: center;
        margin-bottom: 5px;
        color: var(--accent);
        font-size: 9px;
        font-weight: 900;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .service-content h2 {
        margin: 0;
        color: var(--rsbm-text);
        font-size: 17px;
        font-weight: 950;
        letter-spacing: -.02em;
    }

    .service-content p {
        margin: 5px 0 0;
        color: #768295;
        font-size: 11px;
        line-height: 1.5;
    }

    .service-arrow {
        position: relative;
        z-index: 1;
        display: grid;
        width: 34px;
        height: 34px;
        place-items: center;
        border-radius: 10px;
        background: var(--accent-soft);
        color: var(--accent);
        transition: transform .2s ease;
    }

    .service-card:hover .service-arrow {
        transform: translateX(2px);
    }

    .service-footer {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        margin-top: 15px;
        color: #8e98a8;
        font-size: 9.5px;
        line-height: 1.45;
        text-align: center;
    }

    .service-footer svg {
        flex: 0 0 auto;
        color: var(--rsbm-green-dark);
    }

    /* Popup error */
    .service-error-backdrop {
        position: fixed;
        z-index: 9999;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background: rgba(23, 32, 51, .56);
        backdrop-filter: blur(5px);
    }

    .service-error-modal {
        width: min(100%, 420px);
        overflow: hidden;
        border: 1px solid rgba(38, 53, 143, .12);
        border-radius: 20px;
        background: #fff;
        box-shadow: 0 28px 70px rgba(15, 23, 42, .22);
        animation: serviceErrorIn .2s ease-out;
    }

    .service-error-accent {
        height: 4px;
        background: linear-gradient(90deg, var(--rsbm-blue) 0 72%, var(--rsbm-green) 72% 100%);
    }

    .service-error-body {
        padding: 24px;
        text-align: center;
    }

    .service-error-icon {
        display: grid;
        width: 54px;
        height: 54px;
        margin: 0 auto 14px;
        place-items: center;
        border-radius: 14px;
        background: #fef2f2;
        color: #dc2626;
    }

    .service-error-modal h2 {
        margin: 0;
        color: var(--rsbm-text);
        font-size: 19px;
        font-weight: 950;
        letter-spacing: -.02em;
    }

    .service-error-message {
        margin: 9px 0 0;
        color: #64748b;
        font-size: 12px;
        line-height: 1.6;
        overflow-wrap: anywhere;
    }

    .service-error-actions {
        margin-top: 18px;
    }

    .service-error-close {
        min-width: 135px;
        min-height: 42px;
        padding: 0 18px;
        border: 0;
        border-radius: 11px;
        background: linear-gradient(135deg, var(--rsbm-blue), var(--rsbm-blue-dark));
        color: #fff;
        cursor: pointer;
        font-family: inherit;
        font-size: 12px;
        font-weight: 900;
        box-shadow: 0 9px 20px rgba(38, 53, 143, .18);
    }

    .service-error-close:hover {
        transform: translateY(-1px);
    }

    @keyframes serviceErrorIn {
        from {
            opacity: 0;
            transform: translateY(10px) scale(.985);
        }

        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    @media (max-width: 820px) {
        .service-page {
            padding-top: 14px;
        }

        .service-heading {
            grid-template-columns: 1fr;
            gap: 10px;
        }

        .service-summary {
            width: fit-content;
        }

        .service-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 560px) {
        .service-shell {
            width: min(100% - 18px, 520px);
        }

        .service-brand {
            align-items: flex-start;
            margin-bottom: 14px;
            padding-bottom: 12px;
        }

        .brand-logo-shell {
            width: 46px;
            height: 46px;
            flex-basis: 46px;
        }

        .brand-logo {
            width: 39px;
            height: 39px;
        }

        .brand-hospital {
            font-size: 14px;
        }

        .service-badge {
            display: none;
        }

        .service-heading {
            margin-bottom: 14px;
        }

        .service-heading h1 {
            font-size: clamp(28px, 8.5vw, 36px);
        }

        .service-heading p {
            font-size: 12px;
            line-height: 1.55;
        }

        .service-summary {
            padding: 8px 9px;
            white-space: normal;
        }

        .service-card {
            grid-template-columns: 48px minmax(0, 1fr) 30px;
            gap: 11px;
            min-height: 112px;
            padding: 14px 13px;
            border-radius: 16px;
        }

        .service-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
        }

        .service-icon svg {
            width: 24px;
            height: 24px;
        }

        .service-content h2 {
            font-size: 15px;
        }

        .service-content p {
            font-size: 10px;
        }

        .service-arrow {
            width: 30px;
            height: 30px;
            border-radius: 9px;
        }

        .service-footer {
            margin-top: 12px;
            padding: 0 5px;
        }
    }
</style>

<section class="service-page">
    <div class="service-shell">
        <div class="service-brand">
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

            <div class="service-badge">
                <span class="service-badge-dot"></span>
                NADI RSBM
            </div>
        </div>

        <div class="service-heading">
            <div>
                <h1><span>NADI RSBM</span></h1>
                <p>
                    <strong>N</strong>avigasi <strong>A</strong>kses <strong>D</strong>igital <strong>I</strong>nformasi Pasien
                    <br>
                    <strong>Satu Akses Pelayanan Pasien RSUD Bali Mandara</strong>
                </p>
            </div>

            <div class="service-summary">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M12 3L20 7V12C20 17 16.5 20.5 12 22C7.5 20.5 4 17 4 12V7L12 3Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round" />
                    <path d="M9 12L11 14L15.5 9.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                Data pasien telah terverifikasi
            </div>
        </div>

        <div class="service-grid">
            @foreach($menus as $menu)
            <a
                class="service-card {{ $menu['class'] }}"
                href="{{ $menu['url'] }}"
                aria-label="Buka {{ $menu['title'] }}">
                <div class="service-icon">
                    @if($menu['icon'] === 'calendar')
                    {{-- Cek Reservasi --}}
                    <svg width="27" height="27" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <rect x="3" y="5" width="18" height="16" rx="3" stroke="currentColor" stroke-width="2" />
                        <path d="M8 3V7M16 3V7M3 10H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        <path d="M8 14H11M8 17H15" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                    </svg>

                    @elseif($menu['icon'] === 'laboratory')
                    {{-- Hasil Laboratorium --}}
                    <svg width="27" height="27" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M9 3H15M10 3V9L5.5 17.2C4.6 18.8 5.8 21 7.7 21H16.3C18.2 21 19.4 18.8 18.5 17.2L14 9V3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M8 15H16" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                    </svg>

                    @elseif($menu['icon'] === 'radiology')
                    {{-- Hasil Radiologi --}}
                    <svg width="27" height="27" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <rect x="4" y="3" width="16" height="18" rx="3" stroke="currentColor" stroke-width="2" />
                        <circle cx="12" cy="10" r="3" stroke="currentColor" stroke-width="2" />
                        <path d="M8 17C9.1 15.7 10.4 15 12 15C13.6 15 14.9 15.7 16 17" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                    </svg>

                    @elseif($menu['icon'] === 'clock')
                    {{-- Cek Waktu Tunggu --}}
                    <svg width="27" height="27" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2" />
                        <path d="M12 7V12L15.5 14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>

                    @elseif($menu['icon'] === 'circle-play')
                    {{-- Video Edukasi Pasien --}}
                    <svg width="27" height="27" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2" />
                        <path d="M10 8.5L16 12L10 15.5V8.5Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round" />
                    </svg>

                    @elseif($menu['icon'] === 'doctor-calendar')
                    {{-- Jadwal Dokter --}}
                    <svg width="27" height="27" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <circle cx="8.5" cy="7.5" r="3" stroke="currentColor" stroke-width="2" />
                        <path d="M3.5 18.5C4 15.4 5.8 13.5 8.5 13.5C10 13.5 11.2 14 12.1 14.8" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        <rect x="13" y="9.5" width="8" height="10" rx="2" stroke="currentColor" stroke-width="2" />
                        <path d="M15.5 8V11M18.5 8V11M13 13H21" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                        <path d="M15.5 16H18.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                    </svg>

                    @else
                    {{-- Fallback --}}
                    <svg width="27" height="27" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2" />
                        <path d="M12 8V12M12 16H12.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                    </svg>
                    @endif
                </div>

                <div class="service-content">
                    <span class="service-label">Layanan Pasien</span>
                    <h2>{{ $menu['title'] }}</h2>
                    <p>{{ $menu['description'] }}</p>
                </div>

                <span class="service-arrow" aria-hidden="true">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                        <path d="M5 12H19M14 7L19 12L14 17" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </span>
            </a>
            @endforeach
        </div>

        <div class="service-footer">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <rect x="5" y="10" width="14" height="11" rx="2" stroke="currentColor" stroke-width="2" />
                <path d="M8 10V7C8 4.8 9.8 3 12 3C14.2 3 16 4.8 16 7V10" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
            </svg>
            Data pasien digunakan hanya untuk keperluan pelayanan RSUD Bali Mandara Provinsi Bali.
        </div>
    </div>
</section>

@if($errors->has('validasi'))
<div
    id="service-error-backdrop"
    class="service-error-backdrop"
    role="dialog"
    aria-modal="true"
    aria-labelledby="service-error-title">
    <div class="service-error-modal">
        <div class="service-error-accent"></div>

        <div class="service-error-body">
            <div class="service-error-icon" aria-hidden="true">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none">
                    <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2" />
                    <path d="M12 7V13M12 17H12.01" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" />
                </svg>
            </div>

            <h2 id="service-error-title">Layanan Belum Dapat Diproses</h2>

            <p class="service-error-message">
                {{ $errors->first('validasi') }}
            </p>

            <div class="service-error-actions">
                <button
                    id="service-error-close"
                    class="service-error-close"
                    type="button">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const backdrop = document.getElementById('service-error-backdrop');
        const closeButton = document.getElementById('service-error-close');

        if (!backdrop || !closeButton) {
            return;
        }

        function closeErrorPopup() {
            backdrop.remove();
        }

        closeButton.addEventListener('click', closeErrorPopup);

        backdrop.addEventListener('click', function(event) {
            if (event.target === backdrop) {
                closeErrorPopup();
            }
        });

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && document.body.contains(backdrop)) {
                closeErrorPopup();
            }
        });

        closeButton.focus();
    });
</script>
@endif
@endsection