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
        Schema::create('asset_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('assets')->cascadeOnDelete();
            $table->foreignId('asset_attribute_id')->constrained('asset_attributes')->cascadeOnDelete();
            $table->string('nilai')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_attribute_values');
    }
};
