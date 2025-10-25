# Bitcoin Forensic Analysis Tool - Setup Instructions

## Quick Setup for New Device

When moving this project to a new device, you only need to update the database configuration in **ONE FILE**:

### 1. Edit Database Configuration
Open `config.php` and update the database credentials as needed.

### 2. Start the Server
Run `start-server.bat` (Windows) or start your PHP server manually:
```bash
php -S localhost:8000
```

### 3. Open the Application
Navigate to: `http://localhost:8000/project-with-database.html`

## Files That Use Database Configuration
- `api.php` - Uses `config.php` automatically
- `transaction-list.html` - Uses API (no direct database connection)

## Benefits
✅ **Single point of configuration** - Only edit `config.php`  
✅ **Easy deployment** - Copy project and update one file  
✅ **No scattered passwords** - All credentials in one place  
✅ **Consistent connections** - All files use same configuration  

## Troubleshooting
If you get connection errors:
1. Check PostgreSQL is running
2. Verify credentials in `config.php`
3. Ensure database 'bitcoin' exists
4. Check firewall/port settings