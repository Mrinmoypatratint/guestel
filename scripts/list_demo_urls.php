<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$hotel = App\Models\Hotel::where('slug', 'grand-azure')->first();
echo "=========================================================\n";
echo "HOTEL: " . $hotel->name . " (ID: " . $hotel->id . ")\n";
echo "=========================================================\n\n";

echo "--- ROOM DIRECTORY & PERMANENT QR CODES ---\n";
$rooms = App\Models\Room::withoutGlobalScopes()->where('hotel_id', $hotel->id)->with('qrCode', 'roomType', 'floor')->orderBy('number')->get();
foreach ($rooms as $r) {
    echo "Room " . str_pad($r->number, 4) . " | " . str_pad(ucfirst($r->status), 12) . " | " . str_pad($r->roomType?->name ?? 'Standard', 24) . " | " . ($r->floor?->name ?? 'L1') . "\n";
    echo "  → Public QR: http://127.0.0.1:8000/g/" . $r->qrCode?->public_token . "\n";
}

echo "\n--- ACTIVE GUEST SESSIONS ---\n";
$sessions = App\Models\GuestSession::withoutGlobalScopes()->where('hotel_id', $hotel->id)->where('status', 'active')->with('room')->get();
foreach ($sessions as $s) {
    echo "Room " . ($s->room?->number ?? 'N/A') . " -> Direct Stay Portal: http://127.0.0.1:8000/stay/" . $s->public_id . "\n";
    echo "          -> Direct Dining Tab:    http://127.0.0.1:8000/stay/" . $s->public_id . "/restaurant\n";
}

echo "\n--- ACTIVE SERVICE TICKETS ---\n";
$tickets = App\Models\ServiceRequest::withoutGlobalScopes()->where('hotel_id', $hotel->id)->with('service', 'room')->get();
foreach ($tickets as $t) {
    echo "Ticket " . str_pad($t->request_number, 8) . " | Room " . ($t->room?->number ?? 'N/A') . " | " . str_pad($t->status, 12) . " | " . ($t->service?->name ?? 'Service') . "\n";
}

echo "\n--- ACTIVE KITCHEN DINING ORDERS ---\n";
$orders = App\Models\Order::withoutGlobalScopes()->where('hotel_id', $hotel->id)->with('items', 'room')->get();
foreach ($orders as $o) {
    echo "Order #" . str_pad($o->id, 4) . " (" . $o->order_number . ") | Room " . ($o->room?->number ?? 'N/A') . " | " . str_pad($o->status, 12) . " | Total: $" . $o->total . " (" . $o->items->count() . " items)\n";
}
echo "\n=========================================================\n";
