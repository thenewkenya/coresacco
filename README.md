# SACCO Core Management System

A comprehensive Savings and Credit Cooperative (SACCO) management system built with Laravel, designed for seamless Docker deployment via Laravel Sail.

## Quick Start (For Experienced Users)

```bash
git clone https://github.com/thenewkenya/saccocore.git && cd saccocore
cp .env.example .env
docker run --rm -u "$(id -u):$(id -g)" -v "$(pwd):/var/www/html" -w /var/www/html laravelsail/php82-composer:latest composer install --ignore-platform-reqs
./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate && ./vendor/bin/sail artisan migrate:fresh --seed
./vendor/bin/sail artisan sacco:setup-roles --admin-email=admin@sacco.com --admin-password=secure123
```

Your app will be available at [http://localhost](http://localhost)

## Prerequisites

### Required
- **Docker Desktop** - [Download here](https://docker.com/products/docker-desktop)
- **Git** - For cloning the repository

### Operating System Specific
- **Windows**: Enable WSL2 (Windows Subsystem for Linux 2)
- **macOS**: Docker Desktop includes everything needed
- **Linux**: Ensure Docker and Docker Compose are installed

**Note**: No local PHP, Composer, MySQL, or Node.js installation required! Laravel Sail provides everything through Docker containers.

## Detailed Installation

### Step 1: Clone and Prepare
```bash
# Clone the repository
git clone https://github.com/thenewkenya/saccocore.git
cd saccocore

# Copy environment configuration
# Windows (PowerShell): copy .env.example .env
# Unix/Linux/macOS:
cp .env.example .env
```

### Step 2: Bootstrap Dependencies
```bash
# Install PHP dependencies using Docker (no local PHP needed)
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php82-composer:latest \
    composer install --ignore-platform-reqs
```

> **What this does**: Downloads all PHP packages and Laravel Sail using a temporary Docker container

### Step 3: Configure Laravel Sail
```bash
# Install Sail and publish configuration (creates docker-compose.yml)
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php82-composer:latest \
    bash -c "composer require laravel/sail --dev && php artisan sail:install"
```

**What this does**: Installs Laravel Sail and sets up Docker Compose configuration for your Laravel application

### Step 4: Start the Application
```bash
# Start all Docker containers in the background
./vendor/bin/sail up -d

# Install Node.js dependencies
./vendor/bin/sail npm install

# Generate application encryption key
./vendor/bin/sail artisan key:generate
```

### Step 5: Database Setup
```bash
# Run database migrations (creates tables)
./vendor/bin/sail artisan migrate

# Optional: Add sample data
./vendor/bin/sail artisan migrate:fresh --seed
```

### Step 6: Initial Configuration
```bash
# Set up roles and create admin user
./vendor/bin/sail artisan sacco:setup-roles \
    --admin-email=admin@sacco.com \
    --admin-password=YourSecurePassword123

# Build frontend assets
./vendor/bin/sail npm run build
```

**Your application is now available at [http://localhost](http://localhost)**

## Development Workflow

### Starting Development
```bash
# Start the application stack
./vendor/bin/sail up -d

# Watch for file changes (keep this running in a separate terminal)
./vendor/bin/sail npm run dev
```

### Stopping the Application
```bash
# Stop all containers
./vendor/bin/sail down

# Stop and remove volumes (clears database)
./vendor/bin/sail down -v
```

### Daily Commands
```bash
# View container logs
./vendor/bin/sail logs

# Access application container shell
./vendor/bin/sail shell

# Access MySQL database
./vendor/bin/sail mysql

# Run Artisan commands
./vendor/bin/sail artisan [command]

# Install new packages
./vendor/bin/sail composer install
./vendor/bin/sail npm install
```

## Shell Alias (Highly Recommended)

Save time by creating a shell alias:

```bash
# For Bash/Zsh (add to ~/.bashrc or ~/.zshrc)
alias sail='sh $([ -f sail ] && echo sail || echo vendor/bin/sail)'

# For Fish shell (add to ~/.config/fish/config.fish)
alias sail='sh (test -f sail && echo sail || echo vendor/bin/sail)'

# For Windows PowerShell (add to profile)
Set-Alias sail 'vendor/bin/sail'
```

After setting up the alias, restart your terminal and use:
```bash
sail up -d
sail artisan migrate
sail npm run dev
```

## Testing

```bash
# Run all tests
sail artisan test

# Run tests with coverage report
sail artisan test --coverage

# Run specific test file
sail artisan test tests/Feature/DashboardTest.php

# Run tests in parallel (faster)
sail artisan test --parallel
```

## Available Services

Your application includes these services:

| Service | URL | Description |
|---------|-----|-------------|
| **Laravel App** | [http://localhost](http://localhost) | Main SACCO application |
| **Mailpit** | [http://localhost:8025](http://localhost:8025) | Email testing interface |
| **MySQL** | `localhost:3306` | Database (accessible via Sail) |
| **Redis** | `localhost:6379` | Caching and sessions |

## Custom Commands

### SACCO-Specific Commands
```bash
# Set up roles and permissions
sail artisan sacco:setup-roles --admin-email=admin@example.com --admin-password=password

# Generate sample notifications (for testing)
sail artisan sacco:generate-sample-notifications
```

### Laravel Commands
```bash
# Database operations
sail artisan migrate              # Run pending migrations
sail artisan migrate:rollback    # Rollback last migration
sail artisan migrate:fresh --seed # Fresh database with sample data
sail artisan db:seed            # Add sample data only

# Cache management
sail artisan cache:clear         # Clear application cache
sail artisan config:clear       # Clear configuration cache
sail artisan view:clear         # Clear compiled views
sail artisan route:clear        # Clear route cache

# Queue management
sail artisan queue:work          # Process background jobs
sail artisan queue:restart      # Restart queue workers
```

### Frontend Commands
```bash
# Development (watch for changes)
sail npm run dev

# Production build
sail npm run build

# Install new packages
sail npm install package-name
```

## Configuration

### Environment Variables

Key variables in your `.env` file:

```env
# Application
APP_NAME="SACCO Core"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

# Database (automatically configured by Sail)
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=saccocore
DB_USERNAME=sail
DB_PASSWORD=password

# Mail (for notifications)
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
```

### Database Configuration

Sail automatically configures a MySQL database. No manual setup required!

- **Host**: `mysql` (from within containers) or `localhost` (from host)
- **Port**: `3306`
- **Database**: `saccocore`
- **Username**: `sail`
- **Password**: `password`

## Troubleshooting

### Container Issues
```bash
# Rebuild containers from scratch
sail build --no-cache

# View container status
sail ps

# Restart all services
sail restart

# View detailed logs
sail logs -f
```

### Permission Issues (Linux/WSL)
```bash
# Fix file ownership
sudo chown -R $USER:$USER .

# Fix permissions
chmod -R 755 storage bootstrap/cache
```

### Port Conflicts
If port 80 is busy, edit `docker-compose.yml`:
```yaml
services:
  laravel.test:
    ports:
      - '8080:80'  # Use port 8080 instead
```

### Database Issues
```bash
# Reset database completely
sail artisan migrate:fresh --seed

# Check database connection
sail artisan tinker
>>> DB::connection()->getPdo();
```

### Performance Issues
```bash
# Optimize for production
sail artisan optimize

# Clear all caches
sail artisan optimize:clear
```

## Security Notes

- **Change default passwords** in production
- **Use strong passwords** for admin accounts
- **Configure proper mail settings** for notifications
- **Review .env file** before deployment
- **Keep Docker images updated**: `sail build --no-cache`

## Documentation

- **Role & Permissions**: [ROLE_SYSTEM.md](ROLE_SYSTEM.md)
- **Transaction Processing**: [TRANSACTION_PROCESSING_SUMMARY.md](TRANSACTION_PROCESSING_SUMMARY.md)
- **Laravel Sail**: [Official Documentation](https://laravel.com/docs/12.x/sail)
- **Laravel Framework**: [laravel.com/docs](https://laravel.com/docs)

## License

This is a proprietary company product. All rights reserved.

## Support

- **Issues**: [GitHub Issues](https://github.com/thenewkenya/saccocore/issues)
- **Discussions**: [GitHub Discussions](https://github.com/thenewkenya/saccocore/discussions)
- **Email**: support@saccocore.com

