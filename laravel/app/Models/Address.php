<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $table = 'address'; 
    protected $primaryKey = 'id'; 
    public $timestamps = false; 
    
    protected $fillable = ['street', 'descriptive_number', 'postal_code'];


    public function town()
    {
        return $this->belongsTo(Town::class, 'postal_code', 'postal_code');
    }
}
