# ISP Manager SaaS Platform

## Overview
A comprehensive multi-tenant ISP billing and RADIUS management platform built with Laravel 12. This platform enables ISP operators to manage their MikroTik routers, hotspot users, PPPoE customers, vouchers, and billing in a centralized cloud-based system.

## Project Goals
- Multi-tenant SaaS platform for ISP/hotspot/PPPoE management
- RADIUS authentication and accounting integration
- Voucher generation and management
- Customer billing and invoicing
- Real-time monitoring and reporting
- Role-based access control

## Current State
**Status: Foundation Complete**

The core application structure has been built with:
- Laravel 12 framework with PHP 8.4
- Multi-tenant architecture using stancl/tenancy
- Role-based permissions using spatie/laravel-permission
- Tailwind CSS for UI styling
- DataTables for data management

### Completed Features
- [x] Multi-tenant database architecture
- [x] Authentication system (login, register, forgot password)
- [x] Platform admin dashboard
- [x] Tenant management (CRUD)
- [x] Subscription plan management
- [x] User role management
- [x] Basic dashboard layout with sidebar navigation
- [x] Tenant views for NAS, customers, vouchers, invoices, reports

### Pending Features
- [ ] RADIUS server integration (radcheck, radreply, radacct)
- [ ] MikroTik API integration
- [ ] Voucher generation with QR codes
- [ ] Payment gateway integration (Midtrans, Tripay)
- [ ] Real-time user monitoring
- [ ] Invoice PDF generation
- [ ] Automated billing

## Architecture

### Database Structure
**Central Database (Platform)**
- tenants - ISP tenant accounts
- domains - Tenant subdomain mappings
- users - Platform administrators
- subscription_plans - Available subscription tiers
- tenant_subscriptions - Active subscriptions
- platform_invoices - Platform billing
- platform_tickets - Support tickets

**Tenant Database (Per ISP)**
- users - Tenant staff (owner, admin, technician, cashier, reseller)
- customers - End-user accounts (hotspot/PPPoE)
- nas - Router/NAS devices
- service_plans - Internet packages
- vouchers - Hotspot voucher codes
- invoices - Customer billing
- payments - Payment records
- radcheck/radreply/radacct - RADIUS tables

### User Roles

**Platform Level**
- super_admin - Full platform access
- platform_admin - Manage tenants and support
- platform_support - Handle support tickets

**Tenant Level**
- owner - Full tenant access
- admin - Manage operations
- technician - NAS and network management
- cashier - Billing and payments
- reseller - Sell vouchers
- investor - View reports only

## Technical Stack
- **Framework**: Laravel 12
- **PHP Version**: 8.4.10
- **Database**: MySQL (external: 195.88.211.243)
- **Multi-tenancy**: stancl/tenancy v3.9
- **Permissions**: spatie/laravel-permission v6.23
- **PDF Generation**: barryvdh/laravel-dompdf
- **Excel Export**: maatwebsite/excel
- **Frontend**: Tailwind CSS, Alpine.js, DataTables

## Project Structure
```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Platform/        # Platform admin controllers
│   │   └── Tenant/          # Tenant controllers
│   └── Middleware/
│       ├── PlatformAdminMiddleware.php
│       └── TenantUserMiddleware.php
├── Models/
│   ├── Tenant/              # Tenant-specific models
│   └── *.php                # Platform models
database/
├── migrations/              # Central migrations
└── tenant/                  # Tenant-specific migrations
resources/
└── views/
    ├── layouts/             # Blade layouts
    ├── auth/                # Authentication views
    ├── platform/            # Platform admin views
    └── tenant/              # Tenant views
```

## Environment Configuration
- `TENANT_MODE`: cpanel (for cPanel UAPI) or tenancy (for VPS)
- `DB_*`: External MySQL database credentials
- `CPANEL_*`: cPanel credentials for shared hosting mode

## Recent Changes
- 2025-11-26: Initial project setup with Laravel 12
- 2025-11-26: Multi-tenant architecture implementation
- 2025-11-26: Authentication and authorization setup
- 2025-11-26: Platform and tenant views created
- 2025-11-26: Middleware for role-based access

## User Preferences
- Indonesian language for UI text
- Rupiah (Rp) currency format
- Dark mode support
- Mobile-responsive design
