<?php

namespace App\Http\Middleware;

use App\SecurityKey;
use Closure;

class AccessIfAuthenticated
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
        if($request->header('Authorization') != null) {
            $query = SecurityKey::whereHash($request->header('Authorization'));
            if($query->exists()) {
                if(in_array(
                    $request->route()->getName(),
                    $query->first()->permissions->pluck('action')->toArray()
                )) {
                    return $next($request);
                } else {
                    return response([
                        "success" => false,
                        "message" => "You do not have permission to perform this request."
                    ]);
                }
            }
        } else {
            return response([
                "success" => false,
                "message" => "Security Key (API Key) is invalid."
            ]);
        }
    }
}
