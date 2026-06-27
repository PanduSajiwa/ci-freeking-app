<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateParkingQuotaManagementTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'month_year' => [
                'type' => 'VARCHAR',
                'constraint' => 7,
            ],
            'total_quota' => [
                'type' => 'INT',
            ],
            'used_quota' => [
                'type' => 'INT',
                'default' => 0,
            ],
            'created_by' => [
                'type' => 'INT',
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'default' => new RawSql('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('created_by');
        $this->forge->createTable('parking_quota_management');
    }

    public function down()
    {
        $this->forge->dropTable('parking_quota_management');
    }
}
