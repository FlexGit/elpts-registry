<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Filesystem\Filesystem;

class removeDocFiles
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
		$file = new Filesystem;

		$file->cleanDirectory(base_path().'/files/docs/'.md5(session('elpts_registry_user_name')));

        return $next($request);
    }
}
