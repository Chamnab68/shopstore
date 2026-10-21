<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Insert default settings
        \DB::table('settings')->insert([
            ['key' => 'bank_name',           'value' => 'ABA Bank',          'created_at' => now(), 'updated_at' => now()],
            ['key' => 'bank_account_name',   'value' => 'ShopStore Co., Ltd','created_at' => now(), 'updated_at' => now()],
            ['key' => 'bank_account_number', 'value' => '000123456',         'created_at' => now(), 'updated_at' => now()],
            ['key' => 'bank_qr_code',        'value' => null,                'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void {
        Schema::dropIfExists('settings');
    }
};
