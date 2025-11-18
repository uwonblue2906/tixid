<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cinema extends Model
{
    use SoftDeletes;
    //mendaftarkan column-column selain yg bawaannya, selain id dan timestampts softdeletes. agar dpt diisi datanya ke column tsb
    protected $fillable = ['name', 'location'];

    public function schedules()
    {
        //panggil jenis relasinya
        return $this->hasMany(Schedule::class);
    }
}
