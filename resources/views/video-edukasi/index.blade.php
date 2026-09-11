<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1, viewport-fit=cover"
    >
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Video Edukasi Pasien | NADI RSBM</title>

    @php
        $sessionPatient = session('pasien', []);

        $patientName = trim((string) (
            data_get($sessionPatient, 'name')
            ?: data_get($sessionPatient, 'namapasien')
            ?: data_get($sessionPatient, 'patient_name')
            ?: data_get($sessionPatient, 'nama_pasien')
            ?: 'Pasien'
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
        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: Inter, Arial, Helvetica, sans-serif;
            color: #183153;
            background: #f7fbff;
        }

        button,
        input {
            font-family: inherit;
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
        }

        .nadi-page {
            min-height: 100svh;
            padding: 22px 12px 108px;
            background:
                radial-gradient(circle at 8% 5%, rgba(24,170,97,.10), transparent 28%),
                radial-gradient(circle at 96% 8%, rgba(20,119,238,.09), transparent 30%),
                linear-gradient(180deg, #fff 0%, var(--nadi-bg) 100%);
        }

        .nadi-shell {
            width: min(100%, 520px);
            margin: 0 auto;
            overflow: hidden;
            border: 1px solid rgba(22,58,147,.10);
            border-radius: 28px;
            background: rgba(255,255,255,.98);
            box-shadow:
                0 26px 70px rgba(15,45,115,.11),
                0 2px 10px rgba(15,45,115,.04);
        }

        .nadi-main {
            padding: 22px 18px 108px;
        }

        /* Header NADI */
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

        /* Intro */
        .edu-intro {
            position: relative;
            margin-bottom: 14px;
            padding: 15px;
            overflow: hidden;
            border: 1px solid #d8efdf;
            border-radius: 20px;
            background: linear-gradient(
                135deg,
                rgba(237,252,244,.98),
                rgba(248,253,255,.98)
            );
            box-shadow: 0 10px 26px rgba(24,170,97,.06);
        }

        .edu-intro::after {
            position: absolute;
            top: -30px;
            right: -28px;
            width: 92px;
            height: 92px;
            border: 18px solid rgba(24,170,97,.055);
            border-radius: 50%;
            content: '';
        }

        .edu-intro-head {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .edu-intro-icon {
            display: grid;
            width: 45px;
            height: 45px;
            flex: 0 0 45px;
            place-items: center;
            border-radius: 13px;
            background: var(--nadi-green-soft);
            color: var(--nadi-green);
        }

        .edu-intro-title {
            margin: 0;
            color: var(--nadi-blue);
            font-size: 18px;
            font-weight: 950;
            line-height: 1.2;
            letter-spacing: -.025em;
        }

        .edu-intro-text {
            margin: 4px 0 0;
            color: var(--nadi-muted);
            font-size: 10.5px;
            font-weight: 650;
            line-height: 1.5;
        }

        /* Search */
        .edu-search {
            margin-bottom: 14px;
            padding: 13px;
            border: 1px solid var(--nadi-line);
            border-radius: 17px;
            background: #fff;
            box-shadow: 0 8px 22px rgba(15,45,115,.04);
        }

        .edu-search-title {
            display: flex;
            align-items: center;
            gap: 7px;
            margin-bottom: 10px;
            color: var(--nadi-blue);
            font-size: 11.5px;
            font-weight: 950;
        }

        .edu-search-form {
            display: grid;
            grid-template-columns: minmax(0,1fr) auto auto;
            gap: 8px;
        }

        .edu-search-input {
            width: 100%;
            height: 40px;
            min-width: 0;
            padding: 0 12px;
            border: 1px solid #dbe5ef;
            border-radius: 10px;
            outline: none;
            background: #fbfdff;
            color: var(--nadi-dark);
            font-size: 11px;
            font-weight: 700;
            transition: .18s ease;
        }

        .edu-search-input:focus {
            border-color: #93baf0;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(20,119,238,.08);
        }

        .edu-btn {
            display: inline-flex;
            min-height: 40px;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 0 13px;
            border-radius: 10px;
            font-size: 10.5px;
            font-weight: 900;
            line-height: 1;
            text-decoration: none;
            white-space: nowrap;
            cursor: pointer;
            transition: .18s ease;
        }

        .edu-btn-primary {
            border: 0;
            background: linear-gradient(135deg, var(--nadi-blue-2), var(--nadi-blue));
            color: #fff;
            box-shadow: 0 7px 16px rgba(20,119,238,.18);
        }

        .edu-btn-primary:hover {
            transform: translateY(-1px);
            color: #fff;
            box-shadow: 0 9px 19px rgba(20,119,238,.24);
        }

        .edu-btn-reset {
            border: 1px solid #dbe5ef;
            background: #f8fafc;
            color: var(--nadi-muted);
        }

        .edu-btn-reset:hover {
            background: var(--nadi-blue-soft);
            color: var(--nadi-blue);
            text-decoration: none;
        }

        /* TOC */
        .video-toc {
            margin-bottom: 14px;
            padding: 13px;
            border: 1px solid var(--nadi-line);
            border-radius: 17px;
            background: #fff;
            box-shadow: 0 8px 22px rgba(15,45,115,.04);
        }

        .video-toc-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 10px;
        }

        .video-toc-title {
            margin: 0;
            color: var(--nadi-blue);
            font-size: 12.5px;
            font-weight: 950;
        }

        .video-toc-count,
        .edu-result-count {
            display: inline-flex;
            align-items: center;
            min-height: 25px;
            padding: 0 8px;
            border-radius: 999px;
            background: var(--nadi-green-soft);
            color: var(--nadi-green);
            font-size: 8.5px;
            font-weight: 900;
            white-space: nowrap;
        }

        .video-toc-list {
            display: grid;
            grid-template-columns: repeat(2,minmax(0,1fr));
            gap: 7px;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .video-toc-link {
            display: flex;
            min-width: 0;
            align-items: flex-start;
            gap: 9px;
            padding: 9px 10px;
            border: 1px solid #edf2f7;
            border-radius: 10px;
            background: #fbfdff;
            color: #405775;
            text-decoration: none;
            transition: .18s ease;
        }

        .video-toc-link:hover {
            transform: translateY(-1px);
            border-color: #cfe2f6;
            background: var(--nadi-blue-soft);
            color: var(--nadi-blue);
            text-decoration: none;
        }

        .video-toc-number {
            display: inline-grid;
            width: 27px;
            height: 27px;
            flex: 0 0 27px;
            place-items: center;
            border-radius: 50%;
            background: var(--nadi-blue);
            color: #fff;
            font-size: 10px;
            font-weight: 900;
        }

        .video-toc-name {
            min-width: 0;
            padding-top: 4px;
            font-size: 10px;
            font-weight: 800;
            line-height: 1.4;
        }

        /* Result */
        .edu-result-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin: 2px 1px 10px;
        }

        .edu-result-title {
            margin: 0;
            color: var(--nadi-blue);
            font-size: 13px;
            font-weight: 950;
        }

        /* Video cards */
        .video-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 12px;
        }

        .video-card {
            scroll-margin-top: 22px;
            display: grid;
            grid-template-columns: 150px minmax(0,1fr);
            overflow: hidden;
            border: 1px solid var(--nadi-line);
            border-radius: 18px;
            background: #fff;
            box-shadow: 0 9px 24px rgba(15,45,115,.045);
            transition: .18s ease;
        }

        .video-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(15,45,115,.08);
        }

        .thumbnail {
            position: relative;
            min-height: 138px;
            overflow: hidden;
            background: #edf4fa;
        }

        .thumbnail img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform .25s ease;
        }

        .video-card:hover .thumbnail img {
            transform: scale(1.025);
        }

        .thumbnail-empty {
            display: grid;
            width: 100%;
            height: 100%;
            min-height: 138px;
            place-items: center;
            background: linear-gradient(135deg, var(--nadi-green-soft), var(--nadi-blue-soft));
            color: var(--nadi-blue-2);
        }

        .play-button {
            position: absolute;
            top: 50%;
            left: 50%;
            display: grid;
            width: 48px;
            height: 48px;
            place-items: center;
            transform: translate(-50%,-50%);
            border-radius: 50%;
            background: rgba(255,255,255,.96);
            color: var(--nadi-blue-2);
            box-shadow: 0 7px 20px rgba(15,45,115,.18);
        }

        .video-content {
            display: flex;
            min-width: 0;
            flex-direction: column;
            padding: 14px;
        }

        .edu-badge {
            align-self: flex-start;
            margin-bottom: 8px;
            padding: 5px 8px;
            border-radius: 999px;
            background: var(--nadi-green-soft);
            color: #118148;
            font-size: 8.5px;
            font-weight: 900;
        }

        .video-title {
            margin: 0 0 6px;
            color: var(--nadi-dark);
            font-size: 14px;
            font-weight: 950;
            line-height: 1.4;
        }

        .video-description {
            flex: 1;
            margin: 0 0 12px;
            color: var(--nadi-muted);
            font-size: 10.5px;
            line-height: 1.55;
        }

        .watch-button {
            display: inline-flex;
            min-height: 36px;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding: 0 12px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--nadi-blue-2), var(--nadi-blue));
            color: #fff;
            font-size: 10px;
            font-weight: 900;
            text-decoration: none;
            transition: .18s ease;
        }

        .watch-button:hover {
            transform: translateY(-1px);
            color: #fff;
            text-decoration: none;
            box-shadow: 0 7px 16px rgba(20,119,238,.18);
        }

        .edu-alert,
        .edu-empty {
            padding: 20px 16px;
            border: 1px solid var(--nadi-line);
            border-radius: 17px;
            background: #fff;
            color: var(--nadi-muted);
            font-size: 11px;
            font-weight: 750;
            line-height: 1.55;
            text-align: center;
        }

        .edu-alert {
            margin-bottom: 14px;
            border-color: #fecaca;
            background: #fff6f6;
            color: #b42318;
        }

        .edu-empty-icon {
            display: grid;
            width: 56px;
            height: 56px;
            margin: 0 auto 12px;
            place-items: center;
            border-radius: 16px;
            background: var(--nadi-blue-soft);
            color: var(--nadi-blue-2);
        }

        .edu-empty strong {
            display: block;
            margin-bottom: 5px;
            color: var(--nadi-dark);
            font-size: 14px;
            font-weight: 950;
        }

        .edu-empty p {
            margin: 0;
        }

        /* Bottom nav */
        .nadi-bottom-nav {
            position: fixed !important;
            z-index: 99999 !important;
            right: 0 !important;
            bottom: 0 !important;
            left: 0 !important;
            display: grid !important;
            width: 100% !important;
            max-width: none !important;
            grid-template-columns: repeat(5,minmax(0,1fr)) !important;
            transform: none !important;
            visibility: visible !important;
            opacity: 1 !important;
            border-top: 1px solid #e6edf5;
            background: rgba(255,255,255,.98);
            box-shadow: 0 -8px 24px rgba(15,45,115,.08);
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

        /* Desktop */
        @media (min-width: 1024px) {
            .nadi-page {
                position: relative;
                left: 50%;
                width: 100vw;
                min-height: 100vh;
                margin-left: -50vw;
                padding: 28px clamp(28px,3vw,56px) 110px;
            }

            .nadi-shell {
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
                border-bottom: 1px solid rgba(22,58,147,.08);
            }

            .nadi-greeting-small {
                font-size: 13px;
            }

            .nadi-greeting-name {
                max-width: min(520px,45vw);
                font-size: 30px;
            }

            .edu-intro {
                padding: 19px 20px;
                border-radius: 22px;
            }

            .edu-intro-icon {
                width: 52px;
                height: 52px;
                flex-basis: 52px;
                border-radius: 15px;
            }

            .edu-intro-title {
                font-size: 20px;
            }

            .edu-intro-text {
                max-width: 850px;
                font-size: 12px;
            }

            .edu-search {
                padding: 16px;
            }

            .edu-search-title {
                font-size: 13px;
            }

            .edu-search-input {
                height: 42px;
                font-size: 12px;
            }

            .edu-btn {
                min-height: 42px;
                font-size: 11px;
            }

            .video-toc {
                padding: 16px;
            }

            .video-toc-title {
                font-size: 14px;
            }

            .video-toc-count,
            .edu-result-count {
                font-size: 9.5px;
            }

            .video-toc-list {
                grid-template-columns: repeat(3,minmax(0,1fr));
                gap: 9px;
            }

            .video-toc-link {
                padding: 11px 12px;
            }

            .video-toc-name {
                font-size: 11px;
            }

            .edu-result-title {
                font-size: 15px;
            }

            .video-grid {
                grid-template-columns: repeat(3,minmax(0,1fr));
                gap: 16px;
            }

            .video-card {
                display: flex;
                flex-direction: column;
                border-radius: 20px;
            }

            .thumbnail {
                aspect-ratio: 16/9;
                min-height: 0;
            }

            .thumbnail-empty {
                min-height: 0;
                aspect-ratio: 16/9;
            }

            .video-content {
                padding: 16px;
            }

            .edu-badge {
                font-size: 9.5px;
            }

            .video-title {
                font-size: 15px;
            }

            .video-description {
                font-size: 11.5px;
            }

            .watch-button {
                min-height: 40px;
                font-size: 11px;
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

        @media (min-width: 768px) and (max-width: 1023px) {
            .nadi-page {
                position: relative;
                left: 50%;
                width: 100vw;
                margin-left: -50vw;
                padding: 24px 22px 110px;
            }

            .nadi-shell {
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

            .video-grid {
                grid-template-columns: repeat(2,minmax(0,1fr));
            }

            .video-card {
                display: flex;
                flex-direction: column;
            }

            .thumbnail {
                aspect-ratio: 16/9;
                min-height: 0;
            }

            .thumbnail-empty {
                min-height: 0;
                aspect-ratio: 16/9;
            }
        }

        @media (max-width: 767px) {
            .edu-search-form {
                grid-template-columns: 1fr 1fr;
            }

            .edu-search-input {
                grid-column: 1 / -1;
            }
        }

        @media (max-width: 560px) {
            .nadi-page {
                padding: 0;
                background: #fff;
            }

            .nadi-shell {
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

            .video-toc-list {
                grid-template-columns: 1fr;
            }

            .video-card {
                grid-template-columns: 125px minmax(0,1fr);
            }

            .thumbnail,
            .thumbnail-empty {
                min-height: 130px;
            }

            .video-title {
                font-size: 13px;
            }

            .video-description {
                font-size: 10px;
            }

            .nadi-bottom-link {
                min-height: 68px;
                font-size: 8.5px;
            }
        }

        @media (max-width: 390px) {
            .nadi-main {
                padding-right: 10px;
                padding-left: 10px;
            }

            .nadi-greeting-name {
                max-width: 150px;
            }

            .video-card {
                display: flex;
                flex-direction: column;
            }

            .thumbnail {
                aspect-ratio: 16/9;
                min-height: 0;
            }

            .thumbnail-empty {
                min-height: 0;
                aspect-ratio: 16/9;
            }

            .edu-search-form {
                grid-template-columns: 1fr;
            }

            .edu-search-input {
                grid-column: auto;
            }
        }
    
        /* =========================================================
           UX EDUKASI PASIEN — fokus ke konten video
           ========================================================= */
        .nadi-main {
            max-width: 1380px;
            margin: 0 auto;
        }

        .edu-intro {
            margin-bottom: 12px;
        }

        .edu-intro-text {
            font-size: 12px;
        }

        .edu-search {
            padding: 12px;
            border-radius: 15px;
        }

        .edu-search-title {
            margin-bottom: 8px;
            font-size: 12px;
            font-weight: 850;
        }

        .edu-search-input {
            height: 44px;
            font-size: 13px;
            font-weight: 650;
        }

        .edu-btn {
            min-height: 44px;
            font-size: 12px;
            font-weight: 850;
        }

        .edu-result-head {
            margin: 5px 1px 12px;
        }

        .edu-result-title {
            font-size: 16px;
            font-weight: 900;
        }

        .edu-result-count {
            min-height: 28px;
            padding: 0 10px;
            font-size: 10px;
            font-weight: 850;
        }

        .video-grid {
            gap: 16px;
        }

        .video-card {
            border-radius: 18px;
            box-shadow: 0 8px 22px rgba(15,45,115,.045);
        }

        .thumbnail {
            display: block;
            position: relative;
            aspect-ratio: 16 / 9;
            min-height: 0;
            overflow: hidden;
            background: #edf4fa;
        }

        .thumbnail-link {
            color: inherit;
            text-decoration: none;
        }

        .thumbnail-link:focus-visible,
        .watch-button:focus-visible,
        .edu-btn:focus-visible,
        .nadi-icon-button:focus-visible,
        .nadi-logout-button:focus-visible,
        .nadi-bottom-link:focus-visible {
            outline: 3px solid rgba(20,119,238,.28);
            outline-offset: 3px;
        }

        .thumbnail-link:hover .play-button {
            transform: translate(-50%, -50%) scale(1.07);
            box-shadow: 0 9px 24px rgba(15,45,115,.24);
        }

        .play-button {
            transition: .18s ease;
        }

        .video-content {
            padding: 16px;
        }

        .video-title {
            margin-bottom: 7px;
            font-size: 16px;
            font-weight: 900;
            line-height: 1.35;
        }

        .video-description {
            margin-bottom: 14px;
            font-size: 13px;
            line-height: 1.6;
        }

        .watch-button {
            min-height: 42px;
            font-size: 12px;
            font-weight: 850;
        }

        .edu-empty,
        .edu-alert {
            font-size: 12px;
        }

        @media (min-width: 1024px) {
            .nadi-main {
                max-width: 1380px;
                margin: 0 auto;
            }

            .video-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 18px;
            }

            .video-card {
                display: flex;
                flex-direction: column;
            }

            .video-title {
                font-size: 17px;
            }

            .video-description {
                font-size: 13px;
            }

            .nadi-bottom-link {
                min-height: 56px;
            }
        }

        @media (min-width: 768px) and (max-width: 1023px) {
            .video-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .video-card {
                display: flex;
                flex-direction: column;
            }
        }

        @media (max-width: 560px) {
            .edu-intro-text {
                font-size: 11.5px;
            }

            .edu-search-form {
                grid-template-columns: 1fr;
            }

            .edu-search-input {
                grid-column: auto;
            }

            .video-card {
                display: flex;
                flex-direction: column;
            }

            .thumbnail,
            .thumbnail-empty {
                aspect-ratio: 16 / 9;
                min-height: 0;
            }

            .video-content {
                padding: 14px;
            }

            .video-title {
                font-size: 15px;
            }

            .video-description {
                font-size: 12px;
            }

            .watch-button {
                min-height: 40px;
                font-size: 11.5px;
            }
        }

    </style>
</head>

<body>
<section class="nadi-page">
    <div class="nadi-shell">
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
                            action="{{ route($logoutRouteName) }}"
                            method="POST"
                            class="nadi-logout-form"
                            onsubmit="return confirm('Apakah Anda yakin ingin keluar?');"
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
            <section class="edu-intro">
                <div class="edu-intro-head">
                    <div class="edu-intro-icon" aria-hidden="true">
                        <svg width="27" height="27" viewBox="0 0 24 24" fill="none">
                            <rect x="3" y="5" width="18" height="14" rx="3" stroke="currentColor" stroke-width="2"/>
                            <path d="M10 9L16 12L10 15V9Z" fill="currentColor"/>
                        </svg>
                    </div>

                    <div>
                        <h1 class="edu-intro-title">Video Edukasi Pasien</h1>
                        <p class="edu-intro-text">
                            Informasi kesehatan untuk membantu pasien memahami prosedur medis,
                            persiapan tindakan, dan perawatan.
                        </p>
                    </div>
                </div>
            </section>

            @if(isset($error))
                <div class="edu-alert">
                    {{ $error }}
                </div>
            @endif

            {{-- Pencarian --}}
            <section class="edu-search">
                <div class="edu-search-title">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/>
                        <path d="M16.5 16.5L21 21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    <span>Temukan Video</span>
                </div>

                <form
                    action="{{ route('video-edukasi.index') }}"
                    method="GET"
                    class="edu-search-form"
                >
                    <input
                        type="text"
                        name="namaeduboard"
                        class="edu-search-input"
                        placeholder="Cari topik atau judul video..."
                        value="{{ $namaEduBoard ?? '' }}"
                        autocomplete="off"
                    >

                    <button type="submit" class="edu-btn edu-btn-primary">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/>
                            <path d="M16.5 16.5L21 21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        <span>Cari</span>
                    </button>

                    @if(!empty($namaEduBoard))
                        <a
                            href="{{ route('video-edukasi.index') }}"
                            class="edu-btn edu-btn-reset"
                        >
                            Reset
                        </a>
                    @endif
                </form>
            </section>

            <div class="edu-result-head">
                <h2 class="edu-result-title">Video Edukasi</h2>
                <span class="edu-result-count">
                    {{ $total ?? 0 }} video
                </span>
            </div>

            {{-- Daftar video --}}
            @if(isset($videos) && $videos->count() > 0)
                <div class="video-grid">
                    @foreach($videos as $index => $video)
                        <article
                            id="video-{{ $index + 1 }}"
                            class="video-card"
                        >
                            @if(!empty($video['link']))
                                <a
                                    href="{{ $video['link'] }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="thumbnail thumbnail-link"
                                    aria-label="Tonton {{ $video['namaeduboard'] ?? 'Video Edukasi' }}"
                                >
                                    @if(!empty($video['thumbnail']))
                                        <img
                                            src="{{ $video['thumbnail'] }}"
                                            alt="{{ $video['namaeduboard'] ?? 'Video edukasi' }}"
                                            loading="lazy"
                                        >
                                    @else
                                        <div class="thumbnail-empty" aria-hidden="true">
                                            <svg width="46" height="46" viewBox="0 0 24 24" fill="none">
                                                <rect x="3" y="5" width="18" height="14" rx="3" stroke="currentColor" stroke-width="1.7"/>
                                                <path d="M10 9L16 12L10 15V9Z" fill="currentColor"/>
                                            </svg>
                                        </div>
                                    @endif

                                    <span class="play-button" aria-hidden="true">
                                        <svg width="21" height="21" viewBox="0 0 24 24" fill="none">
                                            <path d="M9 7L17 12L9 17V7Z" fill="currentColor"/>
                                        </svg>
                                    </span>
                                </a>
                            @else
                                <div class="thumbnail">
                                    @if(!empty($video['thumbnail']))
                                        <img
                                            src="{{ $video['thumbnail'] }}"
                                            alt="{{ $video['namaeduboard'] ?? 'Video edukasi' }}"
                                            loading="lazy"
                                        >
                                    @else
                                        <div class="thumbnail-empty" aria-hidden="true">
                                            <svg width="46" height="46" viewBox="0 0 24 24" fill="none">
                                                <rect x="3" y="5" width="18" height="14" rx="3" stroke="currentColor" stroke-width="1.7"/>
                                                <path d="M10 9L16 12L10 15V9Z" fill="currentColor"/>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <div class="video-content">

                                <h2 class="video-title">
                                    {{ $video['namaeduboard'] ?? 'Video Edukasi' }}
                                </h2>

                                <p class="video-description">
                                    @if(!empty($video['deskripsi']))
                                        {{ $video['deskripsi'] }}
                                    @else
                                        Tonton video edukasi untuk mendapatkan informasi kesehatan.
                                    @endif
                                </p>

                                @if(!empty($video['link']))
                                    <a
                                        href="{{ $video['link'] }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="watch-button"
                                    >
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M9 7L17 12L9 17V7Z" fill="currentColor"/>
                                        </svg>
                                        <span>Tonton Video</span>
                                    </a>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="edu-empty">
                    <div class="edu-empty-icon">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <rect x="3" y="5" width="18" height="14" rx="3" stroke="currentColor" stroke-width="2"/>
                            <path d="M10 9L16 12L10 15V9Z" fill="currentColor"/>
                        </svg>
                    </div>

                    <strong>Video edukasi belum tersedia</strong>
                    <p>Belum ada video edukasi yang dapat ditampilkan.</p>
                </div>
            @endif
        </main>

        {{-- Bottom navigation NADI --}}
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
</body>
</html>