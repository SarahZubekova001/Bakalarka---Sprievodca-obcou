<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Town extends Model
{
    use HasFactory;

    protected $table = 'town'; 
    protected $primaryKey = 'postal_code'; 
    public $incrementing = false; 
    protected $keyType = 'integer';
    public $timestamps = false; // Ak tabuľka nemá created_at a updated_at

    protected $fillable = ['postal_code', 'name'];

    
    public function addresses()
    {
        return $this->hasMany(Address::class, 'postal_code', 'postal_code');
    }
}
