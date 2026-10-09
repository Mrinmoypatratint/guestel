# PRD Gap Analysis: Documentation vs. Codebase vs. Future Capabilities

**Product**: Guestel — Cloud Hospitality Operating System  
**Version**: 1.0.0-PROD  
**Author**: Senior Product Manager & Software Architect  
**Date**: October 2026  
**Repository**: `Mrinmoypatratint/guestel`

---

## 1. Executive Summary

This Gap Analysis provides an exhaustive, factual comparison between:
1. **Documented Requirements**: Claims made in the original repository Markdown documentation.
2. **Current Codebase Reality**: Actual implementations in Laravel 13, migrations, Eloquent models, controllers, Blade templates, and automated tests.
3. **Proposed & Recommended Capabilities**: Enterprise features, architectural hardening, and production enhancements needed for enterprise SaaS maturity.

### Key Takeaways
- **The codebase is significantly more capable than originally documented in several core areas**:
  - A complete **SaaS Platform Governance & Billing engine** exists in code with INR pricing, 18% GST calculation, tax invoicing, and platform announcements.
  - A full **Multi-Property Switcher** exists with role-aware property filtering (Chefs see kitchens; Housekeepers see hotels with room queues; Admins see property networks).
  - An interactive **Kitchen Display System (KDS)** and **Housekeeping SLA queue** are fully operational with Neumorphic UI.
- **However, critical documentation was never updated to reflect these advances**, creating a documentation-reality drift.
- **Several advanced features remain simulated or partially implemented**:
  - Real-time live feeds currently operate via periodic HTTP page reloads or local state rather than live WebSockets/Pusher.
  - Guest chat relies on database rows and staff dashboard notifications rather than instant push notifications or SMS/WhatsApp integration.
  - Background jobs on Hostinger shared hosting rely on periodic cron runners rather than persistent daemon supervisors.

---

## 2. Comprehensive Module-by-Module Gap Analysis

### 2.1 SaaS Platform Governance & Billing
| Dimension | Documented State | Actual Codebase Reality | Proposed Enterprise Target | Gap Status |
| :--- | :--- | :--- | :--- | :--- |
| **Hotel Onboarding** | Mentioned conceptually in `ARCHITECTURE.md` as `CreateHotelAction`. | Implemented in `Platform\HotelController` with automatic subscription generation, branding record creation, and admin user creation. | Self-service multi-tenant onboarding wizard with email domain verification and Stripe/Razorpay automated subscription setup. | **Ahead of Docs** (Functional in code) |
| **SaaS Billing & Invoicing** | Undocumented in original `DATABASE_SCHEMA.md` and `API_DOCUMENTATION.md`. | Fully implemented via `HotelSubscription` and `HotelInvoice` models, `Platform\BillingController`, supporting INR (`₹`), 18% GST calculation, payment reference logging, and PDF-style print invoices. | Automated webhook-driven payment collection (Razorpay/Stripe auto-debit), overdue dunning alerts, and tax invoice exports (GSTR-1). | **Ahead of Docs** (Requires doc update) |
| **Broadcast Communications** | Not mentioned in any original documentation. | Implemented via `PlatformCommunication` model and `Platform\CommunicationController`, allowing Super Admins to send category-tagged messages to hotel admins or dining staff. | Rich-text email dispatcher via AWS SES/Mailgun with delivery rate, open rate, and bounce tracking. | **Ahead of Docs** (Functional in code) |

---

### 2.2 Authentication, Multi-Property RBAC & Gateway
| Dimension | Documented State | Actual Codebase Reality | Proposed Enterprise Target | Gap Status |
| :--- | :--- | :--- | :--- | :--- |
| **Role-Aware Landing Gateway** | Only standard `/login` was documented. | Split-screen Neumorphic gateway (`resources/views/auth/login.blade.php`) featuring 4 role cards (*Super Admin*, *Hotel Operations*, *Housekeeping Lead*, *Executive Chef*). | Enterprise SSO (SAML 2.0 / Okta / Google Workspace) and mandatory Multi-Factor Authentication (TOTP 2FA). | **Ahead of Docs** (Functional in code) |
| **Multi-Property Switching** | Documented as single-tenant with conceptual `/admin/switch-hotel/{id}` for platform admins only. | Implemented via `property_accesses` and `restaurant_users` tables, `PropertySelectController` (`/select-property`, `/switch-property`), and `User::accessibleProperties()`. Enforces role-based property isolation. | Multi-property portfolio analytics roll-up and regional cluster permissions. | **Ahead of Docs** (Functional in code) |
| **One-Click Test Login** | Undocumented. | Implemented via `LoginController::testLogin` (`/test-login`) with explicit verification in test suite (`tc auth 008`). Disabled in production or restricted to staging. | Strict environment guard (`app()->environment('local', 'testing')`) to prevent credential bypass in production. | **Verified in Code** |

