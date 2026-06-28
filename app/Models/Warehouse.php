<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use BelongsToCompany;

    protected $fillable = ['name', 'address', 'city', 'zip_code', 'phone', 'email', 'is_active', 'creator_id', 'created_by', 'company_id'];

    protected $casts = ['is_active' => 'boolean'];

    public function creator() { return $this->belongsTo(User::class, 'creator_id'); }
    public function createdByUser() { return $this->belongsTo(User::class, 'created_by'); }
}
