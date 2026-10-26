@extends('layouts.app', ['title' => 'Ulasan Pelayanan | NADI'])



@section('content')

@php

    $total = (int) ($stats->total ?? 0);

    $average = (float) ($stats->average_rating ?? 0);

    // Gunakan kategori yang sama dengan pilihan Fokus Kategori Ulasan pada form.
    $selectedFocus = request()->query('focus_category', 'semua');
    $selectedFocus = is_string($selectedFocus) && array_key_exists($selectedFocus, $focusCategories)
        ? $selectedFocus
        : 'semua';
    $options = ['semua' => 'Semua ulasan'] + $focusCategories;


    $patient = session('pasien', []);
    $patientName = data_get($patient, 'name', data_get($patient, 'namapasien', 'Pasien'));
    $mainMenuUrl = Route::has('layanan.menu') ? route('layanan.menu') : url('/layanan/menu');
    $logoutRouteName = Route::has('layanan.logout') ? 'layanan.logout' : (Route::has('logout') ? 'logout' : null);
    $routeOrUrl = function ($routeName, $fallback) {
        return Route::has($routeName) ? route($routeName) : url($fallback);
    };

@endphp

<section class="nadi-page">
  <div class="nadi-phone">
    <main class="nadi-main">
            <header class="nadi-header">
                <div class="nadi-welcome">
                    <div class="nadi-user-icon" aria-hidden="true">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <circle cx="12" cy="8" r="4" fill="currentColor"/>
                            <path d="M4 21C4.7 16.4 7.3 14 12 14C16.7 14 19.3 16.4 20 21" fill="currentColor"/>
                        </svg>
                    </div>

                    <div class="nadi-greeting">
                        <div class="nadi-greeting-small">
                            Selamat Datang,
                        </div>

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
                        <svg width="21" height="21" viewBox="0 0 24 24" fill="none">
                            <path
                                d="M15 18L9 12L15 6"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
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
                                <svg width="21" height="21" viewBox="0 0 24 24" fill="none">
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
                            </button>
                        </form>
                    @endif
                </div>
            </header>

            <section class="medicine-intro">
                <div class="medicine-intro-head">
                    <div class="medicine-intro-icon" aria-hidden="true">★</div>
                    <div>
                        <h1 class="medicine-intro-title">Ulasan Pelayanan</h1>
                        <p class="medicine-intro-text">Bagikan pengalaman Anda untuk meningkatkan kualitas pelayanan RSUD Bali Mandara.</p>
                    </div>
                </div>
            </section>

