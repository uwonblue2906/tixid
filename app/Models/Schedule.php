<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Schedule extends Model
{
    use SoftDeletes;

    protected $fillable = ['cinema_id', 'movie_id', 'hours', 'price'];

    public function casts()
    {
        return [
            //agar format data yg disimpan array bkn json
            'hours' => 'array'
        ];
    }

    public function cinema()
    {
        //karena schedule ada fk cinema_id definisikan dgn belongsto
        return $this->belongsTo(Cinema::class);
    }

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }
    public function tickets()
    {
        return $this->belongsTo(Ticket::class);
    }
}
