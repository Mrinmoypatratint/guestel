# Hospitality Platform API Documentation

## 1. Overview & Conventions
The platform exposes RESTful endpoints for guest mobile interactions, administrative operations, and automated telemetry.

- **Base URL**: `https://yourdomain.com`
- **Data Format**: `application/json`
- **Authentication**: Stateful session cookies with CSRF token for web/SPA clients, and opaque capability tokens for guest sessions.
- **Tenant Isolation**: Every admin endpoint automatically scopes data to the authenticated user's active hotel tenant.

---

## 2. Public & Guest Endpoints

### 1. Health Probe
- **Endpoint**: `GET /health`
- **Purpose**: Uptime and diagnostic monitoring.
- **Authentication**: Public.
- **Response**:
  ```json
  {
    "status": "ok",
    "database": "ok",
    "storage": "ok"
  }
  ```

### 2. QR Code Resolution
- **Endpoint**: `GET /g/{public_token}`
- **Purpose**: Resolves an opaque QR token, creates an ephemeral guest session, records privacy-preserving analytics, and redirects to the guest portal.
- **Parameters**: `public_token` (48-character string).
- **Response**: `HTTP 302 Redirect` to `/stay/{session_public_id}`.

### 3. Guest Concierge Portal
- **Endpoint**: `GET /stay/{session}`
- **Purpose**: Returns the full guest compendium, active offers, announcements, and room status.
- **Response**: Rendered Blade/PWA view or JSON payload.

### 4. Create Service Request
- **Endpoint**: `POST /stay/{session}/service-requests`
- **Headers**: `X-CSRF-TOKEN: {token}`
- **Payload**:
  ```json
  {
    "service_id": 4,
    "guest_notes": "Please bring 2 extra bath towels and lavender bubble bath."
  }
  ```
- **Response** (`HTTP 200`):
  ```json
  {
    "message": "Request logged",
    "request_id": 142,
    "status": "PENDING"
  }
  ```

### 5. In-Room Dining Menu
- **Endpoint**: `GET /stay/{session}/restaurant`
- **Purpose**: Returns available dining categories, dishes, prices, and dietary flags.

### 6. Place Dining Order
- **Endpoint**: `POST /stay/{session}/orders`
- **Headers**: `X-CSRF-TOKEN: {token}`
- **Payload**:
  ```json
  {
    "restaurant_id": 1,
    "idempotency_key": "c9284fae-128a-493b-9a48-df8491823901",
    "items": [
      { "menu_item_id": 12, "quantity": 2, "special_instructions": "Extra crispy fries" },
      { "menu_item_id": 15, "quantity": 1 }
    ]
  }
  ```
- **Response** (`HTTP 200`):
  ```json
  {
    "order_id": 89,
    "subtotal": "48.00",
    "tax": "4.80",
    "total": "52.80",
    "status": "PENDING"
  }
  ```

### 7. Send Concierge Chat Message
- **Endpoint**: `POST /stay/{session}/messages`
- **Payload**:
  ```json
  {
    "message": "Can I arrange an airport transfer for tomorrow at 8:00 AM?"
  }
  ```

---

## 3. Administrative Operations Endpoints

### 1. Operations Quick Search
- **Endpoint**: `GET /admin/search?q={query}`
- **Authentication**: Authenticated Staff (`auth`, `tenant`).
- **Response**:
  ```json
  {
    "results": [
      {
        "category": "Room",
        "title": "Room 101",
        "subtitle": "Ocean View King • occupied",
        "url": "/admin/rooms/1",
        "badge": "occupied"
      },
      {
        "category": "Dining Order",
        "title": "Order #42 (Room 101)",
        "subtitle": "$48.00 • 2 items",
        "url": "/admin/orders",
        "badge": "PREPARING"
      }
    ]
  }
  ```

### 2. Update Room Status
- **Endpoint**: `PATCH /admin/rooms/{room}/status`
- **Payload**:
  ```json
  {
    "status": "cleaning"
  }
  ```

### 3. Update Service Request Ticket
- **Endpoint**: `PATCH /admin/requests/{request}`
- **Payload**:
  ```json
  {
    "status": "COMPLETED",
    "assigned_to": 3,
    "completion_notes": "Towels and bath salts delivered."
  }
  ```

### 4. Update Kitchen Dining Order
- **Endpoint**: `PATCH /admin/orders/{order}`
- **Payload**:
  ```json
  {
    "status": "DELIVERED"
  }
  ```

---

## 4. Error Responses & HTTP Status Codes

| Status Code | Description | Scenario |
| :--- | :--- | :--- |
| `400 Bad Request` | Malformed payload or failed validation | Missing required fields or negative quantities |
| `401 Unauthorized` | Unauthenticated session | Expired admin session cookie |
| `403 Forbidden` | Authorization or tenant boundary denial | Staff attempting to view a different hotel |
| `404 Not Found` | Entity not found | Unknown QR token or non-existent room |
| `410 Gone` | Expired resource | Guest attempting to use an expired session token |
| `429 Too Many Requests` | Rate limit exceeded | Exceeding 5 login attempts or 10 orders/min |
