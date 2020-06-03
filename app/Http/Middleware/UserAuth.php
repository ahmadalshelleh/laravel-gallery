<?php

namespace App\Http\Middleware;

use Closure;
use App\User;

class UserAuth
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
        $token = $request->header('user_auth');
        $checker = User::findOrFail($request['user_id']);
        if($token == $checker->auth_key){
            return $next($request);
        }
        return response()->json(['message'=>'unauthorized'], 401);
    }
}
