@extends('layouts.platform', ['title' => 'SaaS Billing & Invoices'])

@section('content')
<div class="space-y-8" x-data="{ showBillModal: false, showPaymentModal: false, paymentInvoice: null }">
    <!-- Header (Neumorphic) -->
    <div class="neu-flat-lg rounded-3xl p-6 sm:p-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="neu-pill-inset px-2.5 py-0.5 text-xs font-mono font-bold text-[#00214D]">Revenue & Billing Ledger</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">Platform Invoicing & Payment Collection</h1>
            <p class="mt-1 text-xs sm:text-sm text-slate-600 max-w-2xl">Issue service bills, track multi-tenant SaaS subscriptions, and record payments for hotel and restaurant platform services.</p>
        </div>

        <button 
            @click="showBillModal = true" 
            class="neu-btn-primary px-5 py-2.5 rounded-xl text-xs font-bold shadow-md hover:brightness-110 transition inline-flex items-center gap-2 cursor-pointer">
            <x-icon name="plus" class="w-4 h-4 text-white" />
            <span>Generate Service Bill</span>
        </button>
    </div>

    <!-- Financial KPIs (Neumorphic) -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
        <div class="neu-flat rounded-3xl p-6">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Collected</span>
                <div class="flex h-9 w-9 items-center justify-center rounded-xl neu-button text-emerald-600">
                    <x-icon name="currency-inr" class="w-5 h-5" />
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-black tracking-tight text-emerald-700">₹{{ number_format($totalPaid, 2) }}</span>
                <span class="text-xs text-slate-500 font-medium">Lifetime Revenue</span>
            </div>
        </div>

        <div class="neu-flat rounded-3xl p-6">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Pending Receivables</span>
                <div class="flex h-9 w-9 items-center justify-center rounded-xl neu-button text-amber-600">
                    <x-icon name="clock" class="w-5 h-5" />
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-black tracking-tight text-amber-700">₹{{ number_format($totalUnpaid, 2) }}</span>
                <span class="text-xs text-slate-500 font-medium">Awaiting Settlement</span>
            </div>
        </div>

        <div class="neu-flat rounded-3xl p-6">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Overdue Invoices</span>
                <div class="flex h-9 w-9 items-center justify-center rounded-xl neu-button text-rose-600">
                    <x-icon name="bell" class="w-5 h-5" />
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-black tracking-tight text-rose-700">{{ $overdueCount }}</span>
                <span class="text-xs text-slate-500 font-medium">Action Required</span>
            </div>
        </div>
    </div>

    <!-- Invoices List Card (Neumorphic) -->
    <div class="neu-flat rounded-3xl p-6 sm:p-7 space-y-5">
        <!-- Filter Tabs -->
        <div class="flex flex-col sm:flex-row items-center justify-between border-b border-slate-300/60 pb-4 gap-4">
            <div class="flex items-center gap-2">
                <a href="{{ route('platform.billing.index') }}" 
                   class="rounded-xl px-3.5 py-1.5 text-xs font-bold transition {{ $currentStatus === 'all' ? 'neu-inset text-[#00214D]' : 'neu-button text-slate-600 hover:text-slate-900' }}">
                    All Bills
                </a>
                <a href="{{ route('platform.billing.index', ['status' => 'unpaid']) }}" 
                   class="rounded-xl px-3.5 py-1.5 text-xs font-bold transition {{ $currentStatus === 'unpaid' ? 'neu-inset text-[#00214D]' : 'neu-button text-slate-600 hover:text-slate-900' }}">
                    Unpaid
                </a>
                <a href="{{ route('platform.billing.index', ['status' => 'paid']) }}" 
                   class="rounded-xl px-3.5 py-1.5 text-xs font-bold transition {{ $currentStatus === 'paid' ? 'neu-inset text-[#00214D]' : 'neu-button text-slate-600 hover:text-slate-900' }}">
                    Paid
                </a>
                <a href="{{ route('platform.billing.index', ['status' => 'overdue']) }}" 
                   class="rounded-xl px-3.5 py-1.5 text-xs font-bold transition {{ $currentStatus === 'overdue' ? 'neu-inset text-[#00214D]' : 'neu-button text-slate-600 hover:text-slate-900' }}">
                    Overdue
                </a>
            </div>

            <p class="text-xs text-slate-500 font-medium">Showing <strong class="text-slate-900">{{ $invoices->total() }}</strong> total platform service invoices</p>
        </div>

        <!-- Table inside Inset Surface -->
        <div class="neu-inset rounded-2xl overflow-x-auto p-1">
            <table class="w-full text-left text-sm text-slate-700">
                <thead class="text-xs uppercase tracking-wider text-slate-500 border-b border-slate-300/60 bg-transparent">
                    <tr>
                        <th class="px-5 py-3.5">Invoice #</th>
                        <th class="px-5 py-3.5">Hotel / Tenant</th>
                        <th class="px-5 py-3.5">Description</th>
                        <th class="px-5 py-3.5">Base + 18% GST</th>
                        <th class="px-5 py-3.5">Total Amount</th>
                        <th class="px-5 py-3.5">Due Date</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-300/40">
                    @forelse($invoices as $invoice)
                        <tr class="hover:bg-white/40 transition">
                            <td class="px-5 py-3.5 font-mono font-bold text-slate-900">
                                <a href="{{ route('platform.billing.invoices.show', $invoice) }}" class="text-[#0073E6] hover:underline">
                                    {{ $invoice->invoice_number }}
                                </a>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="font-bold text-slate-900 text-xs">{{ $invoice->hotel->name ?? 'Hotel #'.$invoice->hotel_id }}</div>
                                <div class="text-[11px] text-slate-500">{{ $invoice->hotel->city ?? 'N/A' }}</div>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="text-xs text-slate-800 line-clamp-1 font-medium">{{ $invoice->title }}</div>
                                @if($invoice->payment_method)
                                    <div class="text-[10px] text-slate-500 mt-0.5">Paid via: <span class="uppercase font-semibold text-emerald-700">{{ $invoice->payment_method }}</span> ({{ $invoice->payment_reference }})</div>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 font-mono text-xs">
                                <div class="font-semibold text-slate-800">₹{{ number_format((float)$invoice->subtotal, 2) }}</div>
                                <div class="text-[10px] text-slate-500">+ ₹{{ number_format((float)$invoice->tax_amount, 2) }} GST</div>
                            </td>
                            <td class="px-5 py-3.5 font-mono font-bold text-slate-900">
                                ₹{{ number_format((float)$invoice->total_amount, 2) }}
                            </td>
                            <td class="px-5 py-3.5 text-xs">
                                <div class="{{ $invoice->due_date->isPast() && $invoice->status === 'unpaid' ? 'text-rose-600 font-bold' : 'text-slate-600 font-medium' }}">
                                    {{ $invoice->due_date->format('M d, Y') }}
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                @if($invoice->status === 'paid')
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-0.5 text-[11px] font-bold text-emerald-800 border border-emerald-300">
                                        <x-icon name="check" class="w-3 h-3" />
                                        <span>Paid</span>
                                    </span>
                                @elseif($invoice->due_date->isPast())
                                    <span class="inline-flex items-center gap-1 rounded-full bg-rose-100 px-2.5 py-0.5 text-[11px] font-bold text-rose-800 border border-rose-300">
                                        <x-icon name="bell" class="w-3 h-3" />
                                        <span>Overdue</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2.5 py-0.5 text-[11px] font-bold text-amber-800 border border-amber-300">
                                        <x-icon name="clock" class="w-3 h-3" />
                                        <span>Unpaid</span>
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-right space-x-2">
                                @if($invoice->status !== 'paid')
                                    <button 
                                        @click="paymentInvoice = {{ json_encode([
                                             'id' => $invoice->id,
                                             'number' => $invoice->invoice_number,
                                             'hotel_name' => $invoice->hotel->name ?? 'Hotel',
                                             'amount' => number_format((float)$invoice->total_amount, 2),
                                             'action_url' => route('platform.billing.invoices.payment', $invoice)
                                         ]) }}; showPaymentModal = true"
                                        class="neu-button rounded-xl px-3 py-1.5 text-xs font-bold text-emerald-800 hover:text-emerald-950 transition cursor-pointer">
                                        Record Payment
                                    </button>
                                @endif

                                <a href="{{ route('platform.billing.invoices.show', $invoice) }}" 
                                   class="neu-button rounded-xl px-3 py-1.5 text-xs font-bold text-slate-700 hover:text-slate-950 transition">
                                    View / Print
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-sm text-slate-500 font-medium">
                                No invoices found. Click "Generate Service Bill" to create a new SaaS billing invoice.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($invoices->hasPages())
            <div class="pt-2">
                {{ $invoices->links() }}
            </div>
        @endif
    </div>

    <!-- Modal: Generate Service Bill (Neumorphic) -->
    <div x-cloak x-show="showBillModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm">
        <div @click.away="showBillModal = false" class="w-full max-w-lg neu-flat-lg rounded-3xl p-6 sm:p-8 space-y-6 bg-[#e8edf5]">
            <div class="flex items-center justify-between border-b border-slate-300/60 pb-4">
                <div>
                    <h3 class="text-lg font-extrabold text-slate-900">Generate SaaS Service Bill</h3>
                    <p class="text-xs text-slate-500">Create an official GST invoice for platform subscription or support</p>
                </div>
                <button @click="showBillModal = false" class="neu-button rounded-xl h-8 w-8 flex items-center justify-center text-slate-600 hover:text-slate-900 font-bold">&times;</button>
            </div>

            <form method="post" action="{{ route('platform.billing.invoices.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Target Hotel / Tenant *</label>
                    <select name="hotel_id" required class="w-full rounded-xl neu-input px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none">
                        <option value="">Select onboarded hotel...</option>
                        @foreach($hotels as $hotel)
                            <option value="{{ $hotel->id }}">{{ $hotel->name }} ({{ $hotel->city ?? 'India' }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Service Description / Line Item *</label>
                    <input type="text" name="title" required placeholder="e.g. Monthly SaaS Platform Cloud License (Pro Plan)" value="Monthly SaaS Platform License & Support" class="w-full rounded-xl neu-input px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Base Amount (₹ INR) *</label>
                        <input type="number" step="0.01" name="subtotal" required placeholder="9999.00" class="w-full rounded-xl neu-input px-3.5 py-2.5 text-xs text-slate-900 font-mono focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">GST Rate (%)</label>
                        <input type="number" step="0.1" name="tax_percent" value="18" class="w-full rounded-xl neu-input px-3.5 py-2.5 text-xs text-slate-900 font-mono focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Payment Due Date *</label>
                    <input type="date" name="due_date" required value="{{ now()->addDays(15)->format('Y-m-d') }}" class="w-full rounded-xl neu-input px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Invoice Notes / Terms</label>
                    <textarea name="notes" rows="2" placeholder="Payment due within 15 days via UPI, NEFT, or Corporate Net Banking." class="w-full rounded-xl neu-input px-3.5 py-2 text-xs text-slate-900 focus:outline-none">Payment due within 15 days of issue. Multi-tenant SaaS service license.</textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-300/60">
                    <button type="button" @click="showBillModal = false" class="neu-button rounded-xl px-4 py-2 text-xs font-bold text-slate-600 hover:text-slate-900 transition">Cancel</button>
                    <button type="submit" class="neu-btn-primary rounded-xl px-5 py-2 text-xs font-bold text-white shadow-md hover:brightness-110 transition cursor-pointer">Generate Bill</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Record Payment (Neumorphic) -->
    <div x-cloak x-show="showPaymentModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm">
        <div @click.away="showPaymentModal = false" class="w-full max-w-lg neu-flat-lg rounded-3xl p-6 sm:p-8 space-y-6 bg-[#e8edf5]">
            <div class="flex items-center justify-between border-b border-slate-300/60 pb-4">
                <div>
                    <h3 class="text-lg font-extrabold text-slate-900">Record SaaS Payment Receipt</h3>
                    <p class="text-xs text-slate-500">Capture payment details for <span class="font-mono text-[#0073E6] font-bold" x-text="paymentInvoice?.number"></span></p>
                </div>
                <button @click="showPaymentModal = false" class="neu-button rounded-xl h-8 w-8 flex items-center justify-center text-slate-600 hover:text-slate-900 font-bold">&times;</button>
            </div>

            <div class="neu-inset rounded-2xl p-4 space-y-2">
                <div class="flex justify-between text-xs">
                    <span class="text-slate-500 font-medium">Hotel / Tenant:</span>
                    <span class="font-bold text-slate-900" x-text="paymentInvoice?.hotel_name"></span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-slate-500 font-medium">Total Due Amount:</span>
                    <span class="font-mono font-black text-emerald-700" x-text="'₹' + paymentInvoice?.amount"></span>
                </div>
            </div>

            <form method="post" :action="paymentInvoice?.action_url" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Payment Method / Channel *</label>
                    <select name="payment_method" required class="w-full rounded-xl neu-input px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none">
                        <option value="UPI / QR (GPay, PhonePe, Paytm)">UPI / QR (Google Pay, PhonePe, Paytm)</option>
                        <option value="NEFT / RTGS / Bank Transfer">NEFT / RTGS / Bank Transfer</option>
                        <option value="Credit / Debit Card (Razorpay)">Credit / Debit Card (Online Gateway)</option>
                        <option value="Corporate Cheque">Corporate Cheque</option>
                        <option value="Cash Deposit">Direct Cash Deposit</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Transaction Ref / UTR / Cheque Number</label>
                    <input type="text" name="payment_reference" placeholder="e.g. UTR-9382104928 or CHQ-40291" class="w-full rounded-xl neu-input px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Payment Notes</label>
                    <textarea name="notes" rows="2" placeholder="Received payment via HDFC Bank NEFT / GPay..." class="w-full rounded-xl neu-input px-3.5 py-2 text-xs text-slate-900 focus:outline-none"></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-300/60">
                    <button type="button" @click="showPaymentModal = false" class="neu-button rounded-xl px-4 py-2 text-xs font-bold text-slate-600 hover:text-slate-900 transition">Cancel</button>
                    <button type="submit" class="neu-btn-blue rounded-xl px-5 py-2 text-xs font-bold text-white shadow-md hover:brightness-110 transition cursor-pointer">Mark as Paid & Activate</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
