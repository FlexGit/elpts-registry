<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Docs extends Model
{
    protected $table = 'elpts_docs';
    protected $fillable = ['number', 'prefix_number', 'templates_id', 'doctypes_id', 'prefix_id', 'status_id'];

    /**
     * Get Doctype's Doc Fields.
     *
     * @param  int  $doctypes_id
     * @param boolean $only_user_fields
     * @return object DB data
     */
    public function getDocsFields($doctypes_id, $only_user_fields = false)
    {
		$result = DB::table('elpts_docs_fields')
			->whereIn('doctypes_id', [0, $doctypes_id])
			->where('enable', '=', '1')
			->orderBy('sort');
		if($only_user_fields)
		{
	    	$result->where('visible', '=', '0');
	    }

		return $result->get();
    }

    /**
     * Get Doc Fields Values by IDs.
     *
     * @param  array  $ids
     * @return array $values_arr
     */
    public function getDocsFieldsValues($ids = [])
    {
		$rows = DB::table('elpts_docs_fields_values')
			->select('elpts_docs_fields_values.docs_id', 'elpts_docs_fields.name', 'elpts_docs_fields.alias', 'elpts_docs_fields_values.value', 'elpts_docs_fields.type', 'elpts_docs_fields.link', 'elpts_docs_fields_values.fields_id', 'elpts_docs_fields_values.status_id', 'elpts_docs_fields.templates_fields_id')
			->join('elpts_docs_fields', 'elpts_docs_fields_values.fields_id', '=', 'elpts_docs_fields.id')
			->where('elpts_docs_fields.enable', '=', '1')
			->whereIn('elpts_docs_fields_values.docs_id', $ids)
			->orderBy('elpts_docs_fields.sort')
			->get();

		$values_arr = [];
		if (count($rows) > 0)
		{
        	foreach ($rows->all() as $value)
        	{
        		// BUG SITEELPTS-18
        		if(in_array($value->fields_id, array(7,76)))
        		{
        			$value->value = implode('; ', explode(';', $value->value));
        		}

       			$values_arr[$value->docs_id][$value->fields_id]['name'] = $value->name;
       			$values_arr[$value->docs_id][$value->fields_id]['templates_fields_id'] = $value->templates_fields_id;
       			$values_arr[$value->docs_id][$value->fields_id]['alias'] = $value->alias;
       			$values_arr[$value->docs_id][$value->fields_id]['value'] = $value->value;
       			$values_arr[$value->docs_id][$value->fields_id]['type'] = $value->type;
       			$values_arr[$value->docs_id][$value->fields_id]['link'] = $value->link;
       			$values_arr[$value->docs_id][$value->fields_id]['status_id'] = $value->status_id;
        	}
        }

        return $values_arr;
    }

    /**
     * Get Other Docs With The Same OGRN/OGRNIP.
     *
     * @param  int  $id
     * @param  string  $ogrn
     * @return array $values_arr
     */
    public function getPrevDocsByOgrn($id, $ogrn)
    {
		$rows = DB::table('elpts_docs')
			->select('elpts_docs.id', 'elpts_docs.prefix_number', 'elpts_docs.status_id', 'elpts_docs.templates_id', 'elpts_docs.doctypes_id', 'elpts_docs.created_at', 'elpts_templates.name as template')
			->join('elpts_docs_fields_values', 'elpts_docs_fields_values.docs_id', '=', 'elpts_docs.id')
			->leftJoin('elpts_templates', 'elpts_templates.id', '=', 'elpts_docs.templates_id')
			->where([
				['elpts_docs_fields_values.fields_id', '=', '5'],
				['elpts_docs_fields_values.value', '=', $ogrn],
				['elpts_docs.status_id', '>', '0'],
			])
			->orderBy('elpts_docs.doctypes_id')
			->orderBy('elpts_docs.id')
			->get();

		$values_arr = [];
		if (count($rows) > 0)
		{
        	foreach ($rows->all() as $value)
        	{
       			$values_arr[$value->doctypes_id][$value->id]['prefix_number'] = $value->prefix_number;
       			$values_arr[$value->doctypes_id][$value->id]['status_id'] = $value->status_id;
       			$values_arr[$value->doctypes_id][$value->id]['templates_id'] = $value->templates_id;
       			$values_arr[$value->doctypes_id][$value->id]['template'] = $value->template;
       			$values_arr[$value->doctypes_id][$value->id]['doctypes_id'] = $value->doctypes_id;
       			$values_arr[$value->doctypes_id][$value->id]['created_at'] = $value->created_at;
        	}
        }

        return $values_arr;
    }

    /**
     * Get Docs Statuses.
     */
    public function getStatuses($doctypes_id = null)
    {
		$result = DB::table('elpts_statuses')
			->where('enable', '=', DB::raw(1))
			->orderby('sort');
		if(!empty($doctypes_id))
		{
			$result->whereIn('doctypes_id', [0,$doctypes_id]);
		}

		return $result->get();
    }

    /**
     * Get Pays.
     */
    public function getPays()
    {
		return DB::table('elpts_pays')
			->orderby('id')
			->get();
    }

    /**
     * Get Junks.
     */
    public function getJunks()
    {
		return DB::table('elpts_junks')
			->orderby('id')
			->get();
    }

    /**
     * Get Owners.
     */
    public function getOwners()
    {
		return DB::table('elpts_owners')
			->orderby('id')
			->get();
    }

    /**
     * Get Docs Fields Statuses.
     */
    public function getFieldsStatuses()
    {
		return DB::table('elpts_docs_fields_statuses')
			->orderby('id')
			->get();
    }

    /**
     * Update Doc Fields Statuses.
     *
     * @param  int  $id
     * @param  array  $fields
     * @param  \Illuminate\Http\Request  $request
     */
    public function updateDocFieldsStatuses($id, $fields, $request)
    {
   	   	$request_arr = $request->all();

		// Prepare Template Fields Values
		$values = $values_status = [];
		if (count($fields) > 0)
		{
			foreach ($fields as $field)
			{
				if(!empty($request_arr['status_id'.$field->id]))
				{
					$values_status[$field->id] = $request_arr['status_id'.$field->id];
				}
				elseif(!empty($request_arr['doc_field'.$field->id]))
				{
					$values[$field->id] = $request_arr['doc_field'.$field->id];
				}
			}
		}

		if(count($values_status))
		{
			foreach($values_status as $key => $value)
			{
				// Save Doc Fields Status Values
				DB::table('elpts_docs_fields_values')
					->where([
						['docs_id', '=', $id],
						['fields_id', '=', $key],
					])
					->update(['status_id' => $value]);
			}
		}

		if(count($values))
		{
			foreach($values as $key => $value)
			{
				// Save Doc Fields Values
				$v = [
					'docs_id' => $id,
					'fields_id' => $key,
					'value' => $value
				];

				$attributes = [
					'docs_id' => $id,
					'fields_id' => $key
				];

				DB::table('elpts_docs_fields_values')
					->updateOrInsert($attributes, $v);
			}
		}
    }
}
