<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = ['name', 'module', 'description', 'min_tier'];

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    /** Check if a permission is tier-gated (e.g. only Platinum can access) */
    public function isTierGated(): bool
    {
        return !empty($this->min_tier);
    }
}
