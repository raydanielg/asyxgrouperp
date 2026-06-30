<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Models\SalesInvoice;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateRecurringInvoices extends Command
{
    protected $signature = 'invoices:generate-recurring';
    protected $description = 'Generate monthly recurring invoices for active projects with recurring invoicing enabled';

    public function handle(): int
    {
        $projects = Project::where('recurring_invoicing', true)
            ->whereIn('status', ['in_progress', 'planning', 'active'])
            ->get();

        $generated = 0;
        $skipped = 0;

        foreach ($projects as $project) {
            $nextDate = $project->nextInvoiceDate();

            if (!$nextDate) {
                $skipped++;
                continue;
            }

            if ($nextDate->gt(now())) {
                $skipped++;
                continue;
            }

            $existing = SalesInvoice::where('project_id', $project->id)
                ->whereYear('invoice_date', $nextDate->year)
                ->whereMonth('invoice_date', $nextDate->month)
                ->exists();

            if ($existing) {
                $skipped++;
                continue;
            }

            $invoiceNumber = 'INV-' . now()->format('YmdHis') . '-' . Str::random(4);

            SalesInvoice::create([
                'company_id' => $project->company_id,
                'project_id' => $project->id,
                'invoice_number' => $invoiceNumber,
                'invoice_date' => $nextDate,
                'due_date' => $nextDate->copy()->addDays(30),
                'customer_id' => $project->customer_id,
                'subtotal' => $project->billing_amount,
                'tax_amount' => 0,
                'discount_amount' => 0,
                'total_amount' => $project->billing_amount,
                'paid_amount' => 0,
                'balance_amount' => $project->billing_amount,
                'status' => 'draft',
                'type' => 'project',
                'payment_terms' => 'Monthly recurring invoice for ' . $project->title,
                'notes' => 'Auto-generated recurring invoice for ' . $nextDate->format('F Y'),
                'creator_id' => $project->manager_id ?? 1,
                'created_by' => $project->manager_id ?? 1,
            ]);

            $project->update(['last_invoiced_at' => now()]);

            $this->info("Generated invoice for project: {$project->title} ({$nextDate->format('M Y')})");
            $generated++;
        }

        $this->info("Done. Generated: {$generated}, Skipped: {$skipped}");
        return self::SUCCESS;
    }
}
