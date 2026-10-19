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
        $order = ['view' => 0, 'create' => 1, 'edit' => 2, 'approve' => 3, 'delete' => 4];
        $grouped = [];
        foreach ($perms as $p) {
            $grouped[$p->module][] = $p;
        }
        foreach ($grouped as &$modulePerms) {
            usort($modulePerms, function ($a, $b) use ($order) {
                $aAction = explode('-', $a->name)[0];
                $bAction = explode('-', $b->name)[0];
                return ($order[$aAction] ?? 9) <=> ($order[$bAction] ?? 9) ?: strcmp($a->name, $b->name);
            });
        }
        ksort($grouped);
        return $grouped;
    }
}
