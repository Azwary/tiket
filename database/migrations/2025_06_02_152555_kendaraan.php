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
        Schema::create('kendaraan', function (Blueprint $table) {
            $table->id('id_kendaraan'); // primary key auto increment
            $table->string('plat_nomor', 15);
            $table->unsignedBigInteger('id_supir'); // relasi ke supir
            $table->string('status', 20)->nullable();
            $table->timestamps();

            // Foreign key ke tabel supir
            $table->foreign('id_supir')
                ->references('id_supir')
                ->on('supir')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kendaraan');
    }
};
