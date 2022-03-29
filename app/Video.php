<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    public $timestamps = true;
    protected $guarded = ['created_at', 'updated_at'];
}
