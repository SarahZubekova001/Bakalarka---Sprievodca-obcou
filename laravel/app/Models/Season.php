<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Season extends Model
{
    use HasFactory;

    protected $table = 'season'; 
    protected $fillable = ['name', 'id_image'];
    
    public $timestamps = false; 

    public function image() {
        return $this->belongsTo(Image::class, 'id_image');
    }
}
