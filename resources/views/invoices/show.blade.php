@extends('layouts.app')
@section('title', 'Invoice ' . $invoice->invoice_number)

@section('content')
<div class="p-6 max-w-4xl mx-auto">
    <div class="bg-white dark:bg-[#13132a] border border-gray-100 dark:border-white/5 rounded-2xl shadow-xl overflow-hidden">
        <!-- Toolbar -->
        <div class="px-8 py-4 bg-gray-50 dark:bg-white/5 border-b border-gray-100 dark:border-white/5 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold uppercase tracking-widest text-gray-400">Status:</span>
                <span class="text-xs font-bold uppercase tracking-widest px-3 py-1 rounded-lg
                    {{ $invoice->status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">
                    {{ $invoice->status }}
                </span>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('invoices.pdf', $invoice) }}" class="flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-500 text-white text-sm font-bold shadow-lg hover:bg-indigo-600 transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Download PDF
                </a>
                @if($invoice->status !== 'paid')
                <form action="{{ route('invoices.status', $invoice) }}" method="POST">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="paid">
                    <button type="submit" class="px-4 py-2 rounded-xl border border-green-500 text-green-600 text-sm font-bold hover:bg-green-50 transition">Mark as Paid</button>
                </form>
                @endif
            </div>
        </div>

        <div class="p-12">
            <!-- Header -->
            <div class="flex justify-between items-start mb-12">
                <div>
                    <div class="w-12 h-12 rounded-2xl bg-indigo-500 flex items-center justify-center text-white mb-4">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $invoice->workspace->name }}</h2>
                    <p class="text-sm text-gray-500">{{ $invoice->workspace->owner->email }}</p>
                </div>
                <div class="text-right">
                    <h1 class="text-4xl font-black text-gray-900 dark:text-white uppercase tracking-tighter mb-2">Invoice</h1>
                    <p class="text-sm font-bold text-indigo-500">{{ $invoice->invoice_number }}</p>
                </div>
            </div>

            <!-- Addresses -->
            <div class="grid grid-cols-2 gap-12 mb-12">
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3">Bill To</p>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $invoice->client->name }}</h3>
                    <p class="text-sm text-gray-500">{{ $invoice->client->email }}</p>
                    @if($invoice->client->phone)
                    <p class="text-sm text-gray-500 mt-1">{{ $invoice->client->phone }}</p>
                    @endif
                </div>
                <div class="text-right">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3">Invoice Details</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Date Issued: <span class="font-bold text-gray-900 dark:text-white">{{ $invoice->issue_date->format('M d, Y') }}</span></p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Due Date: <span class="font-bold text-gray-900 dark:text-white">{{ $invoice->due_date->format('M d, Y') }}</span></p>
                    @if($invoice->project)
                    <p class="text-sm text-gray-600 dark:text-gray-400">Project: <span class="font-bold text-gray-900 dark:text-white">{{ $invoice->project->name }}</span></p>
                    @endif
                </div>
            </div>

            <!-- Items Table -->
            <table class="w-full mb-12">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-white/5">
                        <th class="py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">Description</th>
                        <th class="py-4 text-center text-[10px] font-bold text-gray-400 uppercase tracking-widest">Qty</th>
                        <th class="py-4 text-right text-[10px] font-bold text-gray-400 uppercase tracking-widest">Price</th>
                        <th class="py-4 text-right text-[10px] font-bold text-gray-400 uppercase tracking-widest">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-white/5">
                    @foreach($invoice->items as $item)
                    <tr>
                        <td class="py-6">
                            <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $item['name'] }}</p>
                        </td>
                        <td class="py-6 text-center text-sm text-gray-600 dark:text-gray-400">{{ $item['qty'] }}</td>
                        <td class="py-6 text-right text-sm text-gray-600 dark:text-gray-400">Rp {{ number_format($item['price'], 0, ',', '.') }}</td>
                        <td class="py-6 text-right text-sm font-bold text-gray-900 dark:text-white">Rp {{ number_format($item['qty'] * $item['price'], 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Summary -->
            <div class="flex justify-end">
                <div class="w-64 space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Subtotal</span>
                        <span class="font-bold text-gray-900 dark:text-white">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Tax</span>
                        <span class="font-bold text-gray-900 dark:text-white">Rp {{ number_format($invoice->tax, 0, ',', '.') }}</span>
                    </div>
                    @if($invoice->discount > 0)
                    <div class="flex justify-between text-sm text-red-500">
                        <span>Discount</span>
                        <span class="font-bold">-Rp {{ number_format($invoice->discount, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    <div class="pt-3 border-t-2 border-gray-100 dark:border-white/5 flex justify-between">
                        <span class="text-lg font-bold text-gray-900 dark:text-white">Total</span>
                        <span class="text-lg font-black text-indigo-500">Rp {{ number_format($invoice->total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            @if($invoice->notes)
            <div class="mt-12 pt-8 border-t border-gray-100 dark:border-white/5">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Notes</p>
                <p class="text-xs text-gray-500 leading-relaxed">{{ $invoice->notes }}</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
