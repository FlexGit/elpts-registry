<?php

namespace App\Http\Middleware;

use Session;
use App\Users;
use Closure;

class checkAdmin
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
	        {
	        	abort(404);
	        }

        	foreach($users as $user)
        	{        		// If User is not Admin
        		if(!$user->admin)
        		{
	        		abort(404);
        		}
        		else
        		{					session(
		        		[
			        		'elpts_registry_user_is_admin' => $user->admin
		        		]
		        	);
	        	}
        	}

	        return $next($request);
        }
        else
        {
        	return redirect('/auth');

        }
    }
}
