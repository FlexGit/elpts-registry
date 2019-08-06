<?php

namespace App\Http\Controllers;

use App\Countries;
use App\Docs;
use App\Doctypes;
use App\Export;
use App\Logs;
use App\Okopfs;
use App\Prefixes;
use App\Settings;
use App\Templates;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Log;
use Maatwebsite\Excel\Facades\Excel;
use Mail;
use Session;

class DocsController extends Controller {
	/**
	 * Display a listing of the resource.
	 *
	 * @param  int $doctypes_id
	 * @param  \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function index($doctypes_id, Request $request) {
		// Get Templates
		$templates = Templates::where('doctypes_id', $doctypes_id)->get();
		
		$docs_quantity = 30;
		if (Session::has('settings_registry_docs_quantity')) {
			$docs_quantity = Session::get('settings_registry_docs_quantity');
		}
		
		$filtered = 0;
		
		// Get Docs_id's
		$result = DB::table('elpts_docs_fields_values')->select('docs_id')->distinct();
		if (isset($request->filter_ogrn)) {
			$filtered = 1;
			$result->where([
				['value', '=', $request->filter_ogrn],
				['fields_id', '=', '5'],
			]);
		}
		$filter_inn = $request->filter_inn;
		if (isset($filter_inn)) {
			if (strlen($filter_inn) == 10) {
				$filter_inn = '00' . $filter_inn;
			}
			$filtered = 1;
			$result->where([
				['value', '=', $filter_inn],
				['fields_id', '=', '4'],
			]);
		}
		if (isset($request->filter_orgname)) {
			$filtered = 1;
			$result->where([
				['value', '=', $request->filter_orgname],
				['fields_id', '=', '41'],
			]);
		}
		$docs_ids = $result->get();
		
		$ids = [];
		if (count($docs_ids)) {
			foreach ($docs_ids->all() as $docs_id) {
				$ids[] = $docs_id->docs_id;
			}
		}
		
		// Get Docs
		$result = DB::table('elpts_docs')->where('doctypes_id', $doctypes_id);
		
		if (count($ids)) {
			$result->whereIn('id', $ids);
		}
		else {
			if ($filtered) {
				$result->where('id', '=', '-1');
			}
		}
		
		if (isset($request->filter_prefix)) {
			$result->where('prefix_id', '=', $request->filter_prefix);
		}
		if (isset($request->filter_status)) {
			if ($request->filter_status) {
				$result->where('status_id', '=', $request->filter_status);
			}
			else {
				$result->where('status_id', '>', $request->filter_status);
			}
		}
		if (isset($request->filter_date_from)) {
			$result->where('created_at', '>=', date('Y-m-d H:i:s', strtotime($request->filter_date_from)));
		}
		if (isset($request->filter_date_to)) {
			$result->where('created_at', '<=', date('Y-m-d H:i:s', strtotime($request->filter_date_to)));
		}
		// By Default Status = '1'
		if (!isset($request->filter_prefix) && !isset($request->filter_status) && !isset($request->filter_ogrn) && !isset($filter_inn) && !isset($request->filter_orgname) && !isset($request->filter_date_from) && !isset($request->filter_date_to)) {
			$result->where('status_id', '=', '1');
		}
		$result->orderby('id', 'asc');
		if ($request->method != 'export') {
			$docs = $result->paginate($docs_quantity);
		}
		else {
			$docs = $result->get();
		}
		
		// Create Docs Object
		$docs_obj = new Docs;
		
		// Prepare Doc IDs to get fields values
		$ids = [];
		if (count($docs)) {
			foreach ($docs->all() as $doc) {
				$ids[] = $doc->id;
			}
		}
		
		// Get Doc Fields
		$docs_fields = $docs_obj->getDocsFields($doctypes_id);
		
		// Get Doc Fields Values
		$doc_values_arr = $docs_obj->getDocsFieldsValues($ids);
		
		// Get Statuses
		$statuses = $docs_obj->getStatuses($doctypes_id);
		
		// Get Countries
		$countries = Countries::all();
		
		// Get Okopfs
		$okopfs = Okopfs::all();
		
		// Get Pays
		$pays = $docs_obj->getPays();
		
		// Get Junks
		$junks = $docs_obj->getJunks();
		
		// Get Owners
		$owners = $docs_obj->getOwners();
		
		// Get Prefixes
		$prefixes = Prefixes::where([
			['enable', '=', '1'],
			['doctypes_id', '=', $doctypes_id],
		])
			->get();
		
		// Get Doctypes
		$doctypes = Doctypes::get();
		$doctype = $doctypes->first(function ($item) use ($doctypes_id) {
			return $item->id == $doctypes_id;
		});
		
		if (empty($docs) || empty($doctype)) {
			abort(404);
		}
		
		// Export Data to Xlsx
		if ($request->method == 'export') {
			$i = 0;
			$data = [];
			if (count($docs)) {
				foreach ($docs->all() as $doc) {
					$data[ $i ] = [];
					$data[ $i ]['№ п/п'] = $i + 1;
					$data[ $i ]['Номер'] = (string)$doc->prefix_number;
					
					if (count($statuses) > 0) {
						foreach ($statuses->all() as $status) {
							if ($status->id == $doc->status_id) {
								$data[ $i ]['Статус'] = (string)$status->name;
								break;
							}
						}
					}
					
					if (count($docs_fields)) {
						foreach ($docs_fields->all() as $fields) {
							if (!in_array($fields->type, ['input', 'checkbox', 'textarea', 'select']) || in_array($fields->valid_rules, ['email_confirm_code']))
								continue;
							
							if (empty($doc_values_arr[ $doc->id ][ $fields->id ]['value']))
								$doc_values_arr[ $doc->id ][ $fields->id ]['value'] = '';
							
							if ($fields->link == 'countries') // Countries
							{
								if (count($countries)) {
									foreach ($countries->all() as $country) {
										if ($country->id == $doc_values_arr[ $doc->id ][ $fields->id ]['value']) {
											$doc_values_arr[ $doc->id ][ $fields->id ]['value'] = $country->name;
										}
									}
								}
							}
							
							if ($fields->link == 'okopfs') // Okopfs
							{
								if (count($okopfs)) {
									foreach ($okopfs->all() as $okopf) {
										if ($okopf->id == $doc_values_arr[ $doc->id ][ $fields->id ]['value']) {
											$doc_values_arr[ $doc->id ][ $fields->id ]['value'] = $okopf->name;
										}
									}
								}
							}
							
							if ($fields->link == 'pays') // Pays
							{
								if (count($pays)) {
									foreach ($pays->all() as $pay) {
										if ($pay->id == $doc_values_arr[ $doc->id ][ $fields->id ]['value']) {
											$doc_values_arr[ $doc->id ][ $fields->id ]['value'] = $pay->name;
										}
									}
								}
							}
							
							if ($fields->link == 'junks') // Junks
							{
								if (count($junks)) {
									foreach ($junks->all() as $junk) {
										if ($junk->id == $doc_values_arr[ $doc->id ][ $fields->id ]['value']) {
											$doc_values_arr[ $doc->id ][ $fields->id ]['value'] = $junk->name;
										}
									}
								}
							}
							
							if ($fields->link == 'owners') // Owners
							{
								if (count($owners)) {
									foreach ($owners->all() as $owner) {
										if ($owner->id == $doc_values_arr[ $doc->id ][ $fields->id ]['value']) {
											$doc_values_arr[ $doc->id ][ $fields->id ]['value'] = $owner->name;
										}
									}
								}
							}
							
							$value = (string)$doc_values_arr[ $doc->id ][ $fields->id ]['value'];
							if ($fields->id == '4' && mb_substr($value, 0, 2) == '00') {
								$value = mb_substr($value, 2);
							}
							$data[ $i ][ (string)$fields->name ] = $value;
						}
					}
					
					$data[ $i ]['Комментарий'] = (string)$doc->comment;
					$data[ $i ]['Дата создания'] = $doc->created_at;
					$i++;
				}
			}
			
			$filename = 'Реестр документов «' . $doctype->name . '» от ' . date('Y-m-d H-i-s') . '.xlsx';
			
			return Excel::download(new Export($data), $filename);
		}
		
		$page = $request->page;
		if (!isset($request->page)) $page = 1;
		
		return view('docs.index')
			->withTemplates($templates)
			->withDocs($docs)
			->withDoctypes($doctypes)
			->withStatuses($statuses)
			->withPrefixes($prefixes)
			->withRequest($request)
			->with('doctypes_id', $doctypes_id)
			->with('docs_quantity', $docs_quantity)
			->with('page', $page)
			->with('doctype', $doctype)
			->with('doc_values_arr', $doc_values_arr);
	}
	
	/**
	 * Display the specified resource.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  int $doctypes_id
	 * @param  int $id
	 * @return \Illuminate\Http\Response
	 */
	public function show(Request $request, $doctypes_id, $id) {
		// Get Docs
		$docs = Docs::where('id', $id)->get()->toArray();
		
		// Get Templates
		$templates = Templates::where('doctypes_id', $doctypes_id)->get()->toArray();
		
		// Get Doctypes
		$doctypes = Doctypes::get();
		$doctype = $doctypes->first(function ($item) use ($doctypes_id) {
			return $item->id == $doctypes_id;
		});
		
		if (empty($docs[0]) || empty($doctype)) {
			abort(404);
		}
		
		// Get Docs Object
		$docs_obj = new Docs;
		
		// Get Settings Object
		$settings_obj = new Settings;
		
		// Get Statuses
		$statuses = $docs_obj->getStatuses();
		
		// Get Pays
		$pays = $docs_obj->getPays();
		
		// Get Junks
		$junks = $docs_obj->getJunks();
		
		// Get Owners
		$owners = $docs_obj->getOwners();
		
		// Get Docs Fields Statuses
		$fields_statuses = $docs_obj->getFieldsStatuses();
		
		// Get Countries
		$countries = Countries::all();
		
		// Get Okopfs
		$okopfs = Okopfs::all();
		
		// Get Only Doc User Fields
		$doc_fields = $docs_obj->getDocsFields($doctypes_id, true);
		
		// Get Doc Fields Values
		$doc_values_arr = $docs_obj->getDocsFieldsValues([$id]);
		
		// Get Other Docs With The Same OGRN/OGRNIP
		$prev_docs = $docs_obj->getPrevDocsByOgrn($id, $doc_values_arr[ $id ]['5']['value']);
		
		$rights = session('template_user_roles');
		
		// Get Docs Fields Roles Rights
		$docs_fields_roles_rights = [];
		if (!empty($rights)) {
			$docs_fields_roles_rights = $settings_obj->getDocsFieldsRolesRights($rights);
		}
		
		return view('docs.show')
			->withDocs($docs[0])
			->withStatuses($statuses)
			->withTemplates($templates)
			->withDoctypes($doctypes)
			->withPays($pays)
			->withJunks($junks)
			->withOwners($owners)
			->withFieldsstatuses($fields_statuses)
			->withCountries($countries)
			->withOkopfs($okopfs)
			->with('doctypes_id', $doctypes_id)
			->with('doc_fields', $doc_fields)
			->with('doc_values_arr', $doc_values_arr)
			->with('prev_docs', $prev_docs)
			->with('doctype', $doctype)
			->with('rights', $rights)
			->with('docs_fields_roles_rights', $docs_fields_roles_rights)
			->with('id', $id)
			->with('ogrn', $doc_values_arr[ $id ]['5']['value'])
			->withRequest($request);
	}
	
