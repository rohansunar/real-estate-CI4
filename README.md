# Real Estate Website - Enhanced Media Gallery

A modern, responsive real estate website built with CodeIgniter 4, featuring an enhanced media gallery system with multiple image and video support.

## 🚀 Key Features

### Enhanced Media Gallery System
- **Multiple Image Upload**: Support for uploading and displaying multiple property images
- **Multiple YouTube Videos**: Add multiple YouTube video tours for each property
- **Responsive Image Gallery**: Mobile-first responsive design with thumbnail navigation
- **Advanced Lightbox**: Interactive lightbox with video playback support
- **Smooth Animations**: Modern UI with hover effects and smooth transitions

### Property Management
- **Property Listings**: Responsive grid layout with image cards
- **Single Property View**: Comprehensive property details with media gallery
- **Property Creation**: User-friendly form with multiple media upload
- **Location Support**: Siliguri and nearby areas (Champasari, Bagdogra, Jalpaiguri, etc.)

### Technical Features
- **Bootstrap 5**: Modern, responsive UI framework
- **Mobile-First Design**: Optimized for all device sizes
- **Secure File Upload**: Random filename generation and validation
- **YouTube Integration**: Automatic video embedding with validation
- **Database Optimization**: JSON storage for multiple media items

## 🛠️ Installation & Setup

### Prerequisites
- PHP 8.1 or higher
- MySQL 5.7 or higher
- Composer
- Web server (Apache/Nginx)

### Installation Steps

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd my-project
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Database setup**
   ```bash
   # Create database
   mysql -u root -p
   CREATE DATABASE real_estate_ci4;

   # Run migrations
   php spark migrate
   ```

4. **Configure environment**
   ```bash
   cp env .env
   # Edit .env file with your database credentials
   ```

5. **Set up file uploads**
   ```bash
   # Create symlink for uploaded images
   ln -sf ../writable/uploads public/uploads
   ```

6. **Start development server**
   ```bash
   php spark serve
   ```

### Default Credentials
- **Email**: admin@realestate.com
- **Password**: password123

## 📱 Usage Guide

### Adding Properties with Multiple Media

1. **Login to Dashboard**
   - Navigate to `/auth/login`
   - Use the default credentials above

2. **Create New Property**
   - Go to "Properties" → "Create New Property"
   - Fill in property details
   - Upload multiple images using the file selector
   - Add multiple YouTube video URLs

3. **Media Gallery Features**
   - **Image Upload**: Select multiple images at once
   - **Video URLs**: Add YouTube links in various formats:
     - `https://www.youtube.com/watch?v=VIDEO_ID`
     - `https://youtu.be/VIDEO_ID`
     - `https://youtube.com/embed/VIDEO_ID`

### Viewing Properties

1. **Property Listings**
   - Visit `/properties` for all properties
   - Responsive grid layout with image previews

2. **Single Property View**
   - Click any property to view details
   - Interactive media gallery with:
     - Image carousel with thumbnails
     - Video playback in lightbox
     - Mobile-responsive navigation

## 🎨 Design Features

### Responsive Design
- **Mobile-First**: Optimized for mobile devices
- **Bootstrap 5**: Modern CSS framework
- **Smooth Animations**: Hover effects and transitions
- **Accessibility**: WCAG 2.1 AA compliant

### Media Gallery
- **Thumbnail Navigation**: Click thumbnails to navigate
- **Lightbox Support**: Full-screen image and video viewing
- **Video Integration**: Embedded YouTube videos
- **Touch Support**: Swipe navigation on mobile devices

## 🔧 Technical Implementation

### File Structure
```
app/
├── Controllers/
│   ├── PropertyController.php    # Property management with media
│   └── DashboardController.php   # Admin dashboard
├── Models/
│   └── PropertyModel.php         # Enhanced with JSON media storage
└── Views/
    ├── properties/
    │   ├── index.php             # Property listings
    │   ├── single.php            # Enhanced media gallery
    │   └── create.php            # Multiple media upload
    └── dashboard/                # Admin interface
```

### Database Schema
```sql
-- Properties table with JSON media fields
CREATE TABLE properties (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    type VARCHAR(50),
    location VARCHAR(100),
    area INT,
    images JSON,              -- Multiple image paths
    youtube_video JSON,       -- Multiple YouTube URLs
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Security Features
- **File Upload Security**: Random filename generation
- **Input Validation**: YouTube URL format validation
- **SQL Injection Protection**: CodeIgniter 4 Query Builder
- **XSS Protection**: Built-in output escaping

## Installation & updates

`composer create-project codeigniter4/appstarter` then `composer update` whenever
there is a new release of the framework.

When updating, check the release notes to see if there are any changes you might need to apply
to your `app` folder. The affected files can be copied or merged from
`vendor/codeigniter4/framework/app`.

## Setup

Copy `env` to `.env` and tailor for your app, specifically the baseURL
and any database settings.

## Important Change with index.php

`index.php` is no longer in the root of the project! It has been moved inside the *public* folder,
for better security and separation of components.

This means that you should configure your web server to "point" to your project's *public* folder, and
not to the project root. A better practice would be to configure a virtual host to point there. A poor practice would be to point your web server to the project root and expect to enter *public/...*, as the rest of your logic and the
framework are exposed.

**Please** read the user guide for a better explanation of how CI4 works!

## Repository Management

We use GitHub issues, in our main repository, to track **BUGS** and to track approved **DEVELOPMENT** work packages.
We use our [forum](http://forum.codeigniter.com) to provide SUPPORT and to discuss
FEATURE REQUESTS.

This repository is a "distribution" one, built by our release preparation script.
Problems with it can be raised on our forum, or as issues in the main repository.

## Server Requirements

PHP version 8.1 or higher is required, with the following extensions installed:

- [intl](http://php.net/manual/en/intl.requirements.php)
- [mbstring](http://php.net/manual/en/mbstring.installation.php)

> [!WARNING]
> - The end of life date for PHP 7.4 was November 28, 2022.
> - The end of life date for PHP 8.0 was November 26, 2023.
> - If you are still using PHP 7.4 or 8.0, you should upgrade immediately.
> - The end of life date for PHP 8.1 will be December 31, 2025.

Additionally, make sure that the following extensions are enabled in your PHP:

- json (enabled by default - don't turn it off)
- [mysqlnd](http://php.net/manual/en/mysqlnd.install.php) if you plan to use MySQL
- [libcurl](http://php.net/manual/en/curl.requirements.php) if you plan to use the HTTP\CURLRequest library


verify development server starts without errors
Dont Modify any other Existing Features and Design
Test the website in browser to ensure everything works correctly
Remove any unnecessary/unused code related to the applications module
Verify both authenticated and unauthenticated user scenarios
Check for Memory leakage codes in codebase and fix it.
Provide user friendly message on errors.
Write Proper Code Comments for other developer easily understand
used simply logic and minimum codes to achieve goal avoid over-engineering
Responsive Design with Mobile Devices first

Design a modern, minimalistic, and elegant responsive. The design should:
Use clean lines, ample white space, and soft neutral tones (e.g., whites, blue, muted pastels).
Use a single-column layout for mobile, expanding to a centered grid layout on desktop.
Incorporate a sticky top navbar with logo and smooth-scrolling anchors.
Design must follow accessibility best practices (WCAG 2.1 AA).
All UI elements must use a consistent design system (typography scale, button variants, spacing units).

CREATE USER 'admin'@'localhost' IDENTIFIED BY 'admin@123';
GRANT ALL PRIVILEGES ON *.* TO 'admin'@'localhost' WITH GRANT OPTION;
FLUSH PRIVILEGES;
EXIT;
