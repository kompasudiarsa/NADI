<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <title>
        Video Edukasi Pasien
    </title>


    <style>

        /*
        |--------------------------------------------------------------------------
        | Global
        |--------------------------------------------------------------------------
        */

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;

            font-family:
                Inter,
                Arial,
                Helvetica,
                sans-serif;

            background:
                #f4f7f6;

            color:
                #1f2937;
        }

        button,
        input {
            font-family: inherit;
        }

        .page {
            min-height: 100vh;
        }


        /*
        |--------------------------------------------------------------------------
        | Header
        |--------------------------------------------------------------------------
        */

        .header {
            background:
                linear-gradient(
                    135deg,
                    #087f5b,
                    #0ca678
                );

            color: white;

            padding:
                28px 20px 50px;
        }

        .header-inner {
            max-width: 1100px;

            margin: auto;
        }

        .header-top {
            display: flex;

            justify-content:
                space-between;

            align-items:
                flex-start;

            gap: 24px;
        }

        .header-title {
            flex: 1;
        }

        .header h1 {
            margin:
                0 0 8px;

            font-size: 28px;

            font-weight: 700;

            line-height: 1.25;
        }

        .header p {
            margin: 0;

            opacity: .92;

            font-size: 15px;

            line-height: 1.6;

            max-width: 620px;
        }


        /*
        |--------------------------------------------------------------------------
        | Header Action
        |--------------------------------------------------------------------------
        */

        .header-actions {
            display: flex;

            align-items: center;

            gap: 9px;

            flex-shrink: 0;
        }

        .header-button {
            display: inline-flex;

            align-items: center;

            justify-content: center;

            gap: 7px;

            min-height: 41px;

            padding:
                9px 15px;

            border-radius: 10px;

            font-size: 13px;

            font-weight: 600;

            line-height: 1;

            text-decoration: none;

            white-space: nowrap;

            transition:
                background .2s ease,
                transform .2s ease,
                box-shadow .2s ease;
        }

        .header-button:hover {
            transform:
                translateY(-1px);
        }


        /*
        |--------------------------------------------------------------------------
        | Menu Utama
        |--------------------------------------------------------------------------
        */

        .header-button-menu {
            color: white;

            background:
                rgba(
                    255,
                    255,
                    255,
                    .15
                );

            border:
                1px solid
                rgba(
                    255,
                    255,
                    255,
                    .30
                );
        }

        .header-button-menu:hover {
            background:
                rgba(
                    255,
                    255,
                    255,
                    .25
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Logout
        |--------------------------------------------------------------------------
        */

        .logout-form {
            margin: 0;
        }

        .header-button-logout {
            border:
                1px solid white;

            background:
                white;

            color:
                #087f5b;

            cursor: pointer;
        }

        .header-button-logout:hover {
            background:
                #f1f5f3;

            box-shadow:
                0 5px 15px
                rgba(
                    0,
                    0,
                    0,
                    .10
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Container
        |--------------------------------------------------------------------------
        */

        .container {
            max-width: 1100px;

            margin:
                -25px auto 0;

            padding:
                0 20px 40px;
        }


        /*
        |--------------------------------------------------------------------------
        | Toolbar
        |--------------------------------------------------------------------------
        */

        .toolbar {
            background: white;

            border-radius: 16px;

            padding: 18px;

            box-shadow:
                0 6px 25px
                rgba(
                    0,
                    0,
                    0,
                    .06
                );

            margin-bottom: 24px;
        }

        .search-form {
            display: flex;

            gap: 10px;
        }

        .search-input {
            flex: 1;

            min-width: 0;

            border:
                1px solid #dfe5e2;

            border-radius: 10px;

            padding:
                12px 14px;

            font-size: 14px;

            color: #212529;

            background: white;

            outline: none;

            transition:
                border .2s ease,
                box-shadow .2s ease;
        }

        .search-input::placeholder {
            color: #9aa5a0;
        }

        .search-input:focus {
            border-color:
                #0ca678;

            box-shadow:
                0 0 0 3px
                rgba(
                    12,
                    166,
                    120,
                    .10
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Search Button
        |--------------------------------------------------------------------------
        */

        .btn-search {
            border: 0;

            border-radius: 10px;

            padding:
                12px 20px;

            background:
                #087f5b;

            color: white;

            cursor: pointer;

            font-weight: 600;

            transition:
                background .2s ease;
        }

        .btn-search:hover {
            background:
                #066649;
        }


        /*
        |--------------------------------------------------------------------------
        | Reset Button
        |--------------------------------------------------------------------------
        */

        .btn-reset {
            border:
                1px solid #dfe5e2;

            border-radius: 10px;

            padding:
                12px 16px;

            background: white;

            color:
                #495057;

            text-decoration: none;

            font-size: 14px;

            display: flex;

            align-items: center;

            justify-content: center;

            transition:
                background .2s ease;
        }

        .btn-reset:hover {
            background:
                #f5f7f6;
        }


        /*
        |--------------------------------------------------------------------------
        | Summary
        |--------------------------------------------------------------------------
        */

        .summary {
            display: flex;

            justify-content:
                space-between;

            align-items: center;

            gap: 15px;

            margin-bottom: 16px;
        }

        .summary-title {
            font-size: 18px;

            font-weight: 700;

            color:
                #212529;
        }

        .total {
            color:
                #68736e;

            font-size: 14px;

            background:
                #e6fcf5;

            padding:
                6px 11px;

            border-radius:
                30px;

            white-space: nowrap;
        }


        /*
        |--------------------------------------------------------------------------
        | Video Grid
        |--------------------------------------------------------------------------
        */

        .video-grid {
            display: grid;

            grid-template-columns:
                repeat(
                    auto-fit,
                    minmax(
                        300px,
                        1fr
                    )
                );

            gap: 20px;
        }


        /*
        |--------------------------------------------------------------------------
        | Video Card
        |--------------------------------------------------------------------------
        */

        .video-card {
            display: flex;

            flex-direction:
                column;

            background:
                white;

            border-radius:
                18px;

            overflow: hidden;

            border:
                1px solid
                #edf0ef;

            box-shadow:
                0 5px 20px
                rgba(
                    0,
                    0,
                    0,
                    .045
                );

            transition:
                transform .2s ease,
                box-shadow .2s ease;
        }

        .video-card:hover {
            transform:
                translateY(-3px);

            box-shadow:
                0 10px 30px
                rgba(
                    0,
                    0,
                    0,
                    .08
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Thumbnail
        |--------------------------------------------------------------------------
        */

        .thumbnail {
            position: relative;

            aspect-ratio:
                16 / 9;

            background:
                #e9ecef;

            overflow: hidden;
        }

        .thumbnail img {
            width: 100%;

            height: 100%;

            object-fit:
                cover;

            display: block;

            transition:
                transform .3s ease;
        }

        .video-card:hover
        .thumbnail img {
            transform:
                scale(1.025);
        }


        /*
        |--------------------------------------------------------------------------
        | Thumbnail Empty
        |--------------------------------------------------------------------------
        */

        .thumbnail-empty {
            width: 100%;

            height: 100%;

            display: flex;

            align-items: center;

            justify-content: center;

            background:
                linear-gradient(
                    135deg,
                    #e6fcf5,
                    #d3f9d8
                );

            font-size: 48px;
        }


        /*
        |--------------------------------------------------------------------------
        | Play Button
        |--------------------------------------------------------------------------
        */

        .play-button {
            position:
                absolute;

            left: 50%;

            top: 50%;

            transform:
                translate(
                    -50%,
                    -50%
                );

            width: 58px;

            height: 58px;

            border-radius: 50%;

            background:
                rgba(
                    255,
                    255,
                    255,
                    .95
                );

            display: flex;

            justify-content:
                center;

            align-items:
                center;

            box-shadow:
                0 5px 20px
                rgba(
                    0,
                    0,
                    0,
                    .20
                );

            font-size: 22px;

            color:
                #087f5b;

            padding-left: 3px;
        }


        /*
        |--------------------------------------------------------------------------
        | Video Content
        |--------------------------------------------------------------------------
        */

        .video-content {
            display: flex;

            flex-direction:
                column;

            flex: 1;

            padding: 20px;
        }


        /*
        |--------------------------------------------------------------------------
        | Badge
        |--------------------------------------------------------------------------
        */

        .badge {
            align-self:
                flex-start;

            display:
                inline-block;

            padding:
                5px 10px;

            border-radius:
                30px;

            background:
                #e6fcf5;

            color:
                #087f5b;

            font-size:
                12px;

            font-weight:
                700;

            margin-bottom:
                12px;
        }


        /*
        |--------------------------------------------------------------------------
        | Video Title
        |--------------------------------------------------------------------------
        */

        .video-title {
            margin:
                0 0 10px;

            font-size:
                18px;

            line-height:
                1.45;

            color:
                #212529;
        }


        /*
        |--------------------------------------------------------------------------
        | Description
        |--------------------------------------------------------------------------
        */

        .video-description {
            margin:
                0 0 18px;

            color:
                #68736e;

            font-size:
                14px;

            line-height:
                1.6;

            flex: 1;
        }


        /*
        |--------------------------------------------------------------------------
        | Watch Button
        |--------------------------------------------------------------------------
        */

        .watch-button {
            display: flex;

            justify-content:
                center;

            align-items:
                center;

            gap: 8px;

            width: 100%;

            padding:
                12px 16px;

            border-radius:
                10px;

            background:
                #087f5b;

            color: white;

            text-decoration:
                none;

            font-weight:
                600;

            font-size:
                14px;

            transition:
                background .2s ease,
                transform .2s ease;
        }

        .watch-button:hover {
            background:
                #066649;
        }


        /*
        |--------------------------------------------------------------------------
        | Empty State
        |--------------------------------------------------------------------------
        */

        .empty {
            background:
                white;

            border-radius:
                16px;

            padding:
                50px 20px;

            text-align:
                center;

            color:
                #68736e;

            border:
                1px solid
                #edf0ef;
        }

        .empty-icon {
            font-size: 48px;

            margin-bottom: 12px;
        }

        .empty strong {
            display: block;

            color:
                #343a40;

            margin-bottom: 8px;
        }

        .empty p {
            margin: 0;

            font-size: 14px;

            line-height: 1.6;
        }


        /*
        |--------------------------------------------------------------------------
        | Alert
        |--------------------------------------------------------------------------
        */

        .alert {
            padding:
                15px 18px;

            border-radius:
                10px;

            margin-bottom:
                20px;

            background:
                #fff5f5;

            border:
                1px solid
                #ffc9c9;

            color:
                #c92a2a;

            font-size:
                14px;
        }


        /*
        |--------------------------------------------------------------------------
        | Mobile
        |--------------------------------------------------------------------------
        */

        @media (
            max-width: 700px
        ) {

            .header {
                padding:
                    23px 16px 45px;
            }

            .header-top {
                flex-direction:
                    column;

                gap: 20px;
            }

            .header h1 {
                font-size:
                    23px;
            }

            .header p {
                font-size:
                    14px;
            }

            .header-actions {
                width: 100%;

                display: grid;

                grid-template-columns:
                    1fr 1fr;
            }

            .header-button {
                width: 100%;

                min-height:
                    43px;
            }

            .logout-form {
                width: 100%;
            }

            .header-button-logout {
                width: 100%;

                height: 43px;
            }

            .container {
                padding:
                    0 14px 30px;
            }

            .toolbar {
                padding: 14px;
            }

            .search-form {
                flex-direction:
                    column;
            }

            .btn-search,
            .btn-reset {
                width: 100%;

                justify-content:
                    center;
            }

            .video-grid {
                grid-template-columns:
                    1fr;
            }

            .summary-title {
                font-size:
                    16px;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Small Mobile
        |--------------------------------------------------------------------------
        */

        @media (
            max-width: 430px
        ) {

            .header-actions {
                grid-template-columns:
                    1fr;
            }

            .summary {
                align-items:
                    flex-start;

                flex-direction:
                    column;

                gap: 7px;
            }

        }

    </style>

</head>


<body>


<div class="page">


    {{-- ============================================================= --}}
    {{-- HEADER                                                        --}}
    {{-- ============================================================= --}}

    <header class="header">

        <div class="header-inner">


            <div class="header-top">


                {{-- JUDUL --}}

                <div class="header-title">

                    <h1>
                        🎓 Video Edukasi Pasien
                    </h1>

                    <p>
                        Informasi dan edukasi kesehatan
                        untuk membantu pasien memahami
                        prosedur medis, persiapan tindakan,
                        serta perawatan.
                    </p>

                </div>


                {{-- MENU HEADER --}}

                <div class="header-actions">


                    {{-- MENU UTAMA --}}

                    <a
                        href="{{ route('layanan.menu') }}"
                        class="
                            header-button
                            header-button-menu
                        "
                    >
                        <span>
                            ←
                        </span>

                        <span>
                            Menu Utama
                        </span>
                    </a>


                    {{-- LOGOUT --}}

                    <form
                        action="{{ route('layanan.logout') }}"
                        method="POST"
                        class="logout-form"
                        onsubmit="
                            return confirm(
                                'Apakah Anda yakin ingin keluar?'
                            );
                        "
                    >

                        @csrf

                        <button
                            type="submit"
                            class="
                                header-button
                                header-button-logout
                            "
                        >

                            <span>
                                ⏻
                            </span>

                            <span>
                                Keluar
                            </span>

                        </button>

                    </form>


                </div>


            </div>


        </div>

    </header>



    {{-- ============================================================= --}}
    {{-- CONTENT                                                       --}}
    {{-- ============================================================= --}}

    <main class="container">


        {{-- ========================================================= --}}
        {{-- ERROR                                                     --}}
        {{-- ========================================================= --}}

        @if(isset($error))

            <div class="alert">

                {{ $error }}

            </div>

        @endif



        {{-- ========================================================= --}}
        {{-- SEARCH                                                    --}}
        {{-- ========================================================= --}}

        <div class="toolbar">

            <form
                action="{{
                    route(
                        'video-edukasi.index'
                    )
                }}"
                method="GET"
                class="search-form"
            >


                <input
                    type="text"
                    name="namaeduboard"
                    class="search-input"
                    placeholder="
                        Cari video edukasi...
                    "
                    value="{{
                        $namaEduBoard
                        ?? ''
                    }}"
                    autocomplete="off"
                >


                <button
                    type="submit"
                    class="btn-search"
                >
                    🔍 Cari
                </button>


                @if(
                    !empty(
                        $namaEduBoard
                    )
                )

                    <a
                        href="{{
                            route(
                                'video-edukasi.index'
                            )
                        }}"
                        class="btn-reset"
                    >
                        Reset
                    </a>

                @endif


            </form>

        </div>



        {{-- ========================================================= --}}
        {{-- SUMMARY                                                   --}}
        {{-- ========================================================= --}}

        <div class="summary">

            <div class="summary-title">

                Edukasi Kesehatan

            </div>


            <div class="total">

                {{
                    $total
                    ?? 0
                }}
                video

            </div>

        </div>



        {{-- ========================================================= --}}
        {{-- VIDEO LIST                                                --}}
        {{-- ========================================================= --}}

        @if(
            isset($videos)
            &&
            $videos->count() > 0
        )


            <div class="video-grid">


                @foreach(
                    $videos
                    as
                    $video
                )


                    <article class="video-card">


                        {{-- ========================================= --}}
                        {{-- THUMBNAIL                                 --}}
                        {{-- ========================================= --}}

                        <div class="thumbnail">


                            @if(
                                !empty(
                                    $video[
                                        'thumbnail'
                                    ]
                                )
                            )


                                <img
                                    src="{{
                                        $video[
                                            'thumbnail'
                                        ]
                                    }}"
                                    alt="{{
                                        $video[
                                            'namaeduboard'
                                        ]
                                        ??
                                        'Video edukasi'
                                    }}"
                                    loading="lazy"
                                >


                            @else


                                <div
                                    class="
                                        thumbnail-empty
                                    "
                                >
                                    🎬
                                </div>


                            @endif



                            @if(
                                !empty(
                                    $video[
                                        'link'
                                    ]
                                )
                            )

                                <div
                                    class="
                                        play-button
                                    "
                                >
                                    ▶
                                </div>

                            @endif


                        </div>



                        {{-- ========================================= --}}
                        {{-- CONTENT                                   --}}
                        {{-- ========================================= --}}

                        <div class="video-content">


                            <span class="badge">

                                Edukasi Pasien

                            </span>



                            <h2 class="video-title">

                                {{
                                    $video[
                                        'namaeduboard'
                                    ]
                                    ??
                                    'Video Edukasi'
                                }}

                            </h2>



                            @if(
                                !empty(
                                    $video[
                                        'deskripsi'
                                    ]
                                )
                            )


                                <p
                                    class="
                                        video-description
                                    "
                                >

                                    {{
                                        $video[
                                            'deskripsi'
                                        ]
                                    }}

                                </p>


                            @else


                                <p
                                    class="
                                        video-description
                                    "
                                >

                                    Tonton video edukasi
                                    untuk mendapatkan
                                    informasi kesehatan.

                                </p>


                            @endif



                            {{-- ===================================== --}}
                            {{-- LINK VIDEO                            --}}
                            {{-- ===================================== --}}

                            @if(
                                !empty(
                                    $video[
                                        'link'
                                    ]
                                )
                            )


                                <a
                                    href="{{
                                        $video[
                                            'link'
                                        ]
                                    }}"
                                    target="_blank"
                                    rel="
                                        noopener
                                        noreferrer
                                    "
                                    class="
                                        watch-button
                                    "
                                >

                                    ▶ Tonton Video

                                </a>


                            @endif


                        </div>


                    </article>


                @endforeach


            </div>


        @else


            {{-- ===================================================== --}}
            {{-- EMPTY STATE                                           --}}
            {{-- ===================================================== --}}

            <div class="empty">


                <div class="empty-icon">

                    🎬

                </div>


                <strong>

                    Video edukasi belum tersedia

                </strong>


                <p>

                    Belum ada video edukasi
                    yang dapat ditampilkan.

                </p>


            </div>


        @endif


    </main>


</div>


</body>

</html>