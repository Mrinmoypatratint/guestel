# Guestel — Cloud Hospitality Operating System

> **One intelligent platform for every part of hospitality.**  
> Built with Laravel 13, PHP 8.3+, Blade, Alpine.js, Tailwind CSS, and Vite.

Guestel is a modern, enterprise multi-tenant hospitality operating system powering independent boutique hotels, luxury resorts, and dining venues. It unifies SaaS platform governance, hotel front-desk operations, operational housekeeping with SLA tracking, and an interactive Kitchen Operations Kanban Board with Kitchen Display Mode (KDS).

---

## 🌟 Key Product Workspaces

### 1. Public Luxury Landing Page (`/`)
- Bespoke, high-end hospitality showcase titled *"Hospitality, beautifully connected"*.
- Live interactive operations feed (real-time service SLA tracking).
- 7 distinct operational modules: Guest Experience, Hotel Operations, Restaurant & F&B, Housekeeping, Service SLA Management, Billing & Invoicing (₹ INR with 18% GST), and Multi-Property Intelligence.
- Enterprise zero-trust security overview.

### 2. Role-Aware Split-Screen Login (`/login`)
- 4-card role gateway selector (*Company Super Admin*, *Hotel Operations*, *Housekeeping Lead*, *Executive Chef / F&B*).
- Server-authoritative role verification and session regeneration.
- Development-only quick-fill assist for frictionless local testing.

### 3. Multi-Property Selector & Switcher (`/select-property`, `/switch-property`)
- Role-aware filtering: Chefs only see restaurants; Housekeepers only see hotels with room queues; Executives see authorized hospitality networks.
- Tamper-proof server-side authorization check (HTTP 403 on IDOR or unauthorized property switch).

### 4. Company Super Admin Console (`/platform`)
- High-level multi-tenant SaaS governance.
- Hotel and resort onboarding.
- SaaS invoicing and subscription billing in ₹ INR.
- Network broadcast communications to hotels and restaurants.

### 5. Hotel Operations Admin (`/admin`)
- Real-time room status and occupancy metrics.
- Service requests, orders, and guest messaging.
- Room-level opaque QR management and high-resolution print sheets.

### 6. Housekeeping Operations Queue (`/admin/requests`)
- Categorized urgency tabs: `URGENT`, `HIGH`, `NORMAL`, `COMPLETED`.
- Live SLA countdown timers with warning thresholds.
- Task categorization for linens, towels, amenities, sanitization, and room inspections.

### 7. Executive Chef & Kitchen Operations Board (`/admin/orders`)
- 6-stage Kanban flow: `NEW` ➔ `ACCEPTED` ➔ `PREPARING` ➔ `READY` ➔ `DELIVERED` ➔ `CANCELLED`.
- Fullscreen high-contrast **Kitchen Display Mode (KDS)** for kitchen tablets and monitors.
- Operational status toggles: `OPEN`, `PAUSED`, `CLOSED`, and `+15 min Rush Delay`.

### 8. Contactless QR Guest Portal (`/g/{token}`)
- Privacy-preserving cryptographic QR scanning.
- In-room digital dining compendium and instant room service orders.
- Direct-to-department service requests (Housekeeping, Maintenance, Front Desk).

---

## 🔑 Demo & Test Credentials (Local Environment)

All demo accounts use the standard development password: **`Admin12345!`**

| Role | Email | Target Route | Operational Scope |
| :--- | :--- | :--- | :--- |
| **Company Super Admin** | `admin@example.com` | `/platform` | Multi-tenant SaaS governance, hotel onboarding, billing & notices. |
| **Hotel Admin** | `admin@example.com` | `/admin` | Grand Azure Hotel (24 Rooms) & Royal Palms Luxury Resort. |
| **Housekeeping Lead** | `maria.santos@grandazure.com` | `/admin/requests` | Grand Azure Hotel Housekeeping. SLA timers, linens & amenities. |
| **Executive Chef** | `chef.marcus@grandazure.com` | `/admin/orders` | The Azure Grill & The Saffron Pavilion. Live tickets & KDS. |

---

## 🚀 Quickstart & Local Installation

### Prerequisites
- PHP 8.3+ with `pdo`, `mbstring`, `openssl`, `sqlite3` or `mysql` extensions
- Composer 2.x
- Node.js 20+ & npm

### Setup Steps
```bash
# 1. Clone repository
git clone https://github.com/Mrinmoypatratint/guestel.git
cd guestel

# 2. Install PHP and JS dependencies
composer install
npm install

# 3. Configure environment
cp .env.example .env
php artisan key:generate

# 4. Run migrations and demo seeds
# (Supports SQLite out of the box or MySQL/MariaDB)
php artisan migrate:fresh --seed

# 5. Link storage for demo images
php artisan storage:link

# 6. Build assets
npm run build

# 7. Start local server
php artisan serve
```

Access the application in your browser:
- **Public Landing Page**: `http://127.0.0.1:8000`
- **Role-Based Login**: `http://127.0.0.1:8000/login`

---

## 🧪 Automated Testing

Guestel includes an end-to-end feature test suite validating multi-tenant isolation, tenant switching, role-based redirects, IDOR defense, and ordering idempotency:

```bash
php artisan test
```

Current test status: **23 passed, 85 assertions** (100% green).

---

## 🛡️ Architecture & Security Highlights

- **Server-Side Authorization**: The backend resolves the user's role and allowed properties on every request. Client-submitted IDs are strictly validated.
- **Tenant Context Isolation**: Eloquent global tenant scopes prevent cross-property data leaks.
- **CSRF & Session Security**: Cryptographic session tokens, regeneration upon login, and secure cookie attributes.
- **Rate-Limiting & Throttling**: Brute-force protection on authentication and sensitive operational routes.

---

## 📄 License

Proprietary — Hotel Guest Platform Technologies. All rights reserved.
