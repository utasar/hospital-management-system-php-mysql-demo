# AI Dr. Care - Installation & Setup Guide

## 📋 Table of Contents
1. [Prerequisites](#prerequisites)
2. [Database Setup](#database-setup)
3. [PHP Backend Configuration](#php-backend-configuration)
4. [ML Service Setup (Optional)](#ml-service-setup-optional)
5. [Web Server Configuration](#web-server-configuration)
6. [First Run](#first-run)
7. [Troubleshooting](#troubleshooting)

## Prerequisites

### Required Software
- **PHP**: Version 7.4 or higher
- **MySQL**: Version 5.7 or higher (or MariaDB 10.2+)
- **Web Server**: Apache 2.4+ or Nginx 1.18+
- **Composer**: PHP dependency manager (optional, for future dependencies)

### Optional Software
- **Python**: 3.8+ (for ML service)
- **Node.js**: 14+ (for future enhancements)

### System Requirements
- **RAM**: Minimum 2GB, Recommended 4GB+
- **Storage**: Minimum 500MB free space
- **OS**: Linux (Ubuntu 20.04+), macOS, or Windows 10/11

## Database Setup

### Step 1: Create Database
```sql
-- Login to MySQL
mysql -u root -p

-- Create database
CREATE DATABASE webdamn_demo CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create database user (recommended)
CREATE USER 'hms_user'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON webdamn_demo.* TO 'hms_user'@'localhost';
FLUSH PRIVILEGES;
```

### Step 2: Import Base Schema
The base hospital management system should already have its tables. If not, you'll need to import the original schema first.

### Step 3: Import AI Dr. Care Extensions
```bash
# Navigate to project directory
cd /path/to/hospital-management-system-php-mysql-demo

# Import AI Dr. Care schema
mysql -u hms_user -p webdamn_demo < database/ai_drcare_schema.sql
```

This will create:
- `hms_ai_diagnosis` - AI diagnosis records
- `hms_vital_signs` - Patient vital signs
- `hms_medications` - Medication database
- `hms_patient_medications` - Patient prescriptions
- `hms_audit_log` - HIPAA/GDPR compliance logs
- `hms_patient_consent` - Consent management
- `hms_medical_reports` - Medical documents

### Step 4: Verify Installation
```sql
-- Check if tables were created
USE webdamn_demo;
SHOW TABLES LIKE 'hms_%';

-- Check medication data
SELECT COUNT(*) FROM hms_medications;
-- Should show 7 medications
```

## PHP Backend Configuration

### Step 1: Configure Database Connection
Edit `config/Database.php`:

```php
<?php
session_start();
class Database{
    
    private $host  = 'localhost';
    private $user  = 'hms_user';  // Your database user
    private $password   = "your_secure_password";  // Your password
    private $database  = "webdamn_demo"; 
    
    // ... rest of the file
}
?>
```

### Step 2: Set Encryption Key (Optional but Recommended)
Set environment variable for encryption:

**Linux/macOS:**
```bash
export ENCRYPTION_KEY="your-very-secure-random-key-here-change-this-in-production"
```

**Windows:**
```cmd
set ENCRYPTION_KEY=your-very-secure-random-key-here-change-this-in-production
```

Or modify `utils/DataEncryption.php` to use a hardcoded key (less secure):
```php
private function getEncryptionKey() {
    return hash('sha256', 'your-secure-key-here', true);
}
```

### Step 3: Set File Permissions
```bash
# Make upload directories writable
mkdir -p uploads/medical_reports
mkdir -p logs
chmod 755 uploads
chmod 755 uploads/medical_reports
chmod 755 logs

# Ensure proper ownership
chown -R www-data:www-data uploads logs  # For Apache on Ubuntu
# OR
chown -R nginx:nginx uploads logs  # For Nginx
```

## ML Service Setup (Optional)

The AI features work without the ML service using the built-in PHP rules engine. However, for advanced ML capabilities:

### Step 1: Install Python Dependencies
```bash
# Navigate to ML service directory
cd ai_services/ml_service

# Create virtual environment (recommended)
python3 -m venv venv
source venv/bin/activate  # On Windows: venv\Scripts\activate

# Install dependencies
pip install -r requirements.txt
```

### Step 2: Start ML Service
```bash
# Make sure you're in the virtual environment
python app.py
```

The service will start on `http://localhost:5000`

### Step 3: Enable ML Service in PHP
Edit `config/APIConfig.php`:

```php
const ML_API_ENDPOINT = 'http://localhost:5000/api/';
const ML_API_ENABLED = true;  // Change to true
```

## Web Server Configuration

### Apache Configuration

#### Option 1: Using .htaccess (if mod_rewrite is enabled)
Create `.htaccess` in the project root:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    
    # Redirect to HTTPS (optional, for production)
    # RewriteCond %{HTTPS} off
    # RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
</IfModule>

# Prevent directory listing
Options -Indexes

# Protect sensitive files
<FilesMatch "^\.">
    Order allow,deny
    Deny from all
</FilesMatch>
```

#### Option 2: Virtual Host Configuration
Edit Apache config (e.g., `/etc/apache2/sites-available/ai-drcare.conf`):

```apache
<VirtualHost *:80>
    ServerName ai-drcare.local
    DocumentRoot /path/to/hospital-management-system-php-mysql-demo
    
    <Directory /path/to/hospital-management-system-php-mysql-demo>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/ai-drcare-error.log
    CustomLog ${APACHE_LOG_DIR}/ai-drcare-access.log combined
</VirtualHost>
```

Enable site and restart:
```bash
sudo a2ensite ai-drcare
sudo systemctl restart apache2
```

### Nginx Configuration

Create `/etc/nginx/sites-available/ai-drcare`:

```nginx
server {
    listen 80;
    server_name ai-drcare.local;
    root /path/to/hospital-management-system-php-mysql-demo;
    index index.php index.html;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
    
    location ~ /\.ht {
        deny all;
    }
    
    location /uploads {
        internal;
    }
}
```

Enable and restart:
```bash
sudo ln -s /etc/nginx/sites-available/ai-drcare /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

## First Run

### Step 1: Access the Application
Open your web browser and navigate to:
- Local development: `http://localhost/`
- Virtual host: `http://ai-drcare.local/`

### Step 2: Login
Use the default credentials provided on the login page:

**Admin:**
- Email: admin@webdamn.com
- Password: 123

**Doctor:**
- Email: mark@webdamn.com or goerge@webdamn.com
- Password: 123

**Patient:**
- Email: robert@webdamn.com or rhodes@webdamn.com
- Password: 123

### Step 3: Test AI Features

1. **AI Symptom Checker**
   - Login as a patient
   - Click "🤖 AI Symptom Checker" in the menu
   - Select symptoms and click "Analyze Symptoms"
   - Verify diagnosis results appear

2. **Vital Signs Tracker**
   - Click "📊 Vital Signs" in the menu
   - Enter vital signs data
   - Click "Save Vital Signs"
   - Verify data is saved and alerts appear

3. **Privacy Center**
   - Click "Privacy Center" or navigate to `privacy.php`
   - Test consent management
   - Try exporting data

### Step 4: Change Default Passwords
**Important:** Change all default passwords immediately!

```sql
-- Update passwords (use MD5 as that's what the system currently uses)
UPDATE hms_patients SET password = MD5('new_secure_password') WHERE email = 'robert@webdamn.com';
UPDATE hms_doctor SET password = MD5('new_secure_password') WHERE email = 'mark@webdamn.com';
-- Update other users...
```

**Note:** The system currently uses MD5 for passwords. For production, upgrade to bcrypt or Argon2.

## Troubleshooting

### Database Connection Errors

**Error:** "Failed to connect to MySQL"

**Solution:**
1. Verify MySQL is running: `sudo systemctl status mysql`
2. Check credentials in `config/Database.php`
3. Verify database exists: `mysql -u hms_user -p -e "SHOW DATABASES;"`

### Table Not Found Errors

**Error:** "Table 'hms_ai_diagnosis' doesn't exist"

**Solution:**
```bash
# Re-import AI schema
mysql -u hms_user -p webdamn_demo < database/ai_drcare_schema.sql
```

### Permission Denied Errors

**Error:** Can't write to uploads directory

**Solution:**
```bash
# Fix permissions
sudo chown -R www-data:www-data uploads/
sudo chmod -R 755 uploads/
```

### PHP Errors

**Error:** "Call to undefined function openssl_encrypt"

**Solution:**
```bash
# Install PHP OpenSSL extension
sudo apt-get install php-openssl
sudo systemctl restart apache2
```

### ML Service Not Working

**Error:** Connection refused to localhost:5000

**Solution:**
1. Check if ML service is running: `curl http://localhost:5000/api/health`
2. Start ML service: `cd ai_services/ml_service && python app.py`
3. Or disable ML service: Set `ML_API_ENABLED = false` in `config/APIConfig.php`

### AJAX Requests Failing

**Error:** 404 on API calls

**Solution:**
1. Check web server configuration
2. Verify API files exist in `/api/` directory
3. Check browser console for detailed errors

## Production Deployment Checklist

Before deploying to production:

- [ ] Change all default passwords
- [ ] Set secure encryption key
- [ ] Enable HTTPS/SSL
- [ ] Upgrade password hashing from MD5 to bcrypt
- [ ] Configure proper database backups
- [ ] Set up error logging
- [ ] Enable audit logging
- [ ] Configure firewall rules
- [ ] Set up monitoring and alerts
- [ ] Review and update privacy policy
- [ ] Test GDPR data export/deletion
- [ ] Perform security audit
- [ ] Load test the application
- [ ] Document custom configurations

## Next Steps

1. **Customize the System**
   - Add your hospital logo
   - Customize colors and branding
   - Add more diseases to the AI database

2. **Integrate External Services**
   - Set up Google Maps API for doctor locations
   - Integrate with medical databases
   - Connect to lab systems

3. **Enhance Security**
   - Implement 2-factor authentication
   - Add API rate limiting
   - Set up intrusion detection

4. **Scale the System**
   - Set up load balancing
   - Configure database replication
   - Implement caching (Redis/Memcached)

## Support

For issues and questions:
- Check the main README.md
- Review API documentation
- Check application logs in `logs/` directory

---

**AI Dr. Care** - Your healthcare companion 🏥💚
