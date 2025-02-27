<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    protected $table = 'image'; 
    protected $fillable = ['id_gallery','path'];
    
    public $timestamps = false; 

    public function category()
{
    return $this->hasOne(Category::class, 'id_image')->onDelete('set null');
}

    public function season()
    {
        return $this->hasOne(Season::class, 'id_image')->onDelete('set null');
    }


    public function gallery()
    {
        return $this->belongsTo(Gallery::class, 'id_gallery'); // Ak je súčasťou galérie
    }
}
