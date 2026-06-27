<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaDirectory extends Model
{
    protected $fillable = ['name', 'slug', 'parent_id', 'creator_id', 'created_by'];

    public function parent() { return $this->belongsTo(MediaDirectory::class, 'parent_id'); }
    public function children() { return $this->hasMany(MediaDirectory::class, 'parent_id'); }
    public function media() { return $this->hasMany(Media::class, 'directory_id'); }
}
