<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::table('nadi_chat_sessions',function(Blueprint $t){
   $t->unsignedInteger('transfer_count')->default(0); $t->timestamp('last_patient_message_at')->nullable();
   $t->string('priority',12)->default('normal'); $t->timestamp('sla_breached_at')->nullable();
  });
  Schema::create('nadi_cs_presence',function(Blueprint $t){$t->unsignedBigInteger('cs_id')->primary();$t->string('status',12)->default('offline');$t->timestamp('last_seen_at')->nullable();$t->timestamps();});
  Schema::create('nadi_chat_feedback',function(Blueprint $t){$t->id();$t->foreignId('session_id')->unique()->constrained('nadi_chat_sessions')->cascadeOnDelete();$t->unsignedTinyInteger('rating');$t->text('comment')->nullable();$t->timestamps();});
  Schema::create('nadi_support_notifications',function(Blueprint $t){$t->id();$t->foreignId('cs_id')->index();$t->foreignId('session_id')->nullable();$t->string('type',40);$t->string('message',250);$t->timestamp('read_at')->nullable();$t->timestamps();});
 }
 public function down(): void {
  Schema::dropIfExists('nadi_support_notifications');Schema::dropIfExists('nadi_chat_feedback');Schema::dropIfExists('nadi_cs_presence');
  Schema::table('nadi_chat_sessions',function(Blueprint $t){$t->dropColumn(['transfer_count','last_patient_message_at','priority','sla_breached_at']);});
 }
};
