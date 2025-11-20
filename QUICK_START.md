# Quick Start Guide

## ✅ Cleanup Complete

All conflicting old PHP files have been removed:
- ❌ Removed: login.php, register.php, logout.php, dashboard.php, admin-dashboard.php, farmer_dashboard.php
- ❌ Removed: add_farmer.php, add_user.php, create_campaign.php
- ❌ Removed: get_*.php API files (now handled by controllers)
- ❌ Removed: database.php, helpers.php (moved to app structure)
- ✅ Kept: index.html (used by home view), index.php (main router)

## 🚀 Running the Application

### Prerequisites:
1. ✅ XAMPP is running (Apache + MySQL)
2. ✅ Database is set up (import `farmstats_db.sql` if not done)

### Steps:

1. **Import Database** (if not already done):
   - Open phpMyAdmin: http://localhost/phpmyadmin
   - Create database `farmstats_db` or import `farmstats_db.sql`
   - Default credentials after import:
     - Admin: `admin@farmstat.com` / `password`
     - Farmer: `farmer@farmstat.com` / `password`

2. **Access the Application**:
   - Open browser: `http://localhost/farmstat/`
   - Or if in htdocs root: `http://localhost/` (if farmstat is in htdocs root)

3. **Test Routes**:
   - Home: `http://localhost/farmstat/`
   - Login: `http://localhost/farmstat/login`
   - Register: `http://localhost/farmstat/register`
   - Dashboard: `http://localhost/farmstat/dashboard` (requires login)
   - Admin: `http://localhost/farmstat/admin/dashboard` (requires admin login)

## 📁 New Structure

```
farmstat/
├── index.php              # Main router (entry point)
├── app/
│   ├── config/           # Configuration files
│   ├── controllers/      # MVC Controllers
│   ├── models/           # Data models
│   ├── views/            # View templates
│   ├── core/             # Core classes (Router, Controller)
│   └── helpers/          # Helper functions
├── assets/
│   ├── css/              # Stylesheets
│   └── js/               # JavaScript files
└── farmstats_db.sql      # Database schema
```

## 🔧 Troubleshooting

### Database Connection Error:
- Check MySQL is running in XAMPP Control Panel
- Verify database `farmstats_db` exists
- Check credentials in `app/config/config.php`:
  ```php
  define('DB_HOST', 'localhost');
  define('DB_NAME', 'farmstats_db');
  define('DB_USER', 'root');
  define('DB_PASS', '');  // Your MySQL password
  ```

### Routes Not Working:
- Ensure `.htaccess` is enabled in Apache
- Check mod_rewrite is enabled
- Verify file permissions

### 404 Errors:
- Check BASE_URL is correctly detected
- Verify `.htaccess` file exists
- Check Apache error logs

## ✨ Features

- ✅ MVC Architecture
- ✅ PHP 8 Compatible
- ✅ Clean URLs (no .php extensions)
- ✅ PDO Database Access
- ✅ Session Management
- ✅ Password Hashing
- ✅ Organized Assets

## 🧪 Test the Setup

Run the test script:
```bash
php test_app.php
```

This will verify:
- Configuration loading
- Database connection
- Autoloader functionality
- File structure

## 📝 Next Steps

1. Test login/registration
2. Verify dashboard loads
3. Test API endpoints (`/api/farmers`, `/api/campaigns`)
4. Customize views as needed
5. Add more features using MVC pattern

