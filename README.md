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
 - [Mobile Money (M-Pesa) Integration](#mobile-money-m-pesa-integration)
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

**Note**: No local PHP, Composer, MySQL, or npm installation required! Laravel Sail provides everything through Docker containers.

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
    --admin-email=admin@yourdomain.com \
    --admin-password=YourSecurePassword123

./vendor/bin/sail npm run build
```

**Your application is now available at [http://localhost](http://localhost)**

> 💡 **Quick Tip**: After setup, your admin account is automatically verified and ready to use. New users who register will need to be verified by an admin before they can access their accounts. You can manage user verification from the Members page.

## User Verification System

The SACCO system includes a comprehensive user verification system to ensure only approved members can access their accounts and perform transactions.

### How User Verification Works

#### For New Users (Members)
1. **Registration**: New users register with basic information (name, email, password)
2. **Default Status**: New users are automatically set to `inactive` status
3. **Limited Access**: Inactive users cannot:
   - View their accounts
   - Make deposits or withdrawals
   - Access transaction history
4. **Verification Required**: Users must be verified by an admin/manager before gaining full access

#### For Admin Users
1. **Auto-Verification**: Admin accounts created via `sacco:setup-roles` are automatically verified
2. **Full Access**: Admins can immediately access all system features
3. **User Management**: Admins can verify, suspend, or reactivate other users

### Verifying Users

#### Method 1: Web Interface (Recommended)
1. **Login as Admin**: Use your admin credentials to access the system
2. **Navigate to Members**: Go to `/members` in your browser
3. **View Users**: See all users with their current status (Active, Inactive, Suspended)
4. **Verify Users**: Click "Verify" next to inactive users to activate them
5. **Manage Status**: Suspend active users or reactivate suspended ones as needed

#### Method 2: Command Line
```bash
# Verify a specific user
./vendor/bin/sail artisan sacco:verify-user user@example.com --status=active

# Suspend a user
./vendor/bin/sail artisan sacco:verify-user user@example.com --status=suspended

# Reactivate a suspended user
./vendor/bin/sail artisan sacco:verify-user user@example.com --status=active
```

### User Status Types

| Status | Description | Access Level |
|--------|-------------|--------------|
| **Active** | Fully verified member | Full access to all features |
| **Inactive** | Pending verification | Limited access, cannot transact |
| **Suspended** | Temporarily disabled | No access to accounts/transactions |

### User Experience Flow

#### For New Members:
1. **Register** → Account created with `inactive` status
2. **Login** → See "No Active Accounts Found" message
3. **Contact SACCO** → Request verification from admin
4. **Admin Verifies** → Status changed to `active`
5. **Full Access** → Can now open accounts and transact

#### For Admins:
1. **Setup** → Admin account auto-verified during setup
2. **Login** → Immediate full access
3. **Manage Users** → Verify new members via web interface
4. **Monitor** → View user statistics and status

### Security Features

- **Role-Based Access**: Uses proper Laravel authorization instead of hardcoded emails
- **Flexible Admin Creation**: Any email can be used for admin accounts
- **Status Validation**: Account access is validated against user status
- **Audit Trail**: All status changes are logged and trackable

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
./vendor/bin/sail mysql
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
| **MySQL** | `localhost:3306` | MySQL 8.0 Database (accessible via Sail) |
| **Redis** | `localhost:6379` | Caching and sessions |

## Custom commands

### SACCO-Specific Commands
```bash
# Set up roles and create admin user
sail artisan sacco:setup-roles --admin-email=admin@example.com --admin-password=password

# User verification commands
sail artisan sacco:verify-user user@example.com --status=active
sail artisan sacco:verify-user user@example.com --status=suspended
sail artisan sacco:verify-user user@example.com --status=inactive

# Generate sample notifications
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
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=sail
DB_PASSWORD=password

# Mail (for notifications)
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
```

### Mobile Money (M-Pesa) Integration

Follow these steps to enable M-Pesa STK Push deposits:

1) Migrate settings

```bash
sail artisan migrate
```

2) Configure credentials in .env (recommended)

Add these keys to your `.env`:

```env
MPESA_CONSUMER_KEY=your_consumer_key
MPESA_CONSUMER_SECRET=your_consumer_secret
MPESA_SHORTCODE=174379
MPESA_PASSKEY=your_lipa_na_mpesa_online_passkey
MPESA_BASE_URL=https://sandbox.safaricom.co.ke
```

3) Enable and set limits in Settings UI

- Go to `System → Settings → Mobile Money` and:
  - Toggle “Enable M-Pesa”
  - Set minimum/maximum amounts and optional fees

3) Set callback URLs in Safaricom portal

- Register the STK Callback URL to point to:
  - `POST {APP_URL}/webhooks/mpesa/callback`

Note: We do not require C2B URLs for STK push. Ensure your app URL is publicly reachable (use a tunnel like `ngrok http 80` during development).

4) Test the flow (sandbox)

- Login as a member with a valid `phone_number` in profile
- Initiate a deposit via the existing transaction/payment UI (no standalone /mobile-money route)
- Choose M-Pesa where applicable, enter amount and your sandbox test MSISDN (e.g., 2547XXXXXXX)
- Approve the STK on your device (or use Daraja test simulator)
- The system creates a pending `Transaction` and updates to `completed` after callback
- You can also monitor polling via `GET /api/transactions/{id}/status` (auth required)

5) Troubleshooting

- If callbacks fail, check application logs and Safaricom callback logs
- Verify settings values and that `APP_URL` is correct and publicly accessible
- Ensure the `settings` table has the mobile money keys (migration `2025_07_03_..._add_mobile_money_settings_to_settings.php`)
- Confirm callback route exists: `POST /webhooks/mpesa/callback`

Security notes:

- Callback routes are unauthenticated by design; payloads are validated by matching `CheckoutRequestID` to a pending transaction. Consider adding IP allowlisting or signature verification if required by your deployment policy.


### Database Configuration

Sail automatically configures a MySQL database. No manual setup required!

- **Host**: `mysql` (from within containers) or `localhost` (from host)
- **Port**: `3306`
- **Database**: `laravel`
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
sail mysql  # Access MySQL CLI directly
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

