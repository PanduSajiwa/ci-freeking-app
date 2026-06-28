<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterCustomersTableMakeNikNullable extends Migration
{
    public function up()
    {
        $fields = [
            'nik' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'unique' => false,
            ],
        ];
        $this->forge->modifyColumn('customers', $fields);
    }

    public function down()
    {
        $fields = [
            'nik' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'unique' => true,
            ],
        ];
        $this->forge->modifyColumn('customers', $fields);
    }
}
