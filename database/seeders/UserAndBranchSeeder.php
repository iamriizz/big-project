<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserAndBranchSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Data Cabang
        $karangmoncol = Branch::updateOrCreate(['name' => 'Fav Dimsum Karangmoncol'], [
            'address' => 'Karangmoncol', 'open_time' => '10:00:00', 'close_time' => '21:00:00',
        ]);
        $pengadegan   = Branch::updateOrCreate(['name' => 'Fav Dimsum Pengadegan'], [
            'address' => 'Pengadegan', 'open_time' => '10:00:00', 'close_time' => '17:00:00',
        ]);
        $bobotsari    = Branch::updateOrCreate(['name' => 'Fav Dimsum Bobotsari'], [
            'address' => 'Bobotsari', 'open_time' => '14:00:00', 'close_time' => '21:00:00',
        ]);

        // 2. Buat Akun Owner (Pusat - Karangmoncol)
        User::updateOrCreate(
            ['email' => 'owner@favdimsum.com'],
            [
                'name'      => 'Owner Fav Dimsum',
                'password'  => Hash::make('password123'),
                'role'      => 'owner',
                'branch_id' => $karangmoncol->id,
            ]
        );

        // 3. Buat Akun Admin Pengadegan
        User::updateOrCreate(
            ['email' => 'admin.pengadegan@favdimsum.com'],
            [
                'name'      => 'Admin Pengadegan',
                'password'  => Hash::make('password123'),
                'role'      => 'admin',
                'branch_id' => $pengadegan->id,
            ]
        );

        // 4. Buat Akun Admin Karangmoncol
        User::updateOrCreate(
            ['email' => 'admin.karangmoncol@favdimsum.com'],
            [
                'name'      => 'Admin Karangmoncol',
                'password'  => Hash::make('password123'),
                'role'      => 'admin',
                'branch_id' => $karangmoncol->id,
            ]
        );

        // 5. Buat Akun Admin Bobotsari
        User::updateOrCreate(
            ['email' => 'admin.bobotsari@favdimsum.com'],
            [
                'name'      => 'Admin Bobotsari',
                'password'  => Hash::make('password123'),
                'role'      => 'admin',
                'branch_id' => $bobotsari->id,
            ]
        );

        // 6. Buat Akun Pembeli (User)
        User::updateOrCreate(
            ['email' => 'semades@gmail.com'],
            [
                'name'      => 'semades',
                'password'  => Hash::make('password123'),
                'role'      => 'user',
                'branch_id' => null,
            ]
        );
    }
}