	/**
	 * Display the specified resource.
	 *
	 * @param  int $doctypes_id
	 * @param  \Illuminate\Http\Request $request
	 * @param  int $id
	 * @return \Illuminate\Http\Response
	 */
	public function update($doctypes_id, Request $request, $id) {
		// Get Doc
		$doc = Docs::findOrFail($id);
		$templates_id = $doc->templates_id;
		
		$doc->comment = $request->comment;
		
		// Save Button
		if ($request->status_id == 'save') {
			$operation_id = 1;
		}
		else {
			$doc->status_id = $request->status_id;
			$operation_id = $request->status_id;
			// Exception
			if ($operation_id == 1) $operation_id = 2;
		}
		
		// Get Docs Object
		$docs_obj = new Docs;
		
		// Get Doc Fields
		$docs_fields = $docs_obj->getDocsFields($doctypes_id);
		
		// Get Doc Fields Values
		$doc_values_arr = $docs_obj->getDocsFieldsValues([$id]);
		
		// Update Docs Fields Statuses
		$docs_obj->updateDocFieldsStatuses($id, $docs_fields, $request);
		
		// Save Doc
		$doc->save();
		
		// Write Log
		$log = new Logs;
		$log->operation_id = $operation_id;
		$log->doc_id = $id;
		$log->user_name = session('elpts_registry_user_name');
		$log->save();
		
		// Get Templates Object
		$template_fields_obj = new Templates;
		
		// Get Template Fields Values
		$template_values_arr = $template_fields_obj->getTemplateFieldsValues($templates_id);
		
		// Send E-mail With Accepted Answer
		if ($request->status_id == 3 && !empty($template_values_arr['4']['value'])) {
			// Replace Vars with Data
			$template_values_arr['4']['value'] = str_replace('%NUMBER%', $doc->prefix_number, $template_values_arr['4']['value']);
			if (!empty($doc_values_arr[$id]['41']['value'])) {
				$template_values_arr['4']['value'] = str_replace('%ORG_NAME%', $doc_values_arr[$id]['41']['value'], $template_values_arr['4']['value']);
			}
			if (!empty($doc_values_arr[$id]['4']['value'])) {
				$template_values_arr['4']['value'] = str_replace('%INN%', $doc_values_arr[$id]['4']['value'], $template_values_arr['4']['value']);
			}
			if (!empty($doc_values_arr[$id]['5']['value'])) {
				$template_values_arr['4']['value'] = str_replace('%OGRN%', $doc_values_arr[$id]['5']['value'], $template_values_arr['4']['value']);
			}
			if (!empty($doc_values_arr[$id]['36']['value'])) {
				$template_values_arr['4']['value'] = str_replace('%SNILS%', $doc_values_arr[$id]['36']['value'], $template_values_arr['4']['value']);
			}
			$template_values_arr['4']['value'] = str_replace('%DATE%', date('d.m.Y'), $template_values_arr['4']['value']);
			
			Mail::send('emails.email_accepted_doc', ['body' => $template_values_arr['4']['value']], function ($message) use ($doc_values_arr, $id) {
				$message->to($doc_values_arr[ $id ]['20']['value'])->subject('Уведомление о положительном результате рассмотрения акцепта документа');
			});
			
			if (count(Mail::failures()) > 0) {
				$i = 0;
				foreach (Mail::failures as $failure) {
					$response['error_code'] = [
						$i => $failure,
					];
					$i++;
				}
			}
			
			// Write Log
			$log = new Logs;
			$log->operation_id = 31;
			$log->doc_id = $id;
			$log->user_name = session('elpts_registry_user_name');
			if (!empty($response['error_code']))
				$log->value = json_encode($response['error_code']);
			else
				$log->value = 'Да';
			$log->save();
		}
		
		// Send E-mail With Rejected Answer
		if ($request->status_id == 4 && !empty($template_values_arr['5']['value'])) {
			// Replace Vars with Data
			$template_values_arr['5']['value'] = str_replace('%NUMBER%', $doc->prefix_number, $template_values_arr['5']['value']);
			if (!empty($doc_values_arr[$id]['41']['value'])) {
				$template_values_arr['5']['value'] = str_replace('%ORG_NAME%', $doc_values_arr[$id]['41']['value'], $template_values_arr['5']['value']);
			}
			if (!empty($doc_values_arr[$id]['4']['value'])) {
				$template_values_arr['5']['value'] = str_replace('%INN%', $doc_values_arr[$id]['4']['value'], $template_values_arr['5']['value']);
			}
			if (!empty($doc_values_arr[$id]['5']['value'])) {
				$template_values_arr['5']['value'] = str_replace('%OGRN%', $doc_values_arr[$id]['5']['value'], $template_values_arr['5']['value']);
			}
			if (!empty($doc_values_arr[$id]['36']['value'])) {
				$template_values_arr['5']['value'] = str_replace('%SNILS%', $doc_values_arr[$id]['36']['value'], $template_values_arr['5']['value']);
			}
			$template_values_arr['5']['value'] = str_replace('%DATE%', date('d.m.Y'), $template_values_arr['5']['value']);
			
			Mail::send('emails.email_rejected_doc', ['body' => $template_values_arr['5']['value'], 'rejected_reason' => $request->rejected_reason], function ($message) use ($doc_values_arr, $id) {
				$message->to($doc_values_arr[ $id ]['20']['value'])->subject('Уведомление об отрицательном результате рассмотрения акцепта документа');
			});
			
			if (count(Mail::failures()) > 0) {
				$i = 0;
				foreach (Mail::failures as $failure) {
					$response['error_code'] = [
						$i => $failure,
					];
					$i++;
				}
			}
			
			// Write Log
			$log = new Logs;
			$log->operation_id = 32;
			$log->doc_id = $id;
			$log->user_name = session('elpts_registry_user_name');
			if (!empty($response['error_code']))
				$log->value = json_encode($response['error_code']);
			else
				$log->value = 'Да';
			$log->save();
		}
		
		return redirect('/docs/' . $doctypes_id . '?page=' . $request->page . '&filter_prefix=' . $request->filter_prefix . '&filter_status=' . $request->filter_status . '&filter_ogrn=' . $request->filter_ogrn . '&filter_inn=' . $request->filter_inn . '&filter_orgname=' . $request->filter_orgname . '&filter_date_from=' . $request->filter_date_from . '&filter_date_to=' . $request->filter_date_to)
			->with('success', 'Документ успешно сохранен!');
	}
	