---

### 2.3 Guest Digital Concierge Experience
| Dimension | Documented State | Actual Codebase Reality | Proposed Enterprise Target | Gap Status |
| :--- | :--- | :--- | :--- | :--- |
| **Contactless QR Resolution** | Documented in `QR_ARCHITECTURE.md` (48-char opaque token, session creation, HMAC IP logging). | Fully implemented via `GuestQrController`, `GuestSession`, and `QrScan`. Cryptographically secure and tested against enumeration. | Dynamic geolocation bounding to prevent remote off-property QR spoofing. | **Fully Aligned** |
| **Room Compendium & Services** | Documented in `GUEST_EXPERIENCE.md`. | Implemented in `StayController::show` and `resources/views/guest/home.blade.php`. Displays hotel branding, Wi-Fi info, hotel policies, facilities, and service requests. | Multi-language localization (i18n) for international guests (English, Spanish, French, Japanese, Arabic). | **Aligned with Code** |
| **Guest Chat** | Documented as bi-directional chat in `ARCHITECTURE.md`. | Basic database-backed messaging via `StayController::message` and `ConversationController`. Records messages and dispatches database notifications. | Real-time WebSocket chat (Pusher/Reverb) or direct WhatsApp Business API concierge integration. | **Partial** (Functional via HTTP; lacks live socket push) |
| **Service Recovery Feedback** | Documented 3-tap feedback (`Great`, `Okay`, `Needs Attention`). | Implemented in `StayController::feedback` and `Feedback` model. | Instant SMS/Push alert to Duty Manager when "Needs Attention" is triggered during an active stay. | **Aligned with Code** |

---

### 2.4 In-Room Dining & Kitchen Display System (KDS)
| Dimension | Documented State | Actual Codebase Reality | Proposed Enterprise Target | Gap Status |
| :--- | :--- | :--- | :--- | :--- |
| **Order Placement & Security** | Documented in `SECURITY.md` (idempotency key, server recalculation of DB price/tax). | Implemented via `StayController::order` and verified in `OrderSecurityTest`. Database-authoritative pricing strictly enforced. | Kitchen preparation time estimation based on active kitchen load and dish complexity. | **Fully Aligned** |
| **Kitchen Operations Board** | Documented as basic tabular orders list in early docs. | Implemented as a comprehensive 6-column Kanban board in `resources/views/admin/orders.blade.php` with one-click status transitions and fullscreen **Kitchen Display Mode (KDS)**. | Kitchen ticket printing (ESC/POS network thermal printer integration) and audio bell on new ticket arrival. | **Ahead of Docs** (Functional in code) |
| **Dining Venue Scoping** | Assumed dining was always a sub-department of a hotel. | Implemented `restaurant_users` and nullable `hotel_id` on `restaurants`, enabling both hotel dining outlets and standalone restaurant venues. | Multi-table QR ordering for dine-in guests in addition to in-room dining delivery. | **Ahead of Docs** (Functional in code) |

---

### 2.5 Housekeeping & Service SLA Dispatch
| Dimension | Documented State | Actual Codebase Reality | Proposed Enterprise Target | Gap Status |
| :--- | :--- | :--- | :--- | :--- |
| **SLA Countdown & Timestamps** | Documented in `ARCHITECTURE.md` (service catalog target response/resolution minutes). | Implemented via `Service` model timestamps (`target_response_minutes`, `target_resolution_minutes`) and calculated in `ServiceRequestController`. | Automated staff dispatch engine using geolocation or room proximity algorithms. | **Fully Aligned** |
| **Urgency Categorization** | Documented as basic request list. | Implemented in `resources/views/admin/requests.blade.php` with urgency tabs (`URGENT`, `HIGH`, `NORMAL`, `COMPLETED`), SLA breach warning badges, and assignment modals. | Visual room inspection checklist with photo upload proof of cleaning. | **Ahead of Docs** (Functional in code) |
| **Automated SLA Escalation** | Documented as `requests:escalate-sla` Artisan scheduled command. | Handled via scheduled Artisan command in `routes/console.php` / `app/Console/Commands`. | Multi-tier escalation notifications (Duty Manager SMS/Email after 15 min breach). | **Aligned with Code** |

