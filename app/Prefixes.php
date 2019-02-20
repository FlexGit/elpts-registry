<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Prefixes extends Model
{
    protected $table = 'elpts_prefixes';
    protected $fillable = ['name', 'doctype_id', 'enable'];
}
