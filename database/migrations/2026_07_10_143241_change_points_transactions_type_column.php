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
    Schema::table('points_transactions', function (Blueprint $table) {
        // ដូរពី enum ទៅជា string ដើម្បីឱ្យដាក់ពាក្យអ្វីក៏បាន
        $table->string('type')->change();
    });
}

public function down(): void
{
    Schema::table('points_transactions', function (Blueprint $table) {
        // ត្រឡប់មកដូចដើមវិញប្រសិនបើចង់ (អាចដាក់ enum តាមលក្ខខណ្ឌដើមរបស់បង)
        $table->enum('type', ['earn', 'redeem'])->change(); 
    });
}
};
