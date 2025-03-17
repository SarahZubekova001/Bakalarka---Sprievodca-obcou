<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $table = 'event'; 
    protected $fillable = [
        'name',
        'date',
        'time',
        'text',
        'id_address',
        'id_image',
    ];
    public $timestamps = false; 

    public function address()
    {
        return $this->belongsTo(Address::class, 'id_address');
    }

    public function image()
    {
        return $this->belongsTo(Image::class, 'id_image');
    }
}
