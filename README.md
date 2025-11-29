# ISP Manager - Multi-Tenant SaaS Platform

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12-red?style=for-the-badge&logo=laravel" alt="Laravel 12">
  <img src="https://img.shields.io/badge/PHP-8.4-blue?style=for-the-badge&logo=php" alt="PHP 8.4">
  <img src="https://img.shields.io/badge/MySQL-8.0-orange?style=for-the-badge&logo=mysql" alt="MySQL">
  <img src="https://img.shields.io/badge/TailwindCSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css" alt="Tailwind CSS">
</p>

<p align="center">
  Platform manajemen ISP berbasis cloud yang komprehensif untuk mengelola router MikroTik, pelanggan Hotspot/PPPoE, voucher, dan billing dalam arsitektur multi-tenant.
</p>

---

## Daftar Isi

- [Tentang Proyek](#tentang-proyek)
- [Fitur Utama](#fitur-utama)
- [Arsitektur Sistem](#arsitektur-sistem)
- [Struktur Database](#struktur-database)
- [Alur Proses (Flowchart)](#alur-proses-flowchart)
- [Role & Permission](#role--permission)
- [Instalasi](#instalasi)
- [Konfigurasi](#konfigurasi)
- [Penggunaan](#penggunaan)
- [API Reference](#api-reference)
- [Technology Stack](#technology-stack)

---

## Tentang Proyek

ISP Manager adalah platform SaaS (Software as a Service) yang dirancang khusus untuk Internet Service Provider (ISP) di Indonesia. Platform ini menyediakan solusi lengkap untuk:

- **Manajemen Router MikroTik** - Konfigurasi otomatis via script generator
- **Autentikasi RADIUS** - Integrasi penuh dengan FreeRADIUS
- **Manajemen Pelanggan** - Hotspot dan PPPoE users
- **Sistem Voucher** - Generate, print, dan manage voucher
- **Billing & Invoice** - Automated billing system
- **Multi-Tenancy** - Setiap ISP memiliki database terpisah

---

## Fitur Utama

### Platform Level (Super Admin)
- Manajemen Tenant (ISP)
- Subscription Plans Management
- Platform Monitoring
- Platform Invoicing
- Support Ticket System
- Activity Logging

### Tenant Level (ISP Owner/Admin)
- NAS/Router Management dengan Map View
- Customer Management (Hotspot & PPPoE)
- Service Plan Configuration
- Voucher Generation & Templates
- IP Pool Management
- Bandwidth Profile Management
- PPPoE & Hotspot Server Configuration
- Invoice & Payment Management
- Financial Reports
- Reseller Management
- Router Script Generator

---

## Arsitektur Sistem

### High-Level Architecture

```mermaid
flowchart TB
    subgraph Internet["Internet"]
        User[("User Browser")]
        MikroTik[("MikroTik Router")]
    end
    
    subgraph Platform["ISP Manager Platform"]
        LB["Load Balancer"]
        App["Laravel Application"]
        
        subgraph Services["Core Services"]
            Auth["Auth Service"]
            Tenant["Tenant Service"]
            Radius["RADIUS Service"]
            Router["Router Script Service"]
        end
        
        subgraph Databases["Databases"]
            CentralDB[("Central DB\n(Platform)")]
            TenantDB1[("Tenant DB 1\n(ISP A)")]
            TenantDB2[("Tenant DB 2\n(ISP B)")]
            TenantDBn[("Tenant DB N\n(ISP N)")]
        end
    end
    
    subgraph External["External Services"]
        FreeRADIUS[("FreeRADIUS\nServer")]
        cPanel["cPanel API"]
    end
    
    User --> LB
    LB --> App
    App --> Services
    Auth --> CentralDB
    Tenant --> CentralDB
    Tenant --> TenantDB1
    Tenant --> TenantDB2
    Tenant --> TenantDBn
    Radius --> FreeRADIUS
    Router --> MikroTik
    App --> cPanel
    MikroTik --> FreeRADIUS
```

### Multi-Tenancy Architecture

```mermaid
flowchart LR
    subgraph Central["Central Platform"]
        direction TB
        CentralApp["Platform App"]
        CentralDB[("Central Database")]
        
        CentralApp --> CentralDB
    end
    
    subgraph Tenants["Tenant Isolation"]
        direction TB
        
        subgraph T1["Tenant: ISP A"]
            T1App["Tenant App Context"]
            T1DB[("berkahcu_t_ispa")]
        end
        
        subgraph T2["Tenant: ISP B"]
            T2App["Tenant App Context"]
            T2DB[("berkahcu_t_ispb")]
        end
        
        subgraph T3["Tenant: ISP C"]
            T3App["Tenant App Context"]
            T3DB[("berkahcu_t_ispc")]
        end
    end
    
    Central --> T1
    Central --> T2
    Central --> T3
    
    T1App --> T1DB
    T2App --> T2DB
    T3App --> T3DB
```

### Request Flow

```mermaid
sequenceDiagram
    participant U as User
    participant M as Middleware
    participant C as Controller
    participant S as Service
    participant DB as Database
    participant R as RADIUS
    
    U->>M: HTTP Request
    M->>M: Authenticate User
    M->>M: Identify Tenant
    M->>M: Set Tenant DB Connection
    M->>C: Forward Request
    C->>S: Business Logic
    S->>DB: Query Tenant DB
    DB-->>S: Result
    
    alt RADIUS Operation
        S->>R: Sync RADIUS Data
        R-->>S: Confirmation
    end
    
    S-->>C: Response Data
    C-->>U: HTTP Response
```

---

## Struktur Database

### Central Database (Platform)

```mermaid
erDiagram
    USERS ||--o{ PLATFORM_ACTIVITY_LOGS : creates
    USERS {
        bigint id PK
        string name
        string email UK
        string password
        string role
        timestamp last_login_at
        timestamps created_updated
    }
    
    TENANTS ||--o{ DOMAINS : has
    TENANTS ||--o| TENANT_SUBSCRIPTIONS : has
    TENANTS {
        uuid id PK
        string name
        string email UK
        string company_name
        string subdomain UK
        string phone
        text address
        string status
        string tenancy_db_name
        string tenancy_db_host
        timestamps created_updated
    }
    
    DOMAINS {
        bigint id PK
        string domain UK
        uuid tenant_id FK
        boolean is_primary
        timestamps created_updated
    }
    
    SUBSCRIPTION_PLANS ||--o{ TENANT_SUBSCRIPTIONS : used_by
    SUBSCRIPTION_PLANS {
        bigint id PK
        string name
        string slug UK
        decimal price_monthly
        decimal price_yearly
        int max_routers
        int max_users
        int max_vouchers
        json features
        boolean is_active
        timestamps created_updated
    }
    
    TENANT_SUBSCRIPTIONS {
        bigint id PK
        uuid tenant_id FK
        bigint plan_id FK
        date starts_at
        date ends_at
        string status
        timestamps created_updated
    }
    
    PLATFORM_ACTIVITY_LOGS {
        bigint id PK
        bigint user_id FK
        string action
        string entity_type
        bigint entity_id
        text description
        json old_values
        json new_values
        timestamps created_updated
    }
```

### Tenant Database (Per ISP)

```mermaid
erDiagram
    NAS ||--o{ CUSTOMERS : serves
    NAS ||--o{ IP_POOLS : contains
    NAS ||--o{ HOTSPOT_SERVERS : has
    NAS ||--o{ PPPOE_SERVERS : has
    NAS {
        bigint id PK
        string name
        string shortname UK
        string nasname
        string secret
        string type
        string api_username
        string api_password
        int api_port
        boolean is_active
        decimal longitude
        decimal latitude
        timestamps created_updated
    }
    
    SERVICE_PLANS ||--o{ CUSTOMERS : subscribes
    SERVICE_PLANS ||--o{ VOUCHERS : generates
    SERVICE_PLANS {
        bigint id PK
        string name
        string code UK
        string type
        decimal price
        int validity
        string bandwidth_up
        string bandwidth_down
        bigint quota_bytes
        boolean is_active
        timestamps created_updated
    }
    
    CUSTOMERS ||--o{ INVOICES : has
    CUSTOMERS ||--o{ CUSTOMER_SESSIONS : logs
    CUSTOMERS {
        bigint id PK
        string username UK
        string password
        string name
        string email
        string phone
        string service_type
        string status
        timestamp expires_at
        decimal balance
        timestamps created_updated
    }
    
    VOUCHERS {
        bigint id PK
        string code UK
        bigint service_plan_id FK
        string status
        string type
        int max_usage
        int used_count
        decimal price
        string batch_id
        timestamp activated_at
        timestamp expires_at
        timestamps created_updated
    }
    
    INVOICES ||--o{ PAYMENTS : receives
    INVOICES {
        bigint id PK
        string invoice_number UK
        bigint customer_id FK
        decimal subtotal
        decimal tax
        decimal total
        string status
        date issue_date
        date due_date
        timestamps created_updated
    }
    
    IP_POOLS ||--o{ PPPOE_PROFILES : uses
    IP_POOLS ||--o{ HOTSPOT_PROFILES : uses
    IP_POOLS {
        bigint id PK
        string name
        string pool_name UK
        string range_start
        string range_end
        bigint nas_id FK
        string type
        int total_ips
        int used_ips
        timestamps created_updated
    }
    
    BANDWIDTH_PROFILES ||--o{ PPPOE_PROFILES : uses
    BANDWIDTH_PROFILES ||--o{ HOTSPOT_PROFILES : uses
    BANDWIDTH_PROFILES {
        bigint id PK
        string name
        string name_bw UK
        string rate_up
        string rate_down
        string burst_limit_up
        string burst_limit_down
        int priority
        timestamps created_updated
    }
    
    PPPOE_PROFILES ||--o{ PPPOE_SERVERS : configures
    PPPOE_PROFILES {
        bigint id PK
        string name
        string profile_name UK
        bigint nas_id FK
        bigint ip_pool_id FK
        bigint bandwidth_id FK
        string local_address
        string dns_server
        int session_timeout
        timestamps created_updated
    }
    
    HOTSPOT_PROFILES ||--o{ HOTSPOT_SERVERS : configures
    HOTSPOT_PROFILES {
        bigint id PK
        string name
        string profile_name UK
        bigint nas_id FK
        bigint ip_pool_id FK
        bigint bandwidth_id FK
        int shared_users
        int session_timeout
        timestamps created_updated
    }
    
    RADCHECK {
        bigint id PK
        string username
        string attribute
        string op
        string value
    }
    
    RADREPLY {
        bigint id PK
        string username
        string attribute
        string op
        string value
    }
    
    RADACCT {
        bigint radacctid PK
        string acctsessionid
        string username
        string nasipaddress
        timestamp acctstarttime
        timestamp acctstoptime
        int acctsessiontime
        bigint acctinputoctets
        bigint acctoutputoctets
    }
```

---

## Alur Proses (Flowchart)

### 1. Tenant Provisioning Flow

```mermaid
flowchart TD
    A[Super Admin Creates Tenant] --> B{Tenant Mode?}
    
    B -->|cPanel| C[Call cPanel API]
    C --> D[Create Database via cPanel]
    D --> E[Set Database Privileges]
    
    B -->|Local| F[Create Database Directly]
    
    E --> G[Store Tenant Credentials]
    F --> G
    
    G --> H[Run Tenant Migrations]
    H --> I[Seed Default Data]
    I --> J[Create Tenant Admin User]
    J --> K[Setup Domain/Subdomain]
    K --> L[Tenant Ready]
    
    L --> M{Send Welcome Email?}
    M -->|Yes| N[Send Credentials to Owner]
    M -->|No| O[Done]
    N --> O
```

### 2. Customer Registration Flow

```mermaid
flowchart TD
    A[Start] --> B[Admin Creates Customer]
    B --> C[Select Service Type]
    
    C -->|Hotspot| D[Generate Hotspot Credentials]
    C -->|PPPoE| E[Generate PPPoE Credentials]
    
    D --> F[Select Service Plan]
    E --> F
    
    F --> G[Calculate Expiry Date]
    G --> H[Save Customer Data]
    
    H --> I[Sync to RADIUS]
    I --> J{Sync Success?}
    
    J -->|Yes| K[Create radcheck Entry]
    K --> L[Create radreply Entry]
    L --> M[Create radusergroup Entry]
    M --> N[Customer Active]
    
    J -->|No| O[Log Error]
    O --> P[Retry or Manual Fix]
    
    N --> Q[Generate Invoice]
    Q --> R[End]
```

### 3. Voucher Generation Flow

```mermaid
flowchart TD
    A[Admin Requests Voucher Generation] --> B[Select Service Plan]
    B --> C[Enter Quantity]
    C --> D[Configure Options]
    
    D --> E{Code Type?}
    E -->|Numeric| F[Generate Numeric Codes]
    E -->|Alphanumeric| G[Generate Alphanumeric Codes]
    E -->|Custom Prefix| H[Generate with Prefix]
    
    F --> I[Create Batch Record]
    G --> I
    H --> I
    
    I --> J[Loop: Generate Each Voucher]
    J --> K[Generate Unique Code]
    K --> L[Create Voucher Record]
    L --> M{More Vouchers?}
    
    M -->|Yes| J
    M -->|No| N[Batch Complete]
    
    N --> O{Print Vouchers?}
    O -->|Yes| P[Select Template]
    P --> Q[Generate PDF]
    Q --> R[Download/Print]
    O -->|No| S[End]
    R --> S
```

### 4. Voucher Activation Flow

```mermaid
flowchart TD
    A[Customer Enters Voucher Code] --> B[Validate Code Format]
    B --> C{Code Valid?}
    
    C -->|No| D[Show Error: Invalid Code]
    C -->|Yes| E[Find Voucher in Database]
    
    E --> F{Voucher Exists?}
    F -->|No| G[Show Error: Code Not Found]
    F -->|Yes| H{Check Status}
    
    H -->|Used| I[Show Error: Already Used]
    H -->|Expired| J[Show Error: Expired]
    H -->|Unused| K[Check Usage Count]
    
    K --> L{Max Usage Reached?}
    L -->|Yes| M[Show Error: Max Usage]
    L -->|No| N[Activate Voucher]
    
    N --> O[Create/Update Customer]
    O --> P[Set Expiry Based on Plan]
    P --> Q[Sync to RADIUS]
    Q --> R[Update Voucher Status]
    R --> S[Log Transaction]
    S --> T[Customer Can Connect]
```

### 5. RADIUS Authentication Flow

```mermaid
sequenceDiagram
    participant C as Customer Device
    participant R as MikroTik Router
    participant F as FreeRADIUS
    participant DB as Tenant Database
    
    C->>R: Connection Request
    R->>F: Access-Request (username, password)
    
    F->>DB: Query radcheck table
    DB-->>F: User credentials
    
    alt Password Match
        F->>DB: Query radreply table
        DB-->>F: Reply attributes
        F->>DB: Query radusergroup
        DB-->>F: Group membership
        F-->>R: Access-Accept + Attributes
        R-->>C: Connection Granted
        
        Note over R,F: Session Started
        R->>F: Accounting-Start
        F->>DB: Insert radacct record
        
        loop Every Interval
            R->>F: Accounting-Update
            F->>DB: Update radacct
        end
        
        R->>F: Accounting-Stop
        F->>DB: Complete radacct record
    else Password Mismatch
        F-->>R: Access-Reject
        R-->>C: Connection Denied
        F->>DB: Log to radpostauth
    end
```

### 6. Invoice & Payment Flow

```mermaid
flowchart TD
    A[Service Period Ends] --> B[System Generates Invoice]
    B --> C[Calculate Amount]
    C --> D[Apply Tax if Any]
    D --> E[Create Invoice Record]
    E --> F[Send Notification to Customer]
    
    F --> G{Customer Pays?}
    
    G -->|Online Payment| H[Process Payment Gateway]
    H --> I{Payment Success?}
    I -->|Yes| J[Record Payment]
    I -->|No| K[Mark as Failed]
    K --> G
    
    G -->|Manual Payment| L[Admin Records Payment]
    L --> J
    
    J --> M[Update Invoice Status: Paid]
    M --> N[Extend Service Period]
    N --> O[Update Customer Expiry]
    O --> P[Sync to RADIUS]
    P --> Q[Log Transaction]
    Q --> R[Send Receipt]
    
    G -->|No Payment| S{Due Date Passed?}
    S -->|No| G
    S -->|Yes| T[Mark as Overdue]
    T --> U[Send Reminder]
    U --> V{Grace Period Expired?}
    V -->|No| G
    V -->|Yes| W[Suspend Customer]
    W --> X[Remove from RADIUS]
```

### 7. Router Script Generation Flow

```mermaid
flowchart TD
    A[Admin Selects Script Type] --> B{Script Type?}
    
    B -->|Full Config| C[Generate Complete Setup]
    B -->|RADIUS Only| D[Generate RADIUS Config]
    B -->|PPPoE Server| E[Generate PPPoE Config]
    B -->|Hotspot Server| F[Generate Hotspot Config]
    B -->|Firewall| G[Generate Firewall Rules]
    
    C --> H[Include All Components]
    
    D --> I[RADIUS Client Settings]
    I --> J[Authentication Settings]
    
    E --> K[PPPoE Server Settings]
    K --> L[PPPoE Profiles]
    L --> M[IP Pool Assignment]
    
    F --> N[Hotspot Server Settings]
    N --> O[Hotspot Profiles]
    O --> P[Walled Garden]
    P --> Q[Login Page Settings]
    
    G --> R[NAT Rules]
    R --> S[Filter Rules]
    S --> T[Mangle Rules]
    
    H --> U[Compile Script]
    J --> U
    M --> U
    Q --> U
    T --> U
    
    U --> V[Display Script]
    V --> W{Action?}
    
    W -->|Copy| X[Copy to Clipboard]
    W -->|Download| Y[Download .rsc File]
    W -->|API Push| Z[Push to Router via API]
    
    Z --> AA{Push Success?}
    AA -->|Yes| AB[Confirm Applied]
    AA -->|No| AC[Show Error]
```

### 8. Report Generation Flow

```mermaid
flowchart TD
    A[Admin Opens Reports] --> B[Select Report Type]
    
    B -->|Revenue| C[Revenue Report]
    B -->|Customer| D[Customer Report]
    B -->|Sales| E[Sales Report]
    
    C --> F[Select Date Range]
    D --> F
    E --> F
    
    F --> G[Query Database]
    G --> H[Aggregate Data]
    H --> I[Calculate Metrics]
    
    I --> J[Generate Charts]
    J --> K[Display Dashboard]
    
    K --> L{Export?}
    L -->|PDF| M[Generate PDF Report]
    L -->|Excel| N[Generate Excel File]
    L -->|No| O[View Only]
    
    M --> P[Download File]
    N --> P
    O --> Q[End]
    P --> Q
```

---

## Role & Permission

### Platform Roles

```mermaid
graph TD
    subgraph Platform["Platform Level Roles"]
        SA[Super Admin]
        PA[Platform Admin]
        PS[Platform Support]
        PC[Platform Cashier]
        PT[Platform Technician]
    end
    
    SA -->|Full Access| ALL[All Platform Features]
    PA -->|Manage| TEN[Tenants & Subscriptions]
    PA -->|Manage| USR[Platform Users]
    PS -->|Handle| TKT[Support Tickets]
    PC -->|Manage| INV[Platform Invoices]
    PT -->|Monitor| MON[System Monitoring]
```

### Tenant Roles

```mermaid
graph TD
    subgraph Tenant["Tenant Level Roles"]
        OW[Owner]
        AD[Admin]
        TE[Technician]
        CA[Cashier]
        RE[Reseller]
    end
    
    OW -->|Full Access| ALL2[All Tenant Features]
    AD -->|Manage| USR2[Users & Customers]
    AD -->|Manage| NAS[NAS Devices]
    TE -->|Configure| NET[Network Settings]
    TE -->|Generate| SCR[Router Scripts]
    CA -->|Process| PAY[Payments & Invoices]
    CA -->|Generate| VOU[Vouchers]
    RE -->|Sell| VOU2[Vouchers Only]
    RE -->|View| BAL[Own Balance & Sales]
```

### Permission Matrix

| Feature | Owner | Admin | Technician | Cashier | Reseller |
|---------|-------|-------|------------|---------|----------|
| Dashboard | ✅ | ✅ | ✅ | ✅ | ✅ |
| NAS Management | ✅ | ✅ | ✅ | ❌ | ❌ |
| Customer Management | ✅ | ✅ | ✅ | ✅ | ❌ |
| Service Plans | ✅ | ✅ | ❌ | ❌ | ❌ |
| Voucher Generate | ✅ | ✅ | ❌ | ✅ | ❌ |
| Voucher Sell | ✅ | ✅ | ❌ | ✅ | ✅ |
| Invoice Management | ✅ | ✅ | ❌ | ✅ | ❌ |
| Reports | ✅ | ✅ | ❌ | ✅ | ❌ |
| User Management | ✅ | ✅ | ❌ | ❌ | ❌ |
| Settings | ✅ | ❌ | ❌ | ❌ | ❌ |
| Router Scripts | ✅ | ✅ | ✅ | ❌ | ❌ |
| IP Pools | ✅ | ✅ | ✅ | ❌ | ❌ |
| Bandwidth Profiles | ✅ | ✅ | ✅ | ❌ | ❌ |

---

## Instalasi

### Prerequisites

- PHP 8.2 atau lebih tinggi
- Composer
- MySQL 8.0 atau lebih tinggi
- Node.js & NPM
- FreeRADIUS Server (untuk autentikasi)

### Langkah Instalasi

```bash
# Clone repository
git clone https://github.com/yourusername/isp-manager.git
cd isp-manager

# Install PHP dependencies
composer install

# Install Node dependencies
npm install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure database di .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=isp_manager
# DB_USERNAME=root
# DB_PASSWORD=

# Run migrations
php artisan migrate

# Seed database
php artisan db:seed

# Build assets
npm run build

# Start server
php artisan serve --host=0.0.0.0 --port=5000
```

---

## Konfigurasi

### Environment Variables

```env
# Application
APP_NAME="ISP Manager"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://ispmanager.id

# Database (Central)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=isp_manager
DB_USERNAME=root
DB_PASSWORD=secret

# Tenant Mode
TENANT_MODE=cpanel  # atau 'local'

# cPanel Integration (jika TENANT_MODE=cpanel)
CPANEL_HOST=domain.com
CPANEL_PORT=2083
CPANEL_USERNAME=cpanel_user
CPANEL_PASSWORD=cpanel_pass

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=587
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=tls
```

### Tenant Database Modes

#### Mode: cPanel
Menggunakan cPanel API untuk provisioning database otomatis.

```php
// config/tenancy.php
'mode' => 'cpanel',
'cpanel' => [
    'host' => env('CPANEL_HOST'),
    'port' => env('CPANEL_PORT', 2083),
    'username' => env('CPANEL_USERNAME'),
    'password' => env('CPANEL_PASSWORD'),
],
```

#### Mode: Local
Membuat database langsung tanpa cPanel.

```php
// config/tenancy.php
'mode' => 'local',
```

---

## Penggunaan

### Login Credentials (Default)

**Super Admin:**
- Email: `superadmin@ispmanager.id`
- Password: `password`

**Tenant Admin:**
- Email: `admin@{subdomain}.ispmanager.id`
- Password: `password`

### Quick Start Guide

1. **Login sebagai Super Admin**
2. **Buat Tenant Baru**
   - Masukkan nama perusahaan ISP
   - Pilih subdomain
   - Pilih subscription plan
3. **Login ke Tenant Dashboard**
4. **Tambah Router/NAS**
   - Masukkan IP address router
   - Masukkan RADIUS secret
   - Konfigurasi API credentials
5. **Buat Service Plan**
   - Set bandwidth, validity, harga
6. **Generate Voucher**
   - Pilih service plan
   - Tentukan jumlah voucher
7. **Configure Router**
   - Generate script dari Router Scripts menu
   - Copy dan paste ke MikroTik terminal

---

## API Reference

### Authentication

```http
POST /api/login
Content-Type: application/json

{
    "email": "user@example.com",
    "password": "password"
}
```

### Customers

```http
GET /api/tenant/customers
Authorization: Bearer {token}

POST /api/tenant/customers
Authorization: Bearer {token}
Content-Type: application/json

{
    "username": "user001",
    "password": "secret123",
    "name": "John Doe",
    "service_plan_id": 1,
    "service_type": "hotspot"
}
```

### Vouchers

```http
POST /api/tenant/vouchers/generate
Authorization: Bearer {token}
Content-Type: application/json

{
    "service_plan_id": 1,
    "quantity": 100,
    "prefix": "WIFI",
    "code_length": 8
}
```

---

## Technology Stack

### Backend
- **Framework:** Laravel 12
- **PHP Version:** 8.4
- **Database:** MySQL 8.0
- **Multi-tenancy:** Custom TenantDatabaseManager
- **Permissions:** spatie/laravel-permission
- **PDF:** barryvdh/laravel-dompdf
- **Excel:** maatwebsite/excel

### Frontend
- **CSS Framework:** Tailwind CSS 3.x
- **JavaScript:** Alpine.js
- **Charts:** ApexCharts.js
- **Maps:** Leaflet.js
- **DataTables:** DataTables.js
- **Icons:** Heroicons

### External Services
- **RADIUS:** FreeRADIUS
- **Router:** MikroTik RouterOS
- **Hosting:** cPanel Integration

---

## Kontribusi

Kontribusi sangat diterima! Silakan buat pull request atau buka issue untuk bug reports dan feature requests.

## Lisensi

Proyek ini dilisensikan di bawah [MIT License](LICENSE).

---

<p align="center">
  Made with ❤️ for Indonesian ISPs
</p>
