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
    Schema::create('loyalty_tiers', function (Blueprint $table) {
        $table->id();
        $table->string('name'); // Silver, Gold, Platinum
        $table->integer('min_spend_threshold')->default(0);
        $table->decimal('discount_percentage', 5, 2)->default(0.00);
        $table->boolean('free_shipping')->default(false);
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loyalty_tiers');
    }
};
