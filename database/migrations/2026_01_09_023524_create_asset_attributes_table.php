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
        Schema::create('asset_attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_id')->constrained('kategoris')->cascadeOnDelete();
            $table->string('nama_atribut');
            $table->enum('tipe_input', ['text', 'number', 'select'])->default('text');
            $table->text('opsi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_attributes');
    }
};
