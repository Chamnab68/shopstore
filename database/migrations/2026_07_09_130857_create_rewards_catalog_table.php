<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('rewards_catalog', function (Blueprint $table) {
        $table->id();
        $table->string('reward_name'); // $5 Voucher, Free Cap
        $table->text('description')->nullable();
        $table->integer('point_cost');
        $table->integer('stock_limit')->default(0);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rewards_catalog');
    }
};
