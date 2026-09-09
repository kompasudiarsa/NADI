@extends('layouts.app', ['title' => 'NADI RSBM | RSUD Bali Mandara Provinsi Bali'])

@section('content')
@php
    /*
    |--------------------------------------------------------------------------
    | Helper URL
    |--------------------------------------------------------------------------
    */
    $routeOrUrl = function ($routeName, $fallback) {
        return Route::has($routeName)
            ? route($routeName)
            : url($fallback);
    };

    /*
    |--------------------------------------------------------------------------
    | Data pasien
    |--------------------------------------------------------------------------
    */
    $patient = session('pasien', []);

    $patientName = data_get(
        $patient,
        'name',
        data_get($patient, 'namapasien', 'Pasien')
    );

    $medicalRecord = data_get(
        $patient,
        'medical_record',
        data_get($patient, 'rm', data_get($patient, 'nocm', null))
    );

    /*
    |--------------------------------------------------------------------------
    | Logout
    |--------------------------------------------------------------------------
    */
    $logoutRouteName = null;

    if (Route::has('layanan.logout')) {
        $logoutRouteName = 'layanan.logout';
    } elseif (Route::has('logout')) {
        $logoutRouteName = 'logout';
    }

    /*
    |--------------------------------------------------------------------------
    | Menu utama
    |--------------------------------------------------------------------------
    | Silakan sesuaikan route jika modul sudah tersedia.
    */
    $menus = [
        [
            'title' => 'Cek Reservasi',
            'description' => 'Lihat informasi reservasi dan jadwal pelayanan pasien.',
            'icon' => 'calendar',
            'url' => $routeOrUrl('reservation.index', '/cek-reservasi'),
            'class' => 'is-blue',
        ],
        [
            'title' => 'Hasil Laboratorium',
            'description' => 'Akses hasil pemeriksaan laboratorium yang telah tersedia.',
            'icon' => 'laboratory',
            'url' => $routeOrUrl('laboratory.index', '/cek-hasil-laboratorium'),
            'class' => 'is-green',
        ],
        [
            'title' => 'Hasil Radiologi',
            'description' => 'Lihat hasil dan informasi pemeriksaan radiologi pasien.',
            'icon' => 'radiology',
            'url' => $routeOrUrl('radiology.index', '/cek-hasil-radiologi'),
            'class' => 'is-blue',
        ],
        [
            'title' => 'Cek Waktu Tunggu',
            'description' => 'Pantau status antrean dan perkiraan waktu tunggu pelayanan.',
            'icon' => 'clock',
            'url' => $routeOrUrl('waktu-tunggu.index', '/cek-waktu-tunggu'),
            'class' => 'is-green',
        ],
        [
            'title' => 'Video Edukasi Pasien',
            'description' => 'Tonton video edukasi untuk memahami prosedur medis dan perawatan.',
            'icon' => 'circle-play',
            'url' => $routeOrUrl('video-edukasi.index', '/video-edukasi'),
            'class' => 'is-blue',
        ],
        [
            'title' => 'Jadwal Dokter',
            'description' => 'Lihat jadwal praktik dokter dan layanan poliklinik yang tersedia.',
            'icon' => 'doctor-calendar',
            'url' => $routeOrUrl('jadwal-dokter.index', '/jadwal-dokter'),
            'class' => 'is-green',
        ],
    ];

    /*
    |--------------------------------------------------------------------------
    | Janji temu berikutnya
    |--------------------------------------------------------------------------
    | View mendukung data dari:
    | - $nextAppointment
    | - $upcomingAppointments
    | - $appointments
    | - session pasien.next_appointment
    | - session pasien.appointments
    |
    | Field yang dikenali:
    | tanggal/date/tglreservasi/tanggalreservasi/tglkontrol
    | dokter/namadokter/doctor_name
    | poli/namapoli/namaruangan
    | jam_mulai/start_time/jam + jam_selesai/end_time
    | lokasi/location/ruangan
    */
    $appointment = $nextAppointment ?? null;

    if (!$appointment) {
        $appointmentList = $upcomingAppointments
            ?? $appointments
            ?? data_get(
                $patient,
                'appointments',
                data_get($patient, 'reservations', [])
            );

        if (is_array($appointmentList) || $appointmentList instanceof \Illuminate\Support\Collection) {
            $appointment = collect($appointmentList)
                ->filter(function ($item) {
                    $dateValue = data_get(
                        $item,
                        'tanggal',
                        data_get(
                            $item,
                            'date',
                            data_get(
                                $item,
                                'tglreservasi',
                                data_get(
                                    $item,
                                    'tanggalreservasi',
                                    data_get($item, 'tglkontrol')
                                )
                            )
                        )
                    );

                    if (!$dateValue) {
                        return true;
                    }

                    try {
                        return \Carbon\Carbon::parse($dateValue)->endOfDay()->gte(now()->startOfDay());
                    } catch (\Throwable $e) {
                        return true;
                    }
                })
                ->sortBy(function ($item) {
                    $dateValue = data_get(
                        $item,
                        'tanggal',
                        data_get(
                            $item,
                            'date',
                            data_get(
                                $item,
                                'tglreservasi',
                                data_get(
                                    $item,
                                    'tanggalreservasi',
                                    data_get($item, 'tglkontrol')
                                )
                            )
                        )
                    );

                    try {
                        return $dateValue
                            ? \Carbon\Carbon::parse($dateValue)->timestamp
                            : PHP_INT_MAX;
                    } catch (\Throwable $e) {
                        return PHP_INT_MAX;
                    }
                })
                ->first();
        }
    }

    if (!$appointment) {
        $appointment = data_get($patient, 'next_appointment');
    }

    $appointmentDoctor = $appointment
        ? data_get(
            $appointment,
            'dokter',
            data_get(
                $appointment,
                'namadokter',
                data_get($appointment, 'doctor_name', data_get($appointment, 'doctor.name'))
            )
        )
        : null;

    $appointmentPoli = $appointment
        ? data_get(
            $appointment,
            'poli',
            data_get(
                $appointment,
                'namapoli',
                data_get(
                    $appointment,
                    'poliklinik',
                    data_get($appointment, 'namaunit', data_get($appointment, 'namaruangan'))
                )
            )
        )
        : null;

    $appointmentLocation = $appointment
        ? data_get(
            $appointment,
            'lokasi',
            data_get(
                $appointment,
                'location',
                data_get(
                    $appointment,
                    'ruangan',
                    data_get($appointment, 'namaruangan', $appointmentPoli)
                )
            )
        )
        : null;

    $appointmentDateRaw = $appointment
        ? data_get(
            $appointment,
            'tanggal',
            data_get(
                $appointment,
                'date',
                data_get(
                    $appointment,
                    'tglreservasi',
                    data_get(
                        $appointment,
                        'tanggalreservasi',
                        data_get($appointment, 'tglkontrol')
                    )
                )
            )
        )
        : null;

    $appointmentStart = $appointment
        ? data_get(
            $appointment,
            'jamreservasi',
            data_get(
                $appointment,
                'jam_mulai',
                data_get(
                    $appointment,
                    'start_time',
                    data_get($appointment, 'jam', data_get($appointment, 'jampraktek'))
                )
            )
        )
        : null;

    $appointmentEnd = $appointment
        ? data_get(
            $appointment,
            'jam_selesai',
            data_get($appointment, 'end_time')
        )
        : null;

    $appointmentDate = null;
    $appointmentDateLabel = null;
    $appointmentStatusLabel = null;

    if ($appointmentDateRaw) {
        try {
            $appointmentDate = \Carbon\Carbon::parse($appointmentDateRaw);

            $dayNames = [
                'Sunday' => 'Min',
                'Monday' => 'Sen',
                'Tuesday' => 'Sel',
                'Wednesday' => 'Rab',
                'Thursday' => 'Kam',
                'Friday' => 'Jum',
                'Saturday' => 'Sab',
            ];

            $monthNames = [
                1 => 'Jan',
                2 => 'Feb',
                3 => 'Mar',
                4 => 'Apr',
                5 => 'Mei',
                6 => 'Jun',
                7 => 'Jul',
                8 => 'Agu',
                9 => 'Sep',
                10 => 'Okt',
                11 => 'Nov',
                12 => 'Des',
            ];

            $appointmentDateLabel =
                ($dayNames[$appointmentDate->format('l')] ?? $appointmentDate->format('D'))
                . ', '
                . $appointmentDate->format('d')
                . ' '
                . ($monthNames[(int) $appointmentDate->format('n')] ?? $appointmentDate->format('M'))
                . ' '
                . $appointmentDate->format('Y');

            if ($appointmentDate->isToday()) {
                $appointmentStatusLabel = 'Hari ini';
            } elseif ($appointmentDate->isTomorrow()) {
                $appointmentStatusLabel = 'Besok';
            } else {
                $appointmentStatusLabel = 'Akan Datang';
            }
        } catch (\Throwable $e) {
            $appointmentDateLabel = $appointmentDateRaw;
        }
    }

    $appointmentTimeLabel = null;

    if ($appointmentStart && $appointmentEnd) {
        $appointmentTimeLabel = $appointmentStart . ' - ' . $appointmentEnd;
    } elseif ($appointmentStart) {
        $appointmentTimeLabel = $appointmentStart;
    }

    $appointmentPatientStatus = $appointment
        ? data_get(
            $appointment,
            'status_pasien',
            data_get($appointment, 'label_statusperiksa')
        )
        : null;

    $appointmentPatientStatusClass = $appointment
        ? data_get($appointment, 'class_statusperiksa', 'is-info')
        : 'is-info';

    $appointmentAllUrl = $routeOrUrl('reservation.index', '/cek-reservasi');

    $appointmentRefreshUrl = Route::has('layanan.kontrol-berikutnya.refresh')
        ? route('layanan.kontrol-berikutnya.refresh')
        : null;

    /*
    |--------------------------------------------------------------------------
    | Surat Kontrol BPJS
    |--------------------------------------------------------------------------
    | Prioritas:
    | 1. nosuratkontrol dari kontrol berikutnya
    | 2. nomor kartu BPJS bila surat kontrol belum ada
    */
    $appointmentControlLetter = $appointment
        ? data_get(
            $appointment,
            'nosuratkontrol',
            data_get($patient, 'reservation_raw.nosuratkontrol')
        )
        : data_get($patient, 'reservation_raw.nosuratkontrol');

    $appointmentBpjsCard = $appointment
        ? data_get(
            $appointment,
            'nobpjs',
            data_get(
                $patient,
                'reservation_raw.nobpjs',
                data_get($patient, 'bpjs_number')
            )
        )
        : data_get(
            $patient,
            'reservation_raw.nobpjs',
            data_get($patient, 'bpjs_number')
        );

    $appointmentBpjsReference = $appointmentControlLetter
        ?: $appointmentBpjsCard;

    $appointmentBpjsReferenceType = $appointmentControlLetter
        ? 'nosuratkontrol'
        : 'nokartu';

    /*
    |--------------------------------------------------------------------------
    | Kode Booking / Nomor Reservasi
    |--------------------------------------------------------------------------
    */
    $appointmentBookingCode = $appointment
        ? data_get(
            $appointment,
            'noreservasi',
            data_get($patient, 'reservation_raw.noreservasi')
        )
        : data_get($patient, 'reservation_raw.noreservasi');

    if (Route::has('bpjs.surat-kontrol.index')) {
        $appointmentBpjsCheckBaseUrl = route('bpjs.surat-kontrol.index');
    } elseif (Route::has('reservation.index')) {
        $appointmentBpjsCheckBaseUrl = route('reservation.index');
    } else {
        $appointmentBpjsCheckBaseUrl = url('/layanan/cek-reservasi');
    }

    $appointmentBpjsCheckUrl = null;

    if ($appointmentBpjsReference) {
        $appointmentBpjsCheckUrl =
            $appointmentBpjsCheckBaseUrl
            . '?'
            . http_build_query([
                $appointmentBpjsReferenceType =>
                    $appointmentBpjsReference,
            ]);
    }
