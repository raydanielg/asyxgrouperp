<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HelpdeskReply extends Model
{
    protected $fillable = ['ticket_id', 'message', 'attachments', 'is_internal', 'created_by'];

    protected $casts = ['attachments' => 'array', 'is_internal' => 'boolean'];

    public function ticket() { return $this->belongsTo(HelpdeskTicket::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
}
