<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id' => 5,
                'nik' => '1111111111111111',
                'full_name' => 'Pandu sajiwa karyawan',
                'phone' => '111111111111',
                'email' => 'pandukaryawan@gmail.com',
                'address' => 'PT Cinta dia',
                'company' => 'PT Cinta dia',
                'created_by' => 5,
                'created_at' => '2025-12-02 00:39:11',
                'updated_at' => '2025-12-02 07:39:11',
            ],
            [
                'id' => 6,
                'nik' => '2222222222222222',
                'full_name' => 'Pandu sajiwa karyawan satu',
                'phone' => '222222222222',
                'email' => 'pandukaryawan1@gmail.com',
                'address' => 'PT alamat palsu',
                'company' => 'PT alamat palsu',
                'created_by' => 9,
                'created_at' => '2025-12-02 00:41:28',
                'updated_at' => '2025-12-02 07:41:28',
            ],
        ];

        $this->db->table('customers')->insertBatch($data);
    }
}
