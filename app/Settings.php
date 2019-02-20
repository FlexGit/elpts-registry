<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Settings extends Model
{
    protected $table = 'elpts_settings';
    protected $fillable = ['value'];

    /**
     * Get Docs Fields Roles Rights.
     *
     * @return array DB data
     */
    public function getDocsFieldsRolesRights($rights)
    {
    	$rights_arr = [];
    	if (count($rights) > 0)
    	{
    		foreach($rights as $k => $v)
    		{
    			if(!$v)	continue;

    			$rights_arr[] = $k;
    		}
    	}

		$values_arr = [];

		if (count($rights_arr) > 0)
		{
			$values = DB::table('elpts_docs_fields_roles_rights')
				->select(DB::raw('docs_fields_id, max(rights_id) as rights_id'))
				->whereIn('roles_id', $rights_arr)
				->groupBy('docs_fields_id')
				->get();

			if (count($values) > 0)
			{
	        	foreach ($values->all() as $value)
	        	{
	        		$values_arr[$value->docs_fields_id]= $value->rights_id;
	        	}
	        }
        }

        return $values_arr;
    }
}
