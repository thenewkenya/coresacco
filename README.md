# Savings and Credit Cooperative Management System

A SACCO management system
## Prerequisites

- PHP
- Composer
- Node.js & NPM
- MySQL/PostgreSQL/SQLite
- Laravel Sail (optional if using docker)
- Docker

## Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/thenewkenya/saccocore.git
   cd saccocore
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure your database in `.env` if needed**


6. **Run database migrations**
   ```bash
   php artisan migrate

   # If you need seed data
   php artisan migrate:fresh --seed 
   ```

7. **Set up roles and create admin user**
   ```bash
   # php artisan sacco is an internal command used to set up roles and users
   # Remember to use a strong password for the admin user
   php artisan sacco:setup-roles --admin-email=admin@sacco.com --admin-password=secure123
   ```

8. **Build frontend assets (if any)**
   ```bash
   npm run build
   ```

9. **Start the development server**
   ```bash
   # php artisan serve
   composer run dev
   ```

## Quick Start with Laravel Sail

If you prefer using Docker:

```bash
# Install Laravel Sail
composer require laravel/sail --dev

# Start the application
./vendor/bin/sail up

# Run setup commands
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan sacco:setup-roles --admin-email=admin@sacco.com --admin-password=secure123
```

## Testing

The project is configured with Pest PHP for testing:

```bash
# Run all tests
php artisan test

# Run tests with coverage
php artisan test --coverage
```

## Available Commands

### Custom Artisan Commands
- `php artisan sacco:setup-roles` - Set up roles and create admin user
- `php artisan migrate` - Run database migrations
- `php artisan db:seed` - Seed database with initial data

### Development Commands
```bash
# Start development server
php artisan serve

# Watch for changes and compile assets
npm run dev

# Build production assets
npm run build

# Run queue worker
php artisan queue:work
```

## Configuration

### Environment Variables
Key environment variables to configure:

```env
APP_NAME="SACCO Core"
APP_ENV=debug
APP_DEBUG=false
APP_URL=https://saccocore.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sacco_core
DB_USERNAME=username
DB_PASSWORD=password

MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=user-email
MAIL_PASSWORD=password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@saccocore.com
```

## Documentation

For detailed information about the role and permission system, see [ROLE_SYSTEM.md](ROLE_SYSTEM.md).
