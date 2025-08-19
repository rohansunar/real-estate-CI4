# 🏠 Real Estate Website - Developer Documentation

## 📋 Table of Contents
- [Project Overview](#project-overview)
- [System Requirements](#system-requirements)
- [Installation & Setup](#installation--setup)
- [Database Configuration](#database-configuration)
- [Default Credentials](#default-credentials)
- [Development Commands](#development-commands)
- [Project Structure](#project-structure)
- [API Endpoints](#api-endpoints)
- [Testing Guidelines](#testing-guidelines)
- [Deployment](#deployment)
- [Troubleshooting](#troubleshooting)

## 🎯 Project Overview

This is a modern, responsive real estate website built with **CodeIgniter 4** framework. The application features a public website for property listings and a dashboard for property management.

### Key Features
- **Responsive Design**: Mobile-first approach with Bootstrap 5
- **Property Management**: CRUD operations for properties
- **User Authentication**: Simple email/password authentication
- **Contact System**: Inquiry management and newsletter subscriptions
- **Blog System**: Content management for real estate articles
- **Agent Profiles**: Real estate agent management
- **Modern UI**: Clean, minimalistic design with smooth animations

### Technology Stack
- **Backend**: CodeIgniter 4 (PHP 8.0+)
- **Frontend**: Bootstrap 5, HTML5, CSS3, JavaScript (ES6+)
- **Database**: MySQL 8.0+
- **Icons**: Font Awesome 6
- **Images**: Unsplash API integration

## 🔧 System Requirements

### Minimum Requirements
- **PHP**: 8.0 or higher
- **MySQL**: 8.0 or higher
- **Apache/Nginx**: Web server with mod_rewrite enabled
- **Composer**: For dependency management
- **Node.js**: 16+ (optional, for asset compilation)

### Recommended Development Environment
- **XAMPP/WAMP/MAMP**: For local development
- **VS Code**: With PHP and CodeIgniter extensions
- **Git**: For version control
- **Postman**: For API testing

## 🚀 Installation & Setup

### 1. Clone the Repository
```bash
git clone <repository-url>
cd my-project
```

### 2. Install Dependencies
```bash
# Install PHP dependencies
composer install

# Set proper permissions (Linux/Mac)
chmod -R 755 writable/
chmod -R 755 public/
```

### 3. Environment Configuration
```bash
# Copy environment file
cp env .env

# Edit .env file with your configuration
nano .env
```

### 4. Configure Environment Variables
Update the `.env` file with your settings:

```env
# Application
CI_ENVIRONMENT = development
app.baseURL = 'http://localhost:8081'

# Database
database.default.hostname = localhost
database.default.database = real_estate_db
database.default.username = root
database.default.password = 
database.default.DBDriver = MySQLi

# Security
encryption.key = your-32-character-secret-key
```

## 🗄️ Database Configuration

### 1. Create Database
```sql
CREATE DATABASE real_estate_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 2. Run Migrations
```bash
# Run all migrations
php spark migrate

# Check migration status
php spark migrate:status

# Rollback migrations (if needed)
php spark migrate:rollback
```

### 3. Seed Database with Sample Data
```bash
# Run all seeders (recommended for development)
php spark db:seed DatabaseSeeder

# Run individual seeders
php spark db:seed UserSeeder
php spark db:seed PropertySeeder
php spark db:seed AgentSeeder
php spark db:seed ContactSeeder
```

### Database Schema Overview
- **users**: User accounts and authentication
- **properties**: Property listings with images and videos
- **agents**: Real estate agent profiles
- **contacts**: Customer inquiries and messages
- **newsletter**: Email subscriptions
- **blog_posts**: Blog articles and content

## 🔐 Default Credentials

### Admin Dashboard Access
```
Email: admin@whiterockrealtor.com
Password: admin123
```

### Test User Account
```
Email: user@example.com
Password: user123
```

### Additional Test Users
All test users have password: `user123`
- john@example.com
- jane@example.com
- mike@example.com
- sarah@example.com
- david@example.com
- lisa@example.com
- robert@example.com
- emily@example.com

## ⚡ Development Commands

### Start Development Server
```bash
# Start CodeIgniter development server
php spark serve

# Start on specific port
php spark serve --port=8080

# Start on specific host
php spark serve --host=0.0.0.0
```

### Database Operations
```bash
# Create new migration
php spark make:migration CreateTableName

# Create new seeder
php spark make:seeder TableSeeder

# Create new model
php spark make:model ModelName

# Create new controller
php spark make:controller ControllerName
```

### Cache Management
```bash
# Clear all caches
php spark cache:clear

# Clear specific cache
php spark cache:clear --driver=file
```

## 📁 Project Structure

```
my-project/
├── app/
│   ├── Controllers/          # Application controllers
│   │   ├── Home.php         # Homepage controller
│   │   ├── Properties.php   # Property management
│   │   ├── Auth.php         # Authentication
│   │   └── Dashboard.php    # Admin dashboard
│   ├── Models/              # Data models
│   │   ├── PropertyModel.php
│   │   ├── UserModel.php
│   │   └── ContactModel.php
│   ├── Views/               # View templates
│   │   ├── layouts/         # Layout templates
│   │   ├── home/           # Homepage views
│   │   ├── properties/     # Property views
│   │   └── dashboard/      # Dashboard views
│   └── Database/
│       ├── Migrations/     # Database migrations
│       └── Seeds/          # Database seeders
├── public/
│   ├── assets/
│   │   ├── css/           # Stylesheets
│   │   ├── js/            # JavaScript files
│   │   └── images/        # Static images
│   └── index.php          # Entry point
├── writable/              # Writable directories
└── vendor/                # Composer dependencies
```

## 🌐 API Endpoints

### Public Endpoints
```
GET  /                     # Homepage
GET  /properties           # Property listings
GET  /properties/{location}/{id}  # Single property view
GET  /about               # About page
GET  /blog                # Blog listings
GET  /blog/{id}           # Single blog post
GET  /contact             # Contact page
POST /contact             # Submit contact form
POST /newsletter          # Newsletter subscription
```

### Authentication Endpoints
```
GET  /auth/login          # Login page
POST /auth/login          # Process login
GET  /auth/register       # Registration page
POST /auth/register       # Process registration
GET  /auth/logout         # Logout user
```

### Dashboard Endpoints (Authenticated)
```
GET  /dashboard           # Dashboard home
GET  /dashboard/properties # Property management
POST /dashboard/properties # Create property
PUT  /dashboard/properties/{id} # Update property
DELETE /dashboard/properties/{id} # Delete property
GET  /dashboard/contacts  # Contact inquiries
GET  /dashboard/agents    # Agent management
GET  /dashboard/blog      # Blog management
```

### API Response Format
```json
{
  "success": true,
  "message": "Operation completed successfully",
  "data": {
    // Response data
  },
  "errors": []
}
```

## 🧪 Testing Guidelines

### Manual Testing Checklist

#### Homepage Testing
- [ ] Hero section displays correctly
- [ ] Property carousel functions properly
- [ ] Testimonials show exactly 3 items
- [ ] Newsletter subscription works
- [ ] All navigation links work
- [ ] Mobile responsiveness (320px - 768px)

#### Property Pages Testing
- [ ] Property listings display correctly
- [ ] Pagination works properly
- [ ] Property filters function
- [ ] Single property page loads
- [ ] Image gallery works
- [ ] Contact form submits successfully
- [ ] Similar properties display correctly

#### Authentication Testing
- [ ] Login with admin credentials
- [ ] Login with test user credentials
- [ ] Registration process works
- [ ] Logout functionality
- [ ] Protected routes redirect to login

#### Dashboard Testing
- [ ] Dashboard loads after login
- [ ] Property CRUD operations
- [ ] Contact inquiry management
- [ ] Agent management
- [ ] Blog post management
- [ ] Mobile dashboard navigation

### Browser Testing
Test on the following browsers:
- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers (iOS Safari, Chrome Mobile)

### Performance Testing
- Page load times < 3 seconds
- Image optimization
- CSS/JS minification
- Database query optimization

## 🚀 Deployment

### Production Environment Setup

#### 1. Server Requirements
- PHP 8.0+ with required extensions
- MySQL 8.0+
- Apache/Nginx with mod_rewrite
- SSL certificate (recommended)

#### 2. Environment Configuration
```env
CI_ENVIRONMENT = production
app.baseURL = 'https://yourdomain.com'
app.forceGlobalSecureRequests = true

# Database (production)
database.default.hostname = your-db-host
database.default.database = your-db-name
database.default.username = your-db-user
database.default.password = your-secure-password

# Security
encryption.key = your-production-32-char-key
```

#### 3. Deployment Steps
```bash
# 1. Upload files to server
rsync -avz --exclude='.git' ./ user@server:/path/to/website/

# 2. Set permissions
chmod -R 755 public/
chmod -R 755 writable/

# 3. Install dependencies
composer install --no-dev --optimize-autoloader

# 4. Run migrations
php spark migrate --env=production

# 5. Seed database (optional)
php spark db:seed DatabaseSeeder --env=production
```

#### 4. Apache Configuration (.htaccess)
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php/$1 [L]

# Security headers
Header always set X-Content-Type-Options nosniff
Header always set X-Frame-Options DENY
Header always set X-XSS-Protection "1; mode=block"
```

## 🔧 Troubleshooting

### Common Issues

#### 1. Database Connection Error
```
Error: Unable to connect to the database
```
**Solution:**
- Check database credentials in `.env`
- Ensure MySQL service is running
- Verify database exists
- Check user permissions

#### 2. Migration Errors
```
Error: Duplicate key name 'email'
```
**Solution:**
- Run `php spark migrate:rollback`
- Check migration files for conflicts
- Run `php spark migrate` again

#### 3. Permission Errors
```
Error: The system cannot write to the file
```
**Solution:**
```bash
chmod -R 755 writable/
chown -R www-data:www-data writable/
```

#### 4. Testimonials Not Showing
**Solution:**
- Check if you're on the homepage
- Verify testimonials section in `layouts/main.php`
- Check browser console for JavaScript errors

#### 5. Properties Not Loading
**Solution:**
- Run database seeders: `php spark db:seed PropertySeeder`
- Check database connection
- Verify property model and controller

### Debug Mode
Enable debug mode for development:
```env
CI_ENVIRONMENT = development
```

### Logging
Check logs in `writable/logs/` for detailed error information.

## 📞 Support

### Documentation
- [CodeIgniter 4 Documentation](https://codeigniter.com/user_guide/)
- [Bootstrap 5 Documentation](https://getbootstrap.com/docs/5.0/)

### Contact
For technical support or questions about this project, please contact the development team.

---

**Last Updated:** August 4, 2025
**Version:** 1.0
**Framework:** CodeIgniter 4.6.2