@endphp

<style>
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
            linear-gradient(180deg, #ffffff 0%, var(--nadi-bg) 100%);
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
        /* Ruang ekstra agar konten terakhir tidak tertutup bottom navigation */
        padding: 22px 18px 105px;
    }

    /* Header */
    .nadi-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        margin-bottom: 18px;
    }

    .nadi-welcome {
        display: flex;
        min-width: 0;
        align-items: center;
        gap: 12px;
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
        font-size: 13px;
        font-weight: 800;
        line-height: 1.15;
    }

    .nadi-greeting-name {
        max-width: 295px;
        overflow: hidden;
        color: var(--nadi-text);
        font-size: clamp(22px, 6vw, 31px);
        font-weight: 950;
        line-height: 1.03;
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
        position: relative;
        display: grid;
        width: 40px;
        height: 40px;
        place-items: center;
        border: 0;
        border-radius: 12px;
        background: #fff;
        color: var(--nadi-blue-2);
        cursor: pointer;
    }

    .nadi-icon-button:hover,
    .nadi-logout-button:hover {
        background: var(--nadi-blue-soft);
    }

    .nadi-notification-dot {
        position: absolute;
        top: 6px;
        right: 6px;
        width: 8px;
        height: 8px;
        border: 2px solid #fff;
        border-radius: 50%;
        background: var(--nadi-danger);
    }

    .nadi-logout-form {
        margin: 0;
    }

    /* Appointment */
    .nadi-appointment {
        margin-bottom: 18px;
        padding: 13px;
        border: 1px solid #d9ebfb;
        border-radius: 20px;
        background:
            linear-gradient(135deg, rgba(235, 247, 255, .98), rgba(247, 252, 255, .98));
        box-shadow: 0 10px 26px rgba(31, 110, 191, .06);
    }

    .nadi-section-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 10px;
    }

    .nadi-section-title {
        margin: 0;
        color: var(--nadi-blue);
        font-size: 15px;
        font-weight: 950;
        letter-spacing: -.02em;
    }


    .nadi-section-actions {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .nadi-refresh-state {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        color: #7b8ba3;
        font-size: 9px;
        font-weight: 800;
        white-space: nowrap;
    }

    .nadi-refresh-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: var(--nadi-green);
        box-shadow: 0 0 0 3px rgba(24, 170, 97, .10);
    }

    .nadi-refresh-state.is-loading .nadi-refresh-dot {
        background: var(--nadi-blue-2);
        animation: nadiRefreshPulse .9s ease-in-out infinite alternate;
    }

    @keyframes nadiRefreshPulse {
        from { opacity: .35; transform: scale(.82); }
        to { opacity: 1; transform: scale(1.18); }
    }

    .nadi-service-status {
        display: inline-flex;
        align-items: center;
        max-width: 100%;
        margin: 0 0 9px 5px;
        padding: 5px 8px;
        border-radius: 999px;
        background: #eef4ff;
        color: #315fba;
        font-size: 9px;
        font-weight: 900;
        line-height: 1;
        vertical-align: top;
    }

    .nadi-service-status.is-success {
        background: #eafaf1;
        color: #118148;
    }

    .nadi-service-status.is-primary {
        background: #eaf2ff;
        color: #1d64d8;
    }

    .nadi-service-status.is-warning {
        background: #fff7df;
        color: #a16207;
    }

    .nadi-service-status.is-danger {
        background: #fff0f0;
        color: #c63838;
    }

    .nadi-service-status.is-info {
        background: #edf6ff;
        color: #1477ee;
    }

    /* Informasi Surat Kontrol BPJS */
    .nadi-bpjs-info-line {
        margin-top: 10px;
    }

    .nadi-bpjs-info-line.is-letter .nadi-bpjs-info-icon {
        background: var(--nadi-green-soft);
        color: var(--nadi-green);
    }

    .nadi-bpjs-info-icon {
        background: #eef5ff;
        color: var(--nadi-blue-2);
    }

    .nadi-bpjs-info-content {
        min-width: 0;
    }

    .nadi-bpjs-info-head {
        display: flex;
        min-width: 0;
        align-items: center;
        gap: 7px;
    }

    .nadi-bpjs-info-label {
        min-width: 0;
        flex: 1 1 auto;
    }

    .nadi-bpjs-info-value {
        color: #4f6687;
        font-weight: 800;
        letter-spacing: .015em;
    }

    .nadi-bpjs-check-button {
        display: inline-flex;
        min-height: 25px;
        flex: 0 0 auto;
        align-items: center;
        justify-content: center;
        gap: 4px;
        padding: 0 9px;
        border-radius: 8px;
        background: var(--nadi-blue-2);
        color: #fff;
        font-size: 8.5px;
        font-weight: 900;
        line-height: 1;
        text-decoration: none;
        box-shadow: 0 4px 10px rgba(20, 119, 238, .16);
        transition:
            transform .15s ease,
            background .15s ease,
            box-shadow .15s ease;
    }

    .nadi-bpjs-check-button:hover {
        transform: translateY(-1px);
        background: var(--nadi-blue);
        color: #fff;
        text-decoration: none;
        box-shadow: 0 6px 14px rgba(22, 58, 147, .18);
    }

    .nadi-booking-info-line {
        margin-top: 10px;
    }

    .nadi-booking-info-icon {
        background: #fff7e8;
        color: #d97706;
    }

    .nadi-booking-info-value {
        color: #7b5a24;
        font-weight: 900;
        letter-spacing: .04em;
    }

    .nadi-booking-info-content {
        min-width: 0;
    }

    .nadi-booking-info-head {
        display: flex;
        min-width: 0;
        align-items: center;
        gap: 7px;
    }

    .nadi-booking-info-title {
        min-width: 0;
        flex: 1 1 auto;
    }

    .nadi-detail-button {
        display: inline-flex;
        min-height: 25px;
        flex: 0 0 auto;
        align-items: center;
        justify-content: center;
        gap: 4px;
        padding: 0 9px;
        border: 0;
        border-radius: 8px;
        background: #fff3df;
        color: #b86508;
        cursor: pointer;
        font-size: 8.5px;
        font-weight: 900;
        line-height: 1;
        box-shadow: inset 0 0 0 1px #f1d6ab;
        transition:
            transform .15s ease,
            background .15s ease;
    }

    .nadi-detail-button:hover {
        transform: translateY(-1px);
        background: #ffe9c4;
    }

    /*
    |--------------------------------------------------------------------------
    | Modal Detail Kontrol
    |--------------------------------------------------------------------------
    */
    .nadi-detail-backdrop {
        position: fixed;
        z-index: 10050;
        inset: 0;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 18px;
        background: rgba(15, 35, 75, .50);
        backdrop-filter: blur(5px);
        -webkit-backdrop-filter: blur(5px);
    }

    .nadi-detail-backdrop.is-open {
        display: flex;
    }

    .nadi-detail-modal {
        width: min(100%, 430px);
        max-height: min(82vh, 720px);
        overflow: hidden;
        border: 1px solid rgba(22, 58, 147, .10);
        border-radius: 22px;
        background: #fff;
        box-shadow: 0 28px 80px rgba(15, 45, 115, .24);
    }

    .nadi-detail-modal-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 17px 18px 14px;
        border-bottom: 1px solid #eaf0f7;
        background:
            linear-gradient(135deg, #edf7ff, #f8fcff);
    }

    .nadi-detail-modal-title-wrap {
        min-width: 0;
    }

    .nadi-detail-modal-kicker {
        margin-bottom: 3px;
        color: var(--nadi-green);
        font-size: 9px;
        font-weight: 950;
        text-transform: uppercase;
        letter-spacing: .08em;
    }

    .nadi-detail-modal-title {
        margin: 0;
        color: var(--nadi-blue);
        font-size: 17px;
        font-weight: 950;
        line-height: 1.2;
    }

    .nadi-detail-close {
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

    .nadi-detail-body {
        max-height: calc(min(82vh, 720px) - 68px);
        overflow-y: auto;
        padding: 16px 18px 20px;
    }

    .nadi-detail-qr {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 8px;
        margin-bottom: 14px;
        padding: 14px;
        border: 1px solid #dbe9f8;
        border-radius: 16px;
        background: #fff;
        text-align: center;
    }

    .nadi-detail-qr-box {
        display: grid;
        width: 164px;
        height: 164px;
        place-items: center;
        padding: 7px;
        border: 1px solid #e2eaf4;
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 6px 18px rgba(15, 45, 115, .07);
    }

    .nadi-detail-qr-box img,
    .nadi-detail-qr-box canvas {
        display: block;
        max-width: 100%;
        height: auto;
    }

    .nadi-detail-qr-title {
        color: var(--nadi-blue);
        font-size: 11px;
        font-weight: 950;
    }

    .nadi-detail-qr-text {
        color: #74849c;
        font-size: 9px;
        font-weight: 700;
        line-height: 1.4;
    }

    .nadi-detail-qr-fallback {
        display: none;
        align-items: center;
        justify-content: center;
        width: 150px;
        min-height: 150px;
        padding: 12px;
        border: 1px dashed #cbd8e8;
        border-radius: 10px;
        color: #7b8ba3;
        font-size: 10px;
        font-weight: 800;
        line-height: 1.45;
    }

    .nadi-detail-highlight {
        margin-bottom: 14px;
        padding: 13px 14px;
        border: 1px solid #dbe9f8;
        border-radius: 15px;
        background: #f7fbff;
    }

    .nadi-detail-highlight-label {
        color: #718096;
        font-size: 9px;
        font-weight: 850;
    }

    .nadi-detail-highlight-value {
        margin-top: 3px;
        overflow-wrap: anywhere;
        color: var(--nadi-blue);
        font-size: 18px;
        font-weight: 950;
        letter-spacing: .03em;
    }

    .nadi-detail-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 9px;
    }

    .nadi-detail-item {
        min-width: 0;
        padding: 10px 11px;
        border: 1px solid #e6edf5;
        border-radius: 12px;
        background: #fff;
    }

    .nadi-detail-item.is-wide {
        grid-column: 1 / -1;
    }

    .nadi-detail-label {
        margin-bottom: 3px;
        color: #8290a5;
        font-size: 8.5px;
        font-weight: 850;
    }

    .nadi-detail-value {
        overflow-wrap: anywhere;
        color: #19386f;
        font-size: 11px;
        font-weight: 850;
        line-height: 1.4;
    }

    .nadi-detail-value.is-status {
        color: var(--nadi-green);
    }

    @media (max-width: 420px) {
        .nadi-detail-grid {
            grid-template-columns: 1fr;
        }

        .nadi-detail-item.is-wide {
            grid-column: auto;
        }

        .nadi-detail-modal {
            border-radius: 18px;
        }
    }

    .nadi-see-all {
        flex: 0 0 auto;
        color: var(--nadi-blue-2);
        font-size: 11px;
        font-weight: 900;
        text-decoration: none;
    }

    .nadi-see-all:hover {
        text-decoration: underline;
    }

    .nadi-appointment-card {
        position: relative;
        display: grid;
        grid-template-columns: minmax(0, 1fr) 86px;
        gap: 12px;
        min-height: 160px;
        padding: 14px;
        overflow: hidden;
        border: 1px solid rgba(22, 58, 147, .07);
        border-radius: 17px;
        background: #fff;
    }

    .nadi-appointment-info {
        min-width: 0;
    }

    .nadi-appointment-status {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        margin-bottom: 9px;
        padding: 5px 8px;
        border-radius: 999px;
        background: var(--nadi-green-soft);
        color: #118148;
        font-size: 9px;
        font-weight: 900;
        line-height: 1;
    }

    .nadi-appointment-status::before {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor;
        content: '';
    }

    .nadi-info-line {
        display: grid;
        grid-template-columns: 28px minmax(0, 1fr);
        gap: 8px;
        align-items: start;
    }

    .nadi-info-line + .nadi-info-line {
        margin-top: 10px;
    }

    .nadi-info-icon {
        display: grid;
        width: 28px;
        height: 28px;
        place-items: center;
        border-radius: 8px;
        background: var(--nadi-blue-soft);
        color: var(--nadi-blue-2);
    }

    .nadi-info-line:first-of-type .nadi-info-icon {
        background: var(--nadi-green-soft);
        color: var(--nadi-green);
    }

    .nadi-info-primary {
        overflow-wrap: anywhere;
        color: var(--nadi-blue);
        font-size: 13px;
        font-weight: 950;
        line-height: 1.3;
    }

    .nadi-info-secondary {
        margin-top: 1px;
        overflow-wrap: anywhere;
        color: var(--nadi-muted);
        font-size: 10.5px;
        font-weight: 650;
        line-height: 1.35;
    }

    .nadi-doctor-avatar {
        align-self: center;
    }

    .nadi-doctor-photo {
        display: block;
        width: 82px;
        height: 82px;
        object-fit: cover;
        border: 4px solid #f1f6fb;
        border-radius: 50%;
        background: #f3f7fb;
    }

    .nadi-doctor-fallback {
        display: none;
        width: 82px;
        height: 82px;
        place-items: center;
        border: 4px solid #f1f6fb;
        border-radius: 50%;
        background: linear-gradient(145deg, #eef6ff, #dfeeff);
        color: var(--nadi-blue-2);
    }

    .nadi-empty-appointment {
        display: flex;
        min-height: 120px;
        align-items: center;
        gap: 12px;
        padding: 14px;
        border: 1px dashed #cddff1;
        border-radius: 16px;
        background: rgba(255, 255, 255, .72);
    }

    .nadi-empty-icon {
        display: grid;
        width: 44px;
        height: 44px;
        flex: 0 0 44px;
        place-items: center;
        border-radius: 13px;
        background: var(--nadi-blue-soft);
        color: var(--nadi-blue-2);
    }

    .nadi-empty-title {
        color: var(--nadi-blue);
        font-size: 12px;
        font-weight: 900;
    }

    .nadi-empty-text {
        margin-top: 3px;
        color: var(--nadi-muted);
        font-size: 10px;
        line-height: 1.45;
    }

    /* Menu tiles */
    .nadi-menu-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 10px;
    }

    .nadi-menu-card {
        --tile-color: var(--nadi-blue-2);
        --tile-soft: var(--nadi-blue-soft);

        position: relative;
        display: flex;
        min-width: 0;
        min-height: 108px;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 9px;
        padding: 12px 7px;
        border: 1px solid var(--nadi-line);
        border-radius: 16px;
        background: #fff;
        color: inherit;
        text-align: center;
        text-decoration: none;
        box-shadow: 0 7px 18px rgba(20, 70, 130, .045);
        transition:
            transform .18s ease,
            box-shadow .18s ease,
            border-color .18s ease;
    }

    .nadi-menu-card:hover {
        transform: translateY(-2px);
        border-color: rgba(20, 119, 238, .25);
        color: inherit;
        text-decoration: none;
        box-shadow: 0 12px 24px rgba(20, 70, 130, .09);
    }

    .nadi-menu-card.is-green {
        --tile-color: var(--nadi-green);
        --tile-soft: var(--nadi-green-soft);
    }

    .nadi-menu-icon {
        display: grid;
        width: 45px;
        height: 45px;
        place-items: center;
        border-radius: 12px;
        background: var(--tile-soft);
        color: var(--tile-color);
    }

    .nadi-menu-title {
        color: var(--nadi-text);
        font-size: 10.5px;
        font-weight: 900;
        line-height: 1.25;
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

    /* Error modal */
    .nadi-error-backdrop {
        position: fixed;
        z-index: 9999;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background: rgba(15, 23, 42, .56);
        backdrop-filter: blur(5px);
    }

    .nadi-error-modal {
        width: min(100%, 390px);
        overflow: hidden;
        border-radius: 20px;
        background: #fff;
        box-shadow: 0 26px 70px rgba(15, 23, 42, .23);
    }

    .nadi-error-accent {
        height: 4px;
        background: linear-gradient(90deg, var(--nadi-blue-2) 0 75%, var(--nadi-green) 75% 100%);
    }

    .nadi-error-body {
        padding: 24px;
        text-align: center;
    }

    .nadi-error-icon {
        display: grid;
        width: 52px;
        height: 52px;
        margin: 0 auto 12px;
        place-items: center;
        border-radius: 14px;
        background: #fff1f2;
        color: #dc2626;
    }

    .nadi-error-title {
        margin: 0;
        color: #172033;
        font-size: 18px;
        font-weight: 950;
    }

    .nadi-error-message {
        margin: 8px 0 0;
        color: #64748b;
        font-size: 12px;
        line-height: 1.55;
    }

    .nadi-error-close {
        min-width: 130px;
        min-height: 40px;
        margin-top: 18px;
        border: 0;
        border-radius: 11px;
        background: linear-gradient(135deg, var(--nadi-blue-2), var(--nadi-blue));
        color: #fff;
        cursor: pointer;
        font-size: 12px;
        font-weight: 900;
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
            max-width: 210px;
            font-size: 23px;
        }

        .nadi-user-icon {
            width: 43px;
            height: 43px;
            flex-basis: 43px;
        }

        .nadi-appointment {
            padding: 11px;
            border-radius: 17px;
        }

        .nadi-appointment-card {
            grid-template-columns: minmax(0, 1fr) 70px;
            gap: 8px;
            padding: 11px;
        }

        .nadi-doctor-photo,
        .nadi-doctor-fallback {
            width: 68px;
            height: 68px;
        }

        .nadi-menu-grid {
            gap: 8px;
        }

        .nadi-menu-card {
            min-height: 102px;
            padding: 10px 5px;
            border-radius: 14px;
        }

        .nadi-menu-icon {
            width: 42px;
            height: 42px;
        }

        .nadi-menu-title {
            font-size: 9.5px;
        }

        .nadi-bottom-link {
            min-height: 68px;
            font-size: 8.5px;
        }
    }

    @media (max-width: 340px) {
        .nadi-main {
            padding-right: 10px;
            padding-bottom: 100px;
            padding-left: 10px;
        }

        .nadi-menu-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .nadi-greeting-name {
            max-width: 165px;
        }
    }
