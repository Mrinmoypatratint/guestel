<?php
return [
 'tenant_session_key'=>env('TENANT_SESSION_KEY','current_hotel_id'),
 'qr_public_prefix'=>env('QR_PUBLIC_PREFIX','g'),
 'guest_session_hours'=>(int) env('GUEST_SESSION_HOURS',24),
 'currency' => env('DEFAULT_CURRENCY', 'INR'),
 'currency_symbol' => env('DEFAULT_CURRENCY_SYMBOL', '₹'),
];
