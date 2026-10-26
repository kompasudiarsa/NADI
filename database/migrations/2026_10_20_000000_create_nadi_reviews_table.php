<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('nadi_reviews', function (Blueprint $table) {
            $table->id();
            // HMAC hash of a session patient identifier; never store/display the RM here.
            $table->string('patient_key', 64)->index();
            $table->string('patient_label', 60)->default('Pasien terverifikasi');
            $table->unsignedTinyInteger('rating');
            $table->text('review');
            $table->string('service_type', 20)->default('rumah_sakit');
            $table->string('service_name', 120)->nullable();
            $table->string('status', 20)->default('pending')->index(); // pending/published/rejected
            $table->text('admin_reply')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nadi_reviews');
    }
};
