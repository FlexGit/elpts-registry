<?php

namespace App\Http\Controllers;

use Session;
use App\Users;
use App\Logs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UsersController extends Controller {
	/**
	 * Post Ajax Auth Request
	 */
	public function ajaxAuthRequestPost() {
		Session::flush();
		
		$input = request()->all();
		
		$ogrn = $input['ogrn'];
		$snils = $input['snils'];
		$snils = substr($snils, 0, 3) . '-' . substr($snils, 3, 3) . '-' . substr($snils, 6, 3) . '-' . substr($snils, 9, 2);
		
		$users = Users::where([
			['ogrn', $ogrn],
			['snils', $snils],
			['enable', '1'],
		])
			->orderBy('id')
			->limit(1)
			->get();
		
		if (!count($users)) {
			return response()->json([
				'response' => [
					'error' => 'users_error',
					'msg' => 'Отсутствуют права доступа',
				],
			]);
		}
		
		$auth_at = date('Y-m-d H:i:s');
		
		// Create Users Object
		$users_obj = new Users;
		
		foreach ($users as $user) {
			// Verify Signature by Signal-COM DSS Server
			$response = $users_obj->signatureVerify($input['file'], $input['signature']);
			
			if ($response['error']) {
				// Write Log
				$log = new Logs;
				$log->operation_id = 26;
				$log->user_name = $user->ogrn;
				$log->value = 'Ошибка: Подпись не прошла верификацию DSS-сервером. ' . $response['error'];
				$log->save();
				
				return response()->json([
					'response' => [
						'error' => $response['error'],
						'msg' => 'Подпись не прошла верификацию DSS-сервером.',
					],
				]);
			}
			
			Users::where('id', $user->id)->update(['auth_at' => $auth_at]);
			
			// Write Log
			$log = new Logs;
			$log->operation_id = 22;
			$log->user_name = $user->name;
			$log->save();
			
			session([
				'elpts_registry_user_id' => $user->id,
				'elpts_registry_user_name' => $user->name,
				'elpts_registry_user_ogrn' => $user->ogrn,
				'elpts_registry_user_snils' => $user->snils,
				'elpts_registry_user_is_admin' => $user->admin,
				'elpts_registry_user_auth_at' => $auth_at,
			]);
			
			return response()->json([
				'response' => [
					'msg' => 'success',
				],
			]);
		}
	}
}
