<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model {
    protected $fillable = ['title','subtitle','image','link','badge','price','original_price','is_active','sort_order'];
    protected $casts = ['is_active'=>'boolean'];
}
