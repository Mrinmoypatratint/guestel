@extends('layouts.platform', ['title' => 'SaaS Billing & Invoices'])

@section('content')
<div class="space-y-8" x-data="{ showBillModal: false, showPaymentModal: false, paymentInvoice: null }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black tracking-tight text-white sm:text-3xl">Platform Invoicing & Payment Collection</h1>
            <p class="mt-1 text-sm text-slate-400">Issue service bills, track multi-tenant SaaS subscriptions, and record payments for hotel and restaurant platform services.</p>
        </div>

        <button 
            @click="showBillModal = true" 
            class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 px-4 py-2.5 text-sm font-semibold text-slate-950 shadow-lg shadow-amber-500/20 hover:from-amber-400 hover:to-amber-500 transition cursor-pointer">
            <x-icon name="plus" class="w-4 h-4" />
            <span>Generate Service Bill</span>
        </button>
    </div>

    <!-- Financial KPIs -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="rounded-2xl border border-slate-800/80 bg-slate-900/60 p-6 backdrop-blur-xl">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Collected</span>
                <span class="rounded-lg bg-emerald-500/10 p-2 text-emerald-400 border border-emerald-500/20">
                    <x-icon name="currency-inr" class="w-5 h-5" />
                </span>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-black tracking-tight text-emerald-400">₹{{ number_format($totalPaid, 2) }}</span>
                <span class="text-xs text-slate-400">Lifetime Revenue</span>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-800/80 bg-slate-900/60 p-6 backdrop-blur-xl">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Pending Receivables</span>
                <span class="rounded-lg bg-amber-500/10 p-2 text-amber-400 border border-amber-500/20">
                    <x-icon name="clock" class="w-5 h-5" />
                </span>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-black tracking-tight text-amber-400">₹{{ number_format($totalUnpaid, 2) }}</span>
                <span class="text-xs text-slate-400">Awaiting Settlement</span>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-800/80 bg-slate-900/60 p-6 backdrop-blur-xl">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Overdue Invoices</span>
                <span class="rounded-lg bg-rose-500/10 p-2 text-rose-400 border border-rose-500/20">
                    <x-icon name="bell" class="w-5 h-5" />
                </span>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-black tracking-tight text-rose-400">{{ $overdueCount }}</span>
                <span class="text-xs text-slate-400">Action Required</span>
            </div>
        </div>
    </div>

    <!-- Invoices List Card -->
    <div class="rounded-2xl border border-slate-800/80 bg-slate-900/60 overflow-hidden shadow-xl backdrop-blur-xl">
        <!-- Filter Tabs -->
        <div class="flex flex-col sm:flex-row items-center justify-between border-b border-slate-800/80 px-6 py-4 gap-4">
            <div class="flex items-center gap-2">
                <a href="{{ route('platform.billing.index') }}" 
                   class="rounded-xl px-3.5 py-1.5 text-xs font-semibold transition {{ $currentStatus === 'all' ? 'bg-amber-500 text-slate-950 shadow-md' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
                    All Bills
                </a>
                <a href="{{ route('platform.billing.index', ['status' => 'unpaid']) }}" 
                   class="rounded-xl px-3.5 py-1.5 text-xs font-semibold transition {{ $currentStatus === 'unpaid' ? 'bg-amber-500 text-slate-950 shadow-md' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
                    Unpaid
                </a>
                <a href="{{ route('platform.billing.index', ['status' => 'paid']) }}" 
                   class="rounded-xl px-3.5 py-1.5 text-xs font-semibold transition {{ $currentStatus === 'paid' ? 'bg-amber-500 text-slate-950 shadow-md' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
                    Paid
                </a>
                <a href="{{ route('platform.billing.index', ['status' => 'overdue']) }}" 
                   class="rounded-xl px-3.5 py-1.5 text-xs font-semibold transition {{ $currentStatus === 'overdue' ? 'bg-amber-500 text-slate-950 shadow-md' : 'text-slate-400 hover:bg-slate-800/60 hover:text-white' }}">
                    Overdue
                </a>
            </div>

            <p class="text-xs text-slate-400">Showing <span class="font-medium text-white">{{ $invoices->total() }}</span> total platform service invoices</p>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300">
                <thead class="bg-slate-950/60 text-xs uppercase tracking-wider text-slate-400 border-b border-slate-800">
                    <tr>
                        <th class="px-6 py-4">Invoice #</th>
                        <th class="px-6 py-4">Hotel / Tenant</th>
                        <th class="px-6 py-4">Description</th>
                        <th class="px-6 py-4">Base + 18% GST</th>
                        <th class="px-6 py-4">Total Amount</th>
                        <th class="px-6 py-4">Due Date</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($invoices as $invoice)
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="px-6 py-4 font-mono font-semibold text-white">
                                <a href="{{ route('platform.billing.invoices.show', $invoice) }}" class="text-amber-400 hover:underline">
                                    {{ $invoice->invoice_number }}
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-white">{{ $invoice->hotel->name ?? 'Hotel #'.$invoice->hotel_id }}</div>
                                <div class="text-xs text-slate-400">{{ $invoice->hotel->city ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-xs text-slate-200 line-clamp-1 font-medium">{{ $invoice->title }}</div>
                                @if($invoice->payment_method)
                                    <div class="text-[11px] text-slate-400">Paid via: <span class="uppercase font-semibold text-emerald-400">{{ $invoice->payment_method }}</span> ({{ $invoice->payment_reference }})</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-mono text-xs">
                                <div>₹{{ number_format((float)$invoice->subtotal, 2) }}</div>
                                <div class="text-[11px] text-slate-400">+ ₹{{ number_format((float)$invoice->tax_amount, 2) }} GST</div>
                            </td>
                            <td class="px-6 py-4 font-mono font-bold text-white">
                                ₹{{ number_format((float)$invoice->total_amount, 2) }}
                            </td>
                            <td class="px-6 py-4 text-xs">
                                <div class="{{ $invoice->due_date->isPast() && $invoice->status === 'unpaid' ? 'text-rose-400 font-semibold' : 'text-slate-300' }}">
                                    {{ $invoice->due_date->format('M d, Y') }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($invoice->status === 'paid')
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-500/10 px-2.5 py-1 text-xs font-semibold text-emerald-400 border border-emerald-500/20">
                                        <x-icon name="check" class="w-3.5 h-3.5" />
                                        <span>Paid</span>
                                    </span>
                                @elseif($invoice->due_date->isPast())
                                    <span class="inline-flex items-center gap-1 rounded-full bg-rose-500/10 px-2.5 py-1 text-xs font-semibold text-rose-400 border border-rose-500/20">
                                        <x-icon name="bell" class="w-3.5 h-3.5" />
                                        <span>Overdue</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-500/10 px-2.5 py-1 text-xs font-semibold text-amber-400 border border-amber-500/20">
                                        <x-icon name="clock" class="w-3.5 h-3.5" />
                                        <span>Unpaid</span>
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                @if($invoice->status !== 'paid')
                                    <button 
                                        @click="paymentInvoice = {{ json_encode([
                                            'id' => $invoice->id,
                                            'number' => $invoice->invoice_number,
                                            'hotel_name' => $invoice->hotel->name ?? 'Hotel',
                                            'amount' => number_format((float)$invoice->total_amount, 2),
                                            'action_url' => route('platform.billing.invoices.payment', $invoice)
                                        ]) }}; showPaymentModal = true"
                                        class="rounded-xl bg-emerald-500/10 px-3 py-1.5 text-xs font-semibold text-emerald-400 hover:bg-emerald-500 hover:text-slate-950 transition border border-emerald-500/20 cursor-pointer">
                                        Record Payment
                                    </button>
                                @endif

                                <a href="{{ route('platform.billing.invoices.show', $invoice) }}" 
                                   class="rounded-xl bg-slate-800 px-3 py-1.5 text-xs font-semibold text-slate-300 hover:bg-slate-700 hover:text-white transition border border-slate-700">
                                    View / Print
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-sm text-slate-400">
                                No invoices found. Click "Generate Service Bill" to create a new SaaS billing invoice.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($invoices->hasPages())
            <div class="border-t border-slate-800 p-4">
                {{ $invoices->links() }}
            </div>
        @endif
    </div>

    <!-- Modal: Generate Service Bill -->
    <div x-cloak x-show="showBillModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md">
        <div @click.away="showBillModal = false" class="w-full max-w-lg rounded-2xl border border-slate-800 bg-slate-900 p-6 shadow-2xl space-y-6">
            <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                <div>
                    <h3 class="text-lg font-bold text-white">Generate SaaS Service Bill</h3>
                    <p class="text-xs text-slate-400">Create an official GST invoice for platform subscription or support</p>
                </div>
                <button @click="showBillModal = false" class="text-slate-400 hover:text-white text-lg font-bold">&times;</button>
            </div>

            <form method="post" action="{{ route('platform.billing.invoices.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Target Hotel / Tenant *</label>
                    <select name="hotel_id" required class="w-full rounded-xl border border-slate-700 bg-slate-950 px-3.5 py-2.5 text-sm text-white focus:border-amber-500 focus:outline-none">
                        <option value="">Select onboarded hotel...</option>
                        @foreach($hotels as $hotel)
                            <option value="{{ $hotel->id }}">{{ $hotel->name }} ({{ $hotel->city ?? 'India' }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Service Description / Line Item *</label>
                    <input type="text" name="title" required placeholder="e.g. Monthly SaaS Platform Cloud License (Pro Plan)" value="Monthly SaaS Platform License & Support" class="w-full rounded-xl border border-slate-700 bg-slate-950 px-3.5 py-2.5 text-sm text-white focus:border-amber-500 focus:outline-none">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Base Amount (₹ INR) *</label>
                        <input type="number" step="0.01" name="subtotal" required placeholder="9999.00" class="w-full rounded-xl border border-slate-700 bg-slate-950 px-3.5 py-2.5 text-sm text-white focus:border-amber-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">GST Rate (%)</label>
                        <input type="number" step="0.1" name="tax_percent" value="18" class="w-full rounded-xl border border-slate-700 bg-slate-950 px-3.5 py-2.5 text-sm text-white focus:border-amber-500 focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Payment Due Date *</label>
                    <input type="date" name="due_date" required value="{{ now()->addDays(15)->format('Y-m-d') }}" class="w-full rounded-xl border border-slate-700 bg-slate-950 px-3.5 py-2.5 text-sm text-white focus:border-amber-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Invoice Notes / Terms</label>
                    <textarea name="notes" rows="2" placeholder="Payment due within 15 days via UPI, NEFT, or Corporate Net Banking." class="w-full rounded-xl border border-slate-700 bg-slate-950 px-3.5 py-2 text-sm text-white focus:border-amber-500 focus:outline-none">Payment due within 15 days of issue. Multi-tenant SaaS service license.</textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800">
                    <button type="button" @click="showBillModal = false" class="rounded-xl px-4 py-2 text-sm font-semibold text-slate-400 hover:text-white transition">Cancel</button>
                    <button type="submit" class="rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 px-5 py-2.5 text-sm font-bold text-slate-950 hover:from-amber-400 hover:to-amber-500 transition shadow-lg shadow-amber-500/20">Generate Bill</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Record Payment -->
    <div x-cloak x-show="showPaymentModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md">
        <div @click.away="showPaymentModal = false" class="w-full max-w-lg rounded-2xl border border-slate-800 bg-slate-900 p-6 shadow-2xl space-y-6">
            <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                <div>
                    <h3 class="text-lg font-bold text-white">Record SaaS Payment Receipt</h3>
                    <p class="text-xs text-slate-400">Capture payment details for <span class="font-mono text-amber-400" x-text="paymentInvoice?.number"></span></p>
                </div>
                <button @click="showPaymentModal = false" class="text-slate-400 hover:text-white text-lg font-bold">&times;</button>
            </div>

            <div class="rounded-xl bg-slate-950/80 border border-slate-800/80 p-4 space-y-2">
                <div class="flex justify-between text-xs">
                    <span class="text-slate-400">Hotel / Tenant:</span>
                    <span class="font-semibold text-white" x-text="paymentInvoice?.hotel_name"></span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-slate-400">Total Due Amount:</span>
                    <span class="font-mono font-bold text-emerald-400" x-text="'₹' + paymentInvoice?.amount"></span>
                </div>
            </div>

            <form method="post" :action="paymentInvoice?.action_url" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Payment Method / Channel *</label>
                    <select name="payment_method" required class="w-full rounded-xl border border-slate-700 bg-slate-950 px-3.5 py-2.5 text-sm text-white focus:border-amber-500 focus:outline-none">
                        <option value="UPI / QR (GPay, PhonePe, Paytm)">UPI / QR (Google Pay, PhonePe, Paytm)</option>
                        <option value="NEFT / RTGS / Bank Transfer">NEFT / RTGS / Bank Transfer</option>
                        <option value="Credit / Debit Card (Razorpay)">Credit / Debit Card (Online Gateway)</option>
                        <option value="Corporate Cheque">Corporate Cheque</option>
                        <option value="Cash Deposit">Direct Cash Deposit</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Transaction Ref / UTR / Cheque Number</label>
                    <input type="text" name="payment_reference" placeholder="e.g. UTR-9382104928 or CHQ-40291" class="w-full rounded-xl border border-slate-700 bg-slate-950 px-3.5 py-2.5 text-sm text-white focus:border-amber-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Payment Notes</label>
                    <textarea name="notes" rows="2" placeholder="Received payment via HDFC Bank NEFT / GPay..." class="w-full rounded-xl border border-slate-700 bg-slate-950 px-3.5 py-2 text-sm text-white focus:border-amber-500 focus:outline-none"></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800">
                    <button type="button" @click="showPaymentModal = false" class="rounded-xl px-4 py-2 text-sm font-semibold text-slate-400 hover:text-white transition">Cancel</button>
                    <button type="submit" class="rounded-xl bg-gradient-to-r from-emerald-500 to-emerald-600 px-5 py-2.5 text-sm font-bold text-slate-950 hover:from-emerald-400 hover:to-emerald-500 transition shadow-lg shadow-emerald-500/20">Mark as Paid & Activate</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
