<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToCompany;

class Setting extends Model
{
    use BelongsToCompany;
    protected $fillable = ['key', 'value', 'is_public', 'created_by'];

    protected $casts = ['is_public' => 'boolean'];

    public static function get($key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function set($key, $value)
    {
        return static::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
