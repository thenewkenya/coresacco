# Savings and Credit Cooperative Management System

A SACCO management system built with Laravel, designed to run seamlessly with Docker via Laravel Sail.

## Prerequisites

- **Docker** - Download from [docker.com](https://docker.com)

> **Note**: No local PHP, Composer, or Node.js installation required! Laravel Sail provides everything through Docker containers.

## Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/thenewkenya/saccocore.git
   cd saccocore
   ```

2. **Copy environment file**
   ```bash
   cp .env.example .env
   ```

3. **Bootstrap with Docker (no local PHP required)**
   ```bash
   # Install PHP dependencies using Docker
   docker run --rm \
       -u "$(id -u):$(id -g)" \
       -v "$(pwd):/var/www/html" \
       -w /var/www/html \
       laravelsail/php82-composer:latest \
       composer install --ignore-platform-reqs
   ```

4. **Install and configure Laravel Sail**
   ```bash
   # Install Sail if not already in composer.json
   docker run --rm \
       -u "$(id -u):$(id -g)" \
       -v "$(pwd):/var/www/html" \
       -w /var/www/html \
       laravelsail/php82-composer:latest \
       composer require laravel/sail --dev

   # Publish Sail configuration
   docker run --rm \
       -u "$(id -u):$(id -g)" \
       -v "$(pwd):/var/www/html" \
       -w /var/www/html \
       laravelsail/php82-composer:latest \
       php artisan sail:install
   ```

5. **Start Laravel Sail**
   ```bash
   # Start the Docker containers
   ./vendor/bin/sail up -d
   ```

6. **Install Node.js dependencies**
   ```bash
   ./vendor/bin/sail npm install
   ```

7. **Environment setup**
   ```bash
   ./vendor/bin/sail artisan key:generate
   ```

8. **Configure your database in `.env` if needed**
   > By default, Sail configures MySQL automatically. The database runs in a Docker container.

9. **Run database migrations**
   ```bash
   ./vendor/bin/sail artisan migrate

   # If you need seed data
   ./vendor/bin/sail artisan migrate:fresh --seed 
   ```

10. **Set up roles and create admin user**
    ```bash
    # Set up roles and users using our custom command
    # Remember to use a strong password for the admin user
    ./vendor/bin/sail artisan sacco:setup-roles --admin-email=admin@sacco.com --admin-password=secure123
    ```

11. **Build frontend assets**
    ```bash
    ./vendor/bin/sail npm run build
    ```

Your application will be available at [http://localhost](http://localhost).

## Watching for Changes

To automatically compile frontend assets when files change:

```bash
# Watch for changes and recompile assets automatically
./vendor/bin/sail npm run dev

# Or with alias
sail npm run dev
```

This command will:
- Watch for changes in your CSS, JS, and other frontend files
- Automatically recompile assets when changes are detected
- Enable hot module replacement (HMR) for faster development
- Keep running until you stop it with `Ctrl+C`

## Shell Alias (Recommended)

To avoid typing `./vendor/bin/sail` repeatedly, create a shell alias:

```bash
# Add to your ~/.bashrc, ~/.zshrc, or ~/.bash_profile
alias sail='sh $([ -f sail ] && echo sail || echo vendor/bin/sail)'
```

After setting up the alias, you can use `sail` instead of `./vendor/bin/sail`:

```bash
sail up -d
sail artisan migrate
sail composer install
```

## Daily Development Workflow

```bash
# Start the application
sail up -d

# Stop the application
sail down

# View logs
sail logs

# Access the application container shell
sail shell

# Run Artisan commands
sail artisan [command]

# Install Composer packages
sail composer [command]

# Install NPM packages
sail npm [command]
```

## Testing

Run tests using Sail with Pest PHP:

```bash
# Run all tests
sail artisan test

# Run tests with coverage
sail artisan test --coverage

# Run specific test files
sail artisan test tests/Feature/DashboardTest.php
```

## Available Commands

### Custom Artisan Commands
```bash
sail artisan sacco:setup-roles    # Set up roles and create admin user
sail artisan migrate              # Run database migrations
sail artisan db:seed             # Seed database with initial data
```

### Development Commands
```bash
# Watch for changes and compile assets (keep this running during development)
sail npm run dev

# Build production assets (for deployment)
sail npm run build

# Run queue worker (for background jobs)
sail artisan queue:work

# Clear caches
sail artisan cache:clear
sail artisan config:clear
sail artisan view:clear
```

## Database Management

```bash
# Access MySQL CLI
sail mysql

# Run migrations
sail artisan migrate

# Rollback migrations
sail artisan migrate:rollback

# Fresh migration with seeding
sail artisan migrate:fresh --seed
```

## Configuration

### Environment Variables
Sail automatically configures database connection. Key variables to customize:

```env
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

# Mail configuration
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=user-email
MAIL_PASSWORD=password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@saccocore.com
```

## Sail Services

By default, this application includes:

- **Laravel Application** (PHP 8.2)
- **MySQL 8.0** - Database
- **Redis** - Caching and sessions
- **Mailpit** - Email testing ([http://localhost:8025](http://localhost:8025))

## Troubleshooting

### Container Issues
```bash
# Rebuild containers
sail build --no-cache

# View container status
sail ps

# Restart services
sail restart
```

### Permission Issues (Linux/WSL)
```bash
# Fix file permissions
sudo chown -R $USER:$USER .
```

### Port Conflicts
If port 80 is already in use, modify `docker-compose.yml`:
```yaml
ports:
    - '8080:80'  # Use port 8080 instead
```

## Documentation

- For detailed role and permission system: [ROLE_SYSTEM.md](ROLE_SYSTEM.md)
- For transaction processing: [TRANSACTION_PROCESSING_SUMMARY.md](TRANSACTION_PROCESSING_SUMMARY.md)
- Laravel Sail Documentation: [https://laravel.com/docs/12.x/sail](https://laravel.com/docs/12.x/sail)

