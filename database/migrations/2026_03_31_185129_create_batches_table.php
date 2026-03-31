<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('obat_id')->constrained('obat')->onDelete('cascade');
            $table->string('batch_number');
            $table->date('expired_date');
            $table->integer('jumlah_awal');
            $table->integer('jumlah_sisa');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('batches');
    }
};
