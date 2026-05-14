@extends('layouts.app')
@section('title', 'Invoices')

@section('content')
<div class="p-6 space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Invoices</h1>
            <p class="text-sm text-gray-400 mt-0.5">Kelola invoice & pembayaran</p>
        </div>
        <a href="{{ route('invoices.create') }}"
           class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-white text-sm font-medium shadow-lg hover:-translate-y-0.5 transition-all"
           style="background: linear-gradient(135deg, #6366f1, #8b5cf6)">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Invoice
        </a>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach([
            ['Total', $stats['total'], 'text-gray-700 dark:text-gray-300', 'bg-gray-100 dark:bg-gray-800'],
            ['Paid', $stats['paid'], 'text-green-700 dark:text-green-400', 'bg-green-50 dark:bg-green-900/20'],
            ['Pending', $stats['pending'], 'text-blue-700 dark:text-blue-400', 'bg-blue-50 dark:bg-blue-900/20'],
            ['Overdue', $stats['overdue'], 'text-red-700 dark:text-red-400', 'bg-red-50 dark:bg-red-900/20'],
        ] as [$label, $value, $textClass, $bgClass])
        <div class="{{ $bgClass }} rounded-2xl p-5">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ $label }}</p>
            <p class="text-lg font-bold {{ $textClass }}">Rp {{ number_format($value, 0, ',', '.') }}</p>
        </div>
        @endforeach
    </div>

    <!-- Invoice table -->
    <div class="bg-white dark:bg-[#13132a] border border-gray-100 dark:border-white/5 rounded-2xl overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 dark:bg-white/5 border-b border-gray-100 dark:border-white/5">
                <tr class="text-left">
                    <th class="px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Invoice #</th>
                    <th class="px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Client</th>
                    <th class="px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Project</th>
                    <th class="px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Amount</th>
                    <th class="px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Due Date</th>
                    <th class="px-5 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 dark:divide-white/5">
                @forelse($invoices as $invoice)
                <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition">
                    <td class="px-5 py-4 text-sm font-medium text-indigo-600 dark:text-indigo-400">
                        <a href="{{ route('invoices.show', $invoice) }}">{{ $invoice->invoice_number }}</a>
                    </td>
                    <td class="px-5 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $invoice->client->name }}</td>
                    <td class="px-5 py-4 text-sm text-gray-500">{{ $invoice->project?->name ?? '-' }}</td>
                    <td class="px-5 py-4 text-sm font-semibold text-gray-900 dark:text-white">{{ $invoice->formatted_total }}</td>
                    <td class="px-5 py-4">
                        <span class="text-xs px-2.5 py-1 rounded-full font-medium
                            {{ $invoice->status === 'paid' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' :
                               ($invoice->status === 'overdue' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' :
                               ($invoice->status === 'sent' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' :
                               'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400')) }}">
                            {{ ucfirst($invoice->status) }}
                        </span>
                    </td>
                    <td class="px-5 py-4 text-sm {{ $invoice->due_date->isPast() && $invoice->status !== 'paid' ? 'text-red-500 font-medium' : 'text-gray-500' }}">
                        {{ $invoice->due_date->format('d M Y') }}
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('invoices.pdf', $invoice) }}" class="text-xs text-indigo-500 hover:text-indigo-700 transition" title="Download PDF">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </a>
                            @if($invoice->status !== 'paid')
                            <form action="{{ route('invoices.status', $invoice) }}" method="POST">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="paid">
                                <button type="submit" class="text-xs text-green-600 hover:text-green-800 transition font-medium">Mark Paid</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-12 text-center text-gray-400 text-sm">
                        Belum ada invoice. <a href="{{ route('invoices.create') }}" class="text-indigo-500">Buat sekarang →</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-5 py-4 border-t border-gray-50 dark:border-white/5">
            {{ $invoices->links() }}
        </div>
    </div>
</div>
@endsection
