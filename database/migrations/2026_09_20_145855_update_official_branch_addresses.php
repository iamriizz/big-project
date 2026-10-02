<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('branches')->where('name', 'Fav Dimsum Karangmoncol')->update(['address' => 'Jl. Kopral Yasan, Gerbolang, Pepedan, Kec. Karangmoncol, Kabupaten Purbalingga, Jawa Tengah 53355']);
        DB::table('branches')->where('name', 'Fav Dimsum Pengadegan')->update(['address' => 'Jl. Gemenggeng, Kec. Pengadegan, Kabupaten Purbalingga, Jawa Tengah 53393']);
        DB::table('branches')->where('name', 'Fav Dimsum Bobotsari')->update(['address' => 'Jl. Kolonel Sugiri, Dusun 2, Bobotsari, Kec. Bobotsari, Kabupaten Purbalingga, Jawa Tengah 53353']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Address updates are intentionally retained on rollback.
    }
};
