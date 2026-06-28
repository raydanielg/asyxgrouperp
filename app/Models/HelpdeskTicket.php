<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class HelpdeskTicket extends Model
{
    use BelongsToCompany;

    protected $fillable = ['ticket_id', 'title', 'description', 'status', 'priority', 'category_id', 'created_by', 'resolved_at', 'company_id'];

    protected $casts = ['resolved_at' => 'datetime'];

    public function category() { return $this->belongsTo(HelpdeskCategory::class); }
    public function replies() { return $this->hasMany(HelpdeskReply::class, 'ticket_id'); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
}
