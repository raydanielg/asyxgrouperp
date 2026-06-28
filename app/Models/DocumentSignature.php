<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentSignature extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id', 'signer_id', 'signer_name', 'signer_email',
        'status', 'signed_at', 'signature_hash', 'ip_address', 'decline_reason', 'order',
    ];

    protected $casts = ['signed_at' => 'datetime'];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function signer()
    {
        return $this->belongsTo(User::class, 'signer_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
