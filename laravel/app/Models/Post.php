<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $table = 'post'; 
    protected $fillable = ['name', 'description', 'id_address', 'opening_hours', 'id_season', 'id_category', 'url_address']; 
    
    public $timestamps = false; 

    public function address() {
        return $this->belongsTo(Address::class, 'id_address')->withDefault();
    }

    public function season() {
        return $this->belongsTo(Season::class, 'id_season')->withDefault();
    }

    public function category() {
        return $this->belongsTo(Category::class, 'id_category')->withDefault();
    }
    public function gallery()
    {
        return $this->hasOne(Gallery::class, 'id_post');
    }
    
    
}
