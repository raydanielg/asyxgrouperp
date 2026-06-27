<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $fillable = ['name', 'file_name', 'mime_type', 'disk', 'size', 'url', 'directory_id', 'creator_id', 'created_by'];

    public function directory() { return $this->belongsTo(MediaDirectory::class, 'directory_id'); }
    public function creator() { return $this->belongsTo(User::class, 'creator_id'); }
}
