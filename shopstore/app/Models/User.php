<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = ['name','email','password','phone','address','role','avatar','is_active'];

    protected $hidden = ['password','remember_token'];

    protected $casts = ['email_verified_at'=>'datetime','password'=>'hashed','is_active'=>'boolean'];

    public function orders() { return $this->hasMany(Order::class); }
    public function carts() { return $this->hasMany(Cart::class); }
    public function apiKeys() { return $this->hasMany(ApiKey::class); }

    public function isAdmin() { return $this->role === 'admin'; }

    public function getAvatarUrlAttribute() {
        return $this->avatar ? asset('storage/'.$this->avatar) : asset('images/default-avatar.png');
    }
}
