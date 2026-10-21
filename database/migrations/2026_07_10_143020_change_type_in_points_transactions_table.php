<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('points_transactions', function (Blueprint $table) {
            // ប្តូរប្រភេទ column 'type' ទៅជា string ធម្មតា ដើម្បីឱ្យបញ្ចូលពាក្យអ្វីក៏បាន
            $table->string('type')->change(); 
        });
    }

    public function down(): void
    {
        Schema::table('points_transactions', function (Blueprint $table) {
            $table->enum('type', ['earn', 'spend'])->change(); // លក្ខខណ្ឌដើមវិញ
        });
    }
};