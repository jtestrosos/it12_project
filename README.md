<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About This Project

This is a Laravel 12 web application built for managing clinic appointments and inventory. The application features:

- User authentication and role-based access control
- Appointment scheduling system
- Inventory management
- System logging and audit trails
- Modern UI built with Tailwind CSS
- Responsive design for mobile and desktop

## Prerequisites

Before you begin, ensure you have the following installed on your system:

- **PHP 8.2 or higher** - [Download PHP](https://www.php.net/downloads.php)
- **Composer** - [Download Composer](https://getcomposer.org/download/)
- **Node.js 18+ and npm** - [Download Node.js](https://nodejs.org/)
- **MySQL/PostgreSQL/SQLite** - Choose your preferred database
- **Git** - [Download Git](https://git-scm.com/downloads)

## Installation & Setup

### 1. Clone the Repository

```bash
git clone <repository-url>
cd it12_project
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Install Node.js Dependencies

```bash
npm install
```

### 4. Environment Configuration

Create a `.env` file by copying the example:

```bash
cp .env.example .env
```

Edit the `.env` file with your database configuration:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 5. Generate Application Key

```bash
php artisan key:generate
```

### 6. Run Database Migrations

```bash
php artisan migrate
or
php artisan migrate:fresh
(if already cloned this before)
```

### 7. Seed the Database (mandatory)

```bash
php artisan db:seed && php artisan db:seed --class=SeederService
```

### 8. Build Frontend Assets

For development:
```bash
npm run dev
```

For production:
```bash
npm run build
```

## Running the Application

### Development Mode

Start the Laravel development server:

```bash
php artisan serve
```

The application will be available at `http://localhost:8000`

### Production Mode

1. Build the frontend assets:
```bash
npm run build
```

2. Start the server:
```bash
php artisan serve
```

### Using Laravel Sail (Docker - Optional)

If you prefer using Docker, you can use Laravel Sail:

```bash
./vendor/bin/sail up
```

## Default Login Credentials

After running the database seeder, you can use these default credentials:

- **Super Admin**: admin@example.com / password
- **Admin**: admin@clinic.com / password
- **Patient**: patient@example.com / password

## Project Structure

```
it12_project/
├── app/
│   ├── Http/Controllers/     # Application controllers
│   ├── Models/              # Eloquent models
│   └── Observers/           # Model observers
├── database/
│   ├── migrations/          # Database migrations
│   └── seeders/            # Database seeders
├── resources/
│   ├── views/              # Blade templates
│   ├── css/                # Stylesheets
│   └── js/                 # JavaScript files
├── routes/                 # Application routes
└── public/                 # Public assets
```

## Features

- **User Management**: Role-based access control (Super Admin, Admin, Patient)
- **Appointment System**: Schedule and manage clinic appointments
- **Inventory Management**: Track medical supplies and equipment
- **System Logging**: Comprehensive audit trail for all actions
- **Responsive Design**: Mobile-friendly interface built with Tailwind CSS

## What's New 
 
- Added `sessions` table for server-side session storage (2025-10-23).
- Extended `users` table with `barangay`, `phone`, and `address` fields (2025-10-23, 2025-10-24).
- Made `backups.filename`, `backups.file_path`, and `backups.size` nullable (2024-12-27).
- Appointments support approval tracking via `approved_by` and `approved_at` and multiple statuses.
- Inventory tracking includes stock status enum and transaction history with `inventory_transactions`.
- DARK MODE (lol)

### Updates – November 2025
- Services & Reports: Added Excel export for appointments with date-range filter.
  - Endpoint: `GET /admin/reports/export/appointments?start_date=YYYY-MM-DD&end_date=YYYY-MM-DD`
  - Output columns: `ID, Patient Name, Date, Status, Created At`
- Inventory: New `location` column and UI fields for adding items.
- Inventory: Restock and Deduct actions with transaction logging.
- Inventory: DB-driven alerts and stat cards (Total, Low Stock, Out of Stock, Expiring Soon).
- Inventory: Search bar (by name, ID, category, location) and category filter.
- Inventory: Table shows Expiry Date, Location, and Status badges.

### After Pulling These Updates
Run the following to install the Excel package and run migrations:
```bash
composer install
php artisan migrate
php artisan config:clear
```
The Excel export relies on `maatwebsite/excel` (^3.1) which is now included in `composer.json`.

## Database Schema

### users

| Column              | Type        | Details |
|---------------------|-------------|---------|
| id                  | bigint PK   | auto-increment |
| name                | string      | required |
| email               | string      | unique, required |
| email_verified_at   | timestamp   | nullable |
| password            | string      | required |
| role                | string      | default `user` |
| barangay            | string      | nullable |
| phone               | string      | nullable |
| address             | text        | nullable |
| remember_token      | string      | nullable |
| created_at          | timestamp   |  |
| updated_at          | timestamp   |  |

### sessions

| Column        | Type      | Details |
|---------------|-----------|---------|
| id            | string PK | primary key |
| user_id       | bigint FK | nullable, indexed -> users.id |
| ip_address    | string    | length 45, nullable |
| user_agent    | text      | nullable |
| payload       | longText  | required |
| last_activity | integer   | indexed |

### appointments

| Column            | Type        | Details |
|-------------------|-------------|---------|
| id                | bigint PK   | auto-increment |
| user_id           | bigint FK   | required -> users.id (cascade on delete) |
| patient_name      | string      | required |
| patient_phone     | string      | required |
| patient_address   | text        | required |
| appointment_date  | date        | required |
| appointment_time  | time        | required |
| service_type      | string      | required |
| status            | enum        | `pending`, `approved`, `rescheduled`, `cancelled`, `completed` (default `pending`) |
| notes             | text        | nullable |
| medical_history   | text        | nullable |
| is_walk_in        | boolean     | default false |
| approved_by       | bigint FK   | nullable -> users.id |
| approved_at       | timestamp   | nullable |
| created_at        | timestamp   |  |
| updated_at        | timestamp   |  |

### inventory

| Column         | Type        | Details |
|----------------|-------------|---------|
| id             | bigint PK   | auto-increment |
| item_name      | string      | required |
| description    | text        | nullable |
| category       | string      | required |
| current_stock  | integer     | required |
| minimum_stock  | integer     | required |
| unit           | string      | required |
| unit_price     | decimal(10,2) | nullable |
| expiry_date    | date        | nullable |
| supplier       | string      | nullable |
| status         | enum        | `in_stock`, `low_stock`, `out_of_stock`, `expired` (default `in_stock`) |
| created_at     | timestamp   |          |
| updated_at     | timestamp   |          |

### inventory_transactions

| Column           | Type        | Details |
|------------------|-------------|---------|
| id               | bigint PK   | auto-increment |
| inventory_id     | bigint FK   | required -> inventory.id (cascade on delete) |
| user_id          | bigint FK   | required -> users.id (cascade on delete) |
| transaction_type | enum        | `restock`, `usage`, `adjustment`, `expired` |
| quantity         | integer     | required |
| notes            | text        | nullable |
| created_at       | timestamp   |          |
| updated_at       | timestamp   |          |

### system_logs

| Column       | Type        | Details |
|--------------|-------------|---------|
| id           | bigint PK   | auto-increment |
| user_id      | bigint FK   | nullable -> users.id (`set null` on delete) |
| action       | string      | required |
| table_name   | string      | nullable |
| record_id    | unsigned bigint | nullable |
| old_values   | json        | nullable |
| new_values   | json        | nullable |
| ip_address   | string      | nullable |
| user_agent   | text        | nullable |
| created_at   | timestamp   |          |
| updated_at   | timestamp   |          |

### backups

| Column       | Type        | Details |
|--------------|-------------|---------|
| id           | bigint PK   | auto-increment |
| type         | string      | e.g., `database`, `files`, `full` |
| filename     | string      | nullable |
| file_path    | string      | nullable |
| size         | string      | nullable (e.g., "45.2 MB") |
| status       | enum        | `in_progress`, `completed`, `failed` (default `in_progress`) |
| created_by   | bigint FK   | nullable -> users.id (`set null` on delete) |
| notes        | text        | nullable |
| completed_at | timestamp   | nullable |
| created_at   | timestamp   |          |
| updated_at   | timestamp   |          |

## Troubleshooting

### Common Issues

1. **Permission Issues**: Ensure storage and bootstrap/cache directories are writable:
```bash
chmod -R 775 storage bootstrap/cache
```

2. **Composer Memory Issues**: Increase memory limit:
```bash
php -d memory_limit=-1 /usr/local/bin/composer install
```

3. **Node.js Issues**: Clear npm cache:
```bash
npm cache clean --force
```

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
