@extends('layouts.app', ['title' => 'Ulasan Pelayanan | NADI'])

@section('content')
@php
    $total = (int) ($stats->total ?? 0);
    $average = (float) ($stats->average_rating ?? 0);
    $patient = session('pasien', []);
    $hasPatientSession = is_array($patient) && (
        data_get($patient, 'medical_record') ||
        data_get($patient, 'rm') ||
        data_get($patient, 'nocm') ||
        data_get($patient, 'no_rm')
    );
@endphp

<div class="nadi-review-page">
    <div class="nadi-review-head">
        <a class="nadi-review-back" href="{{ url('/') }}">&#8592; Kembali ke NADI</a>
        <span class="nadi-review-eyebrow">SUARA PASIEN</span>
        <h1>Ulasan Pelayanan</h1>
        <p>Bantu kami meningkatkan kualitas pelayanan RSUD Bali Mandara.</p>
    </div>

    @if(session('success'))
        <div class="nadi-review-alert success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="nadi-review-alert error">{{ session('error') }}</div>
    @endif

    <section class="nadi-review-summary">
        <div class="nadi-review-score">
            <strong>{{ $total ? number_format($average, 1, ',', '.') : '—' }}</strong>
            <div class="nadi-stars" aria-label="Rata-rata {{ number_format($average, 1) }} dari 5">
                @for($i = 1; $i <= 5; $i++)
                    <span class="{{ $i <= round($average) ? 'active' : '' }}">★</span>
                @endfor
            </div>
            <small>{{ number_format($total) }} ulasan dipublikasikan</small>
        </div>
        <div class="nadi-review-bars">
            @for($star = 5; $star >= 1; $star--)
                @php
                    $count = (int) ($distribution[$star] ?? 0);
                    $percent = $total ? round(($count / $total) * 100) : 0;
                @endphp
                <div class="nadi-review-bar-row">
                    <span>{{ $star }} ★</span>
                    <div class="nadi-review-bar"><i style="width: {{ $percent }}%"></i></div>
                    <small>{{ $count }}</small>
                </div>
            @endfor
        </div>
    </section>

    <section class="nadi-review-form-wrap">
        <h2>Bagikan pengalaman Anda</h2>
        <p class="nadi-review-muted">Jangan tuliskan nomor rekam medis, nomor kartu, atau informasi medis pribadi di ulasan.</p>

        @if($hasPatientSession)
            @if($myReview && $myReview->status === 'pending')
                <div class="nadi-review-note">Ulasan terakhir Anda sedang menunggu moderasi. Mengirim kembali akan mengganti ulasan tersebut.</div>
            @elseif($myReview && $myReview->status === 'published')
                <div class="nadi-review-note">Anda sudah pernah mengirim ulasan. Jika mengirim lagi, ulasan sebelumnya akan diganti dan masuk moderasi kembali.</div>
            @endif

            <form method="POST" action="{{ route('nadi.reviews.store') }}" class="nadi-review-form">
                @csrf
                <label class="nadi-review-label">Penilaian Anda</label>
                <div class="nadi-review-rating" role="radiogroup" aria-label="Pilih rating">
                    @for($i = 5; $i >= 1; $i--)
                        <input type="radio" id="nadi-star-{{ $i }}" name="rating" value="{{ $i }}" required {{ (string) old('rating', $myReview->rating ?? '') === (string) $i ? 'checked' : '' }}>
                        <label for="nadi-star-{{ $i }}" title="{{ $i }} dari 5 bintang">★</label>
                    @endfor
                </div>
                @error('rating')<small class="nadi-review-error">{{ $message }}</small>@enderror

                <label class="nadi-review-label" for="service_type">Layanan yang dinilai</label>
                <select id="service_type" name="service_type" class="nadi-review-control" required>
                    <option value="rumah_sakit" {{ old('service_type', $myReview->service_type ?? 'rumah_sakit') === 'rumah_sakit' ? 'selected' : '' }}>RSUD Bali Mandara secara keseluruhan</option>
                    <option value="poli" {{ old('service_type', $myReview->service_type ?? '') === 'poli' ? 'selected' : '' }}>Poli / unit pelayanan</option>
                    <option value="layanan" {{ old('service_type', $myReview->service_type ?? '') === 'layanan' ? 'selected' : '' }}>Layanan lainnya</option>
                </select>

                <label class="nadi-review-label" for="service_name">Nama poli / layanan <span>(jika dipilih)</span></label>
                <input id="service_name" name="service_name" class="nadi-review-control" maxlength="120" value="{{ old('service_name', in_array($myReview->service_type ?? '', ['poli','layanan']) ? $myReview->service_name : '') }}" placeholder="Contoh: Poli Penyakit Dalam">
                @error('service_name')<small class="nadi-review-error">{{ $message }}</small>@enderror

                <label class="nadi-review-label" for="review">Ceritakan pengalaman Anda</label>
                <textarea id="review" name="review" class="nadi-review-control" rows="4" minlength="8" maxlength="2000" required placeholder="Apa yang sudah baik dan apa yang perlu ditingkatkan?">{{ old('review', $myReview->review ?? '') }}</textarea>
                @error('review')<small class="nadi-review-error">{{ $message }}</small>@enderror

                <button type="submit" class="nadi-review-submit">Kirim ulasan</button>
            </form>
        @else
            <div class="nadi-review-login">
                <p>Untuk menjaga kualitas ulasan, silakan masuk ke NADI terlebih dahulu.</p>
                <a href="{{ url('/') }}">Kembali ke halaman NADI</a>
            </div>
        @endif
    </section>

    <section class="nadi-review-list">
        <div class="nadi-review-list-heading">
            <h2>Ulasan pasien</h2>
            <span>{{ $total }} ulasan</span>
        </div>

        @forelse($reviews as $item)
            <article class="nadi-review-item">
                <div class="nadi-review-avatar">P</div>
                <div class="nadi-review-item-body">
                    <div class="nadi-review-item-top">
                        <strong>{{ $item->patient_label ?: 'Pasien terverifikasi' }}</strong>
                        <time>{{ optional($item->published_at)->translatedFormat('d M Y') }}</time>
                    </div>
                    <div class="nadi-review-item-meta">
                        <span class="nadi-stars small">
                            @for($i = 1; $i <= 5; $i++)<span class="{{ $i <= $item->rating ? 'active' : '' }}">★</span>@endfor
                        </span>
                        <span>{{ $item->service_name ?: 'RSUD Bali Mandara' }}</span>
                    </div>
                    <p class="nadi-review-text">{{ $item->review }}</p>
                    @if($item->admin_reply)
                        <div class="nadi-review-reply">
                            <strong>Tanggapan RSUD Bali Mandara</strong>
                            <p>{{ $item->admin_reply }}</p>
                        </div>
                    @endif
                </div>
            </article>
        @empty
            <div class="nadi-review-empty">
                <div>☆</div>
                <strong>Belum ada ulasan</strong>
                <p>Jadilah yang pertama membagikan pengalaman Anda.</p>
            </div>
        @endforelse

        <div class="nadi-review-pagination">{{ $reviews->links() }}</div>
    </section>
