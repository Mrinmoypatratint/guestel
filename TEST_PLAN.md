# Automated Quality Assurance & Testing Plan

## 1. Testing Philosophy & Standards
The platform uses **PHPUnit / Pest** with transactional database refreshes (`RefreshDatabase`). Mocking is minimized; tests execute against real Eloquent models, database constraints, form requests, and HTTP middleware pipelines to guarantee true production reliability.

---

## 2. Test Suites Summary

| Test Suite | Location | Assertions | Core Invariants Verified |
| :--- | :--- | :--- | :--- |
| **Authentication & Sessions** | `tests/Feature/AuthenticationTest.php` | 2 | Login regenerates session ID, invalid credentials throttled, logout destroys session. |
| **Tenant Isolation** | `tests/Feature/TenantIsolationTest.php` | 1 | Hotel A staff cannot view or modify Hotel B rooms (`404` via scoped model binding). |
| **Platform Boundary** | `tests/Feature/PlatformAdminSecurityTest.php` | 2 | Non-platform admins cannot access `/platform/hotels`; platform admins can provision properties. |
| **Order Security & Idempotency** | `tests/Feature/OrderSecurityTest.php` | 3 | Client prices/taxes are ignored; server recalculates totals from DB; double submissions blocked by idempotency key. |
| **Service Routing & SLA** | `tests/Feature/ServiceRoutingTest.php` | 4 | Service requests automatically derive department mapping, SLA response, and SLA resolution timestamps from database. |
| **Permanent QR Security** | `tests/Feature/QrSecurityTest.php` | 1 | Tampered or unknown QR tokens return clean `404` without leaking internal SQL or stack traces. |
| **QR Print Center** | `tests/Feature/QrCenterTest.php` | 3 | Staff can access QR inventory, view printable desk tent-card sheets, and toggle QR activation. |
| **Operations Search Security** | `tests/Feature/SearchSecurityTest.php` | 3 | `Ctrl+K` search strictly filters records to active tenant hotel; zero cross-tenant leakage. |
| **Guest Concierge & Compendium** | `tests/Feature/GuestPortalCompendiumTest.php` | 8 | QR scan creates guest session, redirects to portal, loads room compendium (amenities, policies, offers) and dining portal. |

---

## 3. Running Test Suites

### Full Test Suite Execution
```bash
php artisan test
```

### Specific Test Suite Execution
```bash
# Security & Tenant Isolation Tests
php artisan test --filter TenantIsolationTest
php artisan test --filter OrderSecurityTest
php artisan test --filter PlatformAdminSecurityTest
php artisan test --filter SearchSecurityTest

# Guest Experience & QR Engine Tests
php artisan test --filter GuestPortalCompendiumTest
php artisan test --filter QrCenterTest
php artisan test --filter QrSecurityTest
```

### Continuous Integration (CI) Automation
In GitHub Actions / GitLab CI, runs on every pull request:
```yaml
name: Production Test Pipeline
on: [push, pull_request]
jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - name: Setup PHP 8.4
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.4'
          extensions: mbstring, pdo_sqlite, pdo_mysql, gd, fileinfo
      - name: Install Dependencies
        run: composer install --no-progress --prefer-dist
      - name: Run Pest / PHPUnit Tests
        run: php artisan test --stop-on-failure
```
