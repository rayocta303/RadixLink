
# ISP Manager SaaS Platform

## Overview
A comprehensive multi-tenant ISP billing and RADIUS management platform built with Laravel 12. This platform enables ISP operators to manage their MikroTik routers, hotspot users, PPPoE customers, vouchers, and billing in a centralized cloud-based system.

## Project Goals
- Multi-tenant SaaS platform for ISP/hotspot/PPPoE management
- RADIUS authentication and accounting integration with FreeRADIUS
- Voucher generation and management
- Customer billing and invoicing
- Real-time monitoring and reporting
- Auto-generate MikroTik router scripts
- Role-based access control
- Reseller management system
- Comprehensive analytics and reporting

## Current State
**Status: Production Ready**

The application is fully functional with all core features complete and operational:
- Laravel 12 framework with PHP 8.4
- Multi-tenant architecture with separate databases per tenant
- FreeRADIUS integration with full RADIUS tables
- MikroTik router script generator
- Complete CRUD operations with real data
- Advanced reporting and analytics
- Reseller management system

### Completed Features
- [x] Multi-tenant database architecture (separate DB per tenant)
- [x] Authentication system (login, register, forgot password)
- [x] Platform admin dashboard with metrics
- [x] Tenant management (CRUD, suspend/activate)
- [x] Subscription plan management
- [x] User role management (platform & tenant level)
- [x] NAS/Router management with map visualization
- [x] Service plans with bandwidth, FUP, validity (hours/days/months)
- [x] Customer management (PPPoE/Hotspot)
- [x] Voucher generation and management
- [x] Invoice and payment management
- [x] **FreeRADIUS integration** (radcheck, radreply, radacct, etc)
- [x] **MikroTik router script generator**
- [x] **Reseller management system** with commission tracking
- [x] **Advanced reporting** (revenue, customers, sales)
- [x] **Interactive charts** with ApexCharts
- [x] Tenant data seeder with comprehensive dummy data
- [x] Dark mode support
- [x] Mobile-responsive design

### Pending Features
- [ ] MikroTik API integration (RouterOS API)
- [ ] Voucher QR code generation
- [ ] Payment gateway integration (Midtrans, Tripay)
- [ ] Real-time user monitoring dashboard
- [ ] Invoice PDF generation
- [ ] Automated billing scheduler
- [ ] Email notifications
- [ ] SMS integration
- [ ] Backup and restore system

## Architecture

### Database Structure

**Central Database (Platform)**
- `tenants` - ISP tenant accounts with database credentials
- `domains` - Tenant subdomain mappings
- `users` - Platform administrators
- `subscription_plans` - Available subscription tiers
- `tenant_subscriptions` - Active subscriptions
- `platform_invoices` - Platform billing
- `platform_tickets` - Support tickets

**Tenant Database (Per ISP)**
- `users` - Tenant staff (owner, admin, technician, cashier, reseller, investor)
- `customers` - End-user accounts (hotspot/PPPoE)
- `nas` - Router/NAS devices with geolocation
- `service_plans` - Internet packages with bandwidth settings
- `vouchers` - Hotspot voucher codes
- `voucher_batches` - Bulk voucher generation tracking
- `voucher_templates` - Reusable voucher templates
- `resellers` - Reseller accounts with balance tracking
- `reseller_transactions` - Reseller balance history
- `invoices` - Customer billing
- `payments` - Payment records
- `transactions` - Financial transaction log
- `tickets` - Support tickets
- `ticket_replies` - Ticket conversation threads
- `radcheck` - RADIUS user authentication
- `radreply` - RADIUS reply attributes (bandwidth, IP)
- `radgroupcheck` - RADIUS group authentication
- `radgroupreply` - RADIUS group reply attributes
- `radusergroup` - User-to-group assignments
- `radacct` - RADIUS accounting (sessions, traffic)
- `radpostauth` - Authentication logs

### Database Schema Details

