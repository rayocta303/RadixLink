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

### Subscription Plans
Available subscription plans (from SubscriptionPlanSeeder):
1. **Free (Trial)** - slug: `free` - Rp 0
2. **Starter** - slug: `starter` - Rp 99.000/bulan
3. **Basic** - slug: `basic` - Rp 199.000/bulan
4. **Professional** - slug: `professional` - Rp 399.000/bulan
5. **Business** - slug: `business` - Rp 799.000/bulan
6. **Enterprise** - slug: `enterprise` - Rp 1.499.000/bulan
7. **Platinum (Unlimited)** - slug: `platinum` - Rp 2.999.000/bulan

### Platform Roles
- `super_admin` - Full access to all platform features
- `platform_admin` - Manage tenants, subscriptions, tickets
- `platform_cashier` - Manage billing and invoices
- `platform_technician` - Server and RADIUS monitoring
- `platform_support` - Handle support tickets

### Default Login Credentials
- **Super Admin**: admin@ispmanager.id / admin123
- **Platform Admin**: platform@ispmanager.id / admin123
- **Support Staff**: support@ispmanager.id / support123
- **Cashier Staff**: cashier@ispmanager.id / cashier123
- **Technician Staff**: technician@ispmanager.id / technician123