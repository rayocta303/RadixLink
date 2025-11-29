# ISP Manager SaaS Platform

## Overview
The ISP Manager SaaS Platform is a comprehensive, multi-tenant cloud-based solution designed for Internet Service Providers (ISPs). Built with Laravel 12, its primary purpose is to centralize the management of MikroTik routers, hotspot and PPPoE users, vouchers, and billing operations. The platform aims to streamline ISP operations, offering robust RADIUS authentication and accounting, automated MikroTik script generation, and a reseller management system, thereby enhancing operational efficiency and market responsiveness. This solution targets rapid market penetration by providing a scalable and feature-rich environment for ISPs to manage their services effectively.

## User Preferences
- Indonesian language for UI text
- Rupiah (Rp) currency format with thousand separator (e.g., Rp 150.000)
- Dark mode support with toggle
- Mobile-responsive design
- DataTables for data listing with search and pagination
- Interactive charts with ApexCharts
- Clean and modern UI with Tailwind CSS

## System Architecture

### Multi-tenancy
The platform employs a multi-tenant architecture where each ISP (tenant) operates with its own segregated database, ensuring data isolation and security. A central database manages platform-level entities like tenants, subscription plans, and platform administrators. Tenant-specific databases store customer data, NAS devices, service plans, vouchers, invoices, and RADIUS-related information.

### Database Structure
- **Central Database (Platform):** Manages `tenants`, `domains`, `users` (platform admins), `subscription_plans`, `tenant_subscriptions`, `platform_invoices`, and `platform_tickets`.
- **Tenant Database (Per ISP):** Contains `users` (tenant staff), `customers`, `nas` (routers), `service_plans`, `vouchers`, `voucher_batches`, `voucher_templates`, `resellers`, `reseller_transactions`, `invoices`, `payments`, `transactions`, `tickets`, `ticket_replies`, and all FreeRADIUS related tables (`radcheck`, `radreply`, `radgroupcheck`, `radgroupreply`, `radusergroup`, `radacct`, `radpostauth`).

### User Roles
- **Platform Level:** `super_admin`, `platform_admin`, `platform_support`.
- **Tenant Level:** `owner`, `admin`, `technician`, `cashier`, `reseller`, `investor`.

### Technical Implementations
- **RADIUS Integration:** Full integration with FreeRADIUS, utilizing dedicated models (`Radcheck`, `Radreply`, `Radacct`, etc.) and a `RadiusService` for synchronizing customer, voucher, and service plan data. Supports RADIUS authentication, accounting, and active session management.
- **Router Script Generator:** A `RouterScriptService` generates MikroTik RouterOS configuration scripts for various purposes (full config, RADIUS, PPPoE, Hotspot, Firewall, profiles). These scripts automate the setup of RADIUS, PPPoE/Hotspot servers, IP pools, and firewall rules on MikroTik devices.
- **Reporting System:** Provides comprehensive `Revenue`, `Customer`, and `Sales` reports with interactive visualizations using ApexCharts.
- **Reseller Management:** Enables individual reseller accounts with balance management, commission tracking, transaction history, and voucher sales analytics.
- **UI/UX:** Utilizes Tailwind CSS and Alpine.js for a modern, mobile-responsive interface, incorporating features like dark mode and interactive data tables with DataTables. Leaflet.js is used for NAS geolocation mapping.

### Technology Stack
- **Framework**: Laravel 12
- **PHP Version**: 8.4.10
- **Database**: MySQL
- **Multi-tenancy**: Custom `TenantDatabaseManager`
- **Permissions**: `spatie/laravel-permission`
- **Charts**: ApexCharts.js
- **Frontend**: Tailwind CSS, Alpine.js, DataTables, Leaflet.js

## External Dependencies

