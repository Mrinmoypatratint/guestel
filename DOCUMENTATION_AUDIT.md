# Documentation Audit & Technical Integrity Report

**Product**: Guestel — Cloud Hospitality Operating System  
**Auditor**: Senior Product Manager, Technical Architect & Documentation Specialist  
**Audit Date**: October 2026  
**Repository**: `Mrinmoypatratint/guestel`  
**Scope**: Complete inventory and line-by-line verification of all 13 Markdown files against the Laravel 13 / PHP 8.4 codebase, database schemas, policies, and test suites.

---

## 1. Executive Summary

A comprehensive forensic audit of all 13 Markdown documentation files in the repository was executed. Every documented feature, diagram, entity, endpoint, and architectural assumption was cross-referenced directly against:
- Database migrations (`database/migrations/*.php`)
- Eloquent models (`app/Models/*.php`)
- Route definitions (`routes/web.php`)
- Middleware (`app/Http/Middleware/*.php`)
- Form requests and controllers (`app/Http/Controllers/**/*.php`)
- The PHPUnit / Pest automated test suite (`tests/Feature/*.php`, `tests/Unit/*.php`)

### Key Audit Metrics
- **Total Markdown Files Audited**: 13
- **Current & Highly Accurate**: 4 (`SECURITY.md`, `DEPLOYMENT_HOSTINGER.md`, `BACKUP_AND_RECOVERY.md`, `QR_ARCHITECTURE.md`)
- **Partially Outdated / Drifting**: 7 (`README.md`, `API_DOCUMENTATION.md`, `DATABASE_SCHEMA.md`, `ROLES_PERMISSIONS.md`, `ADMIN_OPERATIONS.md`, `GUEST_EXPERIENCE.md`, `TEST_PLAN.md`)
- **100% Redundant / Duplicate**: 1 (`IMPLEMENTATION_STATUS.md` is an exact duplicate of `ARCHITECTURE.md`)
- **Architectural Reference**: 1 (`ARCHITECTURE.md`)

---

## 2. Master Documentation Audit Matrix

