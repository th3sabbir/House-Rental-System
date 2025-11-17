# AmarThikana 🏠

[![PHP Version](https://img.shields.io/badge/PHP-7.4%2B-blue.svg)](https://php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-orange.svg)](https://mysql.com/)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

Find your perfect rental home in Bangladesh — AmarThikana is a responsive house rental platform built with PHP, MySQL, HTML, CSS, and JavaScript. It provides tenant, landlord, and admin interfaces for listing management, applications, messaging, and analytics.

![AmarThikana Banner](https://via.placeholder.com/800x200/16a085/ffffff?text=AmarThikana+-+Find+Your+Perfect+Rental+Home)

## 🌐 Live Demo

Experience AmarThikana in action! Visit our live demo at [https://demo.amarthikana.com](https://demo.amarthikana.com)

## 🌟 Features

### For Tenants
- **Advanced Search & Filters**: Search properties by location, type, price range, and amenities
- **Detailed Property Listings**: View comprehensive property details with high-quality images
- **Requesting Tours**: Schedule and participate in property tours
- **Favorites System**: Save and manage favorite properties
- **Messaging System**: Direct communication with landlords

### For Landlords
- **Property Management**: Easily list and manage multiple properties
- **Tenant Screening**: Access tenant applications and background information
- **Tour Requests**: Accept or reject tenant tour requests
- **Communication Tools**: Built-in messaging system with tenants
- **Analytics Dashboard**: Track property views, applications, and performance
- **Image Upload**: Upload multiple high-quality property images
- **Amenities Management**: Specify property amenities and features

### For Administrators
- **User Management**: Manage tenants, landlords, accounts
- **Content Moderation**: Review and approve property listings 
- **System Analytics**: Comprehensive dashboard with system statistics
- **Support System**: Handle user inquiries and support tickets
- **Settings Management**: Configure system-wide settings

### General Features
- **Responsive Design**: Fully responsive design works on all devices
- **Multi-language Support**: Support for English and Bengali
- **Security**: Secure authentication with password hashing and CSRF protection
- **Performance**: Optimized for speed with caching and compression

## 🚀 Tech Stack

- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+ / MariaDB
- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Libraries**:
  - jQuery for DOM manipulation
  - Swiper.js for carousels
  - Font Awesome for icons
  - Google Fonts for typography
- **Server**: Apache/Nginx with mod_rewrite
- **Security**: PDO/MySQLi with prepared statements

## Quick GitHub Summary

- Short description: AmarThikana — a self-hosted house rental platform for Bangladesh with tenant, landlord and admin interfaces.
- Purpose: Demo-ready system for managing property listings, tenant applications, communications and admin moderation.
- Includes: PHP backend, MySQL schema, responsive frontend, file uploads and a lightweight dashboard.

## 📖 Usage

### For Tenants
1. **Register**: Create an account as a tenant
2. **Search**: Use filters to find properties
3. **View Details**: Browse property images and specifications
4. **Contact**: Message landlords or schedule tours
5. **Apply**: Submit rental applications

### For Landlords
1. **Register**: Create an account as a landlord
2. **List Property**: Add property details and images
3. **Manage**: Edit listings and view applications
4. **Communicate**: Respond to tenant inquiries

### For Admins
1. **Login**: Access admin dashboard
2. **Moderate**: Review property listings
3. **Manage Users**: Handle user accounts
4. **Analytics**: View system statistics

## 📁 Project Structure

```
amarthikana/
├── admin/              # Admin dashboard pages
├── api/                # REST API endpoints
├── config/             # Database and configuration files
├── css/                # Stylesheets and themes
├── images/             # Static images and assets
├── includes/           # PHP includes and utilities
├── js/                 # JavaScript files
├── landlord/           # Landlord dashboard pages
├── logs/               # Application logs
├── tenant/             # Tenant dashboard pages
├── uploads/            # User-uploaded files
├── .htaccess           # Apache configuration
├── dump.sql            # Database schema
├── index.php           # Homepage
├── login.php           # Authentication
├── README.md           # This file
└── ...                 # Other PHP pages
```

## 🧪 Testing

### Database Connection Test
Visit `test_connection.php` to verify database connectivity.

### User Registration Test
1. Register a new account
2. Verify email confirmation
3. Test login/logout functionality

### Property Management Test
1. Create a landlord account
2. List a new property
3. Upload images and set amenities
4. View property on frontend

## 🔒 Security

- Password hashing with bcrypt
- CSRF protection on forms
- SQL injection prevention with prepared statements
- XSS protection with input sanitization
- Secure file upload validation
- Session management with secure cookies

## 📊 Performance

- Database query optimization
- Image compression and lazy loading
- Browser caching with .htaccess
- CDN integration ready
- Minified CSS/JS for production

## 🐛 Troubleshooting

### Common Issues

**Database Connection Failed**
- Verify credentials in `config/database.php`
- Ensure MySQL service is running
- Check database exists and user has permissions

**Page Not Found Errors**
- Verify `.htaccess` is enabled
- Check URL rewriting is configured
- Ensure correct file permissions

**Upload Issues**
- Check `uploads/` directory permissions (755)
- Verify PHP upload limits in `php.ini`
- Ensure sufficient disk space

**Email Not Sending**
- Configure SMTP settings
- Check spam folder
- Verify firewall allows outbound connections

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

**Made with ❤️ in Bangladesh** 🇧🇩

*Find your perfect home with AmarThikana - where dreams meet reality.*
