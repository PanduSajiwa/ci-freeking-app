<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class VehicleSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id' => 6,
                'license_plate' => 'AE69EK',
                'vehicle_type' => 'car',
                'brand' => 'Mercedes Bens 69 Keren',
                'model' => 'Jeep',
                'color' => 'Pink',
                'created_by' => 5,
            ],
            [
                'id' => 7,
                'license_plate' => 'B3RAK',
                'vehicle_type' => 'car',
                'brand' => 'CRV',
                'model' => 'SUV',
                'color' => 'Merah',
                'created_by' => 9,
            ],
        ];

        $this->db->table('vehicles')->insertBatch($data);
    }
}
