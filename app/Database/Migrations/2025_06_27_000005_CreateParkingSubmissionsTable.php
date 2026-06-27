<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreateParkingSubmissionsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
            ],
            'submission_code' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'unique' => true,
            ],
            'customer_id' => [
                'type' => 'INT',
            ],
            'vehicle_id' => [
                'type' => 'INT',
            ],
            'submitted_by' => [
                'type' => 'INT',
            ],
            'submission_date' => [
                'type' => 'DATE',
            ],
            'duration_days' => [
                'type' => 'INT',
            ],
            'purpose' => [
                'type' => 'TEXT',
            ],
            'id_card_image' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'vehicle_image' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'supporting_doc_image' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'operation_manager_approval' => [
                'type' => 'ENUM',
                'values' => ['pending', 'approved', 'rejected'],
                'default' => 'pending',
            ],
            'operation_manager_id' => [
                'type' => 'INT',
                'null' => true,
            ],
            'operation_manager_notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'operation_manager_approval_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'parking_dept_approval' => [
                'type' => 'ENUM',
                'values' => ['pending', 'approved', 'rejected'],
                'default' => 'pending',
            ],
            'parking_dept_id' => [
                'type' => 'INT',
                'null' => true,
            ],
            'parking_dept_notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'parking_dept_approval_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'quota_given' => [
                'type' => 'INT',
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'values' => ['draft', 'submitted', 'under_review', 'approved', 'rejected', 'completed'],
                'default' => 'draft',
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
        $this->forge->addKey('customer_id');
        $this->forge->addKey('vehicle_id');
        $this->forge->addKey('submitted_by');
        $this->forge->addKey('operation_manager_id');
        $this->forge->addKey('parking_dept_id');
        $this->forge->createTable('parking_submissions');
    }

    public function down()
    {
        $this->forge->dropTable('parking_submissions');
    }
}