---

### 2.6 Permanent QR Engine & Print Center
| Dimension | Documented State | Actual Codebase Reality | Proposed Enterprise Target | Gap Status |
| :--- | :--- | :--- | :--- | :--- |
| **QR Library Implementation** | Early documentation referenced BaconQrCode. | Implemented via `chillerlan/php-qrcode` (v6.0) in `app/Services/QrCodeService.php`. Pure SVG output generated natively. | NFC chip encoding integration alongside printable QR tokens. | **Minor Doc Drift** |
| **Commercial Print Sheet** | Documented in `QR_ARCHITECTURE.md` (A4 fold-ready tent cards). | Implemented in `resources/views/admin/qr-center/print.blade.php` and verified in `QrCenterTest`. Features cut lines, folding creases, and hotel logo. | Custom designer template builder allowing hotels to upload custom backgrounds and font styles. | **Fully Aligned** |

---

## 3. Discrepancy & Technical Debt Register

| ID | Category | Discrepancy / Debt Description | Code Location | Severity | Recommended Remediation |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **TD-01** | Documentation | `IMPLEMENTATION_STATUS.md` is an exact clone of `ARCHITECTURE.md`. | Repository root | Medium | Replace with pointer to `PRD_GAP_ANALYSIS.md` and `PRD.md`. |
| **TD-02** | Documentation | Database schema document omits 5 live tables and standalone restaurant mapping. | `DATABASE_SCHEMA.md` | Medium | Update `DATABASE_SCHEMA.md` to include all migrations. |
| **TD-03** | Documentation | Test suite metrics documented as 23 passed / 85 assertions instead of 25 passed / 103 assertions. | `README.md`, `TEST_PLAN.md` | Low | Update assertion count to 103 across docs. |
| **TD-04** | Architecture | Shared hosting environment lacks persistent queue worker daemon (Supervisor). | `DEPLOYMENT_HOSTINGER.md` | Medium | Document fallback to database queues executed via cron (`queue:work --stop-when-empty`). |
| **TD-05** | UI / Real-Time | Order and service queues rely on page reloads rather than persistent WebSocket connections. | `resources/views/admin/` | Medium | Introduce Alpine.js polling (`x-init="setInterval(fetchQueue, 15000)"`) or Laravel Reverb/Pusher WebSockets. |
| **TD-06** | Audio Alert | Web Audio API chime requires user gesture (click/keypress) before browser allows audio playback. | `resources/views/admin/dashboard.blade.php` | Low | Maintain persistent "Enable Sound" modal / button that unlocks the AudioContext on first staff interaction. |
| **TD-07** | Security | Test-login route (`/test-login`) bypasses password verification for development convenience. | `routes/web.php` | High | Wrap `/test-login` in `if (app()->environment('local', 'testing'))` to prevent accidental staging/production access. |

---

## 4. Prioritized Remediation & Enhancement Roadmap

### Priority 0 (P0) — Immediate Integrity Fixes
1. Publish comprehensive [`PRD.md`](file:///d:/Project_Abir/hotel-guest-platform-greenfield-v1/hotel-guest-platform/PRD.md) as the single source of truth across all product domains.
2. Ensure `/test-login` route cannot execute in production environment (`app()->isProduction()`).
3. Update `README.md` test counter to 25 passed / 103 assertions.

### Priority 1 (P1) — Core Capability Hardening
1. Add lightweight polling to KDS and Housekeeping queues to eliminate manual page refreshing.
2. Implement automated PDF receipt generation for hotel SaaS invoices.
3. Add multi-language i18n support to the guest portal (`/stay/{session}`).

### Priority 2 (P2) — Hospitality Integrations
1. PMS Integration: Bi-directional synchronization with Opera, Mews, or Cloudbeds for automated guest check-in/checkout binding.
2. Payment Gateway: Native payment collection for guest dining orders via Stripe / Razorpay directly inside the guest portal.
3. Push / SMS Alerts: Twilio / WhatsApp integration for instant staff dispatch and guest notifications.
