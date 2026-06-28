<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use BelongsToCompany;

    protected $guarded = ['id'];

    protected $casts = [
        'start_date' => 'date',
        'due_date' => 'date',
    ];

    public function tasks()
    {
        return $this->hasMany(ProjectTask::class);
    }

    public function bugs()
    {
        return $this->hasMany(ProjectBug::class);
    }

    public function timesheets()
    {
        return $this->hasMany(Timesheet::class);
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function deal()
    {
        return $this->belongsTo(CrmDeal::class);
    }

    public function budgets()
    {
        return $this->hasMany(ProjectBudget::class);
    }

    public function lpos()
    {
        return $this->hasMany(Lpo::class);
    }

    public function officeExpenses()
    {
        return $this->hasMany(OfficeExpense::class);
    }

    public function vendorInvoices()
    {
        return $this->hasMany(VendorInvoice::class);
    }

    public function clientReceipts()
    {
        return $this->hasMany(ClientReceipt::class);
    }

    public function totalProcurementCost()
    {
        return $this->vendorInvoices()->sum('amount_paid');
    }

    public function totalOfficeExpenses()
    {
        return $this->officeExpenses()->where('status', 'approved')->sum('amount');
    }

    public function totalRevenue()
    {
        return $this->clientReceipts()->sum('amount');
    }

    public function totalCost()
    {
        return $this->totalProcurementCost() + $this->totalOfficeExpenses();
    }

    public function profit()
    {
        return $this->totalRevenue() - $this->totalCost();
    }
}
