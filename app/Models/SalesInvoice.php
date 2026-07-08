<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class SalesInvoice extends Model
{
    use BelongsToCompany;

    protected $fillable = ['company_id', 'invoice_number', 'invoice_date', 'due_date', 'customer_id', 'warehouse_id', 'subtotal', 'tax_amount', 'discount_amount', 'total_amount', 'paid_amount', 'balance_amount', 'status', 'type', 'payment_terms', 'notes', 'terms_and_conditions', 'creator_id', 'created_by'];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
    ];

    public function customer() { return $this->belongsTo(User::class, 'customer_id'); }
    public function warehouse() { return $this->belongsTo(Warehouse::class); }
    public function items() { return $this->hasMany(SalesInvoiceItem::class, 'invoice_id'); }
    public function creator() { return $this->belongsTo(User::class, 'creator_id'); }
    public function project() { return $this->belongsTo(Project::class); }
    public function bankAccounts() { return $this->belongsToMany(BankAccount::class, 'invoice_bank_accounts', 'invoice_id', 'bank_account_id'); }
}
