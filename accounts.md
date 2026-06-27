# User Accounts Documentation

## Overview

This document lists all user accounts in the KP System Parking application with their roles, credentials, and permissions.

**System Date**: 2026-06-27

---

## User Roles & Permissions

### 1. **Admin** 👨‍💼
- Full system access
- Can manage all users, customers, vehicles, and parking submissions
- Access to all reports and configurations
- Can approve/reject parking submissions at all levels (both operation manager and parking department approvals)
- Can view all parking submissions and usage
- Can generate reports
- Can manage parking quota allocation

### 2. **Employee** 👤
- Can submit parking requests
- Can view own parking submissions
- Limited access to dashboard
- Cannot approve or manage other submissions

### 3. **Operation Manager** 🏢
- Can review and approve parking submissions (first approval level)
- Can manage parking quotas
- Can view reports
- Limited user management access

---

## Active User Accounts

### ADMIN ROLE
| ID | Username | Full Name | Email | Password |
|----|----------|-----------|-------|----------|
| 17 | admin_system | System Administrator | admin@parkingsystem.com | `admin123` |
| 1 | adminku | udin petot | petot@gmail.com | `password` |
| 7 | pandudept | pandu dept | pandudept@gmail.com | `password` |

**Permissions**: 
- Full system access
- Manage all users, customers, vehicles
- Approve/reject all parking submissions
- Manage parking quotas
- Generate reports
- View parking usage statistics

---

### EMPLOYEE ROLE
| ID | Username | Full Name | Email | Password |
|----|----------|-----------|-------|----------|
| 5 | pandukaryawan | pandu karyawan | pandukaryawan@gmail.com | `password` |
| 9 | pandukaryawan1 | pandukaryawan1 | pandukaryawan1@gmail.com | `password` |

**Permissions**: 
- Submit parking requests
- View own submissions
- View personal dashboard

---

### OPERATION MANAGER ROLE
| ID | Username | Full Name | Email | Password |
|----|----------|-----------|-------|----------|
| 6 | pandumanager | pandu manager | pandumanager@gmail.co | `password` |
| 14 | pandu_mgr | Manager Full Name | manager@example.com | `password` |
| 16 | pandu_mngr | Manager Full Name | pandu_mgr@example.com | `password` |

**Permissions**:
- Review parking submissions
- First-level approval
- Manage parking quotas
- View reports
- Manage operation team

---


---

## Account Management

### Creating New Accounts

**Via Web Interface:**
1. Login as Admin
2. Navigate to **Users Management** (`/users`)
3. Click **Add User**
4. Fill in the form:
   - Username (unique, alphanumeric)
   - Password (will be hashed automatically)
   - Full Name
   - Email
   - Role (select from available roles)
   - Active status (checked = active)
5. Click **Submit**

**Via SQL:**
```sql
INSERT INTO users (username, password, full_name, email, role, is_active) 
VALUES (
  'username',
  'hashed_password_here',
  'Full Name',
  'email@example.com',
  'employee',
  1
);
```

### Generating Hashed Passwords

Use PHP to generate bcrypt hashed passwords:

```bash
php -r "echo password_hash('your_password', PASSWORD_BCRYPT);"
```

Example:
```bash
php -r "echo password_hash('MyPassword123', PASSWORD_BCRYPT);"
# Output: $2y$10$abcdef1234567890123456789012345678901234567890123456
```

### Modifying Accounts

**Via Web Interface:**
1. Login as Admin
2. Go to **Users Management** (`/users`)
3. Click **Edit** next to the user
4. Update fields as needed
5. To change password, fill in the password field (optional)
6. Click **Update**

**Via SQL:**
```sql
UPDATE users 
SET full_name = 'New Name', email = 'new@email.com'
WHERE username = 'username';

-- To update password:
UPDATE users 
SET password = 'new_hashed_password'
WHERE username = 'username';
```

### Deactivating Accounts

To deactivate a user without deleting them:

```sql
UPDATE users SET is_active = 0 WHERE id = user_id;
```

To reactivate:

```sql
UPDATE users SET is_active = 1 WHERE id = user_id;
```

---

## Default Test Credentials

### Quick Test Login

```
Role: Admin
Username: admin_system
Password: admin123
```

```
Role: Operation Manager
Username: pandumanager
Password: (set during seeding)
```

```
Role: Employee
Username: pandukaryawan
Password: (set during seeding)
```

---

## Security Best Practices

1. **Change Default Passwords**
   - After initial setup, change all default passwords
   - Use strong, unique passwords (min 8 characters, mixed case, numbers, symbols)

2. **Admin Account**
   - Keep admin credentials secure
   - Don't share admin credentials
   - Create separate admin accounts for different administrators

3. **Password Reset**
   - If a user forgets password, admin must reset it
   - User should change temporary password on first login

4. **Account Cleanup**
   - Regularly review active accounts
   - Deactivate unused accounts instead of deleting
   - Archive inactive users after 90 days

5. **Audit Trail**
   - Monitor who logs in and when
   - Check update logs for account modifications
   - Review submission approval history

---

## User Table Schema

```sql
CREATE TABLE users (
  id INT NOT NULL AUTO_INCREMENT,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  full_name VARCHAR(100) NOT NULL,
  email VARCHAR(100),
  role ENUM('admin','employee','operation_manager') NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME,
  is_active TINYINT(1) DEFAULT 1,
  PRIMARY KEY (id),
  UNIQUE KEY (username)
);
```

---

## Troubleshooting

### Can't Login
- Verify username and password are correct
- Check if account is active (`is_active = 1`)
- Clear browser cache and cookies
- Try incognito/private mode

### Forgot Password
- Contact admin to reset password
- Admin can update password via Users Management page
- User will receive temporary password

### Permission Denied
- Check user's role
- Verify role has access to that feature
- Contact admin if you need higher permissions

### Account Locked
- Accounts don't auto-lock after failed attempts (no lockout implemented)
- Contact admin if account has issues

---

## Related Files

- **Controller**: `/app/Controllers/Users.php`
- **Model**: `/app/Models/UserModel.php`
- **Auth Controller**: `/app/Controllers/Auth.php`
- **Migrations**: `/app/Database/Migrations/2025_06_27_000001_CreateUsersTable.php`
- **Seeders**: `/app/Database/Seeds/UserSeeder.php`

---

## Last Updated
- **Date**: 2026-06-27
- **Updated By**: System Administrator
- **Total Accounts**: 8 active users
- **Roles Configured**: 3 (admin, employee, operation_manager)
- **Note**: Admin role now includes both admin and parking department functions

---

## Notes

- All passwords are hashed using bcrypt (PASSWORD_BCRYPT)
- Email addresses should be unique (though not enforced in DB)
- `created_at` is automatically set on user creation
- `updated_at` should be manually set during updates
- Users with `is_active = 0` cannot login but records are preserved
