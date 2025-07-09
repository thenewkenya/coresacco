# SACCO Core Management System

A comprehensive Savings and Credit Cooperative (SACCO) management system.

## Table of Contents

- [Prerequisites](#prerequisites)
- [Quick Start](#quick-start)
- [Detailed Installation](#detailed-installation)
- [Development Workflow](#development-workflow)
- [Shell alias (highly recommended)](#shell-alias-highly-recommended)
- [Testing](#testing)
- [Available services](#available-services)
- [Custom commands](#custom-commands)
- [Configuration](#configuration)
- [Production Deployment](#production-deployment)
- [Troubleshooting](#troubleshooting)
- [License](#license)

## Prerequisites

### Required
- **Docker Desktop** - [Download here](https://docker.com/products/docker-desktop)
- **Git** - For cloning the repository

### Operating System Specific
- **Windows**: 
  - ⚠️ **Laravel Sail requires WSL2. You cannot use PowerShell or CMD for Docker/Laravel Sail workflows.**
  - Install [Docker Desktop for Windows](https://docs.docker.com/desktop/windows/install/)
  - Enable WSL2 integration in Docker Desktop settings
  - All commands must be run in WSL2 terminal
- **macOS**: Docker Desktop includes everything needed
- **Linux**: Ensure Docker and Docker Compose are installed

**Note**: No local PHP, Composer, PostgreSQL, or npm installation required! Laravel Sail provides everything through Docker containers.

## Detailed Installation

### Step 1: Clone and Prepare
```bash
git clone https://github.com/thenewkenya/eSacco.git
cd eSacco
cp .env.example .env
```

### Step 2: Bootstrap Dependencies
```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php82-composer:latest \
    composer install --ignore-platform-reqs
```

### Step 3: Configure Laravel Sail
```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php82-composer:latest \
    bash -c "composer require laravel/sail --dev && php artisan sail:install"
```

### Step 4: Start the Application
```bash
./vendor/bin/sail up -d
./vendor/bin/sail npm install
./vendor/bin/sail artisan key:generate
```

> ⚠️ **Note**: Always use Sail's npm inside the container.

### Step 5: Database Setup
```bash
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan migrate:fresh --seed
```

### Step 6: Initial Configuration
```bash
# Set up admin user
./vendor/bin/sail artisan sacco:setup-roles \
    --admin-email=admin@sacco.com \
    --admin-password=YourSecurePassword123

./vendor/bin/sail npm run build
```

**Your application is now available at [http://localhost](http://localhost)**

## Development Workflow

### Starting Development
```bash
./vendor/bin/sail up -d
./vendor/bin/sail npm run dev
```

### Stopping the Application
```bash
./vendor/bin/sail down
./vendor/bin/sail down -v
```

### Daily Commands
```bash
./vendor/bin/sail logs
./vendor/bin/sail shell
./vendor/bin/sail psql
./vendor/bin/sail artisan [command]
./vendor/bin/sail composer install
./vendor/bin/sail npm install
```

## Shell alias

Save time by creating a shell alias:

```bash
# Bash/Zsh (add to ~/.bashrc or ~/.zshrc)
alias sail='sh $([ -f sail ] && echo sail || echo vendor/bin/sail)'
```

> **Windows Users**: Use Sail commands in WSL2. PowerShell is not supported for Laravel Sail.

After setting up the alias, restart your terminal and use:
```bash
sail up -d
sail artisan migrate
sail npm run dev
```

## Testing

```bash
# Sail pest and sail test work as well
sail artisan test
sail artisan test --coverage
sail artisan test tests/Feature/DashboardTest.php
sail artisan test --parallel
```

## Available services

Your application includes these services:

| Service | URL | Description |
|---------|-----|-------------|
| **Laravel App** | [http://localhost](http://localhost) | Main SACCO application |
| **Mailpit** | [http://localhost:8025](http://localhost:8025) | Email testing interface |
| **MySQL** | `localhost:3306` | Database (accessible via Sail) |
| **Redis** | `localhost:6379` | Caching and sessions |

## Custom commands

### SACCO-Specific Commands
```bash
sail artisan sacco:setup-roles --admin-email=admin@example.com --admin-password=password
sail artisan sacco:generate-sample-notifications
```

### Laravel Commands
```bash
sail artisan migrate
sail artisan migrate:rollback
sail artisan migrate:fresh --seed
sail artisan db:seed
sail artisan cache:clear
sail artisan config:clear
sail artisan view:clear
sail artisan route:clear
sail artisan queue:work
sail artisan queue:restart
```

### Frontend Commands
```bash
sail npm run dev
sail npm run build
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
DB_CONNECTION=pgsql
DB_HOST=pgsql
DB_PORT=5432
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

Sail automatically configures a PostgreSQL database. No manual setup required!

- **Host**: `pgsql` (from within containers) or `localhost` (from host)
- **Port**: `5432`
- **Database**: `saccocore`
- **Username**: `sail`
- **Password**: `password`

## Troubleshooting

### Container Issues
```bash
sail build --no-cache
sail ps
sail restart
sail logs -f
```

### Permission Issues (Linux/WSL)
```bash
sudo chown -R $USER:$USER .
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
sail artisan migrate:fresh --seed
sail artisan tinker
>>> DB::connection()->getPdo();
```

### Performance Issues
```bash
sail artisan optimize
sail artisan optimize:clear
```

## Production Deployment

**Coming soon**. For now, refer to Laravel's [deployment documentation](https://laravel.com/docs/deployment) and ensure the following are properly configured:

- Environment variables (`.env` file)
- Queue workers for background jobs
- Caching configuration (Redis/Memcached)
- Database optimization and backups
- SSL certificates and HTTPS
- File permissions and security

## License

This is a proprietary company product. All rights reserved.


> **Note**: This README is comprehensive but may be split into separate documentation files as the project grows (e.g., `docs/INSTALLATION.md`, `docs/COMMANDS.md`).