</div>

<style>
.nadi-review-page{max-width:920px;margin:0 auto;padding:24px 16px 56px;color:#183153}
.nadi-review-head{margin-bottom:22px}.nadi-review-back{display:inline-block;color:#1477ee;text-decoration:none;font-weight:600;font-size:14px;margin-bottom:24px}
.nadi-review-eyebrow{display:block;color:#16885c;font-weight:800;font-size:11px;letter-spacing:1.7px}
.nadi-review-head h1{font-size:clamp(26px,4vw,36px);margin:7px 0;color:#163a93;letter-spacing:-.8px}
.nadi-review-head p,.nadi-review-muted{color:#66758a;margin:0;line-height:1.6}
.nadi-review-summary{display:grid;grid-template-columns:220px 1fr;gap:28px;padding:24px;border:1px solid #e5ebf3;border-radius:18px;background:#fff;margin-bottom:20px}
.nadi-review-score{display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;border-right:1px solid #e9eef5;padding-right:20px}
.nadi-review-score strong{font-size:48px;line-height:1.15;color:#163a93}.nadi-review-score small{color:#778397;margin-top:8px}
.nadi-stars{display:flex;gap:3px;color:#d9e0e9;font-size:24px;line-height:1.3}.nadi-stars .active{color:#f4b323}.nadi-stars.small{font-size:15px}
.nadi-review-bars{display:flex;flex-direction:column;justify-content:center;gap:8px}.nadi-review-bar-row{display:grid;grid-template-columns:42px 1fr 24px;gap:10px;align-items:center;font-size:13px;color:#65748a}
.nadi-review-bar{height:8px;background:#edf1f6;border-radius:20px;overflow:hidden}.nadi-review-bar i{display:block;height:100%;background:#f4b323;border-radius:20px}.nadi-review-bar-row small{text-align:right}
.nadi-review-form-wrap{padding:24px;border:1px solid #e5ebf3;border-radius:18px;background:#fff;margin-bottom:28px}.nadi-review-form-wrap h2,.nadi-review-list h2{font-size:20px;margin:0 0 8px;color:#163a93}
.nadi-review-muted{font-size:13px;margin-bottom:18px}.nadi-review-label{display:block;font-weight:700;font-size:13px;margin:17px 0 8px}.nadi-review-label span{font-weight:400;color:#7a8799}
.nadi-review-rating{display:flex;flex-direction:row-reverse;justify-content:flex-end;gap:5px}.nadi-review-rating input{position:absolute;opacity:0;width:1px;height:1px}.nadi-review-rating label{font-size:36px;color:#d8e0eb;cursor:pointer;line-height:1.2}.nadi-review-rating input:checked~label,.nadi-review-rating label:hover,.nadi-review-rating label:hover~label{color:#f4b323}
.nadi-review-control{display:block;width:100%;box-sizing:border-box;border:1px solid #dbe3ee;border-radius:11px;background:#fff;padding:12px 13px;color:#183153;font:inherit;font-size:14px;outline:none}.nadi-review-control:focus{border-color:#1477ee;box-shadow:0 0 0 3px rgba(20,119,238,.1)}
.nadi-review-submit{margin-top:18px;background:#163a93;color:#fff;border:0;border-radius:11px;padding:13px 20px;font-weight:700;cursor:pointer}.nadi-review-submit:hover{background:#1477ee}
.nadi-review-error{display:block;color:#c53030;margin-top:5px}.nadi-review-alert,.nadi-review-note{padding:13px 15px;border-radius:12px;margin-bottom:16px;font-size:14px}.nadi-review-alert.success{background:#eaf8f0;color:#166534}.nadi-review-alert.error{background:#fff0f0;color:#9b1c1c}.nadi-review-note{background:#eff6ff;color:#1d4f91}
.nadi-review-login{background:#f5f8fc;padding:18px;border-radius:12px}.nadi-review-login p{margin:0 0 10px}.nadi-review-login a{color:#1477ee;font-weight:700}
.nadi-review-list-heading{display:flex;justify-content:space-between;align-items:center;margin-bottom:8px}.nadi-review-list-heading span{font-size:13px;color:#7b8797}
.nadi-review-item{display:flex;gap:14px;padding:20px 0;border-bottom:1px solid #edf0f5}.nadi-review-avatar{width:40px;height:40px;flex:0 0 40px;border-radius:50%;display:flex;align-items:center;justify-content:center;background:#e9f1ff;color:#163a93;font-weight:800}
.nadi-review-item-body{min-width:0;flex:1}.nadi-review-item-top{display:flex;justify-content:space-between;gap:12px}.nadi-review-item-top strong{font-size:14px}.nadi-review-item-top time{font-size:12px;color:#8290a2;white-space:nowrap}.nadi-review-item-meta{display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-top:3px;font-size:12px;color:#718096}
.nadi-review-text{font-size:14px;line-height:1.7;margin:10px 0 0;white-space:pre-line;overflow-wrap:anywhere}.nadi-review-reply{margin-top:13px;padding:12px 14px;border-left:3px solid #18aa61;background:#f3faf6;border-radius:0 10px 10px 0;font-size:13px}.nadi-review-reply p{margin:5px 0 0;line-height:1.6}
.nadi-review-empty{text-align:center;padding:36px 12px;color:#78869a}.nadi-review-empty div{font-size:38px}.nadi-review-empty strong{display:block;color:#183153}.nadi-review-empty p{margin:5px 0}
.nadi-review-pagination{margin-top:22px}.nadi-review-pagination nav{display:flex;justify-content:center}
@media(max-width:600px){.nadi-review-page{padding:18px 13px 40px}.nadi-review-summary{grid-template-columns:1fr;gap:18px;padding:18px}.nadi-review-score{border-right:0;border-bottom:1px solid #e9eef5;padding:0 0 18px}.nadi-review-form-wrap{padding:18px}.nadi-review-item{gap:10px}.nadi-review-item-top{flex-direction:column;gap:2px}.nadi-review-item-top time{order:2}.nadi-review-rating label{font-size:34px}}
</style>

<script>
(function () {
    const type = document.getElementById('service_type');
    const name = document.getElementById('service_name');
    function toggleServiceName() {
        if (!type || !name) return;
        const required = type.value !== 'rumah_sakit';
        name.required = required;
        name.disabled = !required;
        name.style.opacity = required ? '1' : '.55';
        if (!required) name.value = '';
    }
    if (type && name) {
        type.addEventListener('change', toggleServiceName);
        toggleServiceName();
    }
})();
</script>
@endsection
