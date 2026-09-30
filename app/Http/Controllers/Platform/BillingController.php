<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\HotelInvoice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BillingController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless(auth()->user()?->is_platform_admin, 403);

        $status = $request->query('status');
        $query = HotelInvoice::with('hotel')->latest();

        if ($status && in_array($status, ['paid', 'unpaid', 'overdue'])) {
            $query->where('status', $status);
        }

        $invoices = $query->paginate(15)->withQueryString();
        $hotels = Hotel::where('status', 'active')->orderBy('name')->get();

        $totalPaid = (float) HotelInvoice::where('status', 'paid')->sum('total_amount');
        $totalUnpaid = (float) HotelInvoice::where('status', 'unpaid')->sum('total_amount');
        $overdueCount = HotelInvoice::where('status', 'unpaid')->where('due_date', '<', now())->count();

        return view('platform.billing.index', [
            'invoices' => $invoices,
            'hotels' => $hotels,
            'currentStatus' => $status ?? 'all',
            'totalPaid' => $totalPaid,
            'totalUnpaid' => $totalUnpaid,
            'overdueCount' => $overdueCount,
        ]);
    }

    public function storeInvoice(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()?->is_platform_admin, 403);

        $data = $request->validate([
            'hotel_id' => ['required', 'exists:hotels,id'],
            'title' => ['required', 'string', 'max:255'],
            'subtotal' => ['required', 'numeric', 'min:1'],
            'tax_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'due_date' => ['required', 'date', 'after_or_equal:today'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $subtotal = round((float) $data['subtotal'], 2);
        $taxRate = isset($data['tax_percent']) ? (float) $data['tax_percent'] : 18.0;
        $taxAmount = round($subtotal * ($taxRate / 100), 2);
        $total = $subtotal + $taxAmount;

        $invoice = HotelInvoice::create([
            'invoice_number' => 'INV-' . now()->format('Ym') . '-' . strtoupper(Str::random(6)),
            'hotel_id' => $data['hotel_id'],
            'title' => $data['title'],
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total_amount' => $total,
            'currency' => 'INR',
            'status' => 'unpaid',
            'due_date' => $data['due_date'],
            'notes' => $data['notes'] ?? 'SaaS platform license & maintenance fee.',
        ]);

        return redirect()->route('platform.billing.index')
            ->with('status', "Invoice {$invoice->invoice_number} for ₹" . number_format($total, 2) . " generated successfully.");
    }

    public function recordPayment(Request $request, HotelInvoice $invoice): RedirectResponse
    {
        abort_unless(auth()->user()?->is_platform_admin, 403);

        $data = $request->validate([
            'payment_method' => ['required', 'string', 'max:50'],
            'payment_reference' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $invoice->update([
            'status' => 'paid',
            'paid_at' => now(),
            'payment_method' => $data['payment_method'],
            'payment_reference' => $data['payment_reference'] ?? ('PAY-' . strtoupper(Str::random(8))),
            'notes' => $data['notes'] ? ($invoice->notes . "\nPayment Note: " . $data['notes']) : $invoice->notes,
        ]);

        // Keep hotel subscription active
        $invoice->hotel->subscriptions()->updateOrCreate(
            ['hotel_id' => $invoice->hotel_id],
            [
                'status' => 'active',
                'renews_at' => now()->addMonth(),
            ]
        );

        return redirect()->route('platform.billing.index')
            ->with('status', "Payment of ₹" . number_format((float) $invoice->total_amount, 2) . " recorded for {$invoice->invoice_number}. Hotel account activated.");
    }

    public function show(HotelInvoice $invoice): View
    {
        abort_unless(auth()->user()?->is_platform_admin, 403);

        return view('platform.billing.show', [
            'invoice' => $invoice->load('hotel'),
        ]);
    }
}
