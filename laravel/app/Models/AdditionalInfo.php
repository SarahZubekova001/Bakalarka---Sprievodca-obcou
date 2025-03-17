<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class AdditionalInfo extends Model
{
    use HasFactory;

    protected $table = 'additional_info'; 
    public $timestamps = false;           

    protected $fillable = [
        'name',
        'text',
        'id_image',
        'id_address',
    ];

    public function image()
    {
        return $this->belongsTo(Image::class, 'id_image');
    }

    public function address()
    {
        return $this->belongsTo(Address::class, 'id_address');
    }
}