<div class="review-page">

    @if(session('success')) <div class="review-notice success" role="status">{{ session('success') }}</div> @endif

    @if(session('error')) <div class="review-notice error" role="alert">{{ session('error') }}</div> @endif

    @if($errors->any())

        <div class="review-notice error" role="alert">Periksa kembali isian Anda: {{ $errors->first() }}</div>

    @endif



    <section class="review-overview">

        <div class="review-score">

            <div class="review-average">{{ $total ? number_format($average, 1, ',', '.') : '—' }} <span>/ 5</span></div>

            <div class="review-starline" aria-label="Rata-rata {{ number_format($average,1) }} dari 5 bintang">

                @for($i = 1; $i <= 5; $i++) <span class="{{ $i <= round($average) ? 'on' : '' }}">★</span> @endfor

            </div>

            <p>{{ number_format($total, 0, ',', '.') }} ulasan pasien</p>

        </div>

        <div class="review-bars">

            @for($star = 5; $star >= 1; $star--)

                @php $count = (int) ($distribution[$star] ?? 0); $percent = $total ? round(100 * $count / $total) : 0; @endphp

                <div class="review-bar-row"><span>{{ $star }} ★</span><div class="review-track"><div style="width:{{ $percent }}%"></div></div><span>{{ $count }}</span></div>

            @endfor

        </div>

    </section>



    <section class="review-card" id="tulis-ulasan">

        <div class="review-heading"><div><h2>Tulis Ulasan Baru</h2><p>Anda dapat menulis lebih dari satu ulasan untuk layanan yang berbeda.</p></div><span class="review-chip">Langsung tampil</span></div>

        @if($patientKey)

            <form method="POST" action="{{ route('nadi.reviews.store') }}">

                @csrf

                <label class="review-label">Penilaian layanan <span>*</span></label>

                <div class="review-rating" role="radiogroup" aria-label="Rating dari 1 sampai 5">

                    @for($i = 5; $i >= 1; $i--)

                        <input type="radio" name="rating" id="rate-{{ $i }}" value="{{ $i }}" {{ (string)old('rating') === (string)$i ? 'checked' : '' }} required>

                        <label for="rate-{{ $i }}" title="{{ $i }} bintang">★</label>

                    @endfor

                </div>

                <input type="hidden" name="service_type" value="rumah_sakit">
                <input type="hidden" name="service_name" value="">

                <label class="review-label" for="focus_category">Fokus kategori ulasan <span>*</span></label>

                <select id="focus_category" name="focus_category" class="review-input" required>

                    <option value="">-- Pilih fokus ulasan --</option>

                    @foreach($focusCategories as $key => $label)

                        <option value="{{ $key }}" {{ old('focus_category') === $key ? 'selected' : '' }}>{{ $label }}</option>

                    @endforeach

                </select>

                @error('focus_category')<small class="review-error">{{ $message }}</small>@enderror

                <p class="review-tip">Pilih topik utama yang paling sesuai dengan pengalaman Anda. Anda tetap dapat mengulas layanan lainnya secara terpisah.</p>

                <label class="review-label" for="review">Pengalaman Anda <span>*</span></label>

                <textarea name="review" id="review" class="review-input" rows="4" minlength="8" maxlength="2000" required placeholder="Ceritakan pengalaman pelayanan Anda...">{{ old('review') }}</textarea>

                <p class="review-tip">Jangan cantumkan nomor RM, nomor kartu, alamat, atau informasi medis pribadi. Ulasan akan langsung terlihat oleh pengunjung.</p>

                <div class="review-actions"><button type="submit" class="review-primary">★ Kirim Ulasan</button></div>

            </form>

        @else

            <div class="review-login">Silakan masuk sebagai pasien NADI terlebih dahulu untuk menulis ulasan. <a href="{{ url('/') }}">Masuk ke NADI →</a></div>

        @endif

    </section>



    @if($patientKey && $myReviews->isNotEmpty())

        <section class="review-card">

            <div class="review-heading"><div><h2>Ulasan Saya</h2><p>Kelola ulasan yang pernah Anda kirim.</p></div><span class="review-count">{{ $myReviews->count() }} ulasan</span></div>

            @foreach($myReviews as $mine)

                <div class="review-entry review-entry-mine">

                    <div class="review-entry-main">
                        <div class="review-entry-head">
                            <div class="review-entry-title-wrap">
                                <strong class="review-entry-title">{{ $mine->service_name ?: 'RSUD Bali Mandara' }}</strong>
                                <div class="review-entry-meta-row">
                                    <span class="review-starline small">@for($i=1; $i<=5; $i++)<span class="{{ $i <= $mine->rating ? 'on' : '' }}">★</span>@endfor</span>
                                    <span class="review-focus">{{ $focusCategories[$mine->focus_category] ?? 'Lainnya' }}</span>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('nadi.reviews.destroy', $mine->id) }}" onsubmit="return confirm('Hapus ulasan ini secara permanen?')">
                                @csrf @method('DELETE')
                                <button class="review-delete" type="submit" aria-label="Hapus ulasan">Hapus</button>
                            </form>
                        </div>

                        <p class="review-entry-text">{{ $mine->review }}</p>

                        <small class="review-entry-date">{{ optional($mine->created_at)->translatedFormat('d M Y, H:i') }}</small>
                    </div>

                </div>

            @endforeach

        </section>

    @endif



    <section class="review-card">

        <div class="review-heading"><div><h2>Ulasan dari Pasien</h2><p>Pengalaman pasien yang telah diterbitkan.</p></div><span class="review-count">{{ $total }} total</span></div>

        <nav class="review-filters" aria-label="Filter fokus kategori ulasan">

            @foreach($options as $key => $label)

                <a href="{{ route('nadi.reviews.index', $key === 'semua' ? [] : ['focus_category' => $key]) }}" class="{{ $selectedFocus === $key ? 'active' : '' }}">{{ $label }}</a>

            @endforeach

        </nav>

        @forelse($reviews as $item)

            <article class="review-entry review-entry-public">

                <div class="review-avatar">P</div>

                <div class="review-public-body">

                    <div class="review-public-top">
                        <strong>{{ $item->patient_label ?: 'Pasien terverifikasi' }}</strong>
                        <time>{{ optional($item->published_at ?: $item->created_at)->translatedFormat('d M Y') }}</time>
                    </div>

                    <div class="review-public-meta">
                        <span class="review-starline small">@for($i=1; $i<=5; $i++)<span class="{{ $i <= $item->rating ? 'on' : '' }}">★</span>@endfor</span>
                        @if(!empty($item->service_name))<span class="review-service">{{ $item->service_name }}</span>@endif
                        <span class="review-focus">{{ $focusCategories[$item->focus_category] ?? 'Lainnya' }}</span>
                    </div>

                    <p class="review-entry-text review-public-text">{{ $item->review }}</p>

                    @if($item->admin_reply)<div class="review-reply"><strong>Tanggapan Rumah Sakit</strong><p>{{ $item->admin_reply }}</p></div>@endif

                    @if($patientKey && hash_equals((string) $item->patient_key, (string) $patientKey))

                        <form method="POST" action="{{ route('nadi.reviews.destroy', $item->id) }}" onsubmit="return confirm('Yakin ingin menghapus ulasan Anda?')">

                            @csrf @method('DELETE') <button type="submit" class="review-delete inline">Hapus ulasan saya</button>

                        </form>

                    @endif

                </div>

            </article>

        @empty

            <div class="review-empty">☆<strong>Belum ada ulasan</strong><p>Jadilah yang pertama membagikan pengalaman pelayanan Anda.</p></div>

        @endforelse

        @if($reviews->hasPages())<div class="review-pagination">{{ $reviews->links() }}</div>@endif

    </section>