**Service Plans (`service_plans`)**
| Column | Type | Description |
|--------|------|-------------|
| name | string | Package name |
| code | string | Unique package code |
| type | enum | hotspot, pppoe, dhcp, hybrid |
| price | decimal | Price in Rupiah |
| validity | int | Duration value |
| validity_unit | enum | minutes, hours, days, months |
| bandwidth_up | string | Upload speed (e.g., "5M", "512K") |
| bandwidth_down | string | Download speed |
| quota_bytes | bigint | Data quota in bytes (null=unlimited) |
| has_fup | boolean | Fair Usage Policy enabled |
| fup_bandwidth_up/down | string | Speed after FUP threshold |
| simultaneous_use | int | Max concurrent connections |

**Invoices (`invoices`)**
| Column | Type | Description |
|--------|------|-------------|
| invoice_number | string | Unique invoice ID |
| customer_id | foreign | Customer reference |
| type | enum | subscription, voucher, addon, manual |
| subtotal | decimal | Amount before tax |
| tax | decimal | Tax amount |
| discount | decimal | Discount amount |
| total | decimal | Final amount |
| status | enum | draft, pending, paid, overdue, cancelled |
| issue_date | date | Invoice creation date |
| due_date | date | Payment due date |

**Resellers (`resellers`)**
| Column | Type | Description |
|--------|------|-------------|
| user_id | foreign | Associated user account |
| code | string | Unique reseller code |
| name | string | Reseller name |
| phone | string | Contact number |
| address | text | Physical address |
| balance | decimal | Current balance |
| commission_rate | decimal | Commission percentage |
| is_active | boolean | Active status |

### User Roles

**Platform Level**
- `super_admin` - Full platform access
- `platform_admin` - Manage tenants and support
- `platform_support` - Handle support tickets

**Tenant Level**
- `owner` - Full tenant access
- `admin` - Manage operations
- `technician` - NAS and network management
- `cashier` - Billing and payments
- `reseller` - Sell vouchers with commission
- `investor` - View reports only

## FreeRADIUS Integration

### RADIUS Models
Located in `app/Models/Tenant/`:
- `Radcheck.php` - User authentication (password, expiration)
- `Radreply.php` - Reply attributes (bandwidth, IP)
- `Radgroupcheck.php` - Group authentication
- `Radgroupreply.php` - Group bandwidth settings
- `Radusergroup.php` - User-group assignments
- `Radacct.php` - Session accounting
- `Radpostauth.php` - Auth logs

### RadiusService (`app/Services/RadiusService.php`)
Methods:
- `syncCustomer(Customer)` - Sync customer to RADIUS
- `syncVoucher(Voucher)` - Sync voucher to RADIUS
- `syncServicePlan(ServicePlan)` - Sync plan as RADIUS group
- `removeCustomer(Customer)` - Remove from RADIUS
- `getActiveSessionsForUser(username)` - Get active sessions
- `getUsageStats(username, from, to)` - Get traffic stats

### RADIUS Configuration
Environment variables in `.env`:
```
RADIUS_SERVER=127.0.0.1
RADIUS_SECRET=radiussecret
RADIUS_AUTH_PORT=1812
RADIUS_ACCT_PORT=1813
```

## Router Script Generator

### RouterScriptService (`app/Services/RouterScriptService.php`)
Generates MikroTik RouterOS configuration scripts:

**Available Script Types:**
- `full` - Complete configuration (RADIUS + PPPoE + Hotspot + Firewall)
- `radius` - RADIUS server configuration only
- `pppoe` - PPPoE server with RADIUS auth
- `hotspot` - Hotspot server with RADIUS auth
- `firewall` - Firewall and NAT rules
- `profiles` - Service plan profiles as queues

**Usage:**
1. Navigate to Tenant > Router Scripts
2. Select router and script type
3. Configure parameters (interfaces, IP ranges)
4. Generate and download .rsc file

