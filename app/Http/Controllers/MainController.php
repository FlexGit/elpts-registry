<?php

namespace App\Http\Controllers;

use Session;
use App\Doctypes;
use App\Templates;
use App\Docs;
use Illuminate\Http\Request;

class MainController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		// Create Docs Object
		$docs_obj = new Docs;

		// Get Statuses
        $statuses = $docs_obj->getStatuses();

		// Get Doctypes
        $doctypes = Doctypes::where('enable', '1')->get();

        $days = 7;
		if(Session::has('settings_docs_main_period'))
		{
			$days = Session::get('settings_docs_main_period');
		}

		if(count($doctypes))
		{
			$ids = [];
			foreach($doctypes as $doctype)
			{
				// Get Templates
		        $templates[$doctype->id] = Templates::where('doctypes_id', $doctype->id)->get();

		        $last_date = date("Y-m-d", strtotime( '-'.$days.' days' ) );

				// Get Docs
		        $docs[$doctype->id] = Docs::where('doctypes_id', $doctype->id)
		        	->whereDate('created_at', '>=', $last_date)
		        	->where('status_id', '>=', '1')
		        	->orderby('id', 'desc')
		        	->limit(5)
		        	->get();

				if(count($docs[$doctype->id]))
				{
					foreach($docs[$doctype->id]->all() as $doc)
					{
						$ids[] = $doc->id;
				    }
				}
			}

			$doc_values_arr = [];
			if (count($ids))
			{
		        // Get Doc Fields Values
		        $doc_values_arr = $docs_obj->getDocsFieldsValues($ids);
			}
		}

        return view('index')
        		->withTemplates($templates)
        		->withStatuses($statuses)
        		->withDoctypes($doctypes)
        		->withDocs($docs)
        		->with('doc_values_arr', $doc_values_arr);
    }
}
