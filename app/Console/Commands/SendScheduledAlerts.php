<?php

namespace App\Console\Commands;

use App\Models\HelpdeskTicket;
use App\Models\Document;
use App\Models\Employee;
use App\Models\Vehicle;
use App\Models\NotificationTemplate;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;
use App\Notifications\GenericNotification;

class SendScheduledAlerts extends Command
{
    protected $signature = 'alerts:send';
    protected $description = 'Send scheduled alerts for SLA breaches, document expiries, vehicle inspections, and employee contract expiries';

    public function handle(): int
    {
        $this->checkSlaBreaches();
        $this->checkDocumentExpiries();
        $this->checkEmployeeContractExpiries();
        $this->checkVehicleInspections();

        $this->info('Scheduled alerts processed.');
        return Command::SUCCESS;
    }

    protected function checkSlaBreaches(): void
    {
        $overdueTickets = HelpdeskTicket::whereNotIn('status', ['closed', 'resolved'])
            ->where('due_date', '<', now())
            ->limit(100)
            ->get();

        foreach ($overdueTickets as $ticket) {
            $assignee = $ticket->assignedTo ?? $ticket->createdBy;
            if ($assignee) {
                $assignee->notify(new GenericNotification(
                    'SLA Breach Alert',
                    "Ticket #{$ticket->ticket_number} has breached its SLA deadline.",
                    route('admin.helpdesk-tickets.show', $ticket->id)
                ));
            }
        }

        if ($overdueTickets->count() > 0) {
            $this->line("SLA breaches: {$overdueTickets->count()} tickets flagged.");
        }
    }

    protected function checkDocumentExpiries(): void
    {
        $expiringDocs = Document::whereNotNull('expiry_date')
            ->where('expiry_date', '<=', now()->addDays(30))
            ->where('expiry_date', '>=', now())
            ->limit(100)
            ->get();

        foreach ($expiringDocs as $doc) {
            $uploader = $doc->uploadedBy;
            if ($uploader) {
                $daysLeft = now()->diffInDays($doc->expiry_date, false);
                $uploader->notify(new GenericNotification(
                    'Document Expiry Warning',
                    "Document '{$doc->title}' expires in {$daysLeft} days on {$doc->expiry_date->format('d M Y')}.",
                    route('admin.documents.show', $doc->id)
                ));
            }
        }

        if ($expiringDocs->count() > 0) {
            $this->line("Document expiry alerts: {$expiringDocs->count()} documents flagged.");
        }
    }

    protected function checkEmployeeContractExpiries(): void
    {
        $expiringEmployees = Employee::whereNotNull('contract_end_date')
            ->where('contract_end_date', '<=', now()->addDays(30))
            ->where('contract_end_date', '>=', now())
            ->limit(100)
            ->get();

        foreach ($expiringEmployees as $employee) {
            $hrUsers = User::where('role', 'admin')->orWhere('role', 'hr_manager')->limit(10)->get();
            foreach ($hrUsers as $hrUser) {
                $daysLeft = now()->diffInDays($employee->contract_end_date, false);
                $hrUser->notify(new GenericNotification(
                    'Employee Contract Expiry',
                    "Contract for {$employee->full_name} expires in {$daysLeft} days.",
                    route('admin.employees.show', $employee->id)
                ));
            }
        }

        if ($expiringEmployees->count() > 0) {
            $this->line("Contract expiry alerts: {$expiringEmployees->count()} employees flagged.");
        }
    }

    protected function checkVehicleInspections(): void
    {
        $vehicles = Vehicle::whereNotNull('next_inspection_date')
            ->where('next_inspection_date', '<=', now()->addDays(30))
            ->where('next_inspection_date', '>=', now())
            ->limit(100)
            ->get();

        foreach ($vehicles as $vehicle) {
            $admins = User::where('role', 'admin')->limit(5)->get();
            foreach ($admins as $admin) {
                $daysLeft = now()->diffInDays($vehicle->next_inspection_date, false);
                $admin->notify(new GenericNotification(
                    'Vehicle Inspection Due',
                    "Vehicle {$vehicle->registration_number} inspection due in {$daysLeft} days.",
                    route('admin.fleet.show', $vehicle->id)
                ));
            }
        }

        if ($vehicles->count() > 0) {
            $this->line("Vehicle inspection alerts: {$vehicles->count()} vehicles flagged.");
        }
    }
}
