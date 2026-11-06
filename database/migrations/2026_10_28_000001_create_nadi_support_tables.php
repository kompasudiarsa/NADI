<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('nadi_patient_chat_locks', function (Blueprint $t) { $t->string('patient_key',100)->primary(); });
        Schema::create('nadi_chat_sessions', function (Blueprint $t) {
            $t->id();
            $t->string('ticket', 32)->unique();
            $t->string('patient_key', 100)->index(); // patient_id UUID dari session NADI
            $t->string('patient_rm', 40)->nullable();
            $t->string('patient_name', 150)->nullable();
            $t->string('category', 80);
            $t->string('subject', 180)->nullable();
            $t->string('status', 20)->default('waiting')->index();
            $t->foreignId('assigned_cs_id')->nullable()->index();
            $t->timestamp('assigned_at')->nullable();
            $t->timestamp('first_response_at')->nullable();
            $t->timestamp('closed_at')->nullable();
            $t->foreignId('closed_by')->nullable();
            $t->string('close_reason', 250)->nullable();
            $t->timestamp('last_message_at')->nullable();
            $t->timestamps();
            $t->index(['patient_key','status']);
        });
        Schema::create('nadi_chat_messages', function (Blueprint $t) {
            $t->id();
            $t->foreignId('session_id')->constrained('nadi_chat_sessions')->cascadeOnDelete();
            $t->string('sender_type', 10); // patient, cs, system
            $t->foreignId('sender_cs_id')->nullable();
            $t->text('message');
            $t->timestamp('read_at')->nullable();
            $t->timestamps();
            $t->index(['session_id','id']);
        });
        Schema::create('nadi_chat_events', function (Blueprint $t) {
            $t->id();
            $t->foreignId('session_id')->constrained('nadi_chat_sessions')->cascadeOnDelete();
            $t->foreignId('actor_cs_id')->nullable();
            $t->string('event', 40);
            $t->json('details')->nullable();
            $t->timestamps();
        });
        Schema::create('nadi_review_replies', function (Blueprint $t) {
            $t->id();
            $t->string('review_id', 100)->unique();
            $t->text('reply');
            $t->foreignId('cs_id');
            $t->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('nadi_review_replies');
        Schema::dropIfExists('nadi_chat_events');
        Schema::dropIfExists('nadi_chat_messages');
        Schema::dropIfExists('nadi_chat_sessions');
        Schema::dropIfExists('nadi_patient_chat_locks');
    }
};
