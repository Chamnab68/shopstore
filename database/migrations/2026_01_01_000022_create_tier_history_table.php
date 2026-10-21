<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tier_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('from_tier_id')->nullable()->constrained('tiers')->nullOnDelete();
            $table->foreignId('to_tier_id')->constrained('tiers')->cascadeOnDelete();
            $table->enum('change_type', ['upgrade', 'downgrade', 'manual_override'])->default('upgrade');
            $table->string('reason', 255)->nullable();   // required for manual override
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete(); // null = system
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tier_histories');
    }
};