</style>

<section class="nadi-page">
    <div class="nadi-phone">
        <main class="nadi-main">
            {{-- Header --}}
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
                    <button
                        type="button"
                        class="nadi-icon-button"
                        aria-label="Notifikasi"
                        title="Notifikasi"
                    >
                        <span class="nadi-notification-dot"></span>
                        <svg width="23" height="23" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M18 8C18 4.7 15.3 2 12 2C8.7 2 6 4.7 6 8V11.5C6 13 5.5 14.5 4.5 15.6L4 16.2C3.5 16.8 3.9 18 4.8 18H19.2C20.1 18 20.5 16.8 20 16.2L19.5 15.6C18.5 14.5 18 13 18 11.5V8Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                            <path d="M9.5 21H14.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </button>

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

            {{-- Kontrol berikutnya --}}
            <section
                id="kontrol-berikutnya"
                class="nadi-appointment"
                data-refresh-url="{{ $appointmentRefreshUrl }}"
                data-bpjs-check-base-url="{{ $appointmentBpjsCheckBaseUrl }}"
            >
                <div class="nadi-section-head">
                    <h2 class="nadi-section-title">Kontrol Berikutnya</h2>

                    <div class="nadi-section-actions">
                        @if($appointmentRefreshUrl)
                            <span
                                id="kontrol-refresh-state"
                                class="nadi-refresh-state"
                                aria-live="polite"
                            >
                                <span class="nadi-refresh-dot"></span>
                                <span id="kontrol-refresh-text">Otomatis</span>
                            </span>
                        @endif

                        <a class="nadi-see-all" href="{{ $appointmentAllUrl }}">
                            Lihat Semua
                        </a>
                    </div>
                </div>

                {{--
                    Card selalu ada di DOM agar data dapat berubah dari kosong -> ada
                    tanpa perlu reload seluruh halaman.
                --}}
                <div
                    id="kontrol-card"
                    class="nadi-appointment-card"
                    @if(!$appointment) style="display:none;" @endif
                >
                    <div class="nadi-appointment-info">
                        <div class="nadi-control-meta">
                            <span
                                id="kontrol-status-tanggal"
                                class="nadi-appointment-status"
                                @if(!$appointmentStatusLabel) style="display:none;" @endif
                            >
                                {{ $appointmentStatusLabel ?: 'Akan Datang' }}
                            </span>

                            <span
                                id="kontrol-status-pasien"
                                class="nadi-service-status {{ $appointmentPatientStatusClass }}"
                                @if(!$appointmentPatientStatus) style="display:none;" @endif
                            >
                                {{ $appointmentPatientStatus ?: '' }}
                            </span>

                        </div>

                        <div class="nadi-info-line">
                            <div class="nadi-info-icon">
                                <svg width="17" height="17" viewBox="0 0 24 24" fill="none">
                                    <circle cx="12" cy="8" r="3.5" stroke="currentColor" stroke-width="2"/>
                                    <path d="M6 20C6.6 16.4 8.6 14.5 12 14.5C15.4 14.5 17.4 16.4 18 20" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </div>

                            <div>
                                <div id="kontrol-dokter" class="nadi-info-primary">
                                    {{ $appointmentDoctor ?: 'Dokter belum ditentukan' }}
                                </div>
                                <div id="kontrol-poli" class="nadi-info-secondary">
                                    {{ $appointmentPoli ?: 'Layanan rumah sakit' }}
                                </div>
                            </div>
                        </div>

                        <div class="nadi-info-line">
                            <div class="nadi-info-icon">
                                <svg width="17" height="17" viewBox="0 0 24 24" fill="none">
                                    <rect x="3" y="5" width="18" height="16" rx="3" stroke="currentColor" stroke-width="2"/>
                                    <path d="M8 3V7M16 3V7M3 10H21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </div>

                            <div>
                                <div id="kontrol-tanggal" class="nadi-info-primary">
                                    {{ $appointmentDateLabel ?: 'Tanggal belum tersedia' }}
                                </div>

                                <div id="kontrol-jam" class="nadi-info-secondary">
                                    @if($appointmentTimeLabel)
                                        Estimasi layanan {{ $appointmentTimeLabel }}
                                    @else
                                        Estimasi jam layanan mengikuti jadwal rumah sakit
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="nadi-info-line">
                            <div class="nadi-info-icon">
                                <svg width="17" height="17" viewBox="0 0 24 24" fill="none">
                                    <path d="M12 21C15.5 16.8 18 13.8 18 10.5C18 7.2 15.3 4.5 12 4.5C8.7 4.5 6 7.2 6 10.5C6 13.8 8.5 16.8 12 21Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                    <circle cx="12" cy="10.5" r="2" stroke="currentColor" stroke-width="2"/>
                                </svg>
                            </div>

                            <div>
                                <div id="kontrol-lokasi" class="nadi-info-primary">
                                    {{ $appointmentLocation ?: 'RSUD Bali Mandara' }}
                                </div>
                                <div id="kontrol-lokasi-keterangan" class="nadi-info-secondary">
                                    {{ data_get($appointment, 'gedung', data_get($appointment, 'building', 'Silakan ikuti petunjuk lokasi layanan')) }}
                                </div>
                            </div>
                        </div>

                        {{-- Surat Kontrol / Nomor Kartu BPJS --}}
                        <div
                            id="kontrol-bpjs-control"
                            class="nadi-info-line nadi-bpjs-info-line {{ $appointmentControlLetter ? 'is-letter' : '' }}"
                            @if(!$appointmentBpjsReference) style="display:none;" @endif
                        >
                            <div class="nadi-info-icon nadi-bpjs-info-icon">
                                {{-- Ikon dokumen BPJS / surat kontrol --}}
                                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path
                                        d="M7 3.5H14.5L18.5 7.5V20.5H7C5.9 20.5 5 19.6 5 18.5V5.5C5 4.4 5.9 3.5 7 3.5Z"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linejoin="round"
                                    />
                                    <path
                                        d="M14 3.5V8H18.5"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linejoin="round"
                                    />
                                    <path
                                        d="M8.5 12H15M8.5 15.5H13"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linecap="round"
                                    />
                                    <path
                                        d="M16.5 16.5L18 18L21 14.5"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    />
                                </svg>
                            </div>

                            <div class="nadi-bpjs-info-content">
                                <div class="nadi-bpjs-info-head">
                                    <div
                                        id="kontrol-bpjs-label"
                                        class="nadi-info-primary nadi-bpjs-info-label"
                                    >
                                        {{ $appointmentControlLetter
                                            ? 'Surat Kontrol BPJS'
                                            : 'Nomor Kartu BPJS' }}
                                    </div>

                                    <a
                                        id="kontrol-bpjs-check"
                                        class="nadi-bpjs-check-button"
                                        href="{{ $appointmentBpjsCheckUrl ?: '#' }}"
                                        @if(!$appointmentBpjsReference) style="display:none;" @endif
                                        aria-label="Cek Surat Kontrol BPJS"
                                        title="Cek Surat Kontrol BPJS"
                                    >
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/>
                                            <path d="M16.5 16.5L21 21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        </svg>
                                        Cek
                                    </a>
                                </div>

                                <div
                                    id="kontrol-bpjs-value"
                                    class="nadi-info-secondary nadi-bpjs-info-value"
                                    title="{{ $appointmentBpjsReference ?: '' }}"
                                >
                                    {{ $appointmentBpjsReference ?: '' }}
                                </div>
                            </div>
                        </div>

                        {{-- Kode Booking / Nomor Reservasi --}}
                        <div
                            id="kontrol-booking"
                            class="nadi-info-line nadi-booking-info-line"
                            @if(!$appointmentBookingCode) style="display:none;" @endif
                        >
                            <div class="nadi-info-icon nadi-booking-info-icon">
                                {{-- Ikon tiket / booking --}}
                                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path
                                        d="M4 7.5C4 6.4 4.9 5.5 6 5.5H18C19.1 5.5 20 6.4 20 7.5V9C18.9 9 18 9.9 18 11C18 12.1 18.9 13 20 13V16.5C20 17.6 19.1 18.5 18 18.5H6C4.9 18.5 4 17.6 4 16.5V13C5.1 13 6 12.1 6 11C6 9.9 5.1 9 4 9V7.5Z"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linejoin="round"
                                    />
                                    <path
                                        d="M10 8.5V15.5M14 8.5V15.5"
                                        stroke="currentColor"
                                        stroke-width="1.6"
                                        stroke-linecap="round"
                                        stroke-dasharray="1.2 2.2"
                                    />
                                </svg>
                            </div>

                            <div class="nadi-booking-info-content">
                                <div class="nadi-booking-info-head">
                                    <div class="nadi-info-primary nadi-booking-info-title">
                                        Kode Booking / No. Reservasi
                                    </div>

                                    <button
                                        id="kontrol-detail-button"
                                        class="nadi-detail-button"
                                        type="button"
                                        @if(!$appointment) style="display:none;" @endif
                                        aria-label="Lihat detail kontrol"
                                        title="Lihat detail kontrol"
                                    >
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M2.5 12C4.8 7.8 8 5.7 12 5.7C16 5.7 19.2 7.8 21.5 12C19.2 16.2 16 18.3 12 18.3C8 18.3 4.8 16.2 2.5 12Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                            <circle cx="12" cy="12" r="2.6" stroke="currentColor" stroke-width="2"/>
                                        </svg>
                                        Detail
                                    </button>
                                </div>

                                <div
                                    id="kontrol-booking-value"
                                    class="nadi-info-secondary nadi-booking-info-value"
                                    title="{{ $appointmentBookingCode ?: '' }}"
                                >
                                    {{ $appointmentBookingCode ?: '' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="nadi-doctor-avatar">
                        <img
                            id="kontrol-foto-dokter"
                            class="nadi-doctor-photo"
                            src="{{ data_get($appointment, 'foto_dokter', data_get($appointment, 'doctor_photo', asset('images/doctor-default.png'))) }}"
                            data-default-src="{{ asset('images/doctor.png') }}"
                            alt="Dokter"
                            onerror="this.style.display='none'; this.nextElementSibling.style.display='grid';"
                        >

                        <div
                            id="kontrol-foto-fallback"
                            class="nadi-doctor-fallback"
                            aria-hidden="true"
                        >
                            <svg width="38" height="38" viewBox="0 0 24 24" fill="none">
                                <circle cx="12" cy="8" r="4" stroke="currentColor" stroke-width="2"/>
                                <path d="M5 21C5.8 16.8 8.1 14.5 12 14.5C15.9 14.5 18.2 16.8 19 21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                <path d="M17.5 5.5V10.5M15 8H20" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div
                    id="kontrol-empty"
                    class="nadi-empty-appointment"
                    @if($appointment) style="display:none;" @endif
                >
                    <div class="nadi-empty-icon">
                        <svg width="23" height="23" viewBox="0 0 24 24" fill="none">
                            <rect x="3" y="5" width="18" height="16" rx="3" stroke="currentColor" stroke-width="2"/>
                            <path d="M8 3V7M16 3V7M3 10H21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <path d="M8 15H16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </div>

                    <div>
                        <div class="nadi-empty-title">
                            Belum ada kontrol yang akan datang
                        </div>
                        <div class="nadi-empty-text">
                            Jadwal kontrol hari ini maupun berikutnya akan tampil otomatis di bagian ini.
                        </div>
                    </div>
                </div>
            </section>

            {{-- Modal Detail Kontrol / Reservasi --}}
            <div
                id="kontrol-detail-backdrop"
                class="nadi-detail-backdrop"
                role="dialog"
                aria-modal="true"
                aria-labelledby="kontrol-detail-title"
            >
                <div class="nadi-detail-modal">
                    <div class="nadi-detail-modal-head">
                        <div class="nadi-detail-modal-title-wrap">
                            <div class="nadi-detail-modal-kicker">
                                NADI RSBM
                            </div>
                            <h3
                                id="kontrol-detail-title"
                                class="nadi-detail-modal-title"
                            >
                                Detail Kontrol / Reservasi
                            </h3>
                        </div>

                        <button
                            id="kontrol-detail-close"
                            class="nadi-detail-close"
                            type="button"
                            aria-label="Tutup detail"
                            title="Tutup"
                        >
                            <svg width="19" height="19" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M6 6L18 18M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </button>
                    </div>

                    <div class="nadi-detail-body">
                        {{-- QR Code Nomor Reservasi --}}
                        <div
                            id="detail-qr-section"
                            class="nadi-detail-qr"
                        >
                            <div
                                id="detail-qr-code"
                                class="nadi-detail-qr-box"
                                aria-label="QR Code Nomor Reservasi"
                            >
                                <div
                                    id="detail-qr-fallback"
                                    class="nadi-detail-qr-fallback"
                                >
                                    QR Code belum dapat ditampilkan
                                </div>
                            </div>

                            <div class="nadi-detail-qr-title">
                                QR Code Nomor Reservasi
                            </div>

                            <div class="nadi-detail-qr-text">
                                Tunjukkan QR Code ini saat diperlukan untuk identifikasi reservasi.
                            </div>
                        </div>

                       

                        <div class="nadi-detail-grid">
                            <div class="nadi-detail-item is-wide">
                                <div class="nadi-detail-label">Dokter</div>
                                <div id="detail-dokter" class="nadi-detail-value">-</div>
                            </div>

                            <div class="nadi-detail-item">
                                <div class="nadi-detail-label">Poliklinik</div>
                                <div id="detail-poli" class="nadi-detail-value">-</div>
                            </div>

                            <div class="nadi-detail-item">
                                <div class="nadi-detail-label">Tanggal Kontrol</div>
                                <div id="detail-tanggal" class="nadi-detail-value">-</div>
                            </div>

                            <div class="nadi-detail-item">
                                <div class="nadi-detail-label">Jam Layanan</div>
                                <div id="detail-jam" class="nadi-detail-value">-</div>
                            </div>

                            <div class="nadi-detail-item">
                                <div class="nadi-detail-label">Penjamin</div>
                                <div id="detail-penjamin" class="nadi-detail-value">-</div>
                            </div>

                            <div class="nadi-detail-item is-wide">
                                <div class="nadi-detail-label">Surat Kontrol BPJS / No. Kartu</div>
                                <div id="detail-bpjs" class="nadi-detail-value">-</div>
                            </div>

                            <div class="nadi-detail-item">
                                <div class="nadi-detail-label">Status Registrasi</div>
                                <div id="detail-status-registrasi" class="nadi-detail-value is-status">-</div>
                            </div>

                            <div class="nadi-detail-item">
                                <div class="nadi-detail-label">Status Pelayanan</div>
                                <div id="detail-status-pelayanan" class="nadi-detail-value is-status">-</div>
                            </div>

                            <div class="nadi-detail-item">
                                <div class="nadi-detail-label">No. Registrasi</div>
                                <div id="detail-no-registrasi" class="nadi-detail-value">-</div>
                            </div>

                            <div class="nadi-detail-item">
                                <div class="nadi-detail-label">No. Antrean Poli</div>
                                <div id="detail-no-antrean" class="nadi-detail-value">-</div>
                            </div>

                            <div class="nadi-detail-item is-wide">
                                <div class="nadi-detail-label">No. Rujukan</div>
                                <div id="detail-no-rujukan" class="nadi-detail-value">-</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Menu layanan --}}
            <section id="menu-layanan" class="nadi-menu-grid" aria-label="Menu layanan pasien">
                @foreach($menus as $menu)
                    <a
                        href="{{ $menu['url'] }}"
                        class="nadi-menu-card {{ $menu['class'] }}"
                        aria-label="{{ $menu['title'] }}" title="{{ $menu['description'] ?? $menu['title'] }}"
                    >
                        <div class="nadi-menu-icon">
                            @switch($menu['icon'])
                                @case('calendar')
                                    <svg width="27" height="27" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <rect x="3" y="5" width="18" height="16" rx="3" stroke="currentColor" stroke-width="2"/>
                                        <path d="M8 3V7M16 3V7M3 10H21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        <path d="M8 14H11M8 17H15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                    @break

                                @case('laboratory')
                                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M9 3H15M10 3V9L5.5 17.2C4.6 18.8 5.8 21 7.7 21H16.3C18.2 21 19.4 18.8 18.5 17.2L14 9V3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M8 15H16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        <path d="M9.5 18H14.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" opacity=".75"/>
                                    </svg>
                                    @break

                                @case('radiology')
                                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <rect x="4" y="3" width="16" height="18" rx="3" stroke="currentColor" stroke-width="2"/>
                                        <circle cx="12" cy="10" r="3" stroke="currentColor" stroke-width="2"/>
                                        <path d="M8 17C9.1 15.7 10.4 15 12 15C13.6 15 14.9 15.7 16 17" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        <path d="M8 6H9M15 6H16" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                                    </svg>
                                    @break

                                @case('clock')
                                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/>
                                        <path d="M12 7V12L15.5 14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    @break

                                @case('circle-play')
                                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/>
                                        <path d="M10 8.5L16 12L10 15.5V8.5Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                    </svg>
                                    @break

                                @case('doctor-calendar')
                                    <svg width="29" height="29" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <circle cx="8.5" cy="7.5" r="3" stroke="currentColor" stroke-width="2"/>
                                        <path d="M3.5 18.5C4 15.4 5.8 13.5 8.5 13.5C10 13.5 11.2 14 12.1 14.8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        <rect x="13" y="9.5" width="8" height="10" rx="2" stroke="currentColor" stroke-width="2"/>
                                        <path d="M15.5 8V11M18.5 8V11M13 13H21" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                        <path d="M15.5 16H18.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    </svg>
                                    @break

                                @default
                                    <svg width="27" height="27" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/>
                                        <path d="M12 8V12M12 16H12.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                            @endswitch
                        </div>

                        <div class="nadi-menu-title">
                            {{ $menu['title'] }}
                        </div>
                    </a>
                @endforeach
            </section>
        </main>

        {{-- Bottom navigation --}}
        <nav class="nadi-bottom-nav" aria-label="Navigasi utama">
            <a href="{{ request()->url() }}" class="nadi-bottom-link is-active">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                    <path d="M3 11L12 3L21 11V21H14V15H10V21H3V11Z" fill="currentColor"/>
                </svg>
                <span>Beranda</span>
            </a>

            <a href="#menu-layanan" class="nadi-bottom-link" onclick="document.querySelector('.nadi-menu-grid')?.scrollIntoView({behavior:'smooth'}); return false;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                    <rect x="3" y="3" width="7" height="7" rx="2" stroke="currentColor" stroke-width="2"/>
                    <rect x="14" y="3" width="7" height="7" rx="2" stroke="currentColor" stroke-width="2"/>
                    <rect x="3" y="14" width="7" height="7" rx="2" stroke="currentColor" stroke-width="2"/>
                    <rect x="14" y="14" width="7" height="7" rx="2" stroke="currentColor" stroke-width="2"/>
                </svg>
                <span>Layanan</span>
            </a>

            <a href="{{ $routeOrUrl('riwayat.index', '/riwayat') }}" class="nadi-bottom-link">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                    <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/>
                    <path d="M12 7V12L15.5 14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span>Riwayat</span>
            </a>

            <a href="{{ $routeOrUrl('bantuan.index', '/bantuan') }}" class="nadi-bottom-link">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                    <path d="M4 13V11C4 6.6 7.6 3 12 3C16.4 3 20 6.6 20 11V13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <rect x="2.5" y="11.5" width="4" height="7" rx="2" fill="currentColor"/>
                    <rect x="17.5" y="11.5" width="4" height="7" rx="2" fill="currentColor"/>
                </svg>
                <span>Bantuan</span>
            </a>

            <a href="{{ $routeOrUrl('profile.index', '/profil') }}" class="nadi-bottom-link">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                    <circle cx="12" cy="8" r="4" stroke="currentColor" stroke-width="2"/>
                    <path d="M5 21C5.8 16.8 8.1 14.5 12 14.5C15.9 14.5 18.2 16.8 19 21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
                <span>Profil</span>
            </a>
        </nav>
    </div>
