@extends('layouts.app', ['title' => 'Cek Waktu Tunggu | NADI RSBM'])

@section('content')

@php
    $antrean = data_get($result, 'response', []);
    $found = (bool) data_get($result, 'found', false);

    $statusPasien = (string) data_get($antrean, 'status_pasien', '-');
    $statusLower = \Illuminate\Support\Str::lower($statusPasien);

    /*
     * Prioritaskan class status yang dikirim API.
     * class_statusperiksa dapat berisi "is-warning tag".
     */
    $statusClassApi = trim((string) data_get(
        $antrean,
        'class_statusperiksa',
        data_get($antrean, 'status_class', '')
    ));

    $allowedStatusClasses = [
        'is-info',
        'is-warning',
        'is-primary',
        'is-success',
        'is-danger',
    ];

    $statusClass = collect(
        preg_split('/\s+/', $statusClassApi) ?: []
    )->first(function ($class) use ($allowedStatusClasses) {
        return in_array($class, $allowedStatusClasses, true);
    });

    if (!$statusClass) {
        $statusClass = 'is-info';

        if (\Illuminate\Support\Str::contains($statusLower, ['menunggu', 'belum'])) {
            $statusClass = 'is-warning';
        } elseif (\Illuminate\Support\Str::contains(
            $statusLower,
            ['sedang', 'dilayani', 'diperiksa']
        )) {
            $statusClass = 'is-primary';
        } elseif (\Illuminate\Support\Str::contains(
            $statusLower,
            ['selesai', 'closing']
        )) {
            $statusClass = 'is-success';
        }
    }

    /*
     * Seluruh nilai antrean berada di response API.
     * Fallback lama dipertahankan agar kompatibel dengan endpoint lama.
     */
    $totalPasienReservasi = (int) data_get(
        $antrean,
        'total_pasien_reservasi',
        data_get(
            $antrean,
            'total_reservasi',
            data_get(
                $antrean,
                'rincian_antrean.total_reservasi',
                data_get(
                    $antrean,
                    'rincian_antrean_di_depan.total_reservasi',
                    0
                )
            )
        )
    );

    $totalPasienCheckin = (int) data_get(
        $antrean,
        'total_pasien_checkin',
        data_get(
            $antrean,
            'total_teregistrasi',
            data_get(
                $antrean,
                'rincian_antrean.total_teregistrasi',
                data_get(
                    $antrean,
                    'rincian_antrean_di_depan.total_teregistrasi',
                    0
                )
            )
        )
    );

    /*
     * Informasi yang paling dibutuhkan pasien:
     * - berapa pasien di depan
     * - antrean terakhir yang selesai
     * - estimasi waktu jika API memang menyediakannya
     *
     * Tidak membuat estimasi buatan di Blade agar tidak menyesatkan pasien.
     */
    $sisaPasienRaw = data_get(
        $antrean,
        'sisa_pasien_di_depan',
        data_get(
            $antrean,
            'sisa_teregistrasi_di_depan',
            data_get($antrean, 'sisa_reservasi_di_depan')
        )
    );

    $sisaPasienDiDepan = is_numeric($sisaPasienRaw)
        ? max(0, (int) $sisaPasienRaw)
        : null;

    $terakhirSelesai = trim((string) data_get(
        $antrean,
        'terakhir_selesai',
        '-'
    ));

    $estimasiRaw = data_get(
        $antrean,
        'estimasi_waktu_tunggu',
        data_get(
            $antrean,
            'estimasi_menit',
            data_get(
                $antrean,
                'waktu_tunggu_menit',
                data_get($antrean, 'estimasi_tunggu_menit')
            )
        )
    );

    $estimasiLabel = 'Belum tersedia';

    if ($estimasiRaw !== null && $estimasiRaw !== '') {
        $estimasiLabel = is_numeric($estimasiRaw)
            ? '± ' . max(0, (int) $estimasiRaw) . ' menit'
            : trim((string) $estimasiRaw);
    }

    $asalRegistrasi = data_get($antrean, 'asal_registrasi');
    $statusRegistrasi = data_get($antrean, 'status_registrasi');

    $jenisKelamin = trim((string) data_get($antrean, 'jeniskelamin', ''));
    $jenisKelaminLower = \Illuminate\Support\Str::lower($jenisKelamin);

    $isPerempuan = in_array($jenisKelaminLower, [
        'perempuan',
        'wanita',
        'female',
        'p',
    ], true);

    $isLakiLaki = in_array($jenisKelaminLower, [
        'laki-laki',
        'laki laki',
        'pria',
        'male',
        'l',
    ], true);

    /* Data pasien untuk header NADI */
    $sessionPatient = session('pasien', []);

    $patientName = trim((string) (
        data_get($antrean, 'namapasien')
        ?: data_get($sessionPatient, 'name')
        ?: data_get($sessionPatient, 'namapasien')
        ?: data_get($sessionPatient, 'patient_name')
        ?: data_get($sessionPatient, 'nama_pasien')
        ?: 'Pasien'
    ));

    $medicalRecord = trim((string) (
        data_get($antrean, 'nocm')
        ?: data_get($sessionPatient, 'medical_record')
        ?: data_get($sessionPatient, 'rm')
        ?: data_get($sessionPatient, 'nocm')
        ?: ($keyword ?? '-')
    ));

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
       RESET LAYOUT.APP — sama dengan menu utama NADI
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
        min-height: 100svh;
        padding: 22px 12px 104px;
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
        padding: 22px 18px 108px;
    }

    /* =========================================================
       HEADER NADI
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
    .queue-intro {
        position: relative;
        margin-bottom: 14px;
        padding: 15px;
        overflow: hidden;
        border: 1px solid #d9ebfb;
        border-radius: 20px;
        background: linear-gradient(135deg, rgba(235, 247, 255, .98), rgba(247, 252, 255, .98));
        box-shadow: 0 10px 26px rgba(31, 110, 191, .06);
    }

    .queue-intro::after {
        position: absolute;
        top: -30px;
        right: -28px;
        width: 92px;
        height: 92px;
        border: 18px solid rgba(20, 119, 238, .055);
        border-radius: 50%;
        content: '';
    }

    .queue-intro-head {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .queue-intro-icon {
        display: grid;
        width: 45px;
        height: 45px;
        flex: 0 0 45px;
        place-items: center;
        border-radius: 13px;
        background: var(--nadi-blue-soft);
        color: var(--nadi-blue-2);
    }

    .queue-intro-title {
        margin: 0;
        color: var(--nadi-blue);
        font-size: 18px;
        font-weight: 950;
        line-height: 1.2;
        letter-spacing: -.025em;
    }

    .queue-intro-text {
        margin: 4px 0 0;
        color: var(--nadi-muted);
        font-size: 11px;
        font-weight: 650;
        line-height: 1.5;
    }

    /* =========================================================
       ANTREAN UTAMA
       ========================================================= */
    .queue-stack {
        display: grid;
        gap: 14px;
    }

    .queue-card,
    .queue-overview,
    .queue-empty,
    .queue-note-card {
        border: 1px solid var(--nadi-line);
        background: #fff;
        box-shadow: 0 9px 24px rgba(15, 45, 115, .045);
    }

    .queue-card {
        position: relative;
        overflow: hidden;
        border-radius: 20px;
    }

    .queue-card::before {
        display: block;
        height: 4px;
        background: linear-gradient(
            90deg,
            var(--nadi-blue) 0 78%,
            var(--nadi-green) 78% 100%
        );
        content: '';
    }

    .queue-card-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
        padding: 16px 17px 14px;
        border-bottom: 1px solid #edf2f7;
        background: linear-gradient(90deg, rgba(237, 246, 255, .72), rgba(255,255,255,0));
    }

    .queue-poli {
        min-width: 0;
    }

    .queue-kicker {
        margin-bottom: 4px;
        color: var(--nadi-green);
        font-size: 10px;
        font-weight: 900;
        letter-spacing: .05em;
        text-transform: uppercase;
    }

    .queue-poli-name {
        margin: 0;
        color: var(--nadi-dark);
        font-size: 18px;
        font-weight: 950;
        line-height: 1.28;
        overflow-wrap: anywhere;
    }

    .refresh-state {
        display: inline-flex;
        flex: 0 0 auto;
        gap: 7px;
        align-items: center;
        min-height: 30px;
        padding: 0 10px;
        border: 1px solid #e6edf5;
        border-radius: 999px;
        background: #fff;
        color: #71839a;
        font-size: 10px;
        font-weight: 850;
        white-space: nowrap;
    }

    .refresh-state-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: var(--nadi-green);
    }

    .refresh-state.is-loading .refresh-state-dot {
        background: var(--nadi-blue-2);
        animation: queue-pulse 1s infinite;
    }

    .refresh-state.is-error .refresh-state-dot {
        background: var(--nadi-danger);
    }

    @keyframes queue-pulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: .45; transform: scale(.75); }
    }

    .queue-primary-grid {
        display: grid;
        grid-template-columns: 1.35fr .65fr .65fr;
        gap: 10px;
        padding: 14px 16px 5px;
    }

    .queue-primary-item {
        display: flex;
        min-width: 0;
        min-height: 112px;
        flex-direction: column;
        justify-content: space-between;
        padding: 14px 15px;
        border: 1px solid #e7eef6;
        border-radius: 15px;
        background: #fbfdff;
    }

    .queue-primary-item.is-status.is-warning {
        border-color: #f6dfaa;
        background: #fff9e9;
    }

    .queue-primary-item.is-status.is-primary,
    .queue-primary-item.is-status.is-info {
        border-color: #cfe3fa;
        background: var(--nadi-blue-soft);
    }

    .queue-primary-item.is-status.is-success {
        border-color: #ccebd9;
        background: var(--nadi-green-soft);
    }

    .queue-primary-item.is-status.is-danger {
        border-color: #f2cccc;
        background: #fff4f4;
    }

    .queue-primary-label {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #6f8199;
        font-size: 10.5px;
        font-weight: 900;
        letter-spacing: .035em;
        text-transform: uppercase;
    }

    .queue-primary-icon {
        display: grid;
        width: 30px;
        height: 30px;
        flex: 0 0 30px;
        place-items: center;
        border-radius: 9px;
        background: rgba(255,255,255,.88);
        color: var(--nadi-blue-2);
    }

    .queue-primary-value {
        color: var(--nadi-blue);
        font-size: clamp(29px, 5vw, 42px);
        font-weight: 950;
        line-height: 1;
        letter-spacing: -.04em;
        overflow-wrap: anywhere;
    }

    .queue-primary-value.is-text {
        color: var(--nadi-dark);
        font-size: clamp(18px, 3vw, 25px);
        line-height: 1.2;
        letter-spacing: -.02em;
    }

    .queue-patient-strip {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin: 12px 16px 16px;
        padding: 14px 15px;
        border: 1px solid #e7eef6;
        border-radius: 15px;
        background: #fff;
    }

    .queue-patient-main {
        min-width: 0;
    }

    .queue-patient-name-row {
        display: flex;
        min-width: 0;
        align-items: center;
        gap: 9px;
    }

    .queue-patient-name {
        margin: 0;
        color: var(--nadi-dark);
        font-size: 17px;
        font-weight: 950;
        line-height: 1.3;
        overflow-wrap: anywhere;
    }

    .patient-gender-symbol {
        display: inline-grid;
        width: 28px;
        height: 28px;
        flex: 0 0 28px;
        place-items: center;
        border-radius: 999px;
        background: var(--nadi-blue-soft);
        color: var(--nadi-blue-2);
        font-size: 17px;
        font-weight: 900;
    }

    .patient-gender-symbol.is-female {
        background: #fdf2f8;
        color: #db2777;
    }

    .patient-gender-symbol.is-neutral {
        background: #f8fafc;
        color: #64748b;
    }

    .queue-patient-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 4px 10px;
        margin-top: 5px;
        color: #6f8199;
        font-size: 11px;
        font-weight: 700;
        line-height: 1.5;
    }

    .queue-patient-meta strong {
        color: #405775;
    }

    .registration-badge {
        display: inline-flex;
        flex: 0 0 auto;
        align-items: center;
        min-height: 30px;
        padding: 0 10px;
        border-radius: 999px;
        background: var(--nadi-green-soft);
        color: #118148;
        font-size: 10.5px;
        font-weight: 900;
        text-align: center;
    }

    /* =========================================================
       RINGKASAN ANTREAN
       ========================================================= */
    .queue-overview {
        display: grid;
        grid-template-columns: minmax(210px, .8fr) repeat(2, minmax(0, 1fr));
        gap: 10px;
        padding: 12px;
        border-radius: 19px;
    }

    .queue-section-heading {
        display: flex;
        min-width: 0;
        flex-direction: column;
        justify-content: center;
        padding: 14px 13px;
        border-radius: 15px;
        background: linear-gradient(145deg, var(--nadi-blue-soft), #f8faff);
    }

    .queue-section-heading h2 {
        margin: 0;
        color: var(--nadi-blue);
        font-size: 17px;
        font-weight: 950;
        line-height: 1.2;
    }

    .queue-section-heading p {
        margin: 5px 0 0;
        color: #74849a;
        font-size: 10.5px;
        font-weight: 700;
        line-height: 1.5;
    }

    .queue-metric {
        position: relative;
        display: flex;
        min-width: 0;
        min-height: 116px;
        flex-direction: column;
        justify-content: space-between;
        overflow: hidden;
        padding: 14px 15px;
        border: 1px solid #e7eef6;
        border-radius: 15px;
        background: #fff;
    }

    .queue-metric-icon {
        display: grid;
        width: 38px;
        height: 38px;
        place-items: center;
        border-radius: 12px;
        background: var(--nadi-blue-soft);
        color: var(--nadi-blue-2);
    }

    .queue-metric.is-registered .queue-metric-icon {
        background: var(--nadi-green-soft);
        color: var(--nadi-green);
    }

    .queue-metric-bottom {
        display: flex;
        gap: 10px;
        align-items: flex-end;
        justify-content: space-between;
        margin-top: 12px;
    }

    .queue-metric-copy {
        min-width: 0;
    }

    .queue-metric-copy strong {
        display: block;
        margin-bottom: 4px;
        color: #405775;
        font-size: 12px;
        font-weight: 900;
        line-height: 1.3;
    }

    .queue-metric-copy span {
        display: block;
        color: #91a0b2;
        font-size: 9.5px;
        line-height: 1.4;
    }

    .queue-metric-value {
        flex: 0 0 auto;
        color: var(--nadi-blue);
        font-size: clamp(34px, 5vw, 46px);
        font-weight: 950;
        line-height: .92;
        letter-spacing: -.05em;
    }

    .queue-metric.is-registered .queue-metric-value {
        color: var(--nadi-green);
    }

    .queue-note-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        padding: 13px 14px;
        border-radius: 17px;
    }

    .queue-note {
        display: flex;
        min-width: 0;
        align-items: flex-start;
        gap: 9px;
        color: #687b94;
        font-size: 11px;
        line-height: 1.5;
    }

    .queue-note svg {
        flex: 0 0 auto;
        margin-top: 1px;
        color: var(--nadi-blue-2);
    }

    .queue-action {
        display: inline-flex;
        min-height: 38px;
        flex: 0 0 auto;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 0 13px;
        border: 1px solid #cfe3fa;
        border-radius: 10px;
        background: var(--nadi-blue-soft);
        color: var(--nadi-blue-2);
        font-size: 10.5px;
        font-weight: 900;
        text-decoration: none;
        transition: .18s ease;
    }

    .queue-action:hover {
        transform: translateY(-1px);
        border-color: #afd1f4;
        background: #e4f2ff;
        color: var(--nadi-blue);
        text-decoration: none;
    }

    /* =========================================================
       EMPTY STATE
       ========================================================= */
    .queue-empty {
        padding: 34px 20px;
        border-radius: 20px;
        text-align: center;
    }

    .queue-empty-icon {
        display: grid;
        width: 58px;
        height: 58px;
        margin: 0 auto 14px;
        place-items: center;
        border-radius: 17px;
        background: var(--nadi-blue-soft);
        color: var(--nadi-blue-2);
    }

    .queue-empty-kicker {
        color: var(--nadi-green);
        font-size: 10px;
        font-weight: 900;
        letter-spacing: .05em;
        text-transform: uppercase;
    }

    .queue-empty h2 {
        margin: 7px 0 6px;
        color: var(--nadi-dark);
        font-size: 19px;
        font-weight: 950;
    }

    .queue-empty p {
        max-width: 540px;
        margin: 0 auto;
        color: var(--nadi-muted);
        font-size: 11px;
        line-height: 1.55;
    }

    .queue-empty-rm {
        margin-top: 9px !important;
        font-size: 10.5px !important;
    }

    /* =========================================================
       BOTTOM NAVIGATION — sama dengan menu utama NADI
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
        visibility: visible !important;
        opacity: 1 !important;
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
       DESKTOP
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

        .queue-intro {
            margin-bottom: 17px;
            padding: 20px;
            border-radius: 22px;
        }

        .queue-intro-icon {
            width: 52px;
            height: 52px;
            flex-basis: 52px;
            border-radius: 15px;
        }

        .queue-intro-title {
            font-size: 20px;
        }

        .queue-intro-text {
            max-width: 850px;
            font-size: 12px;
        }

        .queue-card {
            border-radius: 22px;
        }

        .queue-card-head {
            align-items: center;
            padding: 18px 20px 16px;
        }

        .queue-kicker {
            font-size: 11px;
        }

        .queue-poli-name {
            font-size: 22px;
        }

        .refresh-state {
            min-height: 34px;
            padding: 0 12px;
            font-size: 11px;
        }

        .queue-primary-grid {
            gap: 12px;
            padding: 17px 19px 6px;
        }

        .queue-primary-item {
            min-height: 130px;
            padding: 17px;
            border-radius: 17px;
        }

        .queue-primary-label {
            font-size: 11px;
        }

        .queue-primary-value {
            font-size: clamp(38px, 4vw, 52px);
        }

        .queue-primary-value.is-text {
            font-size: clamp(22px, 2.5vw, 30px);
        }

        .queue-patient-strip {
            margin: 14px 19px 19px;
            padding: 16px 17px;
            border-radius: 17px;
        }

        .queue-patient-name {
            font-size: 19px;
        }

        .queue-patient-meta {
            font-size: 12px;
        }

        .registration-badge {
            min-height: 34px;
            padding: 0 12px;
            font-size: 11px;
        }

        .queue-overview {
            grid-template-columns: minmax(240px, .8fr) repeat(2, minmax(240px, 1fr));
            gap: 12px;
            padding: 14px;
            border-radius: 21px;
        }

        .queue-section-heading {
            padding: 17px;
            border-radius: 17px;
        }

        .queue-section-heading h2 {
            font-size: 19px;
        }

        .queue-section-heading p {
            font-size: 11px;
        }

        .queue-metric {
            min-height: 132px;
            padding: 17px;
            border-radius: 17px;
        }

        .queue-metric-copy strong {
            font-size: 13px;
        }

        .queue-metric-copy span {
            font-size: 10.5px;
        }

        .queue-note-card {
            padding: 15px 17px;
            border-radius: 19px;
        }

        .queue-note {
            font-size: 12px;
        }

        .queue-action {
            min-height: 40px;
            padding: 0 15px;
            font-size: 11px;
        }

        .nadi-bottom-nav {
            border-top: 1px solid #e1e9f2 !important;
            background: rgba(255,255,255,.97) !important;
            box-shadow: 0 -7px 22px rgba(15,45,115,.08) !important;
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

        .queue-primary-grid {
            grid-template-columns: 1.25fr .75fr .75fr;
        }
    }

    /* =========================================================
       MOBILE
       ========================================================= */
    @media (max-width: 767px) {
        .queue-primary-grid {
            grid-template-columns: 1fr 1fr;
        }

        .queue-primary-item.is-status {
            grid-column: 1 / -1;
        }

        .queue-overview {
            grid-template-columns: 1fr 1fr;
        }

        .queue-section-heading {
            grid-column: 1 / -1;
        }
    }

    @media (max-width: 560px) {
        .nadi-page {
            padding: 0;
            background: #fff;
        }

        .nadi-phone {
            width: 100%;
            min-height: 100svh;
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

        .queue-intro {
            border-radius: 18px;
        }

        .queue-card-head {
            flex-direction: column;
        }

        .refresh-state {
            align-self: flex-start;
        }

        .queue-primary-grid {
            grid-template-columns: 1fr;
        }

        .queue-primary-item.is-status {
            grid-column: auto;
        }

        .queue-primary-item {
            min-height: 96px;
        }

        .queue-patient-strip {
            align-items: flex-start;
            flex-direction: column;
        }

        .registration-badge {
            align-self: flex-start;
        }

        .queue-overview {
            grid-template-columns: 1fr;
            padding: 9px;
        }

        .queue-section-heading {
            grid-column: auto;
        }

        .queue-note-card {
            align-items: stretch;
            flex-direction: column;
        }

        .queue-action {
            width: 100%;
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
    }

    /* =========================================================
       UX PRIORITAS PASIEN
       Nomor antrean -> pasien di depan -> estimasi -> status/loket
       ========================================================= */
    .queue-hero {
        display: grid;
        grid-template-columns: minmax(0, 1.25fr) minmax(260px, .75fr);
        gap: 14px;
        padding: 17px 19px 8px;
    }

    .queue-number-hero {
        position: relative;
        display: flex;
        min-width: 0;
        min-height: 220px;
        flex-direction: column;
        justify-content: space-between;
        overflow: hidden;
        padding: 20px;
        border: 1px solid #cfe3fa;
        border-radius: 19px;
        background:
            radial-gradient(circle at 92% 10%, rgba(20,119,238,.12), transparent 32%),
            linear-gradient(145deg, #f7fbff, var(--nadi-blue-soft));
    }

    .queue-number-hero::after {
        position: absolute;
        right: -42px;
        bottom: -52px;
        width: 160px;
        height: 160px;
        border: 26px solid rgba(22,58,147,.04);
        border-radius: 50%;
        content: '';
        pointer-events: none;
    }

    .queue-hero-label {
        position: relative;
        z-index: 1;
        color: #60738d;
        font-size: 11px;
        font-weight: 850;
        letter-spacing: .07em;
        text-transform: uppercase;
    }

    .queue-hero-number {
        position: relative;
        z-index: 1;
        margin-top: 10px;
        color: var(--nadi-blue);
        font-size: clamp(58px, 9vw, 92px);
        font-weight: 950;
        line-height: .9;
        letter-spacing: -.07em;
        overflow-wrap: anywhere;
    }

    .queue-hero-caption {
        position: relative;
        z-index: 1;
        margin-top: 12px;
        color: #5f7189;
        font-size: 11.5px;
        font-weight: 750;
        line-height: 1.45;
    }

    .queue-wait-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 10px;
    }

    .queue-wait-card {
        display: flex;
        min-width: 0;
        min-height: 102px;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 15px 16px;
        border: 1px solid #e5edf6;
        border-radius: 17px;
        background: #fff;
    }

    .queue-wait-card.is-ahead {
        border-color: #d3eadc;
        background: linear-gradient(145deg, #f8fffb, var(--nadi-green-soft));
    }

    .queue-wait-card.is-estimate {
        border-color: #d8e6fa;
        background: linear-gradient(145deg, #fbfdff, var(--nadi-blue-soft));
    }

    .queue-wait-copy {
        min-width: 0;
    }

    .queue-wait-label {
        margin-bottom: 5px;
        color: #7b8ca3;
        font-size: 10px;
        font-weight: 850;
        letter-spacing: .035em;
        text-transform: uppercase;
    }

    .queue-wait-value {
        color: var(--nadi-dark);
        font-size: 22px;
        font-weight: 900;
        line-height: 1.12;
        overflow-wrap: anywhere;
    }

    .queue-wait-value strong {
        color: var(--nadi-green);
        font-size: 32px;
        font-weight: 950;
    }

    .queue-wait-card.is-estimate .queue-wait-value {
        color: var(--nadi-blue);
    }

    .queue-wait-note {
        margin-top: 4px;
        color: #8493a7;
        font-size: 9.5px;
        font-weight: 650;
        line-height: 1.35;
    }

    .queue-status-strip {
        display: grid;
        grid-template-columns: 1.3fr .7fr;
        gap: 10px;
        margin: 6px 19px 0;
    }

    .queue-status-box {
        min-width: 0;
        padding: 13px 14px;
        border: 1px solid #e5edf6;
        border-radius: 15px;
        background: #fbfdff;
    }

    .queue-status-box.is-warning {
        border-color: #f6dfaa;
        background: #fff9e9;
    }

    .queue-status-box.is-primary,
    .queue-status-box.is-info {
        border-color: #cfe3fa;
        background: var(--nadi-blue-soft);
    }

    .queue-status-box.is-success {
        border-color: #ccebd9;
        background: var(--nadi-green-soft);
    }

    .queue-status-box.is-danger {
        border-color: #f2cccc;
        background: #fff4f4;
    }

    .queue-status-label {
        margin-bottom: 5px;
        color: #7b8ca3;
        font-size: 9.5px;
        font-weight: 850;
        letter-spacing: .035em;
        text-transform: uppercase;
    }

    .queue-status-value {
        color: var(--nadi-dark);
        font-size: 16px;
        font-weight: 900;
        line-height: 1.3;
        overflow-wrap: anywhere;
    }

    .queue-status-box.is-loket .queue-status-value {
        color: var(--nadi-blue);
        font-size: 22px;
        font-weight: 950;
    }

    .queue-service-strip {
        display: grid;
        grid-template-columns: minmax(0, 1.1fr) minmax(0, 1fr) auto;
        gap: 10px;
        align-items: center;
        margin: 10px 19px 19px;
        padding: 13px 14px;
        border: 1px solid #e7eef6;
        border-radius: 15px;
        background: #fff;
    }

    .queue-service-item {
        min-width: 0;
    }

    .queue-service-label {
        margin-bottom: 3px;
        color: #91a0b2;
        font-size: 9px;
        font-weight: 850;
        letter-spacing: .035em;
        text-transform: uppercase;
    }

    .queue-service-value {
        color: #405775;
        font-size: 11.5px;
        font-weight: 850;
        line-height: 1.4;
        overflow-wrap: anywhere;
    }

    .queue-live-bar {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 10px;
        padding: 12px;
        border: 1px solid var(--nadi-line);
        border-radius: 17px;
        background: #fff;
        box-shadow: 0 9px 24px rgba(15,45,115,.04);
    }

    .queue-live-item {
        min-width: 0;
        padding: 11px 12px;
        border-radius: 12px;
        background: #f8fbff;
    }

    .queue-live-label {
        margin-bottom: 4px;
        color: #8797aa;
        font-size: 9px;
        font-weight: 850;
        letter-spacing: .035em;
        text-transform: uppercase;
    }

    .queue-live-value {
        color: var(--nadi-dark);
        font-size: 13px;
        font-weight: 900;
        line-height: 1.35;
        overflow-wrap: anywhere;
    }

    .queue-live-value.is-green {
        color: var(--nadi-green);
    }

    .queue-live-value.is-blue {
        color: var(--nadi-blue);
    }

    .queue-secondary-overview {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
    }

    .queue-secondary-metric {
        display: flex;
        min-width: 0;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 12px 14px;
        border: 1px solid var(--nadi-line);
        border-radius: 15px;
        background: #fff;
    }

    .queue-secondary-copy {
        min-width: 0;
    }

    .queue-secondary-copy strong {
        display: block;
        color: #405775;
        font-size: 11px;
        font-weight: 850;
    }

    .queue-secondary-copy span {
        display: block;
        margin-top: 3px;
        color: #91a0b2;
        font-size: 9px;
        line-height: 1.35;
    }

    .queue-secondary-value {
        flex: 0 0 auto;
        color: var(--nadi-blue);
        font-size: 25px;
        font-weight: 950;
        line-height: 1;
    }

    .queue-secondary-metric.is-checkin .queue-secondary-value {
        color: var(--nadi-green);
    }

    .queue-manual-refresh {
        display: inline-flex;
        min-height: 38px;
        flex: 0 0 auto;
        align-items: center;
        justify-content: center;
        gap: 7px;
        padding: 0 13px;
        border: 1px solid #cfe3fa;
        border-radius: 10px;
        background: var(--nadi-blue-soft);
        color: var(--nadi-blue-2);
        font: inherit;
        font-size: 10.5px;
        font-weight: 900;
        cursor: pointer;
        transition: .18s ease;
    }

    .queue-manual-refresh:hover {
        transform: translateY(-1px);
        border-color: #afd1f4;
        background: #e4f2ff;
        color: var(--nadi-blue);
    }

    .queue-manual-refresh:disabled {
        cursor: wait;
        opacity: .65;
        transform: none;
    }

    @media (min-width: 1024px) {
        .nadi-main {
            max-width: 1380px;
            margin: 0 auto;
        }

        .queue-number-hero {
            min-height: 240px;
        }

        .queue-status-value {
            font-size: 17px;
        }

        .queue-live-value {
            font-size: 14px;
        }
    }

    @media (max-width: 767px) {
        .queue-hero {
            grid-template-columns: 1fr;
            padding: 12px 13px 6px;
        }

        .queue-number-hero {
            min-height: 185px;
        }

        .queue-wait-grid {
            grid-template-columns: 1fr 1fr;
        }

        .queue-wait-card {
            min-height: 94px;
            padding: 13px;
        }

        .queue-wait-value {
            font-size: 17px;
        }

        .queue-wait-value strong {
            font-size: 27px;
        }

        .queue-status-strip {
            margin: 6px 13px 0;
        }

        .queue-service-strip {
            grid-template-columns: 1fr 1fr;
            margin: 10px 13px 13px;
        }

        .queue-service-strip .registration-badge {
            grid-column: 1 / -1;
            justify-self: start;
        }

        .queue-live-bar {
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 7px;
            padding: 9px;
        }

        .queue-live-item {
            padding: 9px 8px;
        }

        .queue-live-label {
            font-size: 7.8px;
        }

        .queue-live-value {
            font-size: 11px;
        }
    }

    @media (max-width: 520px) {
        .queue-wait-grid {
            grid-template-columns: 1fr;
        }

        .queue-status-strip {
            grid-template-columns: 1fr 1fr;
        }

        .queue-service-strip {
            grid-template-columns: 1fr;
        }

        .queue-service-strip .registration-badge {
            grid-column: auto;
        }

        .queue-live-bar {
            grid-template-columns: 1fr;
        }

        .queue-secondary-overview {
            grid-template-columns: 1fr 1fr;
        }

        .queue-note-card {
            gap: 10px;
        }

        .queue-manual-refresh {
            width: 100%;
        }
    }

    @media (max-width: 390px) {
        .queue-secondary-overview {
            grid-template-columns: 1fr;
        }

        .queue-status-strip {
            grid-template-columns: 1fr;
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

            {{-- Intro --}}
            <section class="queue-intro">
                <div class="queue-intro-head">
                    <div class="queue-intro-icon" aria-hidden="true">
                        <svg width="27" height="27" viewBox="0 0 24 24" fill="none">
                            <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/>
                            <path d="M12 7V12L15.5 14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>

                    <div>
                        <h1 class="queue-intro-title">Cek Waktu Tunggu</h1>
                        <p class="queue-intro-text">
                            Pantau status antrean, nomor antrean, loket, dan kondisi pelayanan poli secara berkala.
                        </p>
                    </div>
                </div>
            </section>

            <div class="queue-stack">
                @if(! $found)
                    <section class="queue-empty">
                        <div class="queue-empty-icon">
                            <svg width="29" height="29" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/>
                                <path d="M16.5 16.5L21 21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                <path d="M8.5 11H13.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </div>

                        <div class="queue-empty-kicker">
                            NADI RSBM · {{ !empty($result['is_error']) ? 'Layanan bermasalah' : 'Data tidak ditemukan' }}
                        </div>

                        <h2>Antrean belum tersedia</h2>

                        <p>
                            {{ $result['message'] ?? 'Data antrean pasien tidak ditemukan untuk hari ini.' }}
                        </p>

                        <p class="queue-empty-rm">
                            Nomor RM yang diperiksa:
                            <strong>{{ $keyword }}</strong>
                        </p>
                    </section>
                @else
                    <article class="queue-card">
                        <div class="queue-card-head">
                            <div class="queue-poli">
                                <div class="queue-kicker">Antrean Anda</div>
                                <h2 class="queue-poli-name">
                                    {{ data_get($antrean, 'namaruangan', 'Poli') ?: 'Poli' }}
                                </h2>
                            </div>

                            <div id="refresh-state" class="refresh-state" aria-live="polite">
                                <span class="refresh-state-dot"></span>
                                <span id="refresh-state-text">Data terbaru</span>
                            </div>
                        </div>

                        <div class="queue-hero">
                            <section class="queue-number-hero" aria-label="Nomor antrean pasien">
                                <div>
                                    <div class="queue-hero-label">Nomor Antrean Anda</div>
                                    <div id="queue-number-value" class="queue-hero-number">
                                        {{ data_get($antrean, 'noantrian', '-') }}
                                    </div>
                                </div>

                                <div class="queue-hero-caption">
                                    Tetap berada di sekitar area pelayanan saat antrean Anda semakin dekat.
                                </div>
                            </section>

                            <div class="queue-wait-grid">
                                <section class="queue-wait-card is-ahead">
                                    <div class="queue-wait-copy">
                                        <div class="queue-wait-label">Pasien di Depan Anda</div>
                                        <div class="queue-wait-value">
                                            @if($sisaPasienDiDepan !== null)
                                                <strong id="queue-ahead-value">{{ $sisaPasienDiDepan }}</strong>
                                                <span> pasien</span>
                                            @else
                                                <span id="queue-ahead-value">Belum tersedia</span>
                                            @endif
                                        </div>
                                        <div class="queue-wait-note">
                                            Berdasarkan data antrean dari sistem.
                                        </div>
                                    </div>
                                </section>

                                <section class="queue-wait-card is-estimate">
                                    <div class="queue-wait-copy">
                                        <div class="queue-wait-label">Estimasi Waktu Tunggu</div>
                                        <div id="queue-estimate-value" class="queue-wait-value">
                                            {{ $estimasiLabel }}
                                        </div>
                                        <div class="queue-wait-note">
                                            Hanya tampil bila estimasi tersedia dari API.
                                        </div>
                                    </div>
                                </section>
                            </div>
                        </div>

                        <div class="queue-status-strip">
                            <section
                                id="queue-status-card"
                                class="queue-status-box {{ $statusClass }}"
                            >
                                <div class="queue-status-label">Status Sekarang</div>
                                <div id="queue-status-value" class="queue-status-value">
                                    {{ $statusPasien }}
                                </div>
                            </section>

                            <section class="queue-status-box is-loket">
                                <div class="queue-status-label">Nomor Loket</div>
                                <div id="queue-loket-value" class="queue-status-value">
                                    {{ data_get($antrean, 'antrianloket', '-') ?: '-' }}
                                </div>
                            </section>
                        </div>

                        <div class="queue-service-strip">
                            <div class="queue-service-item">
                                <div class="queue-service-label">Dokter</div>
                                <div class="queue-service-value">
                                    {{ data_get($antrean, 'dokter', '-') ?: '-' }}
                                </div>
                            </div>

                            <div class="queue-service-item">
                                <div class="queue-service-label">No. Rekam Medis</div>
                                <div class="queue-service-value">
                                    {{ $medicalRecord ?: '-' }}
                                </div>
                            </div>

                            @if($statusRegistrasi || $asalRegistrasi)
                                <div class="registration-badge">
                                    {{ $asalRegistrasi ?: $statusRegistrasi }}
                                </div>
                            @endif
                        </div>
                    </article>

                    <section class="queue-live-bar" aria-label="Informasi antrean terkini">
                        <div class="queue-live-item">
                            <div class="queue-live-label">Terakhir Selesai</div>
                            <div id="queue-last-finished-value" class="queue-live-value is-green">
                                {{ $terakhirSelesai !== '' ? $terakhirSelesai : '-' }}
                            </div>
                        </div>

                        <div class="queue-live-item">
                            <div class="queue-live-label">Status Data</div>
                            <div class="queue-live-value is-blue">
                                Auto refresh 10 detik
                            </div>
                        </div>

                        <div class="queue-live-item">
                            <div class="queue-live-label">Diperbarui</div>
                            <div id="queue-updated-at" class="queue-live-value">
                                {{ now()->format('H:i:s') }}
                            </div>
                        </div>
                    </section>

                    <section class="queue-secondary-overview" aria-label="Ringkasan poli">
                        <div class="queue-secondary-metric">
                            <div class="queue-secondary-copy">
                                <strong>Total Reservasi</strong>
                                <span>Informasi tambahan jumlah pasien terdaftar di poli</span>
                            </div>

                            <div id="queue-reservation-value" class="queue-secondary-value">
                                {{ $totalPasienReservasi }}
                            </div>
                        </div>

                        <div class="queue-secondary-metric is-checkin">
                            <div class="queue-secondary-copy">
                                <strong>Sudah Check-in</strong>
                                <span>Informasi tambahan pasien yang sudah registrasi</span>
                            </div>

                            <div id="queue-checkin-value" class="queue-secondary-value">
                                {{ $totalPasienCheckin }}
                            </div>
                        </div>
                    </section>
                @endif

                <section class="queue-note-card">
                    <div class="queue-note">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/>
                            <path d="M12 11V16M12 8H12.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>

                        <span>
                            Nomor antrean dapat berubah mengikuti proses pelayanan di poli.
                        </span>
                    </div>

                    @if($found)
                        <button
                            type="button"
                            id="queue-manual-refresh"
                            class="queue-manual-refresh"
                        >
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M20 6V10H16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M18.3 15A7 7 0 1 1 18.7 8L20 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span>Perbarui Sekarang</span>
                        </button>
                    @else
                        <a class="queue-action" href="{{ route('queue.home') }}">
                            <span>Cek Kembali</span>
                        </a>
                    @endif
                </section>
            </div>
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


@if($found)
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const statusCard = document.getElementById('queue-status-card');
                const statusValue = document.getElementById('queue-status-value');
                const loketValue = document.getElementById('queue-loket-value');
                const queueNumberValue = document.getElementById('queue-number-value');
                const reservationValue = document.getElementById('queue-reservation-value');
                const checkinValue = document.getElementById('queue-checkin-value');
                const aheadValue = document.getElementById('queue-ahead-value');
                const estimateValue = document.getElementById('queue-estimate-value');
                const lastFinishedValue = document.getElementById('queue-last-finished-value');
                const updatedAtValue = document.getElementById('queue-updated-at');
                const manualRefreshButton = document.getElementById('queue-manual-refresh');
                const refreshState = document.getElementById('refresh-state');
                const refreshStateText = document.getElementById('refresh-state-text');

                const statusClasses = [
                    'is-info',
                    'is-warning',
                    'is-primary',
                    'is-success',
                    'is-danger',
                ];

                let sedangRefresh = false;

                function setRefreshState(state, text) {
                    if (!refreshState || !refreshStateText) {
                        return;
                    }

                    refreshState.classList.remove('is-loading', 'is-error');

                    if (state) {
                        refreshState.classList.add(state);
                    }

                    refreshStateText.textContent = text;
                }

                function setText(element, value, fallback = '-') {
                    if (!element) {
                        return;
                    }

                    const normalized = value === null || value === undefined || value === ''
                        ? fallback
                        : value;

                    element.textContent = normalized;
                }

                async function refreshAntrean() {
                    if (sedangRefresh || document.hidden) {
                        return;
                    }

                    sedangRefresh = true;
                    setRefreshState('is-loading', 'Memperbarui');

                    if (manualRefreshButton) {
                        manualRefreshButton.disabled = true;
                    }

                    try {
                        const response = await fetch(
                            "{{ route('queue.refresh') }}",
                            {
                                method: 'POST',
                                headers: {
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                                },
                                body: JSON.stringify({
                                    rm: "{{ $keyword }}",
                                    tanggal_lahir: "{{ $tanggalLahir }}",
                                }),
                                cache: 'no-store',
                            }
                        );

                        const result = await response.json();

                        if (!response.ok || !result.success) {
                            throw new Error(
                                result.message || 'Gagal memperbarui antrean.'
                            );
                        }

                        const data = result.data ?? {};

                        setText(statusValue, data.status_pasien);
                        setText(loketValue, data.antrianloket);
                        setText(queueNumberValue, data.noantrian);

                        const ahead =
                            data.sisa_pasien_di_depan
                            ?? data.sisa_teregistrasi_di_depan
                            ?? data.sisa_reservasi_di_depan;

                        setText(aheadValue, ahead, 'Belum tersedia');

                        const estimate =
                            data.estimasi_waktu_tunggu
                            ?? data.estimasi_menit
                            ?? data.waktu_tunggu_menit
                            ?? data.estimasi_tunggu_menit;

                        if (estimateValue) {
                            if (estimate === null || estimate === undefined || estimate === '') {
                                estimateValue.textContent = 'Belum tersedia';
                            } else if (!Number.isNaN(Number(estimate))) {
                                estimateValue.textContent = '± ' + Math.max(0, Number(estimate)) + ' menit';
                            } else {
                                estimateValue.textContent = String(estimate);
                            }
                        }

                        setText(
                            lastFinishedValue,
                            data.terakhir_selesai,
                            '-'
                        );

                        setText(
                            reservationValue,
                            data.total_pasien_reservasi
                                ?? data.total_reservasi
                                ?? data.sisa_reservasi_di_depan,
                            0
                        );

                        setText(
                            checkinValue,
                            data.total_pasien_checkin
                                ?? data.total_teregistrasi
                                ?? data.sisa_teregistrasi_di_depan,
                            0
                        );

                        if (statusCard) {
                            statusCard.classList.remove(...statusClasses);

                            /*
                             * Prioritaskan class yang dikirim API.
                             * Mendukung nilai seperti "is-warning tag"
                             * tanpa menyebabkan error pada classList.add().
                             */
                            const apiStatusClass = String(
                                data.class_statusperiksa
                                    ?? data.status_class
                                    ?? ''
                            )
                                .trim()
                                .split(/\s+/)
                                .find(className =>
                                    statusClasses.includes(className)
                                );

                            statusCard.classList.add(
                                apiStatusClass ?? 'is-info'
                            );
                        }

                        if (updatedAtValue) {
                            updatedAtValue.textContent = new Date().toLocaleTimeString(
                                'id-ID',
                                {
                                    hour: '2-digit',
                                    minute: '2-digit',
                                    second: '2-digit',
                                }
                            );
                        }

                        setRefreshState('', 'Data terbaru');
                    } catch (error) {
                        setRefreshState('is-error', 'Gagal diperbarui');
                        console.error('Refresh antrean gagal:', error);
                    } finally {
                        sedangRefresh = false;

                        if (manualRefreshButton) {
                            manualRefreshButton.disabled = false;
                        }
                    }
                }

                if (manualRefreshButton) {
                    manualRefreshButton.addEventListener('click', refreshAntrean);
                }

                setInterval(refreshAntrean, 10000);

                document.addEventListener('visibilitychange', function () {
                    if (!document.hidden) {
                        refreshAntrean();
                    }
                });
            });
        </script>
    @endif

@endsection