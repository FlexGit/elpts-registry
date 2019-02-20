<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Countries extends Model
{
    protected $table = 'elpts_countries';
    protected $fillable = ['name', 'enable'];
}
