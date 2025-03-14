<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MainInfo extends Model
{
    use HasFactory;

    protected $table = 'main_info';
    protected $primaryKey = 'id';


    protected $fillable = [
        'town_name',
        'description',
        'logo'
    ];
    public $timestamps = false;

    public function logoImage()
    {
        return $this->belongsTo(Image::class, 'logo', 'id');
    }

    public function gallery()
    {
        return $this->hasOne(Gallery::class, 'id_main_info');
    }

}
