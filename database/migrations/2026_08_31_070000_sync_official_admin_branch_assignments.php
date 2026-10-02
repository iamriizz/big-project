<?php

use App\Models\Branch;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /** Ensure built-in admin accounts point at the official branch IDs. */
    public function up(): void
    {
        $assignments = [
            'owner@favdimsum.com' => 'Fav Dimsum Karangmoncol',
            'admin.karangmoncol@favdimsum.com' => 'Fav Dimsum Karangmoncol',
            'admin.pengadegan@favdimsum.com' => 'Fav Dimsum Pengadegan',
            'admin.bobotsari@favdimsum.com' => 'Fav Dimsum Bobotsari',
        ];

        foreach ($assignments as $email => $branchName) {
            $branchId = Branch::query()->where('name', $branchName)->value('id');

            if ($branchId) {
                DB::table('users')->where('email', $email)->update(['branch_id' => $branchId]);
            }
        }
    }

    public function down(): void
    {
        // Existing account-to-branch assignments cannot be reliably restored.
    }
};
