-- Add created_by owner columns to allow per-user ownership and filtering
-- Run this against your application database (backup first)

ALTER TABLE `customers`
  ADD COLUMN `created_by` INT NULL;

ALTER TABLE `vehicles`
  ADD COLUMN `created_by` INT NULL;

-- Optionally add foreign key constraint if you maintain users table:
-- ALTER TABLE `customers` ADD CONSTRAINT fk_customers_created_by FOREIGN KEY (`created_by`) REFERENCES `users`(`id`);
-- ALTER TABLE `vehicles` ADD CONSTRAINT fk_vehicles_created_by FOREIGN KEY (`created_by`) REFERENCES `users`(`id`);
