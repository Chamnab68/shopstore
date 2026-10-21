<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ApiKey extends Model {
    protected $fillable = ['name','key','user_id','permissions','is_active','expires_at','last_used_at','request_count'];
    protected $casts = ['permissions'=>'array','is_active'=>'boolean','expires_at'=>'datetime','last_used_at'=>'datetime'];

    public function user() { return $this->belongsTo(User::class); }

    public static function generate($name, $userId = null, $permissions = ['read']) {
        return self::create([
            'name' => $name,
            'key' => 'sk-' . bin2hex(random_bytes(20)),
            'user_id' => $userId,
            'permissions' => $permissions,
        ]);
    }

    public function hasPermission($permission) {
        return in_array($permission, $this->permissions ?? []) || in_array('*', $this->permissions ?? []);
    }

    public function isValid() {
        return $this->is_active && (!$this->expires_at || $this->expires_at->isFuture());
    }
}
