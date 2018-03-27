<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Players extends Model
{
    public $timestamps = true;
    protected $table = 'players';
    protected $primaryKey = 'id';

    protected $guarded = [
        //
    ];
}