- **FreeRADIUS Server:** Essential for RADIUS authentication and accounting for hotspot and PPPoE users.
- **MikroTik Routers:** The primary network hardware managed by the platform, requiring RouterOS for script generation and configuration.
- **MySQL Database:** Used as the primary data store for both the central platform and individual tenant databases.
- **ApexCharts.js:** Integrated for generating interactive data visualizations and reports.
- **Leaflet.js:** Used for displaying NAS device locations on geographical maps.
- **barryvdh/laravel-dompdf:** For PDF generation (e.g., invoices).
- **maatwebsite/excel:** For exporting data to Excel format.

## Recent Changes (November 27, 2025)

### Database Migrations
- Removed duplicate `activity_logs` table from central database migration
- Changed all enum types to string types for better database compatibility
- Platform activity logs now use `platform_activity_logs` table
- Tenant activity logs use `activity_logs` table in tenant databases
- Added tenant database credential columns to tenants table:
  - `tenancy_db_name` - Tenant database name
  - `tenancy_db_username` - Database username
  - `tenancy_db_password` - Database password (encrypted)
  - `tenancy_db_host` - Database host

### Tenant Provisioning
- Registration now uses `TenantProvisioningService` for automatic tenant creation
- New tenants get 'free' plan by default with limited resources
- Tenant database is automatically created via cPanel when in 'cpanel' mode
- Plan limits are defined per subscription tier (see Subscription Plans)

### Subscription Plans
Available subscription plans (from SubscriptionPlanSeeder):
1. **Free** - slug: `free` - Rp 0 (1 router, 50 users, 100 vouchers)
2. **Starter** - slug: `starter` - Rp 99.000/bulan (5 routers, 1000 users, 10K vouchers)
3. **Basic** - slug: `basic` - Rp 199.000/bulan (10 routers, 2000 users, 20K vouchers)
4. **Professional** - slug: `professional` - Rp 399.000/bulan (25 routers, 5000 users, 50K vouchers)
5. **Business** - slug: `business` - Rp 799.000/bulan (50 routers, 10K users, 100K vouchers)
6. **Enterprise** - slug: `enterprise` - Rp 1.499.000/bulan (100 routers, 50K users, 500K vouchers)
7. **Platinum (Unlimited)** - slug: `platinum` - Rp 2.999.000/bulan (Unlimited resources)

### Platform Roles
- `super_admin` - Full access to all platform features
- `platform_admin` - Manage tenants, subscriptions, tickets
- `platform_cashier` - Manage billing and invoices
- `platform_technician` - Server and RADIUS monitoring
- `platform_support` - Handle support tickets
- `tenant_owner` - Tenant owner (platform-level role for tenant users)

### Default Login Credentials
- **Super Admin**: admin@ispmanager.id / admin123
- **Platform Admin**: platform@ispmanager.id / admin123
- **Support Staff**: support@ispmanager.id / support123
- **Cashier Staff**: cashier@ispmanager.id / cashier123
- **Technician Staff**: technician@ispmanager.id / technician123

### Bug Fixes
- Fixed DashboardController to use `PlatformActivityLog` model instead of `ActivityLog`
- Removed incorrect `tenant_id` filtering from platform activity logs query
- Removed trial feature (trial_ends_at) from all models, migrations, and views
- Updated platform monitoring to show "Tenant Baru Bulan Ini" instead of "Masa Trial"

## Recent Changes (November 29, 2025)

### Route Name Fixes
- Fixed PPPoE view route names (12 routes total):
  - `tenant.pppoe.create-profile` → `tenant.pppoe.profiles.create`
  - `tenant.pppoe.edit-profile` → `tenant.pppoe.profiles.edit`
  - `tenant.pppoe.destroy-profile` → `tenant.pppoe.profiles.destroy`
  - `tenant.pppoe.create-server` → `tenant.pppoe.servers.create`
  - `tenant.pppoe.edit-server` → `tenant.pppoe.servers.edit`
  - `tenant.pppoe.destroy-server` → `tenant.pppoe.servers.destroy`
- Fixed Hotspot view route names (same pattern as PPPoE)

