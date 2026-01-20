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
        Schema::table('assets', function (Blueprint $table) {
            $table->foreignId('tipe_id')
                ->nullable()
                ->after('id')
                ->constrained('tipes')
                ->nullOnDelete();

            $table->foreignId('vendor_id')
                ->nullable()
                ->after('tipe_id')
                ->constrained('vendors')
                ->nullOnDelete();

            $table->integer('jumlah')
                ->default(1)
                ->after('vendor_id');

            $table->decimal('harga', 15, 2)
                ->default(0)
                ->after('jumlah');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropForeign(['tipe_id']);
            $table->dropForeign(['vendor_id']);

            $table->dropColumn([
                'tipe_id',
                'vendor_id',
                'jumlah',
                'harga'
            ]);
        });
    }
};