### Script Features
- RADIUS incoming (CoA) support
- PPPoE server with RADIUS accounting
- Hotspot with captive portal
- IP pools for each service
- Basic firewall rules
- Service plan profiles as queue trees

## Reporting System

### Available Reports
Located in `resources/views/tenant/reports/`:

**Revenue Report** (`revenue.blade.php`)
- Total revenue metrics
- Daily revenue breakdown
- Interactive area charts
- Payment method analysis
- Revenue trends over time

**Customer Report** (`customers.blade.php`)
- Active/inactive customer counts
- Customer growth trends
- Service plan distribution
- New customer acquisition rate

**Sales Report** (`sales.blade.php`)
- Invoice statistics
- Payment collection rate
- Outstanding invoices
- Top-selling service plans

### Chart Integration
Using ApexCharts for interactive data visualization:
- Area charts for revenue trends
- Donut charts for distribution
- Bar charts for comparisons
- Real-time data updates
- Dark mode support

## Reseller Management

### Reseller Features
- Individual reseller accounts with login access
- Balance management (topup/deduct)
- Commission rate configuration
- Transaction history tracking
- Voucher sales tracking
- Performance analytics

### Reseller Methods (`app/Models/Tenant/Reseller.php`)
- `addBalance($amount, $type, $description)` - Add funds
- `deductBalance($amount, $type, $description)` - Deduct funds
- `transactions()` - Get transaction history

## Technical Stack
- **Framework**: Laravel 12
- **PHP Version**: 8.4.10
- **Database**: MySQL (external: 195.88.211.243)
- **Multi-tenancy**: Custom TenantDatabaseManager
- **Permissions**: spatie/laravel-permission v6.23
- **PDF Generation**: barryvdh/laravel-dompdf
- **Excel Export**: maatwebsite/excel
- **Charts**: ApexCharts.js
- **Frontend**: Tailwind CSS, Alpine.js, DataTables
- **Maps**: Leaflet.js for NAS geolocation

## Project Structure
```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Platform/           # Platform admin controllers
│   │   │   ├── TenantController.php
│   │   │   ├── SubscriptionController.php
│   │   │   ├── PlatformInvoiceController.php
│   │   │   ├── PlatformTicketController.php
│   │   │   ├── PlatformUserController.php
│   │   │   └── PlatformSettingsController.php
│   │   └── Tenant/             # Tenant controllers
│   │       ├── NasController.php
│   │       ├── ServicePlanController.php
│   │       ├── CustomerController.php
│   │       ├── VoucherController.php
│   │       ├── InvoiceController.php
│   │       ├── ReportController.php
│   │       ├── RouterScriptController.php
│   │       └── TenantSettingsController.php
│   └── Middleware/
│       ├── SetTenantConnection.php
│       ├── CheckTenantRole.php
│       ├── CheckPlatformRole.php
│       └── CheckTenantLimits.php
├── Models/
│   ├── Tenant/                 # Tenant-specific models
│   │   ├── TenantModel.php     # Base model for tenant DB
│   │   ├── Customer.php
│   │   ├── ServicePlan.php
│   │   ├── Voucher.php
│   │   ├── VoucherBatch.php
│   │   ├── VoucherTemplate.php
│   │   ├── Invoice.php
│   │   ├── Payment.php
│   │   ├── Transaction.php
│   │   ├── Nas.php
│   │   ├── Reseller.php
│   │   ├── ResellerTransaction.php
│   │   ├── Radcheck.php        # RADIUS models
│   │   ├── Radreply.php
│   │   ├── Radacct.php
│   │   ├── Radgroupcheck.php
│   │   ├── Radgroupreply.php
│   │   ├── Radusergroup.php
│   │   ├── Radpostauth.php
│   │   ├── TenantUser.php
│   │   ├── TenantRole.php
│   │   ├── TenantPermission.php
│   │   ├── TenantSetting.php
│   │   ├── Ticket.php
│   │   └── TicketReply.php
│   └── Tenant.php              # Platform tenant model
├── Services/
│   ├── TenantDatabaseManager.php  # Tenant DB connection
│   ├── RadiusService.php          # RADIUS sync service
│   ├── RouterScriptService.php    # Script generator
│   ├── TenantProvisioningService.php
│   ├── TenantUsageService.php
│   └── CpanelService.php
config/
├── radius.php                  # RADIUS configuration
├── tenancy.php                 # Multi-tenancy config
database/
├── migrations/                 # Central migrations
├── migrations/tenant/          # Tenant-specific migrations
│   ├── 2025_01_01_000001_create_tenant_nas_table.php
│   ├── 2025_01_01_000002_create_radius_tables.php
│   ├── 2025_01_01_000003_create_customers_vouchers_table.php
│   ├── 2025_01_01_000004_create_billing_tables.php
│   └── 2025_01_01_000005_create_tenant_users_table.php
└── seeders/
    ├── TenantDataSeeder.php    # Comprehensive dummy data
    ├── RolePermissionSeeder.php
    └── SubscriptionPlanSeeder.php
resources/
└── views/
    ├── layouts/
    │   ├── app.blade.php
    │   ├── guest.blade.php
    │   └── partials/
    ├── auth/
    ├── platform/
    │   ├── tenants/
    │   ├── subscriptions/
    │   ├── invoices/
    │   ├── tickets/
    │   ├── users/
    │   └── settings/
    └── tenant/
        ├── services/           # Service plan views
        ├── customers/
        ├── vouchers/
        ├── invoices/
        ├── nas/
        ├── reports/            # Analytics views
        │   ├── index.blade.php
        │   ├── revenue.blade.php
        │   ├── customers.blade.php
        │   └── sales.blade.php
        ├── router-scripts/     # Script generator views
        └── settings/
```

