<?php

use App\Models\Branch;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /** Move records from legacy branches recreated by the old checkout flow. */
    public function up(): void
    {
        $officialBranches = [
            'Bobotsari' => 'Fav Dimsum Bobotsari',
            'Karangmoncol' => 'Fav Dimsum Karangmoncol',
            'Pengadegan' => 'Fav Dimsum Pengadegan',
        ];

        DB::transaction(function () use ($officialBranches): void {
            foreach ($officialBranches as $location => $officialName) {
                $official = Branch::query()->where('name', $officialName)->firstOrFail();
                $legacyIds = Branch::query()
                    ->where('id', '!=', $official->id)
                    ->whereRaw('LOWER(name) LIKE ?', ['%'.strtolower($location).'%'])
                    ->pluck('id');

                if ($legacyIds->isEmpty()) {
                    continue;
                }

                foreach (['transactions', 'stocks', 'restock_histories', 'users'] as $table) {
                    DB::table($table)->whereIn('branch_id', $legacyIds)->update(['branch_id' => $official->id]);
                }

                Branch::query()->whereIn('id', $legacyIds)->delete();
            }
        });
    }

    public function down(): void
    {
        // The original branch IDs are intentionally not recreated.
    }
};
