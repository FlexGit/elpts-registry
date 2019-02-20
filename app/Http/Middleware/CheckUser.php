<?php

namespace App\Http\Middleware;

use Session;
use App\Users;
use Closure;

class CheckUser
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
        if(session('elpts_registry_user_ogrn') && session('elpts_registry_user_snils'))
        {
	        $users = Users::where([
	        	['ogrn', session('elpts_registry_user_ogrn')],
	        	['snils', session('elpts_registry_user_snils')],
	        	['enable', '1'],
	        ])->limit(1)->get();

	        if(!count($users))
	        {	        	return redirect('/auth');
	        }

	        /*$hours = 24;
			if(Session::has('settings_check_certificate_period'))
			{
				$hours = Session::get('settings_check_certificate_period');
			}

        	foreach($users as $user)
        	{        		// If Auth was more than 24 hours ago
        		if(strtotime(date('Y-m-d H:i:s')) - strtotime($user->auth_at) > 3600*$hours)
        		{	        		return redirect('/auth');
        		}        	}*/

	        return $next($request);
        }
        else
        {        	return redirect('/auth');
        }
    }
}
