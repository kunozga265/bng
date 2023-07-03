<?php

namespace App\Http\Middleware;

use App\Http\Controllers\AppController;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $actions=$request->route()->getAction();
        $roles=isset($actions['roles'])?$actions['roles']:null;

        $user=(new AppController())->getAuthUser($request);

        if (is_object($user)) {
            if ($user->hasAnyRole($roles) || !$roles) {
                return $next($request);
            }
        }

        if ((new AppController())->isApi($request))
            //API Response
            return response()->json(['message' => 'Unauthorized. Does not have access rights.'], 403);
        else{
            //Web Response
            return Redirect::route('dashboard')->with('error','Unauthorized. You do not have access rights.');
        }
    }
}