</div>
    </main>
  </div>
        <nav class="nadi-bottom-nav" aria-label="Navigasi utama">

            <a
                href="{{ $mainMenuUrl }}"
                class="nadi-bottom-link"
            >
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                    <path
                        d="M3 11L12 3L21 11V21H14V15H10V21H3V11Z"
                        fill="currentColor"
                    />
                </svg>
                <span>Beranda</span>
            </a>

            <a
                href="{{ $mainMenuUrl }}#menu-layanan"
                class="nadi-bottom-link is-active"
            >
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                    <rect
                        x="3"
                        y="3"
                        width="7"
                        height="7"
                        rx="2"
                        stroke="currentColor"
                        stroke-width="2"
                    />
                    <rect
                        x="14"
                        y="3"
                        width="7"
                        height="7"
                        rx="2"
                        stroke="currentColor"
                        stroke-width="2"
                    />
                    <rect
                        x="3"
                        y="14"
                        width="7"
                        height="7"
                        rx="2"
                        stroke="currentColor"
                        stroke-width="2"
                    />
                    <rect
                        x="14"
                        y="14"
                        width="7"
                        height="7"
                        rx="2"
                        stroke="currentColor"
                        stroke-width="2"
                    />
                </svg>
                <span>Layanan</span>
            </a>

            <a
                href="{{ $routeOrUrl('riwayat.index', '/riwayat') }}"
                class="nadi-bottom-link"
            >
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                    <circle
                        cx="12"
                        cy="12"
                        r="9"
                        stroke="currentColor"
                        stroke-width="2"
                    />
                    <path
                        d="M12 7V12L15.5 14"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                    />
                </svg>
                <span>Riwayat</span>
            </a>

            <a
                href="{{ $routeOrUrl('bantuan.index', '/bantuan') }}"
                class="nadi-bottom-link"
            >
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                    <path
                        d="M4 13V11C4 6.6 7.6 3 12 3C16.4 3 20 6.6 20 11V13"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                    />
                    <rect
                        x="2.5"
                        y="11.5"
                        width="4"
                        height="7"
                        rx="2"
                        fill="currentColor"
                    />
                    <rect
                        x="17.5"
                        y="11.5"
                        width="4"
                        height="7"
                        rx="2"
                        fill="currentColor"
                    />
                </svg>
                <span>Bantuan</span>
            </a>

            <a
                href="{{ $routeOrUrl('profile.index', '/profil') }}"
                class="nadi-bottom-link"
            >
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                    <circle
                        cx="12"
                        cy="8"
                        r="4"
                        stroke="currentColor"
                        stroke-width="2"
                    />
                    <path
                        d="M5 21C5.8 16.8 8.1 14.5 12 14.5C15.9 14.5 18.2 16.8 19 21"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                    />
                </svg>
                <span>Profil</span>
            </a>

        </nav>
