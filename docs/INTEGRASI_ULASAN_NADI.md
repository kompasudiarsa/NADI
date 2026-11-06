# Integrasi Ulasan NADI Existing

Form NADI yang ditemukan memakai `nadi.reviews.store` dan kolom form `rating`, `focus_category`, `review`, `service_type`, `service_name`. **Tidak** dibuat tabel baru ulasan rumah sakit. Table existing perlu dipetakan pada env (sesuaikan nama tabel dan nama kolom database sebenarnya).

```dotenv
NADI_REVIEW_TABLE=ISI_TABEL_ULASAN_EXISTING
NADI_REVIEW_ID=id
NADI_REVIEW_PATIENT_NAME=nama_pasien
NADI_REVIEW_CONTENT=review
NADI_REVIEW_RATING=rating
NADI_REVIEW_FOCUS_CATEGORY=focus_category
NADI_REVIEW_CREATED_AT=created_at
```

## Menampilkan balasan CS pada halaman pasien
Sesudah controller ulasan NADI memuat `$reviews`, tambahkan lookup balasan **tanpa** mengubah query/filter/izin hapus ulasan yang sudah ada:

```php
$ids = collect(method_exists($reviews, 'items') ? $reviews->items() : $reviews)
    ->pluck('id')->map(fn($id) => (string)$id)->all();
$csReplies = \Illuminate\Support\Facades\DB::table('nadi_review_replies')
    ->whereIn('review_id',$ids)->get()->keyBy('review_id');
// Kirim $csReplies ke Blade bersama $reviews.
```

Di dalam perulangan Blade ulasan, tambahkan:

```blade
@php($csReply = $csReplies->get((string)$review->id))
@if($csReply)
  <div class="nadi-official-reply" style="padding:12px;border-left:3px solid #1e6091;background:#f3f7fb;margin-top:12px">
    <strong>Balasan Resmi Customer Service</strong>
    <p>{{ $csReply->reply }}</p>
    <small>{{ \Carbon\Carbon::parse($csReply->updated_at)->format('d/m/Y H:i') }}</small>
  </div>
@endif
```

Nama variabel `$reviews`, `$review` dan kolom `id` perlu disesuaikan controller asli. Blade tidak boleh menyembunyikan ulasan tanpa review; ulasan existing tetap langsung tampil dan hanya penulis yang boleh menghapus miliknya menurut otorisasi yang sudah diterapkan di NADI.
