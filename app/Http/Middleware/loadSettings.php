<?php

namespace App\Http\Middleware;

use Session;
use App\Settings;
use Closure;

class loadSettings
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
        // Get Settings
		$settings = Settings::get();

        if(count($settings))
        {
        	foreach($settings as $setting)
        	{
		       	session(['settings_'.$setting->name => $setting->value]);
		    }
		}

        return $next($request);
    }
}
