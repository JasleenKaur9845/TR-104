# BackupVault — Centralised Backup Monitoring & Management System

## Project Files

| File | Purpose |
|---|---|
| `backup_monitor.sql` | MySQL schema — run once to set up all tables |
| `config.php` | Database credentials |
| `auth.php` | Session guard — protects all pages |
| `login.php` | Admin login page |
| `logout.php` | Destroys session, redirects to login |
| `index.php` | Main UI (Dashboard, Servers, Logs, Settings) |
| `receive.php` | Endpoint: receives curl POSTs from backup.sh |
| `start_backup.php` | Endpoint: triggers backup.sh from the UI form |
| `backup.sh` | Shell script: runs backup, sends status updates |
| `api_backups.php` | JSON API: jobs + stats for dashboard |
| `api_logs.php` | JSON API: paginated logs with search/filter |
| `api_servers.php` | JSON API: CRUD for authorized server IPs |
| `change_password.php` | API: update admin password |
| `export_csv.php` | Downloads current filtered logs as CSV |

---

## Quick Setup (Ubuntu/Debian)

### 1. Install xampp

### 2. Import database
```bash
sudo mysql < /var/www/html/backup_system/backup_monitor.sql
```

### 3. Create MySQL user (optional but recommended)
```sql
sudo mysql
CREATE USER 'backupuser'@'localhost' IDENTIFIED BY 'yourpassword';
GRANT ALL PRIVILEGES ON backup_monitor.* TO 'backupuser'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 4. Configure database connection
Edit `config.php`:
```php
define('DB_USER', 'backupuser');
define('DB_PASS', 'yourpassword');
```

### 5. Deploy files
```bash
sudo cp -r backup_system/ /var/www/html/
sudo chown -R www-data:www-data /var/www/html/backup_system/
sudo chmod +x /var/www/html/backup_system/backup.sh
```

### 6. Configure backup.sh
Edit line 11 of `backup.sh`:
```bash
RECEIVE_URL="http://127.0.0.1/backup_system/receive.php"
```

## How It Works

### Monitoring Flow
```
backup.sh  →  curl POST  →  receive.php  →  MySQL  →  dashboard (polls every 2s)
```

### Trigger Flow
```
UI Form  →  start_backup.php  →  backup.sh (background)
         →  RUNNING updates  →  receive.php  →  MySQL  →  dashboard
```

---


**Change via Settings → Change Password after first login.**

---

## Manual Test (curl)
```bash
curl -X POST http://localhost/backup_system/receive.php \
  -d "source_ip=127.0.0.1" \
  -d "destination_ip=192.168.1.50" \
  -d "source_path=/var/www/html" \
  -d "destination_path=/backups/www" \
  -d "status=SUCCESS" \
  -d "percentage=100" \
  -d "reason=Manual test"
```

## Enable Real SCP in backup.sh
Uncomment and configure in `backup.sh`:
```bash
DEST_USER="backup"
scp -r "$DEST_USER@$SOURCE_IP:$SOURCE_PATH" "$DEST_USER@$DEST_IP:$DEST_PATH/"
```
Set up SSH key-based auth for passwordless operation.
