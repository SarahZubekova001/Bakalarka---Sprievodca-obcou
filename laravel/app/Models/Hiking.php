<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Hiking extends Model
{
    use HasFactory;

    protected $table = 'hiking'; 
    protected $fillable = ['name', 'length','hours', 'minutes', 'duration', 'difficulty', 'id_category', 'id_season'];

    public $timestamps = false; 

    public function category()
    {
        return $this->belongsTo(Category::class, 'id_category');
    }

    public function season()
    {
        return $this->belongsTo(Season::class, 'id_season');
    }
    public function getFormattedDurationAttribute()
    {
        return Carbon::parse($this->duration)->format('H:i');
    }

    public function gallery()
    {
        return $this->hasOne(Gallery::class, 'id_hiking');
    }
}
