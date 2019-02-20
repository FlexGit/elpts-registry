<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Doctypes extends Model
{
    protected $table = 'elpts_doctypes';
    protected $fillable = ['name', 'enable'];
}
