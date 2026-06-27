-- Add created_at / updated_at columns for models that use timestamps
-- Run this against your application database (make a DB backup first)

ALTER TABLE `users`
  ADD COLUMN `created_at` DATETIME NULL DEFAULT NULL,
  ADD COLUMN `updated_at` DATETIME NULL DEFAULT NULL;

ALTER TABLE `customers`
  ADD COLUMN `created_at` DATETIME NULL DEFAULT NULL;

ALTER TABLE `parking_submissions`
  ADD COLUMN `created_at` DATETIME NULL DEFAULT NULL,
  ADD COLUMN `updated_at` DATETIME NULL DEFAULT NULL;

ALTER TABLE `parking_quota_management`
  ADD COLUMN `created_at` DATETIME NULL DEFAULT NULL,
  ADD COLUMN `updated_at` DATETIME NULL DEFAULT NULL;

ALTER TABLE `parking_usage`
  ADD COLUMN `created_at` DATETIME NULL DEFAULT NULL,
  ADD COLUMN `updated_at` DATETIME NULL DEFAULT NULL;

-- Optional: if any table already has these columns, you can comment out that block or run each ALTER separately.
-- If your DB uses TIMESTAMP and default CURRENT_TIMESTAMP policy, adjust DATETIME to TIMESTAMP as desired.
