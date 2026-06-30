<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory, BelongsToCompany;

    protected $fillable = [
        'document_number', 'title', 'description', 'category', 'tags', 'file_path',
        'file_type', 'file_size', 'version', 'status', 'is_confidential',
        'reference_type', 'reference_id', 'project_id', 'uploaded_by',
        'company_id', 'signed_at', 'expiry_date', 'parent_document_id',
    ];

    protected $casts = [
        'signed_at' => 'datetime',
        'expiry_date' => 'date',
        'is_confidential' => 'boolean',
    ];

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function parent()
    {
        return $this->belongsTo(Document::class, 'parent_document_id');
    }

    public function versions()
    {
        return $this->hasMany(Document::class, 'parent_document_id')->orderByDesc('version');
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

    public function scopeForProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function isExpired()
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }
}