	/**
	 * Download a file from DB.
	 *
	 * @param  int $doc_id
	 * @param  string $file
	 * @return \Illuminate\Http\Response
	 */
	public function file($doc_id, $file) {
		if (intval($file)) // User's File
		{
			// Get Docs Object
			$docs_obj = new Docs;
			
			// Get Doc Fields Values
			$doc_values_arr = $docs_obj->getDocsFieldsValues([$doc_id]);
			
			$content = $doc_values_arr[ $doc_id ][ intval($file) ]['value'];
			$ext = 'pdf';
		}
		else // Generated File
		{
			// Get Docs
			$doc = Docs::findOrFail($doc_id);
			
			switch ($file) {
				case 'xml':
					$content = $doc->file;
					$ext = 'xml';
				break;
				case 'xmlbase64':
					$content = $doc->file_base64;
					$ext = 'base64';
				break;
				case 'signature':
					$content = $doc->file_sign;
					$ext = 'sig';
				break;
			}
		}
		
		$fileName = md5(time() . $doc_id . $file) . '_base64.' . $ext;
		$filePath = base_path() . '/files/docs/' . $fileName;
		file_put_contents($filePath, $content);
		
		$content = file_get_contents($filePath);
		file_put_contents($filePath, base64_decode($content));
		
		$headers = [
			'Content-Description: File Transfer',
			'Content-Type: application/octet-stream',
			'Content-Disposition: attachment; filename="' . $fileName . '"',
		];
		
		return response()->download($filePath, $fileName, $headers);
	}
}