### Controller Fixes
- **VoucherTemplateController**: Fixed pagination issue - returns `LengthAwarePaginator` instead of `Collection` when tenant DB not connected
- **UserController**: Removed route model binding to prevent "Database connection [tenant] not configured" errors
  - Changed method signatures from `TenantUser $user` to `int $id`
  - Added `checkConnection()` and `connectionErrorRedirect()` helper methods
  - Connection check now runs BEFORE any database query

### View Fixes
- **tenant/users/index.blade.php**: Added dbError alert block and disabled "Tambah User" button when tenant database is not connected

### TenantSeeder Improvements
- Added `seedTenantRoles()` method - seeds 43 permissions and 7 roles (owner, admin, technician, cashier, support, reseller, investor)
- Added `seedTenantOwnerUser()` method - creates owner user with role assignment for each new tenant
- Tenant owner credentials: `owner@{subdomain}.id` / `owner123`

### Tenant Database Schema
All tenant migrations verified and complete:
1. `create_tenant_nas_table` - nas, service_plans tables
2. `create_radius_tables` - radcheck, radreply, radgroupcheck, radgroupreply, radusergroup, radacct, radpostauth
3. `create_customers_vouchers_table` - customers, vouchers, voucher_templates, voucher_batches
4. `create_billing_tables` - invoices, payments, transactions, resellers, reseller_transactions
5. `create_tenant_users_table` - users, roles, permissions, model_has_roles, model_has_permissions, role_has_permissions, tenant_settings, tickets, ticket_replies
6. `create_network_management_tables` - ip_pools, bandwidth_profiles, pppoe_profiles, hotspot_profiles, hotspot_servers, pppoe_servers, customer_sessions
7. `create_tenant_activity_logs_table` - activity_logs

### Tenant Roles (in tenant database)
- `owner` - Full access to all tenant features
- `admin` - Manage operations: users, NAS, plans, vouchers
- `technician` - Technical access: debug, monitoring, NAS management
- `cashier` - Transactions: print vouchers, manage invoices, payments
- `support` - Light technical help: reset customer accounts
- `reseller` - Sub-tenant: manage own clients, top-up balance
- `investor` - View-only: access financial reports

## Recent Changes (November 29, 2025 - Session 2)

### TenantSeeder Migration Fix
- Fixed `runTenantMigrations()` method in TenantSeeder.php:
  - Using consistent connection name 'tenant_migration' instead of random suffix
  - Setting `strict: false` in database config to avoid strict mode issues
  - Added `Schema::connection($connectionName)->disableForeignKeyConstraints()` before migrations
  - Enabling foreign key constraints after migration completes
  - Better error handling with detailed logging
  - Confirming database connection before running migrations
- All 35 tenant tables now created successfully:
  - nas, service_plans
  - radcheck, radreply, radgroupcheck, radgroupreply, radusergroup, radacct, radpostauth
  - customers, vouchers, voucher_templates, voucher_batches
  - invoices, payments, transactions, resellers, reseller_transactions
  - users, password_reset_tokens, roles, permissions, model_has_roles, model_has_permissions, role_has_permissions
  - tenant_settings, tickets, ticket_replies
  - ip_pools, bandwidth_profiles, pppoe_profiles, hotspot_profiles, hotspot_servers, pppoe_servers, customer_sessions
  - activity_logs

### Documentation Update
- Created comprehensive README.md with:
  - Project overview and features
  - System architecture with Mermaid diagrams
  - Database structure with ERD diagrams
  - 8 detailed flowcharts:
    1. Tenant Provisioning Flow
    2. Customer Registration Flow
    3. Voucher Generation Flow
    4. Voucher Activation Flow
    5. RADIUS Authentication Flow (Sequence Diagram)
    6. Invoice & Payment Flow
    7. Router Script Generation Flow
    8. Report Generation Flow
  - Role & Permission matrix
  - Installation guide
  - Configuration guide
  - API reference
  - Technology stack