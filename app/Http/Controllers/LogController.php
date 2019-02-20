<?php

namespace App\Http\Controllers;

use Session;
use App\Logs;
use App\Docs;
use Illuminate\Http\Request;

class LogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $doc_id = null)
    {
		// Create Logs Object
		$log_obj = new Logs;

        $rows_quantity = 30;
		if(Session::has('settings_rows_quantity'))
		{
			$rows_quantity = Session::get('settings_rows_quantity');
		}

		$operation_type_arr = ['Статусы', 'Проверки и верификации', 'Отправка почты', 'Другое'];

		// Get Docs
        $docs = Docs::find($doc_id);

        // Get Logs
        $logs = $log_obj->getLogs($request, $doc_id, $rows_quantity, $operation_type_arr);

        // Get Operations
		$operations_arr = $log_obj->getOperations($operation_type_arr);

		$filter_operations_arr = [];
		if (count($operations_arr))
		{
			foreach ($operations_arr as $k => $v)
			{
				$filter_operations_arr[$v['type']][$k] = $v['name'];
			}
		}

		$page = $request->page;
		if(!isset($request->page)) $page = 1;

        return view('log.index')
        	->withLogs($logs)
        	->withDocs($docs)
        	->with('operations_arr', $operations_arr)
        	->with('filter_operations_arr', $filter_operations_arr)
       		->with('rows_quantity', $rows_quantity)
       		->with('page', $page)
        	->with('doc_id', $doc_id)
        	->with('operation_type_arr', $operation_type_arr)
			->withRequest($request);
    }
}
