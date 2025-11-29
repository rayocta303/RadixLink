# ISP Manager SaaS Platform

## Overview
The ISP Manager SaaS Platform is a multi-tenant cloud-based solution for Internet Service Providers (ISPs). Its core purpose is to centralize the management of MikroTik routers, hotspot and PPPoE users, vouchers, and billing operations. The platform aims to streamline ISP operations through robust RADIUS authentication and accounting, automated MikroTik script generation, and a reseller management system. This enhances operational efficiency and market responsiveness, providing a scalable and feature-rich environment for ISPs.

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
The platform utilizes a multi-tenant architecture with segregated databases for each ISP (tenant) to ensure data isolation. A central database manages platform-level entities like tenants and subscription plans, while tenant-specific databases store all customer, service, and RADIUS-related data. Tenant provisioning includes automatic database creation and initial setup.

### Database Structure
- **Central Database (Platform):** Manages `tenants`, `domains`, `users` (platform admins), `subscription_plans`, `tenant_subscriptions`, `platform_invoices`, and `platform_tickets`.
- **Tenant Database (Per ISP):** Contains `users` (tenant staff), `customers`, `nas` (routers), `service_plans`, `vouchers`, `voucher_batches`, `voucher_templates`, `resellers`, `reseller_transactions`, `invoices`, `payments`, `transactions`, `tickets`, `ticket_replies`, and all FreeRADIUS related tables (`radcheck`, `radreply`, `radgroupcheck`, `radgroupreply`, `radusergroup`, `radacct`, `radpostauth`), along with network management tables (`ip_pools`, `bandwidth_profiles`, `pppoe_profiles`, `hotspot_profiles`, `hotspot_servers`, `pppoe_servers`, `customer_sessions`).

### User Roles
- **Platform Level:** `super_admin`, `platform_admin`, `platform_support`.
- **Tenant Level:** `owner`, `admin`, `technician`, `cashier`, `reseller`, `investor`.

### Technical Implementations
- **RADIUS Integration:** Full integration with FreeRADIUS for authentication, accounting, and active session management, synchronizing customer, voucher, and service plan data.
- **Router Script Generator:** Generates MikroTik RouterOS configuration scripts for various configurations (RADIUS, PPPoE, Hotspot, Firewall, profiles) to automate device setup.
- **Reporting System:** Provides `Revenue`, `Customer`, and `Sales` reports with interactive ApexCharts visualizations.
- **Reseller Management:** Features individual reseller accounts with balance management, commission tracking, and transaction history.
- **UI/UX:** Built with Tailwind CSS and Alpine.js for a modern, mobile-responsive interface, including dark mode, interactive DataTables, and Leaflet.js for NAS geolocation.
- **Service Plan Enhancement:** Service plans are fully integrated with MikroTik configurations, including `router_name`, `pool`, `prepaid` status, and MikroTik script generation methods (`generateMikrotikScript`, `generatePppoeScript`, `generateHotspotScript`, `generateUserScript`).

### Technology Stack
- **Framework**: Laravel 12
- **PHP Version**: 8.4.10
- **Database**: MySQL
- **Multi-tenancy**: Custom `TenantDatabaseManager`
- **Permissions**: `spatie/laravel-permission`
- **Charts**: ApexCharts.js
- **Frontend**: Tailwind CSS, Alpine.js, DataTables, Leaflet.js

## External Dependencies

- **FreeRADIUS Server:** Used for RADIUS authentication and accounting.
- **MikroTik Routers:** The primary network hardware managed by the platform, configured via RouterOS scripts.
- **MySQL Database:** The primary data store for both the central platform and tenant databases.
- **ApexCharts.js:** Integrated for interactive data visualizations.
- **Leaflet.js:** Used for displaying NAS device locations on maps.
- **barryvdh/laravel-dompdf:** For PDF generation.
- **maatwebsite/excel:** For exporting data to Excel.