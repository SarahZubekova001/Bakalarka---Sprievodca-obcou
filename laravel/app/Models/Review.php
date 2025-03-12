<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $table = 'review';

    protected $primaryKey = 'id_post';

    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'mail',
        'id_post',
        'id_restaurant',
        'text',
        'evaluation'
    ];

    public function post()
    {
        return $this->belongsTo(Post::class, 'id_post');
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class, 'id_restaurant');
    }
}
