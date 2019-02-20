<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Validator;
use GuzzleHttp\Client;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // SNILS
		Validator::extend('is_snils', function($attribute, $value, $parameters, $validator)
		{
			$pattern = '/^\d{3}-\d{3}-\d{3}-\d{2}$/';

			if(preg_match($pattern, $value))
                return true;

            return false;
        });

		// INN
		Validator::extend('is_inn', function($attribute, $value, $parameters, $validator) {
			$pattern1 = '/^\d{10}$/';
			$pattern2 = '/^\d{12}$/';

			if(preg_match($pattern1, $value) || preg_match($pattern2, $value))
                return true;

            return false;
        });

		// OGRN
		Validator::extend('is_ogrn', function($attribute, $value, $parameters, $validator) {
			$pattern1 = '/^\d{13}$/';
			$pattern2 = '/^\d{15}$/';

			if(preg_match($pattern1, $value) || preg_match($pattern2, $value))
                return true;

            return false;
        });

		// KPP
		Validator::extend('is_kpp', function($attribute, $value, $parameters, $validator) {
			$pattern = '/^\d{9}$/';

			if(preg_match($pattern, $value))
                return true;

            return false;
        });

        // Email Confirmation Code
    	Validator::extend('email_confirm_code', function($attribute, $value, $parameters, $validator)
		{
			$inputs = $validator->getData();

			$query = DB::table('elpts_email_confirmation')
						->where([
							['code', '=', $value],
							['email', '=', $inputs['doc_field20']],
						]);

			return $query->exists();
        });

		// Phone
		Validator::extend('is_phone', function($attribute, $value, $parameters, $validator) {
			$pattern = '/^8-\d{3}-\d{3}-\d{2}-\d{2}$/';

			if(preg_match($pattern, $value))
                return true;

            return false;
        });

        // Check if OKVED exists in Document Template
    	Validator::extend('is_okved_exists', function($attribute, $value, $parameters, $validator)
		{
			$inputs = $validator->getData();

			$rows = DB::table('elpts_templates_fields_values')
				->where([
					['fields_id', '=', '7'],
					['templates_id', '=', $inputs['templates_id']],
				])
				->get();

			$template_values_arr = [];
			if (count($rows) > 0)
			{
	        	foreach ($rows->all() as $row)
	        	{
	       			$template_values_arr = explode(';', $row->value);
	        	}
	        }

			$values_arr = explode(';', $value);
			if (count($values_arr) > 0)
			{
				foreach ($values_arr as $v)
				{
			        if(in_array($v, $template_values_arr))
			        	return true;
				}
			}

			return false;
        });

        // Check if OGRN/OGRNIP not exists in Contract Offers list in status 'For approval' or 'Coherently'
    	Validator::extend('is_ogrn_not_exists_contract_offer', function($attribute, $value, $parameters, $validator)
		{
			$status_id_arr = ['1', '3'];

			$rows = DB::table('elpts_docs_fields_values')
				->join('elpts_docs', 'elpts_docs_fields_values.docs_id', '=', 'elpts_docs.id')
				->where([
					['elpts_docs_fields_values.fields_id', '=', '5'],
					['elpts_docs_fields_values.value', '=', $value],
				])
				->whereIn('elpts_docs.status_id', $status_id_arr)
				->get();

			if (count($rows) > 0)
	        	return false;

			return true;
        });

        // Check if OGRN/OGRNIP not exists in Admin Rights Request list in status 'Coherently'
    	Validator::extend('is_ogrn_not_exists_rights_request', function($attribute, $value, $parameters, $validator)
		{
			$status_id_arr = ['3'];

			$rows = DB::table('elpts_docs_fields_values')
				->join('elpts_docs', 'elpts_docs_fields_values.docs_id', '=', 'elpts_docs.id')
				->where([
					['elpts_docs_fields_values.fields_id', '=', '34'],
					['elpts_docs_fields_values.value', '=', $value],
				])
				->whereIn('elpts_docs.status_id', $status_id_arr)
				->get();

			if (count($rows) > 0)
	        	return false;

			return true;
        });

        // Check if OGRN/OGRNIP not exists in Accept Request list in status 'Coherently'
    	Validator::extend('is_ogrn_not_exists_accept_request', function($attribute, $value, $parameters, $validator)
		{
			$status_id_arr = ['3'];

			$rows = DB::table('elpts_docs_fields_values')
				->join('elpts_docs', 'elpts_docs_fields_values.docs_id', '=', 'elpts_docs.id')
				->where([
					['elpts_docs_fields_values.fields_id', '=', '39'],
					['elpts_docs_fields_values.value', '=', $value],
				])
				->whereIn('elpts_docs.status_id', $status_id_arr)
				->get();

			if (count($rows) > 0)
	        	return false;

			return true;
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
