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
    // 🛠️ បន្ថែមលក្ខខណ្ឌពិនិត្យ៖ បើគ្មាន Table ឈ្មោះ role_user ទេ ទើបឱ្យវាបង្កើត
    if (!Schema::hasTable('role_user')) {
        Schema::create('role_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('points');
    });
}
};
