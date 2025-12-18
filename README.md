# SMP Negeri 6 Sudimoro - School Website

A modern, responsive school website built with Laravel 12 and MySQL. Features an elegant navy blue and white design with a complete admin panel for content management.

![Laravel](https://img.shields.io/badge/Laravel-12.x-red?style=flat-square&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.2+-blue?style=flat-square&logo=php)
![MySQL](https://img.shields.io/badge/MySQL-8.0+-orange?style=flat-square&logo=mysql)

---

## 📋 System Requirements

| Requirement | Version                   |
| ----------- | ------------------------- |
| PHP         | 8.2 or higher             |
| Composer    | 2.0 or higher             |
| MySQL       | 8.0 or higher             |
| Node.js     | 18.x or higher (optional) |

### Required PHP Extensions

- BCMath
- Ctype
- Fileinfo
- JSON
- Mbstring
- OpenSSL
- PDO
- PDO_MySQL
- Tokenizer
- XML

---

## 🚀 Installation

### 1. Clone or Download

```bash
cd c:\xampp\htdocs
git clone <repository-url> webskul
cd webskul
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Environment Setup

```bash
# Copy environment file
copy .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Database Configuration

Edit the `.env` file with your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=schooldb
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Create Database

Create a MySQL database named `schooldb` using phpMyAdmin or MySQL command:

```sql
CREATE DATABASE schooldb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 6. Run Migrations & Seeders

```bash
# Run migrations
php artisan migrate

# Seed sample data (optional but recommended)
php artisan db:seed
```

### 7. Storage Link

```bash
php artisan storage:link
```

### 8. Start Development Server

```bash
php artisan serve
```

The website will be available at: **http://127.0.0.1:8000**

---

## 🔐 Default Admin Credentials

| Field    | Value                       |
| -------- | --------------------------- |
| URL      | http://127.0.0.1:8000/login |
| Email    | admin@smpn6sudimoro.sch.id  |
| Password | password123                 |

> ⚠️ **Important**: Change the default password after first login!

---

## 📁 Features

### Frontend (Public)

- ✅ Home page with hero section and statistics
- ✅ School profile (vision, mission, history)
- ✅ Teacher profiles with photos
- ✅ Activities/News with pagination
- ✅ Important information announcements
- ✅ Responsive design (mobile, tablet, desktop)
- ✅ Dynamic favicon from school logo

### Backend (Admin Panel)

- ✅ Dashboard with statistics
- ✅ School profile management
- ✅ Teacher CRUD with photo upload
- ✅ Activities/News CRUD with image upload
- ✅ Information/Announcements CRUD
- ✅ Admin profile (change email/password)
- ✅ SMTP settings configuration
- ✅ Password reset via email

---

## 📧 SMTP Configuration (Optional)

To enable password reset via email:

1. Login to admin panel
2. Go to **Pengaturan SMTP**
3. Enter your SMTP settings:

**Gmail Example:**
| Setting | Value |
|---------|-------|
| Mail Driver | SMTP |
| SMTP Host | smtp.gmail.com |
| SMTP Port | 587 |
| Encryption | TLS |
| Username | your-email@gmail.com |
| Password | [App Password](https://support.google.com/accounts/answer/185833) |

4. Click "Kirim Test Email" to verify settings

---

## 🗂️ Project Structure

```
webskul/
├── app/
│   ├── Http/Controllers/
│   │   ├── Admin/          # Admin panel controllers
│   │   └── Frontend/       # Public website controllers
│   └── Models/             # Eloquent models
├── database/
│   ├── migrations/         # Database migrations
│   └── seeders/            # Sample data seeders
├── resources/views/
│   ├── admin/              # Admin panel views
│   │   ├── layouts/        # Admin layout
│   │   ├── activities/     # Activity CRUD views
│   │   ├── information/    # Information CRUD views
│   │   ├── profile/        # Admin profile views
│   │   ├── school-profile/ # School profile views
│   │   ├── settings/       # SMTP settings views
│   │   └── teachers/       # Teacher CRUD views
│   ├── emails/             # Email templates
│   ├── layouts/            # Frontend layout
│   └── pages/              # Frontend pages
├── routes/
│   └── web.php             # All routes
└── public/
    └── storage/            # Uploaded files (symlink)
```

---

## 🔧 Useful Commands

```bash
# Start development server
php artisan serve

# Clear all cache
php artisan optimize:clear

# Reset database with fresh data
php artisan migrate:fresh --seed

# List all routes
php artisan route:list
```

---

## 📝 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

## 👨‍💻 Developed By

Aang Wirawan - Built with ❤️ using Laravel 12
