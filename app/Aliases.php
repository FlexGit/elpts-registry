<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Aliases extends Model
{
    protected $table = 'elpts_docs_fields_aliases';
    protected $fillable = ['alias', 'templates_id', 'docs_fields_id'];
}