</section>

<script>
    window.nadiCurrentAppointment = @json($appointment);
</script>
<script src="{{ asset('js/nadi-qrcode-local.js') }}"></script>
@if($appointmentRefreshUrl)
<script>
document.addEventListener('DOMContentLoaded', function () {
    const section = document.getElementById('kontrol-berikutnya');

    if (!section) {
        return;
    }

    const refreshUrl = section.dataset.refreshUrl || '';

    if (!refreshUrl) {
        return;
    }

    const refreshState = document.getElementById('kontrol-refresh-state');
    const refreshText = document.getElementById('kontrol-refresh-text');
    const card = document.getElementById('kontrol-card');
    const emptyState = document.getElementById('kontrol-empty');

    const statusTanggalEl = document.getElementById('kontrol-status-tanggal');
    const statusPasienEl = document.getElementById('kontrol-status-pasien');
    const dokterEl = document.getElementById('kontrol-dokter');
    const poliEl = document.getElementById('kontrol-poli');
    const tanggalEl = document.getElementById('kontrol-tanggal');
    const jamEl = document.getElementById('kontrol-jam');
    const lokasiEl = document.getElementById('kontrol-lokasi');
    const lokasiKeteranganEl = document.getElementById('kontrol-lokasi-keterangan');
    const fotoDokterEl = document.getElementById('kontrol-foto-dokter');
    const fotoFallbackEl = document.getElementById('kontrol-foto-fallback');

    const bpjsControlEl = document.getElementById('kontrol-bpjs-control');
    const bpjsLabelEl = document.getElementById('kontrol-bpjs-label');
    const bpjsValueEl = document.getElementById('kontrol-bpjs-value');
    const bpjsCheckEl = document.getElementById('kontrol-bpjs-check');

    const bookingEl = document.getElementById('kontrol-booking');
    const bookingValueEl = document.getElementById('kontrol-booking-value');

    const bpjsCheckBaseUrl = section.dataset.bpjsCheckBaseUrl || '';

    const defaultDoctorPhoto = fotoDokterEl
        ? (fotoDokterEl.dataset.defaultSrc || '')
        : '';

    let isRefreshing = false;
    let refreshTimer = null;

    function setRefreshState(text, loading) {
        if (refreshText) {
            refreshText.textContent = text;
        }

        if (refreshState) {
            refreshState.classList.toggle('is-loading', Boolean(loading));
        }
    }

    function formatDate(value) {
        if (!value) {
            return 'Tanggal belum tersedia';
        }

        const date = new Date(String(value).substring(0, 10) + 'T00:00:00');

        if (Number.isNaN(date.getTime())) {
            return value;
        }

        const days = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
        const months = [
            'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
            'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'
        ];

        return days[date.getDay()]
            + ', '
            + String(date.getDate()).padStart(2, '0')
            + ' '
            + months[date.getMonth()]
            + ' '
            + date.getFullYear();
    }

    function getDateStatus(value) {
        if (!value) {
            return '';
        }

        const target = new Date(String(value).substring(0, 10) + 'T00:00:00');

        if (Number.isNaN(target.getTime())) {
            return 'Akan Datang';
        }

        const today = new Date();
        today.setHours(0, 0, 0, 0);

        const tomorrow = new Date(today);
        tomorrow.setDate(tomorrow.getDate() + 1);

        if (target.getTime() === today.getTime()) {
            return 'Hari ini';
        }

        if (target.getTime() === tomorrow.getTime()) {
            return 'Besok';
        }

        return 'Akan Datang';
    }

    function formatTimeRange(value) {
        if (!value) {
            return '';
        }

        return String(value).replace(
            /(\d{2}:\d{2}):\d{2}/g,
            '$1'
        );
    }

    function normalizeStatusClass(value) {
        const allowed = [
            'is-success',
            'is-primary',
            'is-warning',
            'is-danger',
            'is-info'
        ];

        return allowed.includes(value)
            ? value
            : 'is-info';
    }

    function showEmpty() {
        window.nadiCurrentAppointment = null;

        if (card) {
            card.style.display = 'none';
        }

        if (emptyState) {
            emptyState.style.display = 'flex';
        }

        const detailButton = document.getElementById('kontrol-detail-button');

        if (detailButton) {
            detailButton.style.display = 'none';
        }
    }

    function showAppointment(data) {
        if (!data) {
            showEmpty();
            return;
        }

        window.nadiCurrentAppointment = data;

        const detailButton = document.getElementById('kontrol-detail-button');

        if (detailButton) {
            detailButton.style.display = 'inline-flex';
        }

        if (emptyState) {
            emptyState.style.display = 'none';
        }

        if (card) {
            card.style.display = 'grid';
        }

        const doctor = data.dokter
            || data.namadokter
            || data.doctor_name
            || 'Dokter belum ditentukan';

        const poli = data.poli
            || data.namapoli
            || data.namaruangan
            || 'Layanan rumah sakit';

        const location = data.lokasi
            || data.location
            || data.ruangan
            || data.namaruangan
            || poli
            || 'RSUD Bali Mandara';

        const dateValue = data.tanggalreservasi
            || data.tglreservasi
            || data.tglkontrol
            || data.tanggal
            || data.date
            || '';

        const timeValue = data.jamreservasi
            || data.jam
            || data.jam_mulai
            || data.start_time
            || data.jampraktek
            || '';

        const patientStatus = data.status_pasien
            || data.label_statusperiksa
            || '';

        const patientStatusClass = normalizeStatusClass(
            data.class_statusperiksa || 'is-info'
        );

        if (dokterEl) {
            dokterEl.textContent = doctor;
        }

        if (poliEl) {
            poliEl.textContent = poli;
        }

        if (tanggalEl) {
            tanggalEl.textContent = formatDate(dateValue);
        }

        if (jamEl) {
            const formattedTime = formatTimeRange(timeValue);
            jamEl.textContent = formattedTime
                ? 'Estimasi layanan ' + formattedTime
                : 'Estimasi jam layanan mengikuti jadwal rumah sakit';
        }

        if (lokasiEl) {
            lokasiEl.textContent = location;
        }

        if (lokasiKeteranganEl) {
            lokasiKeteranganEl.textContent = data.gedung
                || data.building
                || 'Silakan ikuti petunjuk lokasi layanan';
        }

        if (statusTanggalEl) {
            const dateStatus = getDateStatus(dateValue);
            statusTanggalEl.textContent = dateStatus;
            statusTanggalEl.style.display = dateStatus ? 'inline-flex' : 'none';
        }

        if (statusPasienEl) {
            statusPasienEl.textContent = patientStatus;

            statusPasienEl.classList.remove(
                'is-success',
                'is-primary',
                'is-warning',
                'is-danger',
                'is-info'
            );

            statusPasienEl.classList.add(patientStatusClass);
            statusPasienEl.style.display = patientStatus ? 'inline-flex' : 'none';
        }

        const controlLetter = data.nosuratkontrol || '';
        const bpjsCard = data.nobpjs || data.bpjs_number || '';
        const bpjsReference = controlLetter || bpjsCard;
        const bpjsReferenceType = controlLetter ? 'nosuratkontrol' : 'nokartu';

        if (bpjsControlEl) {
            if (bpjsReference) {
                bpjsControlEl.style.display = 'grid';
                bpjsControlEl.classList.toggle('is-letter', Boolean(controlLetter));

                if (bpjsLabelEl) {
                    bpjsLabelEl.textContent = controlLetter
                        ? 'Surat Kontrol BPJS'
                        : 'Nomor Kartu BPJS';
                }

                if (bpjsValueEl) {
                    bpjsValueEl.textContent = bpjsReference;
                    bpjsValueEl.setAttribute('title', bpjsReference);
                }

                if (bpjsCheckEl) {
                    const separator = bpjsCheckBaseUrl.includes('?') ? '&' : '?';

                    bpjsCheckEl.href = bpjsCheckBaseUrl
                        ? (
                            bpjsCheckBaseUrl
                            + separator
                            + encodeURIComponent(bpjsReferenceType)
                            + '='
                            + encodeURIComponent(bpjsReference)
                        )
                        : '#';

                    bpjsCheckEl.style.display = 'inline-flex';
                }
            } else {
                bpjsControlEl.style.display = 'none';

                if (bpjsCheckEl) {
                    bpjsCheckEl.style.display = 'none';
                    bpjsCheckEl.href = '#';
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Kode Booking / Nomor Reservasi
        |--------------------------------------------------------------------------
        */
        const bookingCode = data.noreservasi
            || data.kodebooking
            || data.kode_booking
            || '';

        if (bookingEl) {
            bookingEl.style.display = bookingCode
                ? 'grid'
                : 'none';
        }

        if (bookingValueEl) {
            bookingValueEl.textContent = bookingCode;
            bookingValueEl.setAttribute('title', bookingCode);
        }

        if (fotoDokterEl) {
            const newPhoto = data.foto_dokter
                || data.doctor_photo
                || defaultDoctorPhoto;

            fotoDokterEl.style.display = 'block';

            if (fotoFallbackEl) {
                fotoFallbackEl.style.display = 'none';
            }

            if (newPhoto && fotoDokterEl.getAttribute('src') !== newPhoto) {
                fotoDokterEl.setAttribute('src', newPhoto);
            }
        }
    }

    async function refreshKontrol() {
        if (isRefreshing) {
            return;
        }

        isRefreshing = true;
        setRefreshState('Memperbarui...', true);

        try {
            const response = await fetch(refreshUrl, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin',
                cache: 'no-store'
            });

            let result = null;

            try {
                result = await response.json();
            } catch (parseError) {
                throw new Error('Respons refresh kontrol bukan JSON.');
            }

            if (!response.ok || !result || result.success !== true) {
                throw new Error(
                    result && result.message
                        ? result.message
                        : 'Kontrol berikutnya gagal diperbarui.'
                );
            }

            if (result.data) {
                showAppointment(result.data);
            } else {
                showEmpty();
            }

            setRefreshState('Terbaru', false);
        } catch (error) {
            console.error('Refresh kontrol berikutnya:', error);
            setRefreshState('Gagal refresh', false);
        } finally {
            isRefreshing = false;
        }
    }

    function startAutoRefresh() {
        if (refreshTimer) {
            window.clearInterval(refreshTimer);
        }

        refreshTimer = window.setInterval(
            refreshKontrol,
            30000
        );
    }

    // Ambil data terbaru saat halaman selesai dimuat.
    window.setTimeout(refreshKontrol, 500);

    // Kemudian refresh otomatis setiap 30 detik.
    startAutoRefresh();

    // Saat pasien kembali ke tab/browser, langsung perbarui lagi.
    document.addEventListener('visibilitychange', function () {
        if (!document.hidden) {
            refreshKontrol();
        }
    });

    window.addEventListener('focus', refreshKontrol);
});
</script>
@endif

<script src="{{ asset('js/nadi-qrcode.js') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const detailButton = document.getElementById('kontrol-detail-button');
    const backdrop = document.getElementById('kontrol-detail-backdrop');
    const closeButton = document.getElementById('kontrol-detail-close');
    const qrSection = document.getElementById('detail-qr-section');
    const qrCodeEl = document.getElementById('detail-qr-code');
    const qrFallbackEl = document.getElementById('detail-qr-fallback');

    if (!detailButton || !backdrop || !closeButton) {
        return;
    }

    const fields = {
        booking: document.getElementById('detail-kode-booking'),
        doctor: document.getElementById('detail-dokter'),
        poli: document.getElementById('detail-poli'),
        date: document.getElementById('detail-tanggal'),
        time: document.getElementById('detail-jam'),
        insurer: document.getElementById('detail-penjamin'),
        bpjs: document.getElementById('detail-bpjs'),
        registrationStatus: document.getElementById('detail-status-registrasi'),
        serviceStatus: document.getElementById('detail-status-pelayanan'),
        registrationNumber: document.getElementById('detail-no-registrasi'),
        queueNumber: document.getElementById('detail-no-antrean'),
        referralNumber: document.getElementById('detail-no-rujukan')
    };

    function setValue(element, value) {
        if (!element) {
            return;
        }

        const normalized = value === null
            || value === undefined
            || value === ''
                ? '-'
                : String(value);

        element.textContent = normalized;
    }

    function formatDetailDate(value) {
        if (!value) {
            return '-';
        }

        const date = new Date(
            String(value).substring(0, 10) + 'T00:00:00'
        );

        if (Number.isNaN(date.getTime())) {
            return String(value);
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

    function formatDetailTime(value) {
        if (!value) {
            return '-';
        }

        return String(value).replace(
            /(\d{2}:\d{2}):\d{2}/g,
            '$1'
        );
    }

    function renderReservationQr(value) {
        const booking = value && value !== '-'
            ? String(value)
            : '';

        if (!qrCodeEl) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Bersihkan QR sebelumnya
        |--------------------------------------------------------------------------
        */
        while (qrCodeEl.firstChild) {
            qrCodeEl.removeChild(
                qrCodeEl.firstChild
            );
        }

        if (!booking) {
            if (qrSection) {
                qrSection.style.display = 'none';
            }

            return;
        }

        if (qrSection) {
            qrSection.style.display = 'flex';
        }

        /*
        |--------------------------------------------------------------------------
        | Generate QR di browser
        |--------------------------------------------------------------------------
        | Nilai noreservasi tidak dikirim ke layanan QR eksternal.
        | Library hanya dipakai untuk menggambar QR pada browser.
        |--------------------------------------------------------------------------
        */
        if (
            window.NadiQRCode &&
            typeof window.NadiQRCode.render === 'function'
        ) {
            try {
                window.NadiQRCode.render(
                    qrCodeEl,
                    booking,
                    150
                );

                return;
            } catch (qrError) {
                console.error(
                    'Gagal membuat QR Code:',
                    qrError
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Fallback jika library QR gagal dimuat
        |--------------------------------------------------------------------------
        */
        const fallback =
            document.createElement('div');

        fallback.className =
            'nadi-detail-qr-fallback';

        fallback.style.display =
            'flex';

        fallback.textContent =
            'QR Code tidak dapat dibuat. No. Reservasi: '
            + booking;

        qrCodeEl.appendChild(
            fallback
        );
    }

    function populateDetail(data) {
        data = data || {};

        const booking =
            data.noreservasi
            || data.kodebooking
            || data.kode_booking
            || '-';

        const doctor =
            data.dokter
            || data.namadokter
            || data.doctor_name
            || '-';

        const poli =
            data.poli
            || data.namapoli
            || data.namaruangan
            || '-';

        const date =
            data.tanggalreservasi
            || data.tglreservasi
            || data.tglkontrol
            || data.tanggal
            || data.date
            || '';

        const time =
            data.jamreservasi
            || data.jam
            || data.jam_mulai
            || data.start_time
            || data.jampraktek
            || '';

        const bpjs =
            data.nosuratkontrol
                ? 'Surat Kontrol: ' + data.nosuratkontrol
                : (
                    data.nobpjs || data.bpjs_number
                        ? 'No. Kartu: ' + (data.nobpjs || data.bpjs_number)
                        : '-'
                );

        renderReservationQr(
            booking
        );

        setValue(fields.booking, booking);
        setValue(fields.doctor, doctor);
        setValue(fields.poli, poli);
        setValue(fields.date, formatDetailDate(date));
        setValue(fields.time, formatDetailTime(time));
        setValue(fields.insurer, data.kelompokpasien || '-');
        setValue(fields.bpjs, bpjs);
        setValue(
            fields.registrationStatus,
            data.status_registrasi
            || (
                data.sudah_teregistrasi
                    ? 'Sudah Teregistrasi'
                    : 'Belum Teregistrasi'
            )
        );
        setValue(
            fields.serviceStatus,
            data.status_pasien
            || data.label_statusperiksa
            || data.status
            || '-'
        );
        setValue(
            fields.registrationNumber,
            data.noregistrasi || '-'
        );
        setValue(
            fields.queueNumber,
            data.noantrianpoli
            || data.noantrian
            || '-'
        );
        setValue(
            fields.referralNumber,
            data.norujukan || '-'
        );
    }

    function openDetail() {
        const data = window.nadiCurrentAppointment || null;

        if (!data) {
            return;
        }

        populateDetail(data);

        backdrop.classList.add('is-open');
        document.body.style.overflow = 'hidden';
        closeButton.focus();
    }

    function closeDetail() {
        backdrop.classList.remove('is-open');
        document.body.style.overflow = '';
        detailButton.focus();
    }

    detailButton.addEventListener('click', openDetail);
    closeButton.addEventListener('click', closeDetail);

    backdrop.addEventListener('click', function (event) {
        if (event.target === backdrop) {
            closeDetail();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (
            event.key === 'Escape'
            && backdrop.classList.contains('is-open')
        ) {
            closeDetail();
        }
    });
});
</script>

@if($errors->has('validasi'))
    <div
        id="nadi-error-backdrop"
        class="nadi-error-backdrop"
        role="dialog"
        aria-modal="true"
        aria-labelledby="nadi-error-title"
    >
        <div class="nadi-error-modal">
            <div class="nadi-error-accent"></div>

            <div class="nadi-error-body">
                <div class="nadi-error-icon" aria-hidden="true">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/>
                        <path d="M12 7V13M12 17H12.01" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/>
                    </svg>
                </div>

                <h2 id="nadi-error-title" class="nadi-error-title">
                    Layanan Belum Dapat Diproses
                </h2>

                <p class="nadi-error-message">
                    {{ $errors->first('validasi') }}
                </p>

                <button
                    id="nadi-error-close"
                    class="nadi-error-close"
                    type="button"
                >
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const backdrop = document.getElementById('nadi-error-backdrop');
            const closeButton = document.getElementById('nadi-error-close');

            if (!backdrop || !closeButton) {
                return;
            }

            function closeErrorPopup() {
                backdrop.remove();
            }

            closeButton.addEventListener('click', closeErrorPopup);

            backdrop.addEventListener('click', function (event) {
                if (event.target === backdrop) {
                    closeErrorPopup();
                }
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape' && document.body.contains(backdrop)) {
                    closeErrorPopup();
                }
            });
        });
    </script>
@endif
@endsection