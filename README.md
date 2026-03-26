# Task Management System

This project is a Laravel 13 task management system with two roles managed by `spatie/laravel-permission`.

## Features

- Admin can view all users, tasks, and subtasks
- Admin can create, assign, edit, and delete tasks
- User can view only assigned tasks
- User can update task status and mark tasks as completed
- User can create subtasks under assigned tasks
- Task listing supports search, status filter, and Laravel pagination
- REST API endpoints are available under `/api/v1/*`

## Requirements

Before starting, make sure your local machine has:

- PHP 8.3 or higher
- Composer
- Node.js and npm
- A database
  Recommended:
  - SQLite for a quick setup
  - MySQL if you want to match the local `.env` used in development

If you use Laravel Herd, PHP, Composer, Node, npm, and local site serving are already handled for you by Herd.

## Quick Start

If you want the fastest setup, run:

```bash
cp .env.example .env
touch database/database.sqlite
composer run setup
php artisan db:seed --no-interaction
```

Before running `composer run setup`, make sure your `.env` database settings are correct.

- If you want SQLite, the quick start above is enough
- If you want MySQL, update `.env` first and then run `composer run setup`

## Manual Local Installation

Follow these steps from the project root:

### 1. Install PHP dependencies

```bash
composer install
```

### 2. Install frontend dependencies

```bash
npm install
```

### 3. Create environment file

```bash
cp .env.example .env
```

### 4. Generate the application key

```bash
php artisan key:generate
```

### 5. Configure the database in `.env`

Choose one database option before running migrations.

#### Option A: SQLite

Create the SQLite file:

```bash
touch database/database.sqlite
```

Then update `.env`:

```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/project/database/database.sqlite
```

#### Option B: MySQL

Update `.env` with your MySQL credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password
```

### 6. Run database migrations

```bash
php artisan migrate
```

### 7. Seed demo data

```bash
php artisan db:seed
```

### 8. Build frontend assets

For a production-style asset build:

```bash
npm run build
```

For development with live asset rebuilding:

```bash
npm run dev
```

## Demo Login Credentials

The seeder creates these demo accounts:

### Admin

- Email: `super@admin.com`
- Password: `Pass@123`

### User

- Email: `user@test.com`
- Password: `Pass@123`

## Useful Commands

### Run tests

```bash
php artisan test
```

### Run code formatter

```bash
vendor/bin/pint --dirty --format agent
```

### Re-seed database

```bash
php artisan migrate:fresh --seed
```

## API Notes

- Task endpoints are available in `routes/api.php`
- The API uses authenticated Laravel routes under `/api/v1`
- The Blade UI submits regular forms directly to API routes
- No JavaScript `fetch()` calls are required

## Laravel Notes

This README follows the project setup already defined in `composer.json` and Laravel's official installation and Vite workflow:

- Laravel installation: https://laravel.com/docs/12.x/installation
- Laravel Vite: https://laravel.com/docs/12.x/vite
- Laravel configuration: https://laravel.com/docs/12.x/configuration
