@extends('layouts.app', ['title' => 'Ulasan Pelayanan | NADI'])

@section('content')
@php
    $total = (int) ($stats->total ?? 0);
    $average = (float) ($stats->average_rating ?? 0);
    $options = ['semua' => 'Semua ulasan', 'rumah_sakit' => 'Rumah Sakit', 'poli' => 'Poli / Unit', 'layanan' => 'Layanan Lain'];
@endphp
<div class="review-page">
    <header class="review-hero">
        <a href="{{ url('/menu') }}" class="review-back">← Kembali ke Menu</a>
        <div class="review-hero-main">
            <div class="review-icon">★</div>
            <div>
                <span class="review-eyebrow">NADI • SUARA PASIEN</span>
                <h1>Ulasan Pelayanan</h1>
                <p>Bagikan pengalaman Anda untuk meningkatkan kualitas pelayanan RSUD Bali Mandara.</p>
            </div>
        </div>
    </header>

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
                <div class="review-fields">
                    <div><label class="review-label" for="service_type">Bagian yang diulas <span>*</span></label>
                        <select name="service_type" id="service_type" class="review-input" required>
                            <option value="rumah_sakit" {{ old('service_type') === 'rumah_sakit' ? 'selected' : '' }}>RSUD Bali Mandara (Umum)</option>
                            <option value="poli" {{ old('service_type') === 'poli' ? 'selected' : '' }}>Poli / Unit Pelayanan</option>
                            <option value="layanan" {{ old('service_type') === 'layanan' ? 'selected' : '' }}>Layanan Lainnya</option>
                        </select>
                    </div>
                    <div><label class="review-label" for="service_name">Nama poli / bagian layanan</label>
                        <input name="service_name" id="service_name" class="review-input" type="text" maxlength="120" value="{{ old('service_name') }}" placeholder="Contoh: Poli Jantung, Farmasi, Radiologi">
                    </div>
                </div>
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
                <div class="review-my-row">
                    <div class="review-my-content"><strong>{{ $mine->service_name ?: 'RSUD Bali Mandara' }}</strong>
                        <span class="review-starline small">@for($i=1; $i<=5; $i++)<span class="{{ $i <= $mine->rating ? 'on' : '' }}">★</span>@endfor</span>
                        <p>{{ $mine->review }}</p>
                        <small>{{ optional($mine->created_at)->translatedFormat('d M Y, H:i') }}</small>
                    </div>
                    <form method="POST" action="{{ route('nadi.reviews.destroy', $mine->id) }}" onsubmit="return confirm('Hapus ulasan ini secara permanen?')">
                        @csrf @method('DELETE')
                        <button class="review-delete" type="submit" aria-label="Hapus ulasan">Hapus</button>
                    </form>
                </div>
            @endforeach
        </section>
    @endif

    <section class="review-card">
        <div class="review-heading"><div><h2>Ulasan dari Pasien</h2><p>Pengalaman pasien yang telah diterbitkan.</p></div><span class="review-count">{{ $total }} total</span></div>
        <nav class="review-filters" aria-label="Filter jenis ulasan">
            @foreach($options as $key => $label)
                <a href="{{ route('nadi.reviews.index', ['service' => $key]) }}" class="{{ $service === $key ? 'active' : '' }}">{{ $label }}</a>
            @endforeach
        </nav>
        @forelse($reviews as $item)
            <article class="review-public-row">
                <div class="review-avatar">P</div>
                <div class="review-public-body">
                    <div class="review-public-top"><strong>{{ $item->patient_label ?: 'Pasien terverifikasi' }}</strong><time>{{ optional($item->published_at ?: $item->created_at)->translatedFormat('d M Y') }}</time></div>
                    <div class="review-public-meta"><span class="review-starline small">@for($i=1; $i<=5; $i++)<span class="{{ $i <= $item->rating ? 'on' : '' }}">★</span>@endfor</span><span class="review-service">{{ $item->service_name ?: 'RSUD Bali Mandara' }}</span></div>
                    <p class="review-public-text">{{ $item->review }}</p>
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
.review-my-row,.review-public-row{display:flex;gap:14px;padding:17px 0;border-top:1px solid #edf1f6}.review-my-row{justify-content:space-between}.review-my-content{min-width:0}.review-my-content strong{font-size:14px;display:inline-block;margin-right:12px}.review-my-content p,.review-public-text{white-space:pre-line;overflow-wrap:anywhere;line-height:1.7;margin:8px 0;font-size:14px}.review-my-content small{color:#8a98ab}.review-delete{background:#fff6f6;color:#c34242;border:1px solid #ffd9d9;border-radius:9px;padding:8px 11px;cursor:pointer;font-weight:700;font-size:12px}.review-delete.inline{margin-top:10px}.review-delete:hover{background:#ffecec}
.review-avatar{width:42px;height:42px;flex:0 0 42px;border-radius:50%;display:grid;place-items:center;background:#e8f2ff;color:#17549c;font-weight:800}.review-public-body{min-width:0;flex:1}.review-public-top{display:flex;gap:10px;justify-content:space-between}.review-public-top strong{font-size:14px}.review-public-top time{font-size:12px;color:#93a0b1}.review-public-meta{display:flex;align-items:center;gap:12px;margin:4px 0 6px;flex-wrap:wrap}.review-service{font-size:12px;background:#edf7ff;color:#316d9e;border-radius:99px;padding:4px 9px}.review-reply{background:#f1faf6;border-left:3px solid #15a270;padding:12px;border-radius:0 10px 10px 0;font-size:13px}.review-reply p{margin:6px 0 0}.review-filters{display:flex;gap:8px;overflow:auto;padding-bottom:15px}.review-filters a{white-space:nowrap;text-decoration:none;padding:8px 14px;border:1px solid #e3e9f1;color:#62768f;border-radius:50px;font-size:12px;font-weight:700}.review-filters a.active{background:#1557a5;color:white;border-color:#1557a5}.review-empty{text-align:center;font-size:37px;color:#b1bdcb;padding:35px 10px}.review-empty strong{display:block;font-size:15px;color:#345}.review-empty p{font-size:13px}.review-pagination{margin-top:20px}.review-login{background:#f4f8fd;border-radius:12px;padding:18px;color:#567}.review-login a{display:block;margin-top:8px;color:#1557a5;font-weight:700;text-decoration:none}.review-notice{padding:13px 16px;margin-bottom:18px;border-radius:11px;font-size:14px}.review-notice.success{background:#e6f8ed;color:#147546}.review-notice.error{background:#fff0f0;color:#a13535}
@media(max-width:650px){.review-page{padding:14px 11px 45px}.review-hero{padding:22px 18px;border-radius:17px}.review-icon{width:50px;height:50px;flex-basis:50px;font-size:28px}.review-overview{grid-template-columns:1fr;gap:12px}.review-bars{padding:20px}.review-card{padding:18px}.review-fields{grid-template-columns:1fr;gap:0}.review-heading{flex-wrap:wrap}.review-public-top{flex-wrap:wrap}.review-my-row{align-items:flex-start}}
</style>
<script>
(function () {
    const type = document.getElementById('service_type');
    const name = document.getElementById('service_name');
    function updateName() {
        if (!type || !name) return;
        const needed = type.value !== 'rumah_sakit';
        name.required = needed;
        name.disabled = !needed;
        if (!needed) name.value = '';
    }
    if (type && name) { type.addEventListener('change', updateName); updateName(); }
})();
</script>
@endsection
