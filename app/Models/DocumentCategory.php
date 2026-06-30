<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class DocumentCategory extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id', 'name', 'slug', 'icon', 'color', 'description', 'sort_order',
    ];

    public function documents()
    {
        return $this->hasMany(Document::class, 'category', 'slug');
    }
}
