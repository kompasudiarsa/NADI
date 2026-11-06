<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::create('nadi_cs_users',function(Blueprint $t){
   $t->id();$t->string('name',120);$t->string('email',190)->unique();$t->string('password');
   $t->string('role',20)->default('cs');$t->boolean('is_active')->default(true);
   $t->rememberToken();$t->timestamps();
  });
 }
 public function down(): void {Schema::dropIfExists('nadi_cs_users');}
};
