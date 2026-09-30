@extends('layouts.platform', ['title' => 'Invoice ' . $invoice->invoice_number])

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Actions Bar -->
    <div class="flex items-center justify-between no-print">
        <a href="{{ route('platform.billing.index') }}" class="inline-flex items-center gap-2 text-sm text-slate-400 hover:text-white transition">
            <x-icon name="arrow-left" class="w-4 h-4" />
            <span>Back to Invoices & Bills</span>
        </a>

        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="inline-flex items-center gap-2 rounded-xl bg-slate-800 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700 transition border border-slate-700 cursor-pointer">
                <x-icon name="printer" class="w-4 h-4" />
                <span>Print Tax Invoice</span>
            </button>
        </div>
    </div>

    <!-- Official Tax Invoice Document -->
    <div class="rounded-3xl border border-slate-800 bg-slate-900/90 p-8 sm:p-12 shadow-2xl backdrop-blur-xl relative overflow-hidden" id="printable-invoice">
        <!-- Status Watermark / Stamp -->
        <div class="absolute right-8 top-8">
            @if($invoice->status === 'paid')
                <div class="rotate-12 rounded-xl border-2 border-emerald-500/50 bg-emerald-500/10 px-4 py-1.5 font-mono text-sm font-black uppercase tracking-widest text-emerald-400 shadow-lg">
                    PAID IN FULL
                </div>
            @elseif($invoice->due_date->isPast())
                <div class="rotate-12 rounded-xl border-2 border-rose-500/50 bg-rose-500/10 px-4 py-1.5 font-mono text-sm font-black uppercase tracking-widest text-rose-400 shadow-lg">
                    OVERDUE
                </div>
            @else
                <div class="rotate-12 rounded-xl border-2 border-amber-500/50 bg-amber-500/10 px-4 py-1.5 font-mono text-sm font-black uppercase tracking-widest text-amber-400 shadow-lg">
                    PAYMENT DUE
                </div>
            @endif
        </div>

        <!-- SaaS Provider Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-800 pb-8 gap-6">
            <div class="space-y-1">
                <div class="flex items-center gap-2.5">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-amber-400 to-amber-600 font-black text-slate-950 text-xl shadow">
                        S
                    </div>
                    <span class="text-xl font-black tracking-tight text-white">SaaS Master Technologies India Pvt. Ltd.</span>
                </div>
                <p class="text-xs text-slate-400">Enterprise Cloud Hospitality & Restaurant Platform Provider</p>
                <div class="text-[11px] text-slate-400 space-y-0.5 pt-1">
                    <p>GSTIN: <span class="font-mono text-slate-300">29AAACH7492M1Z8</span> · SAC: <span class="font-mono text-slate-300">998313</span></p>
                    <p>Tower 4, SaaS Innovation Park, Tech Corridor, Bangalore, Karnataka 560103</p>
                    <p>Billing Support: billing@platform-hospitality.com | +91 80 4000 5920</p>
                </div>
            </div>

            <div class="text-left sm:text-right space-y-1 pt-4 sm:pt-0">
                <span class="text-xs font-bold uppercase tracking-wider text-amber-400">Official Tax Invoice</span>
                <h2 class="text-xl font-mono font-bold text-white">{{ $invoice->invoice_number }}</h2>
                <p class="text-xs text-slate-400">Date of Issue: <span class="font-medium text-slate-300">{{ $invoice->created_at->format('M d, Y') }}</span></p>
                <p class="text-xs text-slate-400">Payment Due: <span class="font-medium text-slate-300">{{ $invoice->due_date->format('M d, Y') }}</span></p>
            </div>
        </div>

        <!-- Bill To / Tenant Details -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 py-8 border-b border-slate-800 text-sm">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Billed To (Tenant Partner):</span>
                <h3 class="text-base font-bold text-white mt-1">{{ $invoice->hotel->name ?? 'Hotel Client' }}</h3>
                <p class="text-xs text-slate-400 mt-0.5">{{ $invoice->hotel->address ?? 'Commercial Premises' }}</p>
                <p class="text-xs text-slate-400">{{ $invoice->hotel->city ?? '' }}, India</p>
                <p class="text-xs text-slate-400 mt-2">Email: <span class="text-slate-300">{{ $invoice->hotel->email ?? 'admin@hotel.com' }}</span></p>
                <p class="text-xs text-slate-400">Phone: <span class="text-slate-300">{{ $invoice->hotel->phone ?? '+91 98765 43210' }}</span></p>
            </div>

            <div class="sm:text-right space-y-1">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Payment Summary:</span>
                <div class="mt-1">
                    <p class="text-xs text-slate-400">Currency: <span class="font-bold text-white">{{ $invoice->currency }} (Indian Rupee)</span></p>
                    <p class="text-xs text-slate-400">Service Period: <span class="text-slate-300">{{ $invoice->created_at->format('M Y') }}</span></p>
                    @if($invoice->status === 'paid')
                        <div class="mt-2 rounded-xl bg-emerald-500/10 border border-emerald-500/20 p-3 sm:inline-block text-left text-xs">
                            <p class="text-emerald-400 font-semibold">Payment Settled</p>
                            <p class="text-slate-300">Method: {{ $invoice->payment_method }}</p>
                            <p class="text-slate-300 font-mono text-[11px]">UTR / Ref: {{ $invoice->payment_reference }}</p>
                            <p class="text-slate-400 text-[10px]">Paid At: {{ $invoice->paid_at?->format('M d, Y H:i') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Line Items Table -->
        <div class="py-8 border-b border-slate-800">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-slate-800 text-xs uppercase tracking-wider text-slate-400">
                        <th class="pb-3">Service Description</th>
                        <th class="pb-3 text-center">SAC Code</th>
                        <th class="pb-3 text-right">Taxable Subtotal</th>
                        <th class="pb-3 text-right">GST (18%)</th>
                        <th class="pb-3 text-right">Total (INR)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/40 text-xs">
                    <tr>
                        <td class="py-4">
                            <p class="font-semibold text-white">{{ $invoice->title }}</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">Multi-tenant software licensing, cloud hosting, guest QR portal & restaurant ordering engine.</p>
                        </td>
                        <td class="py-4 text-center font-mono text-slate-400">998313</td>
                        <td class="py-4 text-right font-mono text-slate-300">₹{{ number_format((float)$invoice->subtotal, 2) }}</td>
                        <td class="py-4 text-right font-mono text-slate-300">₹{{ number_format((float)$invoice->tax_amount, 2) }}</td>
                        <td class="py-4 text-right font-mono font-bold text-white">₹{{ number_format((float)$invoice->total_amount, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Totals & Tax Breakdown -->
        <div class="flex flex-col sm:flex-row justify-between items-start pt-6 gap-6">
            <div class="text-xs text-slate-400 max-w-sm space-y-1">
                <p class="font-bold uppercase tracking-wider text-slate-300">Remittance & Bank Details:</p>
                <p>Account Name: SaaS Master Technologies India Pvt Ltd</p>
                <p>Bank: HDFC Bank Ltd, Indiranagar Branch</p>
                <p>Account No: <span class="font-mono text-slate-200">50200083921045</span> | IFSC: <span class="font-mono text-slate-200">HDFC0000128</span></p>
                <p>UPI ID: <span class="font-mono text-amber-400">saaspay@hdfcbank</span></p>
                <p class="pt-2 text-[10px] text-slate-400">Notes: {{ $invoice->notes }}</p>
            </div>

            <div class="w-full sm:w-72 space-y-2 text-xs">
                <div class="flex justify-between text-slate-400">
                    <span>Taxable Base Value:</span>
                    <span class="font-mono font-medium text-white">₹{{ number_format((float)$invoice->subtotal, 2) }}</span>
                </div>
                <div class="flex justify-between text-slate-400">
                    <span>CGST (9.0%):</span>
                    <span class="font-mono text-slate-300">₹{{ number_format((float)$invoice->tax_amount / 2, 2) }}</span>
                </div>
                <div class="flex justify-between text-slate-400">
                    <span>SGST (9.0%):</span>
                    <span class="font-mono text-slate-300">₹{{ number_format((float)$invoice->tax_amount / 2, 2) }}</span>
                </div>
                <div class="flex justify-between text-base font-bold text-white border-t border-slate-800 pt-2">
                    <span>Total Amount (₹):</span>
                    <span class="font-mono text-amber-400">₹{{ number_format((float)$invoice->total_amount, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Footer Sign-off -->
        <div class="mt-12 pt-6 border-t border-slate-800/80 text-center text-[11px] text-slate-400">
            This is a computer-generated tax invoice issued by the SaaS Platform Provider. No physical signature is required.
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
