<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id' => 1,
                'username' => 'adminku',
                'password' => '$2y$10$z/ma34R3KAYPYcIv/mY6p.NPULbftJPeHjH.xzPF7VSFSYci/tBXm',
                'full_name' => 'udin petot',
                'email' => 'petot@gmail.com',
                'role' => 'admin',
                'is_active' => 1,
                'created_at' => '2025-11-30 12:14:01',
                'updated_at' => '2025-12-05 04:48:02',
            ],
            [
                'id' => 5,
                'username' => 'pandukaryawan',
                'password' => '$2y$10$r9kmfjYtz3zqb6TFuCXYiuYEmt3L8pVXquCYmW2h3RuyWaHSUkbmy',
                'full_name' => 'pandu karyawan',
                'email' => 'pandukaryawan@gmail.com',
                'role' => 'employee',
                'is_active' => 1,
                'created_at' => '2025-11-30 09:15:05',
                'updated_at' => '2025-11-30 16:15:05',
            ],
            [
                'id' => 6,
                'username' => 'pandumanager',
                'password' => '$2y$10$BQi0a8ynyss5NC81oKuR9.It2zD1O3KhT03lJEGVN904eLhKyoRB2',
                'full_name' => 'pandu manager',
                'email' => 'pandumanager@gmail.co',
                'role' => 'operation_manager',
                'is_active' => 1,
                'created_at' => '2025-11-30 09:15:39',
                'updated_at' => '2025-11-30 16:15:39',
            ],
            [
                'id' => 7,
                'username' => 'pandudept',
                'password' => '$2y$10$xh26DWxfCIb0M9xcz4Qmw.We4cUBcX8X9N03J8IPwwZgqDJj3PWie',
                'full_name' => 'pandu dept',
                'email' => 'pandudept@gmail.com',
                'role' => 'parking_dept',
                'is_active' => 1,
                'created_at' => '2025-11-30 09:15:56',
                'updated_at' => '2025-11-30 16:15:56',
            ],
            [
                'id' => 8,
                'username' => 'pandusajiwa',
                'password' => '$2y$10$Z1m8zCNeELJq1jrOKUtb/.PVr8K2BUb1jdarnEAyvaQn8YPcMb5a2',
                'full_name' => 'pandu sajiwa',
                'email' => 'pandusajiwa@gmail.co',
                'role' => 'admin',
                'is_active' => 1,
                'created_at' => '2025-11-30 09:19:50',
                'updated_at' => '2025-11-30 16:19:50',
            ],
            [
                'id' => 9,
                'username' => 'pandukaryawan1',
                'password' => '$2y$10$KdftLbooBskHsF3KmUkAHu9qPMiqa6u9VQ4mPSXtmuQS3gqF1YU4q',
                'full_name' => 'pandukaryawan1',
                'email' => 'pandukaryawan1@gmail.com',
                'role' => 'employee',
                'is_active' => 1,
                'created_at' => '2025-12-02 00:29:24',
                'updated_at' => '2025-12-02 07:29:24',
            ],
        ];

        $this->db->table('users')->insertBatch($data);
    }
}
