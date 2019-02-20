<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Logs extends Model
{
    protected $table = 'elpts_logs';
    protected $fillable = ['operation_id', 'doc_id', 'user_name', 'value'];

    /**
     * Get Operations.
     *
     * @return object DB data
     */
    public function getOperations($operation_type_arr = [])
    {
		$result = DB::table('elpts_operations')
			->where('enable', '1')
			->orderBy('type', 'desc')
			->orderBy('sort', 'asc');

		if (count($operation_type_arr))
		{
			$result->whereIn('type', $operation_type_arr);
		}

		$rows = $result->get();

		$operations_arr = [];
		if(count($rows))
		{
			foreach($rows->all() as $v)
			{
				$operations_arr[$v->id]['name'] = $v->name;
				$operations_arr[$v->id]['type'] = $v->type;
			}
		}

		return $operations_arr;
    }

    /**
     * Get Logs.
     *
     * param object $request
     * param int $doc_id
     * param int $rows_quantity
     * @return object DB data
     */
    public function getLogs($request, $doc_id, $rows_quantity, $operation_type_arr = [])
    {
		$result = DB::table('elpts_logs')
			->select('elpts_logs.id', 'elpts_logs.user_name', 'elpts_logs.doc_id', 'elpts_logs.operation_id', 'elpts_logs.created_at', 'elpts_logs.value', 'elpts_operations.name', 'elpts_docs.prefix_number')
			->join('elpts_operations', 'elpts_operations.id', '=', 'elpts_logs.operation_id')
			->leftJoin('elpts_docs', 'elpts_docs.id', '=', 'elpts_logs.doc_id')
			->orderBy('elpts_logs.id', 'desc');

		if (count($operation_type_arr))
		{
			$result->whereIn('elpts_operations.type', $operation_type_arr);
		}

		if ($request->filter_doc)
		{
			$result->where('elpts_docs.prefix_number', '=', $request->filter_doc);
		}
		elseif (intval($doc_id))
		{
			$result->where('elpts_logs.doc_id', '=', $doc_id);
		}

		if ($request->filter_operation)
		{
			$result->where('elpts_logs.operation_id', '=', $request->filter_operation);
		}

		if ($request->filter_date_from)
		{
			$result->where('elpts_logs.created_at', '>=', date('Y-m-d H:i:s', strtotime($request->filter_date_from)));
		}

		if ($request->filter_date_to)
		{
			$result->where('elpts_logs.created_at', '<=', date('Y-m-d H:i:s', strtotime($request->filter_date_to)));
		}

		return $result->paginate($rows_quantity);
    }
}
