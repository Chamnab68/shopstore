<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    // ថែមលក្ខខណ្ឌឆែកមើល ប្រសិនបមិនទាន់មានតារាងនេះ ទើបបង្កើត
    if (!Schema::hasTable('reward_redemptions')) {
        Schema::create('reward_redemptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('reward_id')->constrained()->onDelete('cascade');
            $table->integer('points_spent');
            $table->string('redemption_code')->unique();
            $table->string('status')->default('pending');
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamp('fulfilled_at')->nullable();
            $table->timestamps();
        });
    }
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reward_redemptions');
    }
};
