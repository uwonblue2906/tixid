<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class isGuest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()== FALSE){
             return $next($request);
        } else {
            if (Auth::user() ->role == 'admin'){
                //jika role admin, ke admin dashboard
                return redirect()->route('admin.dashboard');
            } else{
                //selain admin ke home
                return  redirect()->route('home');
            }
        }

    }
}
