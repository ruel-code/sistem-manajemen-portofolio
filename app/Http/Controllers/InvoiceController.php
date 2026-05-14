<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Project;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        $workspace = session('current_workspace');
        $invoices = Invoice::where('workspace_id', $workspace->id)
            ->with(['client', 'project'])
            ->latest()
            ->paginate(15);

        $stats = [
            'total' => Invoice::where('workspace_id', $workspace->id)->sum('total'),
            'paid' => Invoice::where('workspace_id', $workspace->id)->where('status', 'paid')->sum('total'),
            'pending' => Invoice::where('workspace_id', $workspace->id)->where('status', 'sent')->sum('total'),
            'overdue' => Invoice::where('workspace_id', $workspace->id)->where('status', 'overdue')->sum('total'),
        ];

        return view('invoices.index', compact('invoices', 'stats'));
    }

    public function create()
    {
        $workspace = session('current_workspace');
        $projects = Project::where('workspace_id', $workspace->id)->get();
        $clients = $workspace->members()->wherePivot('role', 'client')->get();

        return view('invoices.create', compact('projects', 'clients', 'workspace'));
    }

    public function store(Request $request)
    {
        $workspace = session('current_workspace');

        $validated = $request->validate([
            'client_id' => 'required|exists:users,id',
            'project_id' => 'nullable|exists:projects,id',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string',
            'items.*.qty' => 'required|numeric|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $subtotal = collect($validated['items'])->sum(fn($item) => $item['qty'] * $item['price']);
        $total = $subtotal + ($validated['tax'] ?? 0) - ($validated['discount'] ?? 0);

        $invoice = Invoice::create([
            ...$validated,
            'workspace_id' => $workspace->id,
            'invoice_number' => Invoice::generateNumber(),
            'subtotal' => $subtotal,
            'total' => $total,
            'status' => 'draft',
        ]);

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Invoice berhasil dibuat!');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['client', 'project', 'workspace']);
        return view('invoices.show', compact('invoice'));
    }

    public function pdf(Invoice $invoice)
    {
        $invoice->load(['client', 'project', 'workspace']);
        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));
        return $pdf->download("Invoice-{$invoice->invoice_number}.pdf");
    }

    public function updateStatus(Request $request, Invoice $invoice)
    {
        $request->validate(['status' => 'required|in:draft,sent,paid,overdue,cancelled']);

        $invoice->update([
            'status' => $request->status,
            'paid_at' => $request->status === 'paid' ? now() : null,
        ]);

        return back()->with('success', 'Status invoice diperbarui!');
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return redirect()->route('invoices.index')->with('success', 'Invoice dihapus.');
    }
}
