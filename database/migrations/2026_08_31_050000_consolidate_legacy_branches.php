<?php

use App\Models\Branch;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /** Consolidate legacy branch names without losing their related records. */
    public function up(): void
    {
        $officialBranches = [
            'Bobotsari' => ['name' => 'Fav Dimsum Bobotsari', 'address' => 'Bobotsari'],
            'Karangmoncol' => ['name' => 'Fav Dimsum Karangmoncol', 'address' => 'Karangmoncol'],
            'Pengadegan' => ['name' => 'Fav Dimsum Pengadegan', 'address' => 'Pengadegan'],
        ];

        DB::transaction(function () use ($officialBranches): void {
            foreach ($officialBranches as $location => $attributes) {
                $official = Branch::query()
                    ->whereRaw('LOWER(name) = ?', [strtolower($attributes['name'])])
                    ->orderBy('id')
                    ->first();

                if (! $official) {
                    $official = Branch::query()->create($attributes);
                } else {
                    $official->update($attributes);
                }

                // Includes "Bobotsari", "Omzet Bobotsari", and the former
                // "Fav Dimsum Karangmoncol (Pusat)".
                $legacyIds = Branch::query()
                    ->where('id', '!=', $official->id)
                    ->whereRaw('LOWER(name) LIKE ?', ['%'.strtolower($location).'%'])
                    ->pluck('id');

                if ($legacyIds->isEmpty()) {
                    continue;
                }

                foreach (['transactions', 'stocks', 'restock_histories', 'users'] as $table) {
                    DB::table($table)
                        ->whereIn('branch_id', $legacyIds)
                        ->update(['branch_id' => $official->id]);
                }

                Branch::query()->whereIn('id', $legacyIds)->delete();
            }
        });
    }

    public function down(): void
    {
        // Consolidation is intentionally irreversible: related records are kept.
    }
};
