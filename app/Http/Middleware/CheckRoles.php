<?php

namespace App\Http\Middleware;

use Session;
use App\Docs;
use App\Users;
use Closure;

class checkRoles
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
		if(Session::has('template_user_roles'))
		{
			Session::forget('template_user_roles');
		}

		// Create User Object
		$user_obj = new Users;

		// Get Doc
        $docs = Docs::where('id', $request->id)->get()->toArray();

		if (empty($docs[0]['templates_id']))
	        return $next($request);

		// Get Template User Roles
		$templateUserRoles = $user_obj->getTemplateUserRoles($docs[0]['templates_id'], session('elpts_registry_user_id'));

		if(!empty($templateUserRoles[session('elpts_registry_user_id')][$docs[0]['templates_id']]))
		{
	        if (count($templateUserRoles[session('elpts_registry_user_id')][$docs[0]['templates_id']]) > 0)
	        {
	        	session([
	        		'template_user_roles' => $templateUserRoles[session('elpts_registry_user_id')][$docs[0]['templates_id']]
	        	]);
	        }
	    }

        return $next($request);
    }
}
