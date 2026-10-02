<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('transactions', 'status')) {
            Schema::table('transactions', function (Blueprint $table): void {
                $table->string('status')->default('completed')->after('payment_method');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('transactions', 'status')) {
            Schema::table('transactions', function (Blueprint $table): void {
                $table->dropColumn('status');
            });
        }
    }
};
