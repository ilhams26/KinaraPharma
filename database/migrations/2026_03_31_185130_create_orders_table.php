<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('order_code')->unique();
            $table->enum('metode_pembayaran', ['midtrans', 'cash']);
            $table->decimal('total_harga', 10, 2);
            $table->enum('status', ['diproses', 'siap_diambil', 'selesai', 'dibatalkan'])->default('diproses');
            $table->enum('payment_status', ['paid', 'unpaid'])->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
