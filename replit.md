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

## Current State
**Status: Core Features Complete**

The application is now fully functional with:
- Laravel 12 framework with PHP 8.4
- Multi-tenant architecture with separate databases per tenant
- FreeRADIUS integration with full RADIUS tables
- MikroTik router script generator
- Complete CRUD operations with real data

### Completed Features
- [x] Multi-tenant database architecture (separate DB per tenant)
- [x] Authentication system (login, register, forgot password)
- [x] Platform admin dashboard
- [x] Tenant management (CRUD, suspend/activate)
- [x] Subscription plan management
- [x] User role management (platform & tenant level)
- [x] NAS/Router management
- [x] Service plans with bandwidth, FUP, validity (hours/days/months)
- [x] Customer management (PPPoE/Hotspot)
- [x] Voucher generation and management
- [x] Invoice and payment management
- [x] **FreeRADIUS integration** (radcheck, radreply, radacct, etc)
- [x] **MikroTik router script generator**
- [x] Tenant data seeder with dummy data

### Pending Features
- [ ] MikroTik API integration (RouterOS API)
- [ ] Voucher QR code generation
- [ ] Payment gateway integration (Midtrans, Tripay)
- [ ] Real-time user monitoring
- [ ] Invoice PDF generation
- [ ] Automated billing scheduler

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
- `users` - Tenant staff (owner, admin, technician, cashier, reseller)
- `customers` - End-user accounts (hotspot/PPPoE)
- `nas` - Router/NAS devices
- `service_plans` - Internet packages with bandwidth settings
- `vouchers` - Hotspot voucher codes
- `invoices` - Customer billing
- `payments` - Payment records
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
- `reseller` - Sell vouchers
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
- Service plan profiles

## Technical Stack
- **Framework**: Laravel 12
- **PHP Version**: 8.4.10
- **Database**: MySQL (external: 195.88.211.243)
- **Multi-tenancy**: Custom TenantDatabaseManager
- **Permissions**: spatie/laravel-permission v6.23
- **PDF Generation**: barryvdh/laravel-dompdf
- **Excel Export**: maatwebsite/excel
- **Frontend**: Tailwind CSS, Alpine.js, DataTables

## Project Structure
```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Platform/           # Platform admin controllers
│   │   └── Tenant/             # Tenant controllers
│   │       ├── NasController.php
│   │       ├── ServicePlanController.php
│   │       ├── CustomerController.php
│   │       ├── VoucherController.php
│   │       ├── InvoiceController.php
│   │       └── RouterScriptController.php
│   └── Middleware/
├── Models/
│   ├── Tenant/                 # Tenant-specific models
│   │   ├── TenantModel.php     # Base model for tenant DB
│   │   ├── Customer.php
│   │   ├── ServicePlan.php
│   │   ├── Voucher.php
│   │   ├── Invoice.php
│   │   ├── Nas.php
│   │   ├── Radcheck.php        # RADIUS models
│   │   ├── Radreply.php
│   │   ├── Radacct.php
│   │   └── ...
│   └── Tenant.php              # Platform tenant model
├── Services/
│   ├── TenantDatabaseManager.php  # Tenant DB connection
│   ├── RadiusService.php          # RADIUS sync service
│   └── RouterScriptService.php    # Script generator
config/
├── radius.php                  # RADIUS configuration
database/
├── migrations/                 # Central migrations
├── migrations/tenant/          # Tenant-specific migrations
│   ├── 2025_01_01_000001_create_tenant_nas_table.php
│   ├── 2025_01_01_000002_create_radius_tables.php
│   ├── 2025_01_01_000003_create_customers_vouchers_table.php
│   └── 2025_01_01_000004_create_billing_tables.php
└── seeders/
    └── TenantDataSeeder.php    # Dummy data seeder
resources/
└── views/
    ├── layouts/
    ├── auth/
    ├── platform/
    └── tenant/
        ├── services/           # Service plan views
        ├── customers/
        ├── vouchers/
        ├── invoices/
        ├── nas/
        └── router-scripts/     # Script generator views
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

### Tenant Routes (authenticated)
| Method | URI | Controller | Action |
|--------|-----|------------|--------|
| GET | /tenant/services | ServicePlanController | index |
| POST | /tenant/services | ServicePlanController | store |
| GET | /tenant/customers | CustomerController | index |
| POST | /tenant/customers/{id}/suspend | CustomerController | suspend |
| GET | /tenant/invoices | InvoiceController | index |
| POST | /tenant/invoices/{id}/pay | InvoiceController | pay |
| GET | /tenant/router-scripts | RouterScriptController | index |
| POST | /tenant/router-scripts/generate | RouterScriptController | generate |

## Recent Changes

### 2025-11-27
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
- Rupiah (Rp) currency format with thousand separator
- Dark mode support
- Mobile-responsive design
- DataTables for data listing

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

## Deployment Notes

1. Ensure MySQL database is accessible from server
2. Configure proper timezone in `config/app.php`
3. Set up cron for Laravel scheduler
4. Configure FreeRADIUS to use tenant database
5. Update router scripts with actual RADIUS server IP
