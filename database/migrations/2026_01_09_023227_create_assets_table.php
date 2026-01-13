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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('kode_aset')->unique(); // barcode
            $table->string('nama_aset');
            $table->foreignId('kategori_id')->constrained('kategoris');
            $table->foreignId('lokasi_id')->nullable()->constrained('lokasis');
            $table->enum('kondisi', ['baik', 'rusak', 'maintenance'])->default('baik');

            $table->string('kelengkapan')->nullable();
            $table->date('tanggal_pembelian')->nullable();
            $table->string('foto')->nullable(); // path file

            $table->text('catatan')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
