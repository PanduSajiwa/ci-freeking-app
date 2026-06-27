# Database Migration Guide

This guide explains how to migrate your MySQL database using CodeIgniter 4 migrations.

## Prerequisites

- MySQL server running
- PHP 8.1 or higher
- CodeIgniter 4 framework installed

## Configuration

Make sure your `.env` file has the correct database configuration:

```
database.default.hostname = localhost
database.default.database = kp_system_parking
database.default.username = root
database.default.password = [your_password]
database.default.DBDriver = MySQLi
database.default.port = 3306
```

## Running Migrations

To create all tables, run from your project root:

```bash
php spark migrate
```

This will run all migrations in order:
1. `2025_06_27_000001_CreateUsersTable` - Users table
2. `2025_06_27_000002_CreateCustomersTable` - Customers table
3. `2025_06_27_000003_CreateVehiclesTable` - Vehicles table
4. `2025_06_27_000004_CreateParkingQuotaManagementTable` - Parking quota table
5. `2025_06_27_000005_CreateParkingSubmissionsTable` - Parking submissions table
6. `2025_06_27_000006_CreateParkingUsageTable` - Parking usage table

## Seeding Data

To populate the database with sample data, run:

```bash
php spark db:seed DatabaseSeeder
```

Or seed individual tables:

```bash
php spark db:seed UserSeeder
php spark db:seed CustomerSeeder
php spark db:seed VehicleSeeder
php spark db:seed ParkingSubmissionSeeder
```

## Rollback Migrations

To undo the last batch of migrations:

```bash
php spark migrate:rollback
```

To undo all migrations:

```bash
php spark migrate:refresh
```

## View Migration Status

To check which migrations have been run:

```bash
php spark migrate:status
```

## Tables Created

### users
- id, username, password, full_name, email, role, created_at, updated_at, is_active

### customers
- id, nik, full_name, phone, email, address, company, created_at, updated_at, created_by

### vehicles
- id, license_plate, vehicle_type, brand, model, color, created_by

### parking_quota_management
- id, month_year, total_quota, used_quota, created_by, created_at, updated_at

### parking_submissions
- id, submission_code, customer_id, vehicle_id, submitted_by, submission_date, duration_days, purpose, id_card_image, vehicle_image, supporting_doc_image, operation_manager_approval, operation_manager_id, operation_manager_notes, operation_manager_approval_date, parking_dept_approval, parking_dept_id, parking_dept_notes, parking_dept_approval_date, quota_given, status, created_at, updated_at

### parking_usage
- id, submission_id, usage_date, notes, created_at, updated_at

## Notes

- All tables use `utf8mb4` charset and `utf8mb4_general_ci` collation
- Foreign key constraints are defined in the migrations
- Timestamps use CURRENT_TIMESTAMP for automatic updates
- The migrations preserve your existing data structure from the backup
