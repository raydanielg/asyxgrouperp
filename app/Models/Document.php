<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_number', 'title', 'description', 'category', 'file_path',
        'file_type', 'file_size', 'version', 'status', 'reference_type',
        'reference_id', 'uploaded_by', 'company_id', 'signed_at',
    ];

    protected $casts = ['signed_at' => 'datetime'];

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function signatures()
    {
        return $this->hasMany(DocumentSignature::class)->orderBy('order');
    }

    public function accessLogs()
    {
        return $this->hasMany(DocumentAccessLog::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function scopeSigned($query)
    {
        return $query->where('status', 'signed');
    }

    public function scopePendingSignature($query)
    {
        return $query->where('status', 'pending_signature');
    }
}