</section>

<style>

.review-page{max-width:1050px;margin:0 auto;padding:22px 16px 60px;color:#263951;font-family:inherit}

.review-hero{padding:26px 28px;background:linear-gradient(115deg,#1550aa,#0b86a5);color:white;border-radius:22px;margin-bottom:20px;box-shadow:0 12px 28px rgba(17,79,149,.14)}

.review-back{display:inline-flex;text-decoration:none;color:#e3f7ff;font-size:13px;margin-bottom:20px;font-weight:600}

.review-hero-main{display:flex;gap:18px;align-items:center}.review-icon{width:65px;height:65px;flex:0 0 65px;background:#ffffff24;border-radius:18px;display:grid;place-items:center;font-size:35px;color:#ffd05a}

.review-eyebrow{font-size:11px;font-weight:800;letter-spacing:1.2px;color:#b8f2ff}.review-hero h1{font-size:clamp(24px,3.5vw,32px);margin:4px 0 7px;font-weight:800}.review-hero p{font-size:14px;margin:0;color:#e4f3ff;line-height:1.6}

.review-overview{display:grid;grid-template-columns:270px 1fr;gap:18px;margin-bottom:20px}.review-score,.review-bars,.review-card{border:1px solid #e4eaf2;background:white;border-radius:18px;box-shadow:0 4px 20px rgba(37,61,91,.035)}

.review-score{text-align:center;padding:24px}.review-average{font-size:46px;line-height:1.15;color:#19458f;font-weight:800}.review-average span{font-size:16px;font-weight:500;color:#9aa7b8}.review-score p{font-size:13px;color:#8391a3;margin:6px 0 0}

.review-starline{display:inline-flex;gap:2px;font-size:23px;color:#d7dfeb}.review-starline .on{color:#f7b731}.review-starline.small{font-size:16px;vertical-align:middle}.review-bars{padding:24px 30px;display:flex;flex-direction:column;justify-content:center;gap:10px}.review-bar-row{display:grid;grid-template-columns:38px 1fr 30px;gap:13px;align-items:center;font-size:13px;color:#6f7f95}.review-track{background:#f0f4f8;border-radius:30px;height:9px;overflow:hidden}.review-track div{background:#f7b731;height:100%;border-radius:30px}

.review-card{padding:26px;margin-bottom:20px}.review-heading{display:flex;justify-content:space-between;align-items:flex-start;gap:15px;margin-bottom:20px}.review-heading h2{font-size:19px;color:#19458f;margin:0 0 6px}.review-heading p{font-size:13px;color:#8290a3;margin:0;line-height:1.5}.review-chip,.review-count{font-size:12px;padding:7px 12px;background:#e8f8f3;color:#087955;border-radius:999px;white-space:nowrap;font-weight:700}.review-count{background:#eef4ff;color:#235c9c}

.review-label{display:block;font-size:13px;font-weight:700;margin:15px 0 7px}.review-label span{color:#cc4d4d}.review-rating{display:flex;flex-direction:row-reverse;justify-content:flex-end;gap:8px}.review-rating input{position:absolute;opacity:0;width:1px;height:1px}.review-rating label{font-size:38px;line-height:1.3;color:#d9e1ec;cursor:pointer}.review-rating input:checked~label,.review-rating label:hover,.review-rating label:hover~label{color:#f7b731}.review-rating input:focus-visible+label{outline:2px solid #2477ce}

.review-fields{display:grid;grid-template-columns:1fr 1fr;gap:16px}.review-input{width:100%;border:1px solid #dce5ef;border-radius:11px;padding:12px 13px;box-sizing:border-box;background:white;color:#263951;font:inherit;font-size:14px;outline:none}.review-input:focus{border-color:#2378c5;box-shadow:0 0 0 3px #dfeeff}.review-input:disabled{background:#f5f7fa;color:#91a0b2}.review-tip{font-size:12px;color:#8290a3;margin:10px 0}.review-actions{margin-top:18px}.review-primary{background:#1557a5;color:white;border:0;border-radius:11px;padding:13px 20px;font:inherit;font-size:14px;font-weight:700;cursor:pointer}.review-primary:hover{background:#0b7eab}

.review-entry{display:flex;gap:14px;align-items:flex-start;padding:18px;border:1px solid #e7eef6;border-radius:18px;background:linear-gradient(180deg,#ffffff 0%,#fbfdff 100%);box-shadow:0 8px 24px rgba(20,62,109,.05)}.review-entry + .review-entry{margin-top:14px}.review-entry-mine{padding:20px}.review-entry-main,.review-public-body{min-width:0;flex:1}.review-entry-head{display:flex;justify-content:space-between;align-items:flex-start;gap:12px}.review-entry-title{display:block;font-size:15px;color:#184996;font-weight:800;line-height:1.35}.review-entry-title-wrap{min-width:0}.review-entry-meta-row{display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-top:6px}.review-entry-text,.review-public-text{white-space:pre-line;overflow-wrap:anywhere;line-height:1.75;margin:12px 0 0;font-size:14px;color:#2e4360}.review-entry-date{display:inline-block;margin-top:12px;color:#8a98ab;font-size:12px}.review-delete{background:#fff7f7;color:#d14d4d;border:1px solid #f4c9c9;border-radius:12px;padding:10px 14px;cursor:pointer;font-weight:800;font-size:12px;line-height:1;white-space:nowrap}.review-delete.inline{margin-top:14px}.review-delete:hover{background:#ffecec}

.review-focus{display:inline-flex;align-items:center;font-size:12px;background:#e9f8ee;color:#147547;border-radius:999px;padding:5px 10px;font-weight:800}.review-error{display:block;color:#b42318;font-size:12px;margin-top:5px}

.review-avatar{width:48px;height:48px;flex:0 0 48px;border-radius:18px;display:grid;place-items:center;background:linear-gradient(145deg,#eaf3ff,#d7e8ff);color:#17549c;font-size:24px;font-weight:900;box-shadow:inset 0 0 0 1px rgba(23,84,156,.06)}.review-public-top{display:flex;gap:10px;justify-content:space-between;align-items:flex-start}.review-public-top strong{font-size:15px;color:#184996}.review-public-top time{font-size:12px;color:#93a0b1;white-space:nowrap}.review-public-meta{display:flex;align-items:center;gap:10px;margin:8px 0 0;flex-wrap:wrap}.review-service{font-size:12px;background:#edf7ff;color:#316d9e;border-radius:999px;padding:5px 10px;font-weight:700}.review-reply{margin-top:14px;background:#f1faf6;border-left:3px solid #15a270;padding:12px 14px;border-radius:0 12px 12px 0;font-size:13px}.review-reply p{margin:6px 0 0}.review-filters{display:flex;gap:8px;overflow:auto;padding-bottom:15px}.review-filters a{white-space:nowrap;text-decoration:none;padding:10px 16px;border:1px solid #e3e9f1;color:#62768f;border-radius:999px;font-size:12px;font-weight:800;background:#fff}.review-filters a.active{background:#1557a5;color:white;border-color:#1557a5;box-shadow:0 8px 18px rgba(21,87,165,.16)}.review-empty{text-align:center;font-size:37px;color:#b1bdcb;padding:35px 10px}.review-empty strong{display:block;font-size:15px;color:#345}.review-empty p{font-size:13px}.review-pagination{margin-top:20px}.review-login{background:#f4f8fd;border-radius:12px;padding:18px;color:#567}.review-login a{display:block;margin-top:8px;color:#1557a5;font-weight:700;text-decoration:none}.review-notice{padding:13px 16px;margin-bottom:18px;border-radius:11px;font-size:14px}.review-notice.success{background:#e6f8ed;color:#147546}.review-notice.error{background:#fff0f0;color:#a13535}

@media(max-width:650px){.review-page{padding:14px 11px 45px}.review-hero{padding:22px 18px;border-radius:17px}.review-icon{width:50px;height:50px;flex-basis:50px;font-size:28px}.review-overview{grid-template-columns:1fr;gap:12px}.review-bars{padding:20px}.review-card{padding:18px}.review-fields{grid-template-columns:1fr;gap:0}.review-heading{flex-wrap:wrap}.review-public-top,.review-entry-head{flex-wrap:wrap}.review-entry{padding:16px}.review-entry-mine{padding:16px}.review-delete{padding:9px 12px}.review-avatar{width:42px;height:42px;flex-basis:42px;border-radius:14px;font-size:20px}}


/* Desain diselaraskan dengan modal ulasan NADI */
body { background: #f7fbff; }
.review-page, .review-page * {box-sizing:border-box}
.review-page {max-width:580px;margin:0 auto;padding:20px 13px 72px;color:#163a93}
.review-hero {position:relative;background:#fff;color:#163a93;border:1px solid #e1eaf3;border-radius:24px;padding:23px 20px 21px;margin-bottom:14px;text-align:center;box-shadow:0 16px 40px rgba(15,45,115,.08)}
.review-back {display:inline-flex;align-items:center;justify-content:center;position:absolute;left:13px;top:12px;padding:7px 11px;background:#edf2f7;color:#475569;border-radius:10px;font-size:11px;text-decoration:none;margin:0}
.review-back:hover {color:#163a93;background:#dfeaff;text-decoration:none}
.review-hero-main {display:flex;flex-direction:column;gap:8px;align-items:center;padding-top:27px}
.review-icon {width:62px;height:62px;flex:0 0 62px;margin:0;background:linear-gradient(130deg,#e7f4ff,#e6ffef);border-radius:20px;display:grid;place-items:center;font-size:31px;color:#f6b80f}
.review-eyebrow {font-size:10px;font-weight:900;letter-spacing:1px;color:#18aa61}
.review-hero h1 {font-size:22px;color:#163a93;font-weight:900;margin:4px 0 6px}
.review-hero p {font-size:13px;color:#64748b;max-width:380px;margin:0 auto;line-height:1.65}
.review-overview {display:grid;grid-template-columns:minmax(0,1fr) minmax(0,1.15fr);gap:10px;margin-bottom:14px}
.review-score,.review-bars,.review-card {border:1px solid #e1eaf3;border-radius:20px;background:#fff;box-shadow:0 8px 26px rgba(15,45,115,.05)}
.review-score {padding:17px 8px}.review-average {font-size:36px;color:#163a93}.review-average span {font-size:12px}
.review-starline {font-size:20px;color:#cbd5e1}.review-starline .on {color:#f6b80f}.review-score p {font-size:11px;color:#64748b}
.review-bars {padding:16px 13px;gap:7px}.review-bar-row {grid-template-columns:31px 1fr 21px;gap:7px;font-size:11px}.review-track div {background:#f6b80f}
.review-card {padding:20px 18px;margin-bottom:14px}
.review-heading {display:flex;align-items:flex-start;gap:8px;margin-bottom:13px}.review-heading h2 {font-size:17px;font-weight:900;color:#163a93}.review-heading p {color:#64748b;font-size:12px}
.review-chip,.review-count {font-size:10px;padding:5px 8px;background:#e6ffef;color:#12814b}
.review-label {font-size:12px;color:#334155;font-weight:800;margin:13px 0 6px}
.review-rating {display:flex;flex-direction:row-reverse;justify-content:center;gap:6px;margin:8px 0 14px}
.review-rating label {font-size:38px;color:#cbd5e1;cursor:pointer;margin:0}
.review-rating input:checked~label,.review-rating label:hover,.review-rating label:hover~label {color:#f6b80f}
.review-fields {display:grid;grid-template-columns:1fr;gap:0}
.review-input {width:100%;border:1px solid #d6e2ef;border-radius:12px;padding:12px;font-size:13px;color:#1e293b;background:#fff}
.review-input:focus {border-color:#1477ee;box-shadow:0 0 0 3px #e4f1ff}
.review-tip {font-size:11px;color:#64748b;line-height:1.55}
.review-actions {margin-top:18px}.review-primary {width:100%;min-height:44px;background:linear-gradient(110deg,#1477ee,#163a93);color:#fff;border:0;border-radius:12px;padding:12px 18px;font-size:13px;font-weight:850;cursor:pointer}.review-primary:hover{filter:brightness(1.07)}
.review-filters a.active {background:#163a93;border-color:#163a93}.review-filters a {font-size:11px}
.review-public-row,.review-my-row {gap:10px;padding:16px 0}.review-avatar {background:linear-gradient(130deg,#e7f4ff,#e6ffef);color:#163a93}
.review-focus {background:#e6ffef;color:#12814b}
@media(max-width:480px){.review-page{padding:12px 10px 55px}.review-hero{padding:20px 14px}.review-overview{grid-template-columns:1fr 1fr}.review-card{padding:18px 14px}.review-heading{flex-wrap:wrap}.review-public-top{flex-wrap:wrap}.review-bar-row{gap:5px}.review-bars{padding:15px 8px}.review-rating label{font-size:35px}}
@media(max-width:340px){.review-overview{grid-template-columns:1fr}.review-rating label{font-size:31px}}

/* Konsistensi layout dengan halaman Aturan Minum Obat */
html,body{margin:0!important;padding:0!important}
#app>nav.navbar,#app>.navbar,body>nav.navbar,nav.navbar.navbar-expand-md,.navbar.navbar-expand-md{display:none!important;height:0!important;min-height:0!important;margin:0!important;padding:0!important;border:0!important;box-shadow:none!important}
#app,#app>main,main.py-4,.py-4{margin-top:0!important;padding-top:0!important}
.content-header{display:none!important}
:root{--nadi-blue:#163a93;--nadi-blue-2:#1477ee;--nadi-green:#18aa61;--nadi-green-soft:#eafaf1;--nadi-line:#dfe8f2;--nadi-muted:#66758e}
.nadi-page,.nadi-page *{box-sizing:border-box}
.nadi-page{min-height:calc(100svh - 72px);padding:22px 12px 34px;background:radial-gradient(circle at 8% 5%,rgba(24,170,97,.10),transparent 28%),radial-gradient(circle at 96% 8%,rgba(20,119,238,.09),transparent 30%),linear-gradient(180deg,#fff,#f7fbff);color:#0f2d73}
.nadi-phone{width:min(100%,520px);margin:0 auto;overflow:hidden;border:1px solid rgba(22,58,147,.10);border-radius:28px;background:rgba(255,255,255,.98);box-shadow:0 26px 70px rgba(15,45,115,.11),0 2px 10px rgba(15,45,115,.04)}
.nadi-main{padding:22px 18px 105px}
.nadi-header{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:16px}
.nadi-welcome{display:flex;min-width:0;align-items:center;gap:11px}
.nadi-user-icon{display:grid;width:48px;height:48px;flex:0 0 48px;place-items:center;border-radius:50%;background:linear-gradient(145deg,#dff8c9,#bfeea7);color:var(--nadi-green)}
.nadi-greeting{min-width:0}.nadi-greeting-small{color:var(--nadi-blue);font-size:12px;font-weight:800;line-height:1.2}
.nadi-greeting-name{max-width:285px;overflow:hidden;color:#0f2d73;font-size:clamp(20px,5.5vw,28px);font-weight:950;line-height:1.05;text-overflow:ellipsis;white-space:nowrap;letter-spacing:-.035em}
.nadi-header-actions{display:flex;align-items:center;gap:7px}
.nadi-icon-button,.nadi-logout-button{display:grid;width:40px;height:40px;flex:0 0 40px;place-items:center;border:0;border-radius:12px;background:#fff;color:var(--nadi-blue-2);cursor:pointer;text-decoration:none}
.nadi-icon-button:hover,.nadi-logout-button:hover{background:#edf6ff;color:var(--nadi-blue);text-decoration:none}.nadi-logout-form{margin:0}
.medicine-intro{position:relative;margin-bottom:13px;padding:15px;overflow:hidden;border:1px solid #d8efdf;border-radius:20px;background:linear-gradient(135deg,rgba(237,252,244,.98),rgba(248,253,255,.98));box-shadow:0 10px 26px rgba(24,170,97,.06)}
.medicine-intro::after{position:absolute;top:-31px;right:-28px;width:92px;height:92px;border:18px solid rgba(24,170,97,.055);border-radius:50%;content:''}
.medicine-intro-head{position:relative;z-index:1;display:flex;align-items:center;gap:12px}
.medicine-intro-icon{display:grid;width:45px;height:45px;flex:0 0 45px;place-items:center;border-radius:13px;background:var(--nadi-green-soft);color:#f5aa0a;font-size:27px}
.medicine-intro-title{margin:0;color:var(--nadi-blue);font-size:18px;font-weight:950;line-height:1.2;letter-spacing:-.025em}
.medicine-intro-text{margin:4px 0 0;color:var(--nadi-muted);font-size:10.5px;font-weight:650;line-height:1.45}
.nadi-bottom-nav{position:fixed!important;z-index:99999!important;right:0!important;bottom:0!important;left:0!important;display:grid!important;width:100%!important;max-width:none!important;grid-template-columns:repeat(5,minmax(0,1fr))!important;transform:none!important;border-top:1px solid #e6edf5;background:rgba(255,255,255,.98)!important;box-shadow:0 -8px 24px rgba(15,45,115,.10);backdrop-filter:blur(12px);padding-bottom:env(safe-area-inset-bottom);visibility:visible!important;opacity:1!important}
.nadi-bottom-link{position:relative;display:flex;min-height:72px;flex-direction:column;align-items:center;justify-content:center;gap:5px;color:#667891;font-size:9px;font-weight:750;text-decoration:none}
.nadi-bottom-link:hover,.nadi-bottom-link.is-active{color:var(--nadi-blue-2);text-decoration:none}.nadi-bottom-link.is-active{font-weight:900}
.nadi-bottom-link.is-active::after{position:absolute;right:28%;bottom:7px;left:28%;height:3px;border-radius:999px;background:var(--nadi-blue-2);content:''}
/* Ulasan disusun dengan pola kartu dan spacing aturan obat */
.review-page{width:100%;max-width:none;margin:0;padding:0!important;color:#163a93}
.review-overview{gap:10px;margin-bottom:13px}.review-score,.review-bars,.review-card{border:1px solid #dfe8f2;border-radius:18px;box-shadow:0 9px 24px rgba(15,45,115,.045)}
.review-card{padding:15px;margin-bottom:13px}.review-heading h2{font-size:15px}.review-heading p{font-size:11px}
.review-entry{padding:13px;border-radius:15px;box-shadow:0 5px 16px rgba(15,45,115,.035)}
.review-entry+.review-entry{margin-top:10px}.review-entry-mine{padding:13px}.review-entry-title{font-size:13px}
.review-entry-text,.review-public-text{font-size:12px;line-height:1.6}.review-public-top strong{font-size:12px}
.review-rating label{font-size:35px}.review-input{font-size:12px}.review-filters a{font-size:10.5px;padding:8px 11px}
.review-public-meta{gap:7px}.review-avatar{width:40px;height:40px;flex:0 0 40px;border-radius:13px;font-size:19px}
.review-score{padding:14px 8px}.review-bars{padding:13px 9px}
@media(max-width:420px){.nadi-page{padding:0;background:#fff}.nadi-phone{width:100%;min-height:calc(100svh - 72px);border:0;border-radius:0;box-shadow:none}.nadi-main{padding:18px 14px calc(100px + env(safe-area-inset-bottom))}.nadi-greeting-name{max-width:205px;font-size:22px}.nadi-user-icon{width:43px;height:43px;flex-basis:43px}.nadi-bottom-link{min-height:68px;font-size:8.5px}}
@media(min-width:768px){.nadi-page{position:relative;left:50%;width:100vw;min-height:100vh;margin-left:-50vw;padding:26px clamp(24px,3vw,48px) 110px}.nadi-phone{width:100%;max-width:none;margin:0;overflow:visible;border:0;border-radius:0;background:transparent;box-shadow:none}.nadi-main{width:100%;padding:0 0 88px}.nadi-header{margin-bottom:20px;padding:0 2px 18px;border-bottom:1px solid rgba(22,58,147,.08)}.nadi-greeting-name{max-width:min(520px,45vw);font-size:30px}.medicine-intro{margin-bottom:16px;padding:20px;border-radius:22px}.medicine-intro-title{font-size:20px}.medicine-intro-text{font-size:12px}.review-page{max-width:1150px}.review-overview{grid-template-columns:270px 1fr}.review-card{padding:22px}.nadi-bottom-link{min-height:66px;flex-direction:row;gap:8px;font-size:12px}.nadi-bottom-link svg{width:20px;height:20px}.nadi-bottom-link.is-active::after{right:38%;left:38%;bottom:5px}}

</style>


@endsection
