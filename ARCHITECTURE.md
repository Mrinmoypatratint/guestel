# Architecture
```mermaid
flowchart LR
 Guest[Guest mobile browser] --> QR[/g/{opaque token}]
 QR --> Laravel[Laravel 13]
 Admin[Hotel staff browser] --> Laravel
 Platform[Super Admin] --> Laravel
 Laravel --> Auth[Auth + RBAC]
 Laravel --> Tenant[Tenant Context + Policies]
 Tenant --> MySQL[(MySQL 8)]
 Laravel --> Storage[Laravel Storage]
 Laravel --> Cron[Hostinger Cron / Scheduler]
```
Tenant-sensitive models carry `hotel_id`. `ResolveTenant` establishes a hotel only from an authenticated membership, never from client-provided `hotel_id`. `BelongsToTenant` adds a global Eloquent scope and protects tenant assignment during inserts. Policies and permission middleware provide additional authorization.


## Hotel onboarding
```mermaid
sequenceDiagram
  participant SA as Super Admin
  participant A as CreateHotelAction
  participant DB as MySQL
  participant Mail as Password Broker
  SA->>A: validated hotel + admin
  A->>DB: BEGIN
  A->>DB: hotel + branding + membership + role + departments + services + hotel QR
  A->>DB: COMMIT
  SA->>Mail: send expiring admin reset link
```

## Authentication and tenant isolation
```mermaid
flowchart LR
 Login --> Session[Laravel session]
 Session --> Auth[Authenticated user]
 Auth --> RT[ResolveTenant]
 RT --> Membership{hotel_users active?}
 Membership -- no --> Deny[403]
 Membership -- yes --> Context[TenantContext]
 Context --> Scope[Global hotel_id scope]
 Context --> Policy[Policies + permissions]
 Scope --> Action[Business action]
 Policy --> Action
```

## Service request + SLA
```mermaid
sequenceDiagram
 Guest->>App: choose service
 App->>DB: resolve service inside guest hotel
 App->>DB: derive department + SLA timestamps
 App->>DB: create request + status history
 App->>Staff: database notification
 Scheduler->>DB: find overdue active requests
 Scheduler->>DB: ESCALATED + history
```

## Restaurant order
```mermaid
sequenceDiagram
 Guest->>App: item IDs + quantity + idempotency key
 App->>DB: lock guest session
 App->>DB: resolve live menu prices/modifiers/tax
 App->>DB: create order + item snapshots + history
 App->>Kitchen: database notification
 Note over App,DB: Client price, tax, discount, hotel_id and room_id are never authoritative
```

## Chat and notification flow
```mermaid
flowchart LR
 Guest --> Message[(messages)]
 Message --> Conversation[(conversation)]
 Conversation --> Notify[(database notifications)]
 Notify --> Reception[Hotel reception]
 Reception --> Reply[(staff reply)]
 Reply --> Guest
```

## Backup / deployment
```mermaid
flowchart TB
 Git[Versioned source] --> Hostinger[Hostinger PHP hosting]
 Hostinger --> Public[/public only]
 Hostinger --> MySQL[(MySQL)]
 Hostinger --> Files[(storage/app/public)]
 Cron[Hostinger Cron] --> Scheduler[artisan schedule:run]
 MySQL --> DBBackup[Daily DB backup]
 Files --> FileBackup[File backup]
 DBBackup --> Offsite[Secondary recovery copy]
 FileBackup --> Offsite
```
