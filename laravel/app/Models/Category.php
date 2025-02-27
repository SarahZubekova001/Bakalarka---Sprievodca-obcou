<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'category'; 
    protected $fillable = ['name', 'id_image']; 
    
    public $timestamps = false; 

    public function image() {
        return $this->belongsTo(Image::class, 'id_image')->withDefault();
    }

    
}
