# FarmStat MVC Restructure

## Overview
This project has been restructured to follow the MVC (Model-View-Controller) pattern with proper separation of concerns, PHP 8 compatibility, and organized asset management.

## Structure

```
farmstat/
├── app/
│   ├── config/
│   │   ├── config.php          # Application configuration
│   │   └── database.php        # Database connection (PDO)
│   ├── core/
│   │   ├── Controller.php      # Base controller class
│   │   ├── Router.php          # Routing system
│   │   └── DatabaseCompat.php  # mysqli compatibility layer
│   ├── controllers/
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   ├── FarmerController.php
│   │   ├── CampaignController.php
│   │   └── HomeController.php
│   ├── models/
│   │   ├── User.php
│   │   ├── Farmer.php
│   │   └── Campaign.php
│   └── views/
│       ├── layouts/
│       │   ├── header.php
│       │   └── footer.php
│       ├── auth/
│       │   ├── login.php
│       │   └── register.php
│       ├── dashboard/
│       │   ├── admin.php
│       │   └── farmer.php
│       └── home/
│           └── index.php
├── assets/
│   ├── css/
│   │   ├── styles.css          # Main styles
│   │   ├── auth.css            # Auth page styles
│   │   └── admin-css.css       # Admin dashboard styles
│   └── js/
│       ├── script.js           # Main JavaScript
│       ├── adminjava.js         # Admin JavaScript
│       └── s.js                 # Additional scripts
├── index.php                    # Main router entry point
├── .htaccess                    # URL rewriting rules
└── farmstats_db.sql             # Database schema

```

## PHP 8 Compatibility

### Changes Made:
1. **PDO Standardization**: All new code uses PDO instead of mysqli
2. **Type Declarations**: Added proper type hints (PHP 8 compatible)
3. **Null Safety**: Used null coalescing operators and proper null checks
4. **Removed Deprecated Functions**: Updated all deprecated PHP functions
5. **Error Handling**: Improved exception handling

### Database Access:
- **New Code**: Use `Database::getConnection()` which returns PDO
- **Legacy Code**: Use `DatabaseCompat::getConnection()` for mysqli compatibility

## Installation

1. **Database Setup**:
   - Import `farmstats_db.sql` into phpMyAdmin
   - Or let the application create it automatically on first run

2. **Configuration**:
   - Edit `app/config/config.php` to set your database credentials
   - Update `APP_URL` if your installation path differs

3. **Web Server**:
   - Ensure mod_rewrite is enabled (for .htaccess)
   - Point document root to the project directory

## Routes

### Public Routes:
- `/` - Home page
- `/login` - Login page (GET/POST)
- `/register` - Registration page (GET/POST)

### Protected Routes:
- `/dashboard` - User dashboard
- `/admin/dashboard` - Admin dashboard

### API Routes:
- `/api/farmers` - Get/create farmers (GET/POST)
- `/api/campaigns` - Get/create campaigns (GET/POST)

## Key Features

### MVC Pattern:
- **Models**: Data access layer (User, Farmer, Campaign)
- **Views**: Presentation layer (PHP templates)
- **Controllers**: Business logic and request handling

### Security:
- Password hashing using `password_hash()`
- Prepared statements (PDO)
- Session management
- CSRF protection ready

### Asset Organization:
- CSS files in `/assets/css/`
- JavaScript files in `/assets/js/`
- Proper separation of concerns

## Migration Notes

### For Developers:
1. **New Features**: Use the MVC structure
2. **Legacy Files**: Old PHP files still work but should be migrated
3. **Database**: Prefer PDO over mysqli for new code

### Breaking Changes:
- Old direct file access (e.g., `login.php`) now routes through `index.php`
- Database connection method changed from mysqli to PDO
- File structure reorganized

## Default Credentials

After importing the SQL file:
- **Admin**: admin@farmstat.com / password
- **Farmer**: farmer@farmstat.com / password

**Note**: Change these passwords in production!

## Troubleshooting

### Routes Not Working:
- Check `.htaccess` is enabled
- Verify mod_rewrite is active
- Check Apache/Nginx configuration

### Database Connection Issues:
- Verify credentials in `app/config/config.php`
- Check MySQL service is running
- Ensure database exists or let app create it

### PHP 8 Errors:
- Check PHP version: `php -v` (should be 8.0+)
- Review error logs for specific issues
- Ensure all extensions are installed (PDO, mysqli)

## Next Steps

1. Migrate remaining legacy files to MVC structure
2. Add more comprehensive error handling
3. Implement CSRF protection
4. Add unit tests
5. Optimize database queries
6. Add API documentation

## Support

For issues or questions, refer to the code comments or create an issue in the repository.

