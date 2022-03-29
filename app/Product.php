<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public $timestamps = true;
    protected $guarded = ['created_at', 'updated_at'];
}