## Environment Configuration

### Required Environment Variables
```env
# Application
APP_NAME="ISP Manager"
APP_URL=https://your-domain.com

# Database (Central)
DB_CONNECTION=mysql
DB_HOST=195.88.211.243
DB_DATABASE=central_db
DB_USERNAME=user
DB_PASSWORD=pass

# Tenant Mode
TENANT_MODE=cpanel    # or 'tenancy' for VPS

# cPanel (if TENANT_MODE=cpanel)
CPANEL_URL=https://your-cpanel:2083
CPANEL_USERNAME=cpanel_user
CPANEL_API_TOKEN=token
CPANEL_DB_PREFIX=tenant_

# RADIUS
RADIUS_SERVER=127.0.0.1
RADIUS_SECRET=radiussecret
RADIUS_AUTH_PORT=1812
RADIUS_ACCT_PORT=1813
```

### Tenant Database Credentials
Stored in `tenants` table:
- `tenancy_db_name` - Database name
- `tenancy_db_username` - DB username
- `tenancy_db_password` - DB password
- `tenancy_db_host` - DB host

## API Routes

### Platform Routes (authenticated, platform admin)
| Method | URI | Controller | Action |
|--------|-----|------------|--------|
| GET | /platform/tenants | TenantController | index |
| POST | /platform/tenants | TenantController | store |
| GET | /platform/subscriptions | SubscriptionController | index |
| GET | /platform/invoices | PlatformInvoiceController | index |
| GET | /platform/tickets | PlatformTicketController | index |

### Tenant Routes (authenticated, tenant user)
| Method | URI | Controller | Action |
|--------|-----|------------|--------|
| GET | /tenant/services | ServicePlanController | index |
| POST | /tenant/services | ServicePlanController | store |
| GET | /tenant/customers | CustomerController | index |
| POST | /tenant/customers/{id}/suspend | CustomerController | suspend |
| GET | /tenant/invoices | InvoiceController | index |
| POST | /tenant/invoices/{id}/pay | InvoiceController | pay |
| GET | /tenant/reports | ReportController | index |
| GET | /tenant/reports/revenue | ReportController | revenue |
| GET | /tenant/reports/customers | ReportController | customers |
| GET | /tenant/reports/sales | ReportController | sales |
| GET | /tenant/router-scripts | RouterScriptController | index |
| POST | /tenant/router-scripts/generate | RouterScriptController | generate |
| GET | /tenant/nas-map | NasController | map |

