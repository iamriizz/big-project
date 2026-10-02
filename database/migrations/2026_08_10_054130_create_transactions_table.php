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
        Schema::create('transactions', function (Blueprint $table) {
    $table->id();
    $table->string('invoice_number')->unique();
    $table->foreignId('branch_id')->constrained()->onDelete('cascade');
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->decimal('total_amount', 12, 2);
    $table->string('payment_method')->default('cash');
    $table->timestamps();
    $table->string('queue_number')->nullable(); // Nomor Antrean (misal: A-001)
    $table->string('pickup_type')->default('now'); // 'now' atau 'scheduled'
    $table->time('pickup_time')->nullable(); // Jam Pengambilan
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
