<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('nadi_reviews') && !Schema::hasColumn('nadi_reviews', 'focus_category')) {
            Schema::table('nadi_reviews', function (Blueprint $table) {
                // Nullable untuk menjaga kompatibilitas dengan ulasan yang sudah ada.
                $table->string('focus_category', 50)->nullable()->index();
            });
        }
    }

    public function down(): void
    {
        // Data ulasan dipertahankan; jangan otomatis menghapus kolom.
    }
};
