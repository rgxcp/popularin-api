<?php

namespace App\Http\Middleware;

use Closure;
use App\Developer;

class IsDeveloper
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
        $isDeveloper = Developer::where('api_token', $request->header('api_token'))->exists();

        if (!$isDeveloper) {
            return response()->json([
                'status' => 919,
                'message' => 'Unauthenticated Developer',
                'problem' => 'You must sign up or sign in as developer to use all the feature of this API'
            ]);
        }
        
        return $next($request);
    }
}
