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
        Schema::create('tb_eng_team', function (Blueprint $table) {
            $table->id('id_team');
            $table->string('na_team');
            // $table->foreignId('id_karyawan')->nullable()->constrained('tb_karyawan', 'id_karyawan');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_eng_team', function (Blueprint $table) {
            // $table->dropForeign(['id_karyawan']);
        });
        Schema::dropIfExists('tb_eng_team');
    }
};
