# Modul Bantuan & CS NADI (Laravel 8–11)

## Fitur
- Pasien dengan session `pasien.patient_id` dapat membuat satu chat aktif, mengirim pesan, menutup sesi, dan memulai sesi baru.
- Tiket `NADI-YYMMDD-XXXXXXXX`, antrean menunggu, claim atomik (row-lock), chat eksklusif untuk petugas yang claim.
- CS dapat membalas, menutup dan melihat daftar tiket; audit peristiwa dan waktu respons tersimpan.
- Dashboard ulasan menggunakan **tabel ulasan NADI yang sudah ada** melalui mapping konfigurasi. Balasan resmi CS disimpan di tabel tambahan (tidak mengubah ulasan asal).
- Polling 5 detik, CSRF, validasi panjang pesan, pembatasan request, otorisasi per tiket, output pesan textContent (XSS-safe).

## Instalasi
1. Salin folder app/, config/, database/, resources/, routes/ ke root Laravel NADI.
2. Tambahkan `require __DIR__.'/nadi_support.php';` satu kali dalam `routes/web.php`.
3. Jalankan `php artisan migrate` kemudian `php artisan optimize:clear`.
4. Pastikan setelah login pasien sesi `session('pasien')` memiliki `patient_id`, `medical_record`, dan `patient_name`.
5. Pastikan autentikasi petugas `auth()` tersedia dan user yang berhak memiliki `role=cs` atau `role=admin`. Jika berbeda, sesuaikan `config/nadi_support.php`, gunakan policy/gate sesuai infrastruktur Anda.
6. Arahkan tombol bantuan NADI ke `route('nadi.bantuan.index')`.
7. Dashboard CS tersedia di `route('cs.nadi.dashboard')`.
8. Mapping kolom ulasan NADI melalui env (contoh saja; gunakan kolom sebenarnya):

```
NADI_CS_ROLE_FIELD=role
NADI_CS_ROLE_VALUES=cs,admin
NADI_REVIEW_TABLE=nama_tabel_ulasan_anda
NADI_REVIEW_ID=id
NADI_REVIEW_PATIENT_NAME=nama_pasien
NADI_REVIEW_CONTENT=ulasan
NADI_REVIEW_RATING=rating
NADI_REVIEW_CREATED_AT=created_at
```

## Batasan untuk integrasi produksi
- Skema tabel ulasan existing belum diberikan sehingga mapping harus diisi terlebih dahulu. Saat belum dikonfigurasi, halaman review menampilkan error konfigurasi, bukan mengarang data.
- Chat hanya teks. Lampiran, unggahan dokumen, lampiran gambar, serah-terima antar CS, SLA, unread counters, broadcast, jam operasional, dan survei CS dapat ditambahkan tahap berikutnya.
- Pasien login lewat session existing; pastikan penguatan otentikasi pasien, timeout sesi login, dan kebijakan logout NADI berlaku.
- Database dengan traffic tinggi: gunakan locking `nadi_patient_chat_locks` yang sudah disediakan agar dua tab pasien tidak membuka tiket ganda.
- CS perlu memisahkan kredensial petugas dari kredensial pasien; gunakan Laravel session auth yang berlaku dan kebijakan akses berbasis peran.
- Untuk audit wajib, retensi, penghapusan/anonimisasi data, klasifikasi data kesehatan, serta enkripsi at rest perlu mengikuti kebijakan RS dan regulasi yang berlaku.
- Jangan gunakan chat untuk kata sandi/OTP/diagnosis atau data klinis sensitif. Audit log jangan menyimpan isi pesan rahasia.

## Pengujian penerimaan
1. Login pasien → Bantuan → buat tiket, pastikan satu tiket aktif.
2. Buka dua tab bersamaan → pastikan tetap satu tiket aktif.
3. Login CS A & B → claim tiket yang sama → hanya satu yang berhasil; petugas lain menerima HTTP 409.
4. Pasien dan CS saling mengirim pesan dan melihat pembaruan maksimal siklus polling 5 detik.
5. CS menutup tiket → pesan tidak bisa dikirim, tiket berikutnya memiliki ID baru.
6. Pasien lain tidak boleh membaca tiket orang lain (HTTP 403).
7. CS tanpa role tidak bisa masuk dashboard (HTTP 403).
8. Review muncul sesuai tabel lama; balasan CS terlihat di dashboard (untuk tampil di halaman ulasan pasien, hubungkan pembacaan nadi_review_replies pada Blade ulasan existing).


## Ekstensi Oktober 2026
- Presence CS online/sibuk/offline, heartbeat UI setiap 5 detik; saat offline CS tidak dapat claim tiket baru.
- Transfer antar CS online, audit serah terima, pemberitahuan internal saat chat dialihkan atau pasien membalas.
- Antrean FCFS (tiket terlama lebih dahulu), notifikasi Web Notification saat izin diberikan; polling tetap aktif saat dashboard dibuka.
- Pesan otomatis jam kerja Senin–Jumat 08:00–16:00 WITA (konfigurasikan jadwal CS, termasuk libur).
- Penilaian khusus chat CS 1–5 setelah close, terpisah dari ulasan pelayanan RS.
- Dashboard laporan: total, selesai, menunggu, rata-rata respons pertama, rating CS.
- SLA parameter di config `NADI_CS_SLA_MINUTES` (metrik waktu respons; eskalasi otomatis belum aktif).

### Integrasi ulasan NADI saat ini
Dari Blade NADI yang ditemukan, route submit `nadi.reviews.store` menggunakan field `rating`, `service_type`, `service_name`, `focus_category`, `review`. Fitur CS tidak membuat ulang route submit atau tabel ulasan tersebut. Set `NADI_REVIEW_TABLE`, `NADI_REVIEW_CONTENT=review`, `NADI_REVIEW_FOCUS_CATEGORY=focus_category`, dst **menurut database sebenarnya**. Contoh `review` di sini adalah nama field formulir, belum tentu nama kolom database. CS mengelola balasan di tabel `nadi_review_replies` yang baru. Untuk menampilkan balasan dalam halaman ulasan NADI yang sudah ada, join/lookup balasan berdasarkan ID ulasan di controller existing, atau gunakan contoh snippet docs/INTEGRASI_ULASAN_NADI.md.

### Catatan implementasi
Untuk penggunaan nyata: ganti localStorage status CS dengan session/Redis presence sesuai kebijakan, atur roster dan hari libur, tambahkan cron SLA escalation, retensi, sanitasi data dan kebijakan privasi. Gunakan HTTPS, atur akses CS secara tepat. Tidak disarankan menjalankan sistem sebelum UAT dan audit keamanan. Dashboard saat ini memerlukan jendela terbuka agar notifikasi dan heartbeat berjalan; ini bukan push background.
