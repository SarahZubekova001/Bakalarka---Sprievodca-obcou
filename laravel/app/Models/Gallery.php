<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    use HasFactory;

    protected $table = 'gallery'; 
    protected $fillable = [ 'id_post', 'id_restaurant', 'id_hiking']; 
    
    public $timestamps = false; 

    public function post() {
        return $this->belongsTo(Post::class, 'id_post')->withDefault();
    }

    public function restaurant() {
        return $this->belongsTo(Restaurant::class, 'id_restaurant')->withDefault();
    }

    public function hiking() {
        return $this->belongsTo(Hiking::class, 'id_hiking')->withDefault();
    }
    public function mainInfo()
    {
        return $this->belongsTo(MainInfo::class, 'id_main_info');
    }
    public function images()
    {
        return $this->hasMany(Image::class, 'id_gallery');
    }

    
}
