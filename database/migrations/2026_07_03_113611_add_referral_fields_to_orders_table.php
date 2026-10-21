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
        Schema::table('orders', function (Blueprint $table) {
            // បន្ថែម column ទាំងពីរនេះដោយកំណត់តម្លៃ default ស្មើ 0
            $table->decimal('referral_amount_used', 10, 2)->default(0.00)->after('total');
            $table->decimal('remaining_payable', 10, 2)->default(0.00)->after('referral_amount_used');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['referral_amount_used', 'remaining_payable']);
        });
    }
};