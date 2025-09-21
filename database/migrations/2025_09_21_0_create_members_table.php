<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            // Personal & contact details (extend as needed)
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();

            // Referral system
            $table->string('referral_code')->unique();
            $table->foreignId('referrer_id')->nullable()->constrained('members')->nullOnDelete();

            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('members');
    }
};
