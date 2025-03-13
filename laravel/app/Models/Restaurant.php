<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    use HasFactory;

    protected $table = 'restaurant'; 
    protected $fillable = ['name', 'opening_hours', 'phone_number', 'id_address', 'url_address']; 
    
    public $timestamps = false; 

    public function address() {
        return $this->belongsTo(Address::class, 'id_address')->withDefault();
    }

    public function gallery()
    {
        return $this->hasOne(Gallery::class, 'id_restaurant');
    }
    
    public function reviews()
    {
        return $this->hasMany(Review::class, 'id_restaurant');
    }
}
