<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Okopfs extends Model
{
    protected $table = 'elpts_okopfs';
    protected $fillable = ['id', 'name', 'enable'];
}
