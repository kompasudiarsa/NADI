<?php
return [
    // Ganti sesuai struktur tabel users (mis. role = 'cs' atau is_cs = 1).
    'cs_role_field' => env('NADI_CS_ROLE_FIELD', 'role'),
    'cs_role_values' => array_filter(explode(',', env('NADI_CS_ROLE_VALUES', 'cs,admin'))),
    'poll_interval_ms' => 5000,
    'sla_minutes' => (int) env('NADI_CS_SLA_MINUTES', 10),
    'office_timezone' => env('NADI_CS_TIMEZONE','Asia/Makassar'),
    'office_start' => env('NADI_CS_OFFICE_START','08:00'),
    'office_end' => env('NADI_CS_OFFICE_END','16:00'),
    'office_days' => [1,2,3,4,5],
    'max_open_sessions_per_patient' => 1,
    // Adapter ke tabel ulasan NADI existing. Isi di .env jika sudah mengetahui nama kolomnya.
    'review_focus_category' => env('NADI_REVIEW_FOCUS_CATEGORY', 'focus_category'),
    'review_table' => env('NADI_REVIEW_TABLE', ''),
    'review_id' => env('NADI_REVIEW_ID', 'id'),
    'review_patient_name' => env('NADI_REVIEW_PATIENT_NAME', 'nama_pasien'),
    'review_content' => env('NADI_REVIEW_CONTENT', 'ulasan'),
    'review_rating' => env('NADI_REVIEW_RATING', 'rating'),
    'review_created_at' => env('NADI_REVIEW_CREATED_AT', 'created_at'),
];
