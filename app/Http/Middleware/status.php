<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class status
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): JsonResponse
    { $user=DB::table('users')->where('email',$request['email'])->first();
       
        if($user!==null){
       if( $user->status =='active')
        return $next($request);}

        $auth=auth()->user();
        if($auth!==null){
       if( $auth->status =='active')
        return $next($request);}

        return response()->json(['message' => 'Your account is inactive or banned.'], 403); 
       }

    }
