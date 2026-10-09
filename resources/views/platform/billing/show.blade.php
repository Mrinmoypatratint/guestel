@extends('layouts.platform', ['title' => 'Invoice ' . $invoice->invoice_number])

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Actions Bar -->
    <div class="flex items-center justify-between no-print">
        <a href="{{ route('platform.billing.index') }}" class="neu-button inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-slate-700 hover:text-slate-900 transition">
            <x-icon name="arrow-left" class="w-4 h-4" />
            <span>Back to Invoices & Bills</span>
        </a>

        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="neu-btn-primary inline-flex items-center gap-2 rounded-xl px-4 py-2 text-xs font-bold text-white shadow-md hover:brightness-110 transition cursor-pointer">
                <x-icon name="printer" class="w-4 h-4 text-white" />
                <span>Print Tax Invoice</span>
            </button>
        </div>
    </div>

    <!-- Official Tax Invoice Document (Neumorphic Card) -->
    <div class="neu-flat-lg rounded-3xl p-8 sm:p-12 bg-[#e8edf5] relative overflow-hidden text-slate-800" id="printable-invoice">
        <!-- Status Watermark / Stamp -->
        <div class="absolute right-8 top-8">
            @if($invoice->status === 'paid')
                <div class="rotate-12 rounded-xl border-2 border-emerald-600 bg-emerald-100 px-4 py-1.5 font-mono text-sm font-black uppercase tracking-widest text-emerald-800 shadow-md">
                    PAID IN FULL
                </div>
            @elseif($invoice->due_date->isPast())
                <div class="rotate-12 rounded-xl border-2 border-rose-600 bg-rose-100 px-4 py-1.5 font-mono text-sm font-black uppercase tracking-widest text-rose-800 shadow-md">
                    OVERDUE
                </div>
            @else
                <div class="rotate-12 rounded-xl border-2 border-amber-600 bg-amber-100 px-4 py-1.5 font-mono text-sm font-black uppercase tracking-widest text-amber-800 shadow-md">
                    PAYMENT DUE
                </div>
            @endif
        </div>

        <!-- SaaS Provider Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-300/60 pb-8 gap-6">
            <div class="space-y-1">
                <div class="flex items-center gap-2.5">
                    <x-brand-logo size="md" :tagline="null" />
                    <span class="text-xl font-black tracking-tight text-slate-900">Talisha Software Technologies Pvt. Ltd.</span>
                </div>
                <p class="text-xs text-slate-500 font-medium">Enterprise Cloud Hospitality & Restaurant Platform Provider</p>
                <div class="text-[11px] text-slate-500 space-y-0.5 pt-1 font-medium">
                    <p>GSTIN: <span class="font-mono text-slate-800 font-bold">29AAACH7492M1Z8</span> · SAC: <span class="font-mono text-slate-800 font-bold">998313</span></p>
                    <p>Tower 4, Innovation Park, Kolkata & Bangalore Hubs</p>
                    <p>Billing Support: billing@guestel.com | +91 80 4000 5920</p>
                </div>
            </div>

            <div class="text-left sm:text-right space-y-1 pt-4 sm:pt-0">
                <span class="neu-pill-inset px-2.5 py-0.5 text-xs font-mono font-bold text-[#00214D]">Official Tax Invoice</span>
                <h2 class="text-xl font-mono font-black text-slate-900 mt-1">{{ $invoice->invoice_number }}</h2>
                <p class="text-xs text-slate-500">Date of Issue: <strong class="text-slate-800">{{ $invoice->created_at->format('M d, Y') }}</strong></p>
                <p class="text-xs text-slate-500">Payment Due: <strong class="text-slate-800">{{ $invoice->due_date->format('M d, Y') }}</strong></p>
            </div>
        </div>

        <!-- Bill To / Tenant Details -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 py-8 border-b border-slate-300/60 text-sm">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Billed To (Tenant Partner):</span>
                <h3 class="text-base font-extrabold text-slate-900 mt-1">{{ $invoice->hotel->name ?? 'Hotel Client' }}</h3>
                <p class="text-xs text-slate-600 mt-0.5">{{ $invoice->hotel->address ?? 'Commercial Premises' }}</p>
                <p class="text-xs text-slate-600">{{ $invoice->hotel->city ?? '' }}, India</p>
                <p class="text-xs text-slate-600 mt-2">Email: <span class="text-slate-900 font-medium">{{ $invoice->hotel->email ?? 'admin@hotel.com' }}</span></p>
                <p class="text-xs text-slate-600">Phone: <span class="text-slate-900 font-medium">{{ $invoice->hotel->phone ?? '+91 98765 43210' }}</span></p>
            </div>

            <div class="sm:text-right space-y-1">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Payment Summary:</span>
                <div class="mt-1">
                    <p class="text-xs text-slate-600">Currency: <strong class="text-slate-900">{{ $invoice->currency }} (Indian Rupee)</strong></p>
                    <p class="text-xs text-slate-600">Service Period: <strong class="text-slate-900">{{ $invoice->created_at->format('M Y') }}</strong></p>
                    @if($invoice->status === 'paid')
                        <div class="mt-2 neu-inset p-3 rounded-2xl sm:inline-block text-left text-xs">
                            <p class="text-emerald-800 font-bold">Payment Settled</p>
                            <p class="text-slate-700">Method: {{ $invoice->payment_method }}</p>
                            <p class="text-slate-700 font-mono text-[11px]">UTR / Ref: {{ $invoice->payment_reference }}</p>
                            <p class="text-slate-500 text-[10px]">Paid At: {{ $invoice->paid_at?->format('M d, Y H:i') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Line Items Table inside Inset Surface -->
        <div class="py-8 border-b border-slate-300/60">
            <div class="neu-inset rounded-2xl p-4">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-300/60 text-xs uppercase tracking-wider text-slate-500">
                            <th class="pb-3">Service Description</th>
                            <th class="pb-3 text-center">SAC Code</th>
                            <th class="pb-3 text-right">Taxable Subtotal</th>
                            <th class="pb-3 text-right">GST (18%)</th>
                            <th class="pb-3 text-right">Total (INR)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-300/40 text-xs">
                        <tr>
                            <td class="py-4">
                                <p class="font-bold text-slate-900 text-sm">{{ $invoice->title }}</p>
                                <p class="text-[11px] text-slate-500 mt-0.5">Multi-tenant software licensing, cloud hosting, guest QR portal & restaurant ordering engine.</p>
                            </td>
                            <td class="py-4 text-center font-mono text-slate-600">998313</td>
                            <td class="py-4 text-right font-mono text-slate-800 font-semibold">₹{{ number_format((float)$invoice->subtotal, 2) }}</td>
                            <td class="py-4 text-right font-mono text-slate-800 font-semibold">₹{{ number_format((float)$invoice->tax_amount, 2) }}</td>
                            <td class="py-4 text-right font-mono font-black text-slate-900 text-sm">₹{{ number_format((float)$invoice->total_amount, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Totals & Tax Breakdown -->
        <div class="flex flex-col sm:flex-row justify-between items-start pt-6 gap-6">
            <div class="text-xs text-slate-600 max-w-sm space-y-1 neu-inset p-4 rounded-2xl">
                <p class="font-bold uppercase tracking-wider text-slate-900">Remittance & Bank Details:</p>
                <p>Account Name: Talisha Software Technologies Pvt Ltd</p>
                <p>Bank: HDFC Bank Ltd</p>
                <p>Account No: <span class="font-mono text-slate-900 font-bold">50200083921045</span> | IFSC: <span class="font-mono text-slate-900 font-bold">HDFC0000128</span></p>
                <p>UPI ID: <span class="font-mono text-[#0073E6] font-bold">talishapay@hdfcbank</span></p>
                <p class="pt-1 text-[10px] text-slate-500">Notes: {{ $invoice->notes }}</p>
            </div>

            <div class="w-full sm:w-72 space-y-2 text-xs neu-inset p-4 rounded-2xl">
                <div class="flex justify-between text-slate-600">
                    <span>Taxable Base Value:</span>
                    <span class="font-mono font-bold text-slate-900">₹{{ number_format((float)$invoice->subtotal, 2) }}</span>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>CGST (9.0%):</span>
                    <span class="font-mono text-slate-700">₹{{ number_format((float)$invoice->tax_amount / 2, 2) }}</span>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>SGST (9.0%):</span>
                    <span class="font-mono text-slate-700">₹{{ number_format((float)$invoice->tax_amount / 2, 2) }}</span>
                </div>
                <div class="flex justify-between text-base font-black text-slate-900 border-t border-slate-300/60 pt-2">
                    <span>Total Amount (₹):</span>
                    <span class="font-mono text-[#00214D]">₹{{ number_format((float)$invoice->total_amount, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Footer Sign-off -->
        <div class="mt-10 pt-6 border-t border-slate-300/60 text-center text-[11px] text-slate-500">
            This is a computer-generated tax invoice issued by Guestel Cloud OS / Talisha Software. No physical signature is required.
        </div>
    </div>
</div>

<style>
@media print {
    body { background-color: #fff !important; color: #000 !important; }
    aside, header, .no-print { display: none !important; }
    #printable-invoice { border: none !important; background: transparent !important; color: #000 !important; box-shadow: none !important; padding: 0 !important; }
    #printable-invoice * { color: #000 !important; border-color: #ddd !important; }
}
</style>
@endsection
