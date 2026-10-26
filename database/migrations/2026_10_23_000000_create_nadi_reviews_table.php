<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Untuk instalasi baru. Jika nadi_reviews sudah ada, samakan kolom
        // dengan model/controller, dan HAPUS unique index pada pasien.
        if (!Schema::hasTable('nadi_reviews')) {
            Schema::create('nadi_reviews', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('patient_key', 64)->index();
                $table->string('patient_label', 120)->nullable();
                $table->unsignedTinyInteger('rating');
                $table->string('service_type', 30);
                $table->string('service_name', 120)->nullable();
                $table->string('focus_category', 50)->nullable()->index();
                $table->text('review');
                $table->string('status', 20)->default('published')->index();
                $table->timestamp('published_at')->nullable()->index();
                $table->text('admin_reply')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        // Jangan otomatis hapus tabel agar ulasan pasien tidak hilang.
    }
};
