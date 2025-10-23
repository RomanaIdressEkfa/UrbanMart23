# Project Rules and Guidelines

## Database Migration Rules

### 🚫 Never Run Full Migration Commands
1. **DO NOT** run the following commands:
   - `php artisan migrate`
   - `php artisan migrate:fresh`
   - `php artisan migrate:refresh`
   - `php artisan migrate:reset`

### ✅ Correct Migration Practices
1. **Always** run migrations for specific tables only:
   ```bash
   php artisan migrate --path=/database/migrations/YYYY_MM_DD_HHMMSS_migration_name.php
   ```

2. **Before running any migration:**
   - Backup the affected tables
   - Review the migration file content
   - Test in development environment first

### 🔄 Rolling Back Migrations
1. If you need to rollback, use specific migration:
   ```bash
   php artisan migrate:rollback --path=/database/migrations/YYYY_MM_DD_HHMMSS_migration_name.php
   ```

### 📝 Migration File Naming Convention
1. Use descriptive names for migration files
2. Include the table name in the migration filename
3. Use proper date and time prefix

### 🔍 Migration Review Process
1. Peer review required for all migration files
2. Document all schema changes
3. Update related models and controllers accordingly
