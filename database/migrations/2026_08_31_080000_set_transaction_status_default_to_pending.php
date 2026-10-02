<?php

use App\Models\Transaction;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table): void {
            $table->string('status')->default(Transaction::STATUS_PENDING)->change();
        });

        DB::table('transactions')->whereNull('status')->update(['status' => Transaction::STATUS_COMPLETED]);
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table): void {
            $table->string('status')->default(Transaction::STATUS_COMPLETED)->change();
        });
    }
};
