# SACCO Core - Savings and Credit Cooperative Management System

A comprehensive Laravel-based management system for Savings and Credit Cooperative Organizations (SACCOs). Built with Laravel 12, Livewire, and Tailwind CSS, featuring role-based access control, member management, account operations, loan processing, and reporting capabilities.

## 🚀 Features

### Core Functionality
- **Member Management**: Complete member registration, profile management, and member directory
- **Account Management**: Savings accounts, deposits, withdrawals, and transaction history
- **Loan Management**: Loan applications, approval workflows, disbursement, and repayment tracking
- **Branch Management**: Multi-branch support with branch-specific operations
- **Insurance Integration**: Member insurance coverage tracking
- **Role-Based Access Control**: Granular permissions system with 4 user roles

### User Roles & Permissions
- **Admin**: Full system access and configuration
- **Manager**: Branch management and advanced operations
- **Staff**: Day-to-day member and loan operations
- **Member**: View own accounts and loan information

### Technical Features
- **RESTful API**: Complete API with authentication and permission middleware
- **Livewire Components**: Real-time, reactive user interface
- **Tailwind CSS**: Modern, responsive design
- **Laravel Sanctum**: API authentication
- **Database Migrations**: Complete database schema
- **Testing Ready**: Pest PHP testing framework configured

## 📋 Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js & NPM
- MySQL/PostgreSQL/SQLite
- Laravel Sail (optional, for Docker development)

## 🛠️ Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
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

5. **Configure your database in `.env`**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=sacco_core
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

6. **Run database migrations**
   ```bash
   php artisan migrate
   ```

7. **Set up roles and create admin user**
   ```bash
   php artisan sacco:setup-roles --admin-email=admin@sacco.com --admin-password=secure123
   ```

8. **Build frontend assets**
   ```bash
   npm run build
   ```

9. **Start the development server**
   ```bash
   php artisan serve
   ```

## 🚀 Quick Start with Laravel Sail

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

## 📊 Database Schema

### Core Models
- **Users**: Authentication and user management
- **Members**: SACCO member information and profiles
- **Accounts**: Savings accounts with balance tracking
- **Loans**: Loan applications and management
- **Transactions**: Financial transaction records
- **Branches**: Branch information and management
- **Roles**: User roles and permissions
- **Insurance**: Member insurance coverage
- **LoanTypes**: Different types of loans offered

### Key Relationships
- Users can have multiple roles
- Members belong to branches
- Accounts belong to members
- Loans are associated with members and loan types
- Transactions are linked to accounts

## 🔐 Authentication & Authorization

### API Authentication
The system uses Laravel Sanctum for API authentication:

```bash
# Get authentication token
POST /api/auth/login
{
    "email": "user@example.com",
    "password": "password"
}
```

### Permission System
The role-based permission system includes:

- **Member Management**: `view-members`, `create-members`, `edit-members`, `delete-members`
- **Account Management**: `view-accounts`, `create-accounts`, `edit-accounts`, `delete-accounts`, `process-transactions`
- **Loan Management**: `view-loans`, `create-loans`, `edit-loans`, `delete-loans`, `approve-loans`, `disburse-loans`
- **Branch Management**: `view-branches`, `manage-branches`
- **Reports & Settings**: `view-reports`, `export-reports`, `manage-settings`, `manage-roles`

## 🌐 API Endpoints

### Members
- `GET /api/members` - List all members
- `GET /api/members/{id}` - Get member details
- `POST /api/members` - Create new member
- `PUT /api/members/{id}` - Update member
- `DELETE /api/members/{id}` - Delete member

### Accounts
- `GET /api/accounts` - List all accounts
- `GET /api/accounts/{id}` - Get account details
- `POST /api/accounts` - Create new account
- `POST /api/accounts/{id}/deposit` - Process deposit
- `POST /api/accounts/{id}/withdraw` - Process withdrawal

### Loans
- `GET /api/loans` - List all loans
- `GET /api/loans/{id}` - Get loan details
- `POST /api/loans` - Create loan application
- `POST /api/loans/{id}/approve` - Approve loan
- `POST /api/loans/{id}/disburse` - Disburse loan
- `POST /api/loans/{id}/repay` - Process loan repayment

All endpoints require authentication and appropriate permissions.

## 🎨 Frontend

### Technologies
- **Livewire**: Real-time components and interactions
- **Tailwind CSS**: Utility-first CSS framework
- **Vite**: Modern build tool for assets
- **Alpine.js**: Lightweight JavaScript framework (included with Livewire)

### Key Components
- **MemberManager**: Complete member management interface
- **Authentication**: Login, registration, and password reset
- **Dashboard**: Role-based dashboard with key metrics
- **Settings**: System configuration and user management

## 🧪 Testing

The project is configured with Pest PHP for testing:

```bash
# Run all tests
php artisan test

# Run tests with coverage
php artisan test --coverage
```

## 📝 Available Commands

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

## 🔧 Configuration

### Environment Variables
Key environment variables to configure:

```env
APP_NAME="SACCO Core"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sacco_core
DB_USERNAME=your_username
DB_PASSWORD=your_password

MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@your-domain.com
```

## 📚 Documentation

For detailed information about the role and permission system, see [ROLE_SYSTEM.md](ROLE_SYSTEM.md).

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🆘 Support

For support and questions:
- Create an issue in the repository
- Check the [ROLE_SYSTEM.md](ROLE_SYSTEM.md) for detailed permission system documentation
- Review the Laravel and Livewire documentation for framework-specific questions

## 🗺️ Roadmap

- [ ] Advanced reporting and analytics
- [ ] Mobile application
- [ ] SMS/Email notifications
- [ ] Integration with external payment systems
- [ ] Multi-language support
- [ ] Advanced audit logging
- [ ] Bulk operations for member management
- [ ] Document management system

---

**Built with ❤️ using Laravel, Livewire, and Tailwind CSS** 