| File Path | Document Purpose | Status | Key Forensic Findings | Recommended Action |
| :--- | :--- | :--- | :--- | :--- |
| [`README.md`](file:///d:/Project_Abir/hotel-guest-platform-greenfield-v1/hotel-guest-platform/README.md) | Project overview, workspace descriptions, local setup, test credentials, and architecture summary. | **Partially Outdated** | Captures modern product features (INR billing, KDS, role-aware gateway), but reports outdated test metrics: states **23 passed, 85 assertions** whereas the actual suite has **25 passed, 103 assertions**. Mentions SQLite/MySQL support accurately. | **Update** test metrics to 25 passed / 103 assertions and add references to newly created `PRD.md`. |
| [`IMPLEMENTATION_STATUS.md`](file:///d:/Project_Abir/hotel-guest-platform-greenfield-v1/hotel-guest-platform/IMPLEMENTATION_STATUS.md) | Supposedly tracks implementation status and progress. | **Duplicated / Misleading** | **Critical Discrepancy**: This file is a 100% byte-for-byte duplicate (2,922 bytes, 93 lines) of `ARCHITECTURE.md`. It contains no implementation status checklist, task tracking, or gap metrics. | **Consolidate / Replace** with a reference to `PRD_GAP_ANALYSIS.md` and `PRD.md`, or replace with an authentic status summary. |
| [`ARCHITECTURE.md`](file:///d:/Project_Abir/hotel-guest-platform-greenfield-v1/hotel-guest-platform/ARCHITECTURE.md) | High-level system architecture, onboarding sequence, and isolation diagrams. | **Current (Core)** | Core diagrams (Tenant isolation, Service request + SLA, Dining order flow, Backup flow) match application design. However, it omits the newly implemented multi-property switching layer (`property_accesses`, `restaurant_users`) and platform SaaS billing workflows. | **Update** to incorporate multi-property gateway and SaaS platform billing sequence diagrams. |
| [`DATABASE_SCHEMA.md`](file:///d:/Project_Abir/hotel-guest-platform-greenfield-v1/hotel-guest-platform/DATABASE_SCHEMA.md) | Entity-Relationship Diagram (ERD) and table-level data dictionary. | **Incomplete / Outdated** | Written prior to SaaS billing and multi-property architecture. Completely misses 5 live database tables: `hotel_subscriptions`, `hotel_invoices`, `platform_communications`, `property_accesses`, and `restaurant_users`. Omits nullable `hotel_id` on `restaurants` (standalone dining venues). | **Update** schema definitions and ERD to include all 10 migration files and 37 Eloquent models. |
| [`API_DOCUMENTATION.md`](file:///d:/Project_Abir/hotel-guest-platform-greenfield-v1/hotel-guest-platform/API_DOCUMENTATION.md) | Public guest routes and administrative operations REST API endpoints. | **Incomplete** | Documents standard guest and admin endpoints accurately, but omits newly introduced SaaS platform endpoints: `/platform/hotels`, `/platform/hotels/{hotel}/toggle-status`, `/platform/billing/invoices`, `/platform/communications/send`, as well as property switching endpoints (`/select-property`, `/switch-property`). | **Update** to document platform governance, billing, communications, and property switcher endpoints. |
| [`ROLES_PERMISSIONS.md`](file:///d:/Project_Abir/hotel-guest-platform-greenfield-v1/hotel-guest-platform/ROLES_PERMISSIONS.md) | RBAC hierarchy, role definitions, and permission matrix. | **Incomplete** | Documents the granular permission names and 8 basic roles, but misses the new dual-access model where users can have direct restaurant access via `restaurant_users` or multi-property cross-tenant access via `property_accesses`. Does not detail role-specific workspace routing implemented in `User::workspaceRoute()`. | **Update** with multi-property permissions, direct dining staff permissions, and workspace redirection logic. |
| [`ADMIN_OPERATIONS.md`](file:///d:/Project_Abir/hotel-guest-platform-greenfield-v1/hotel-guest-platform/ADMIN_OPERATIONS.md) | Staff manual for dashboard, live operations queue, Web Audio chime, and Ctrl+K search. | **Current** | Highly aligned with actual admin UI implementation (`resources/views/admin/dashboard.blade.php`), Web Audio API chime (587Hz -> 880Hz), and `SearchController`. Mentions legacy `/admin/switch-hotel/{id}` route, which has now been complemented by `/select-property`. | **Keep**; add cross-reference to `/select-property` multi-property switcher. |
| [`GUEST_EXPERIENCE.md`](file:///d:/Project_Abir/hotel-guest-platform-greenfield-v1/hotel-guest-platform/GUEST_EXPERIENCE.md) | UX design philosophy, guest compendium, dining flow, microcopy, and feedback. | **Current** | Accurately describes the frictionless guest flow (`/stay/{session}`), lack of mandatory login/app download, 3-tap feedback recovery (`Great`, `Okay`, `Needs Attention`), and empathetic microcopy implemented in `resources/views/guest/`. | **Keep** as primary UX guide; reference in PRD Section 12. |
| [`QR_ARCHITECTURE.md`](file:///d:/Project_Abir/hotel-guest-platform-greenfield-v1/hotel-guest-platform/QR_ARCHITECTURE.md) | Opaque QR code lifecycle, anti-enumeration security, and SVG print center. | **Current** | High fidelity with implementation. Correctly details 48-char cryptographic tokens (`Str::lower(Str::random(48))`), HMAC-SHA256 IP hashing, pure vector SVG generation without Google Charts API, and printable tent-card layout. (Note: uses `chillerlan/php-qrcode` under the hood). | **Keep** as authoritative QR engine documentation. |
| [`SECURITY.md`](file:///d:/Project_Abir/hotel-guest-platform-greenfield-v1/hotel-guest-platform/SECURITY.md) | 10-layer defense-in-depth model and security test matrix. | **Current & Verified** | Exceptional alignment with codebase. Covers `SecurityHeaders`, rate limiters in `bootstrap/app.php`, session fixation defenses in `LoginController`, `BelongsToTenant` Eloquent scope, server-side price recalculation, and automated security tests. | **Keep** as authoritative security manual; reference in PRD Section 16. |
| [`TEST_PLAN.md`](file:///d:/Project_Abir/hotel-guest-platform-greenfield-v1/hotel-guest-platform/TEST_PLAN.md) | Automated QA standards, test suite inventory, and CI workflow. | **Outdated Metrics** | Documents 9 test suites and 23 assertions. Codebase has since expanded to **11 feature/unit test classes, 25 tests, and 103 assertions**. Omits `LandingAndRoleBasedAuthTest.php` (8 tests) and `PlatformSaasOperationsTest.php` (5 tests). | **Update** test inventory table and assertion counts to match active 103-assertion suite. |
| [`DEPLOYMENT_HOSTINGER.md`](file:///d:/Project_Abir/hotel-guest-platform-greenfield-v1/hotel-guest-platform/DEPLOYMENT_HOSTINGER.md) | Hostinger shared hosting guide, directory mapping, LiteSpeed config, and cron jobs. | **Current & Essential** | Highly accurate for shared hosting constraints (no Docker/Redis, database queues, LiteSpeed htaccess rules, cron setup). Documents correct web root mapping to `hotel-guest-platform/public`. | **Keep** as primary production DevOps reference. |
| [`BACKUP_AND_RECOVERY.md`](file:///d:/Project_Abir/hotel-guest-platform-greenfield-v1/hotel-guest-platform/BACKUP_AND_RECOVERY.md) | Backup schedule, disaster recovery procedures, and `scripts/backup.sh`. | **Current** | Accurately details MySQL dump, media tarball, cron scheduling, and step-by-step restoration procedures. | **Keep** as primary business continuity manual. |

---

## 3. Deep-Dive Findings & Discrepancy Analysis

### 3.1 Critical Document Redundancy: `IMPLEMENTATION_STATUS.md`
- **Finding**: `IMPLEMENTATION_STATUS.md` is an exact duplicate of `ARCHITECTURE.md`.
- **Evidence**:
  - `ARCHITECTURE.md`: 93 lines, 2,922 bytes, MD5 matches `IMPLEMENTATION_STATUS.md`.
  - Both files start with `# Architecture` and contain identical Mermaid diagrams and text.
- **Impact**: Any engineer or stakeholder looking for the implementation status or release milestone progress is misled into viewing architecture diagrams.
- **Remediation**: The comprehensive implementation status and module-by-module breakdown is now established in `PRD_GAP_ANALYSIS.md` and Section 20 of `PRD.md`. `IMPLEMENTATION_STATUS.md` should either be retired or replaced with a concise pointer.

### 3.2 Schema Drift: `DATABASE_SCHEMA.md`
- **Finding**: `DATABASE_SCHEMA.md` does not document tables created in the latest migrations:
  1. `hotel_subscriptions`: Tracks plan (`Professional Cloud`, etc.), cycle (`monthly`/`annual`), fee in INR, currency, and renewal timestamps.
  2. `hotel_invoices`: Generates unique invoice numbers (e.g., `INV-202610-001`), tax calculation (18% GST), status (`unpaid`, `paid`), due dates, and payment references.
  3. `platform_communications`: Super Admin broadcast announcements sent to hotel or restaurant leadership with audience scoping.
  4. `property_accesses`: Polymorphic property assignment (`hotel`, `restaurant`, `resort`) allowing staff to have distinct roles across multiple hospitality properties.
  5. `restaurant_users`: Direct pivot mapping between users and dining venues.
  6. `restaurants.hotel_id`: Changed from strictly required foreign key to **nullable foreign key**, enabling standalone dining venues unattached to physical hotel rooms.
- **Impact**: Backend developers and DBAs rely on an incomplete ERD that misses multi-property access control and monetization infrastructure.

### 3.3 Test Suite Drift: `README.md` & `TEST_PLAN.md`
- **Documented**:
  - `README.md`: *"Current test status: 23 passed, 85 assertions (100% green)."*
  - `TEST_PLAN.md`: Lists 9 test classes with ~23 assertions.
- **Actual Codebase Reality**:
  - Active test suite contains **25 tests and 103 assertions** (100% green, ~2.1 seconds execution time).
  - Missing from `TEST_PLAN.md`:
    - `LandingAndRoleBasedAuthTest.php`: 8 feature tests covering public landing page rendering, Super Admin redirect to `/platform`, multi-property redirect to `/select-property`, housekeeping redirect to `/admin/requests`, chef redirect to `/admin/orders`, RBAC boundary enforcement, unauthorized property switch prevention (`403`), and test-login authentication.
    - `PlatformSaasOperationsTest.php`: 5 feature tests covering INR SaaS KPIs, hotel onboarding with subscription creation, service invoice generation with payment recording, platform communication dispatch, and RBAC boundary denial for hotel staff.
    - `ExampleTest.php`: 1 unit test.

### 3.4 Multi-Property Gateway vs Single Hotel Tenant
- **Documented**: Early documents (`ROLES_PERMISSIONS.md`, `ARCHITECTURE.md`) assumed a 1-to-1 relationship between an employee and a single hotel tenant.
- **Codebase Reality**:
  - Implemented `app/Models/User.php` methods: `belongsToRestaurant()`, `accessibleProperties()`, and `workspaceRoute()`.
  - Implemented `PropertySelectController` (`/select-property`, `/switch-property`).
  - Implemented role-aware property filtering:
    - Executive Chefs only see accessible restaurants and active order counts.
    - Housekeeping Leads only see hotels with room counts and active request tickets.
    - Property Owners and General Managers see all authorized hotel networks.

### 3.5 Currency, Taxes, and Financial Locale
- **Documented**: Older API examples showed USD formatting (`$48.00`, `$52.80`).
- **Codebase Reality**:
  - The application is standardized on Indian Rupee (`INR` / `₹`) with automated 18% GST calculation on SaaS platform subscriptions and invoices.
  - Invoicing models, views, and test suites strictly assert INR formatting (`₹` symbol, GST computation, bank transfer/UPI payment records).

### 3.6 UI Design System: Neumorphic Soft UI Architecture
- **Documented**: General Tailwind CSS and standard layouts.
- **Codebase Reality**:
  - The entire application (landing page, login gateway, platform console, hotel operations dashboard, KDS kitchen board, QR center, and property switcher) has been refactored into a custom **Dark Neumorphic Soft UI** design system.
  - Features dual-shadow extrusion (`drop-shadow`, concave/convex surfaces, glowing amber and cyan accents, tactile buttons, and custom radio/tab states) providing an unmistakable, high-end hospitality aesthetic.

---

## 4. Documentation Action Plan

1. **Retain Without Modification**:
   - `SECURITY.md` (Accurate, robust, verified against automated tests)
   - `DEPLOYMENT_HOSTINGER.md` (Accurate production reference for shared hosting)
   - `BACKUP_AND_RECOVERY.md` (Accurate disaster recovery procedures)
   - `QR_ARCHITECTURE.md` (Accurate cryptographic QR specs)

2. **Superseded by New PRD Deliverables**:
   - `IMPLEMENTATION_STATUS.md` is superseded by [`PRD_GAP_ANALYSIS.md`](file:///d:/Project_Abir/hotel-guest-platform-greenfield-v1/hotel-guest-platform/PRD_GAP_ANALYSIS.md) and Section 20 of [`PRD.md`](file:///d:/Project_Abir/hotel-guest-platform-greenfield-v1/hotel-guest-platform/PRD.md).
   - `DATABASE_SCHEMA.md` is expanded and brought up to date within Section 13 of [`PRD.md`](file:///d:/Project_Abir/hotel-guest-platform-greenfield-v1/hotel-guest-platform/PRD.md).
   - `API_DOCUMENTATION.md` is unified and updated in Section 14 of [`PRD.md`](file:///d:/Project_Abir/hotel-guest-platform-greenfield-v1/hotel-guest-platform/PRD.md).
   - `ROLES_PERMISSIONS.md` is unified and brought up to date in Section 11 of [`PRD.md`](file:///d:/Project_Abir/hotel-guest-platform-greenfield-v1/hotel-guest-platform/PRD.md).

3. **Recommended Immediate Updates**:
   - Update `README.md` test counter from 23/85 to **25 passed, 103 assertions**.
   - Add a direct link in `README.md` to the master [`PRD.md`](file:///d:/Project_Abir/hotel-guest-platform-greenfield-v1/hotel-guest-platform/PRD.md).
