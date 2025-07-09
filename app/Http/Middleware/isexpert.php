<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class isexpert
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(Request $request, Closure $next,$role): \Illuminate\Http\JsonResponse
    {
        if (auth()->check()&&Auth::user()->role== 'isexpert')
         return $next($request);
        else{
            return response()->json(['error'=>'you are not allow']);
        }
    }
}
