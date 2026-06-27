<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = ['name', 'label', 'module', 'group'];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permission');
    }

    public static function groupedByModule(): array
    {
        $perms = self::orderBy('module')->orderBy('name')->get();
        $grouped = [];
        foreach ($perms as $p) {
            if (!isset($grouped[$p->module])) {
                $grouped[$p->module] = [];
            }
            $grouped[$p->module][] = $p;
        }
        return $grouped;
    }
}
