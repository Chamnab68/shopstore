<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // បន្ថែម column សម្រាប់រក្សាទុកពិន្ទុ (Default ស្មើ 0)
            $table->integer('loyalty_points')->default(0)->after('email');
            
            // បន្ថែម column សម្រាប់រក្សាទុកកម្រិត Tier (Default ស្មើ standard ឬ silver)
            $table->string('current_tier')->default('standard')->after('loyalty_points');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['loyalty_points', 'current_tier']);
        });
    }
};