## Recent Changes

### 2025-11-27 (Latest)
- Added comprehensive reporting system with 3 report types
- Integrated ApexCharts for interactive data visualization
- Implemented reseller management system
- Added transaction history tracking
- Enhanced dashboard with real-time metrics
- Fixed revenue calculations and currency formatting
- Improved dark mode support across all views
- Added NAS geolocation map with Leaflet.js
- Enhanced mobile responsiveness

### 2025-11-27 (Earlier)
- Added FreeRADIUS integration with all RADIUS models
- Created RadiusService for syncing users to RADIUS
- Created RouterScriptService for MikroTik script generation
- Added router script generator views
- Fixed ServicePlan controller to use correct schema columns
- Added validity_unit support (minutes/hours/days/months)
- Fixed Invoice controller and views
- Added comprehensive dummy data seeder

### 2025-11-26
- Initial project setup with Laravel 12
- Multi-tenant architecture implementation
- Authentication and authorization setup
- Platform and tenant views created

## User Preferences
- Indonesian language for UI text
- Rupiah (Rp) currency format with thousand separator (e.g., Rp 150.000)
- Dark mode support with toggle
- Mobile-responsive design
- DataTables for data listing with search and pagination
- Interactive charts with ApexCharts
- Clean and modern UI with Tailwind CSS

## Testing

### Run Tenant Seeder
```bash
php artisan tinker --execute="
use App\Models\Tenant;
use App\Services\TenantDatabaseManager;

\$tenant = Tenant::first();
TenantDatabaseManager::setTenant(\$tenant);
Artisan::call('db:seed', ['--class' => 'TenantDataSeeder', '--force' => true]);
"
```

### Test RADIUS Connection
```bash
radtest username password localhost 0 testing123
```

### Reseed Tenant Data
```bash
php artisan reseed:tenant {tenant_id}
```

## Deployment Notes

### Server Requirements
- PHP 8.4 or higher
- MySQL 5.7 or higher
- FreeRADIUS server
- Web server (Apache/Nginx)
- Composer
- Node.js & NPM

### Installation Steps
1. Clone repository
2. Run `composer install`
3. Configure `.env` file
4. Run `php artisan key:generate`
5. Run `php artisan migrate`
6. Run `php artisan db:seed`
7. Configure FreeRADIUS to use tenant databases
8. Set up cron for Laravel scheduler
9. Configure web server to point to `/public`

### Production Considerations
- Install Tailwind CSS properly (not via CDN)
- Enable caching: `php artisan config:cache`
- Optimize autoloader: `composer install --optimize-autoloader --no-dev`
- Set up proper SSL certificates
- Configure backup system
- Set up monitoring and logging
- Implement rate limiting
- Configure firewall rules

### Security
- All routes protected by authentication middleware
- Role-based access control (RBAC) implemented
- CSRF protection enabled
- SQL injection prevention via Eloquent ORM
- XSS protection via Blade templating
- Password hashing with bcrypt
- Secure session management

## Performance Optimization
- Database query optimization with eager loading
- Caching for frequently accessed data
- Lazy loading for charts and heavy components
- Pagination for large datasets
- CDN for static assets (production)
- Database indexing on foreign keys
- Connection pooling for multi-tenant databases

## Support & Documentation
- Inline code comments for complex logic
- README.md for setup instructions
- This replit.md for comprehensive documentation
- Laravel 12 official documentation
- FreeRADIUS documentation
- MikroTik RouterOS wiki

## License
Proprietary - All rights reserved

## Development Server
Run on port 5000 (forwarded to 80/443 in production):
```bash
php artisan serve --host=0.0.0.0 --port=5000
```
