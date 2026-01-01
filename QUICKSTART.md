# Quick Start Guide - AI Dr. Care

Get up and running with AI Dr. Care in under 10 minutes!

## Prerequisites

- PHP 7.4+ and MySQL 5.7+ installed
- Web server (Apache/Nginx) running
- Basic command line knowledge

## 5-Minute Setup

### 1. Get the Code (1 min)

```bash
git clone https://github.com/utasar/hospital-management-system-php-mysql-demo.git
cd hospital-management-system-php-mysql-demo
```

### 2. Database Setup (2 min)

```bash
# Login to MySQL
mysql -u root -p

# In MySQL prompt:
CREATE DATABASE webdamn_demo CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Import schema (if base tables don't exist, you'll need the original schema first)
# Then import AI extensions:
USE webdamn_demo;
source database/ai_drcare_schema.sql;
exit;
```

### 3. Configure Database (1 min)

Edit `config/Database.php`:

```php
private $host  = 'localhost';
private $user  = 'root';  // Your MySQL username
private $password = "";   // Your MySQL password
private $database = "webdamn_demo";
```

### 4. Start the Application (1 min)

**Using PHP Built-in Server (Development):**
```bash
php -S localhost:8000
```

**Or place in your web server directory:**
```bash
# For Apache
sudo cp -r . /var/www/html/ai-drcare/

# For Nginx
sudo cp -r . /usr/share/nginx/html/ai-drcare/
```

### 5. Access and Login

Open your browser: `http://localhost:8000/` (or your configured URL)

**Default Login Credentials:**

**Patient Account:**
- Email: `robert@webdamn.com`
- Password: `123`

**Doctor Account:**
- Email: `mark@webdamn.com`
- Password: `123`

**Admin Account:**
- Email: `admin@webdamn.com`
- Password: `123`

## Try the AI Features

### 1. AI Symptom Checker (2 min)

1. Login as a patient (robert@webdamn.com)
2. Click **"🤖 AI Symptom Checker"** in the menu
3. Select symptoms:
   - Click on symptom badges (e.g., "fever", "cough", "headache")
   - Or type custom symptoms
4. Click **"Analyze Symptoms"**
5. View AI-powered diagnosis results with confidence scores
6. Click **"Find Specialist Doctor"** to see recommended doctors

### 2. Vital Signs Tracker (2 min)

1. Click **"📊 Vital Signs"** in the menu
2. Enter your vital signs:
   - Temperature: 37.5
   - Blood Pressure: 120/80
   - Heart Rate: 75
   - Oxygen Saturation: 98
3. Click **"Save Vital Signs"**
4. View health alerts and charts

### 3. Doctor Recommendation (1 min)

1. From the AI Symptom Checker results
2. Click **"Find Specialist Doctor"**
3. Browse recommended doctors by specialization
4. Check availability and fees
5. Click **"Book Appointment"**

## Optional: Python ML Service

For advanced AI capabilities:

```bash
# Navigate to ML service
cd ai_services/ml_service

# Install dependencies
pip install -r requirements.txt

# Start ML service
python app.py
```

ML service runs on `http://localhost:5000`

Enable in `config/APIConfig.php`:
```php
const ML_API_ENABLED = true;
```

## Common Issues & Solutions

### Database Connection Failed
```
Error: Failed to connect to MySQL
```
**Solution:** Check MySQL credentials in `config/Database.php`

### Table Not Found
```
Error: Table 'hms_ai_diagnosis' doesn't exist
```
**Solution:** Run the schema script:
```bash
mysql -u root -p webdamn_demo < database/ai_drcare_schema.sql
```

### Permission Denied
```
Error: Can't write to directory
```
**Solution:**
```bash
chmod 755 uploads/ logs/
```

### Port Already in Use
```
Error: Address already in use
```
**Solution:** Use a different port:
```bash
php -S localhost:8080
```

## Next Steps

### Explore More Features

1. **Privacy Center**
   - Go to `privacy.php`
   - Manage consent preferences
   - Export your data (GDPR)

2. **Appointment Management**
   - Book appointments
   - View appointment history
   - Check doctor availability

3. **Patient Dashboard**
   - View health summary
   - Access AI features
   - Track vital signs trends

### Customize the System

1. **Change Branding**
   - Update logos in `images/`
   - Modify colors in CSS files
   - Edit text in PHP templates

2. **Add Diseases**
   - Edit `class/AISymptomAnalyzer.php`
   - Add disease patterns
   - Update symptom lists

3. **Add Medications**
   ```sql
   INSERT INTO hms_medications (name, generic_name, category, description, common_uses)
   VALUES ('Medication Name', 'Generic Name', 'Category', 'Description', 'Uses');
   ```

### For Production

⚠️ **Important:** Before going to production:

1. **Change all default passwords**
   ```sql
   UPDATE hms_patients SET password = MD5('new_password') WHERE email = 'robert@webdamn.com';
   -- TODO: Upgrade to bcrypt
   ```

2. **Set encryption key**
   ```bash
   export ENCRYPTION_KEY="your-secure-random-key-here"
   ```

3. **Enable HTTPS**
   - Get SSL certificate
   - Configure web server for HTTPS
   - Force HTTPS redirects

4. **Review security checklist** in `SECURITY.md`

## Learning Resources

- **Full Documentation:** `README.md`
- **Installation Guide:** `INSTALLATION.md`
- **Feature List:** `FEATURES.md`
- **Security Info:** `SECURITY.md`
- **ML Service:** `ai_services/ml_service/README.md`

## Demo Scenarios

### Scenario 1: Patient with Flu Symptoms

1. Login as patient
2. Go to AI Symptom Checker
3. Select: fever, cough, body ache, headache, fatigue
4. Analyze - should suggest "Influenza" with high confidence
5. Find specialist - filter for "General Physician"
6. Book appointment

### Scenario 2: Health Monitoring

1. Login as patient
2. Go to Vital Signs
3. Enter daily readings for 3-4 days
4. View charts showing trends
5. Check health alerts for any abnormal readings

### Scenario 3: Doctor Workflow

1. Login as doctor (mark@webdamn.com)
2. View patient list
3. Check patient's AI diagnosis history
4. Review vital signs trends
5. Make informed consultation decisions

## Getting Help

- Check documentation files
- Review code comments
- Check browser console for errors
- Verify database connection
- Ensure proper file permissions

## Quick Commands Reference

```bash
# Start PHP server
php -S localhost:8000

# Import database
mysql -u root -p webdamn_demo < database/ai_drcare_schema.sql

# Check MySQL status
sudo systemctl status mysql

# Restart web server
sudo systemctl restart apache2  # Apache
sudo systemctl restart nginx    # Nginx

# View PHP error log
tail -f /var/log/apache2/error.log  # Apache
tail -f /var/log/nginx/error.log    # Nginx

# Fix permissions
chmod 755 uploads/ logs/
chown -R www-data:www-data uploads/ logs/  # Linux
```

---

**You're all set!** 🎉

Start exploring AI Dr. Care's intelligent healthcare features. For detailed information, check the comprehensive documentation in `README.md`.

**AI Dr. Care** - Your intelligent healthcare companion 🏥🤖
