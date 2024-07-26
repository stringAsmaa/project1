<?php

namespace App\Http\Controllers;

use App\Models\socialite as ModelsSocialite;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Laravel\Sanctum\PersonalAccessToken;
use Spatie\Permission\Traits\HasRoles;

class SocialiteController extends Controller
{



    public function redirect(){

        return Socialite::driver('google')->stateless()->redirect();
        
    }

public function callBack(){

     $User = Socialite::driver('google')->stateless()->user();
      $email=$User->email;
$exist_email=User::where('email',$email)->first();

//في حال كان الايميل موجود بس  نحدث التوكين
    if($exist_email !==null){
       if($exist_email->status == 'banned'){
        return response()->json(
            ['message' => 'Your account is inactive or banned.'], 403); 

       }
        Auth::login($exist_email);
        $exist_email->tokens()->delete();
    return response()->json([
        'message' => 'Login successful',
    'token'=>$exist_email->createToken('api_token')->plainTextToken
]);
    }
        
    $new_user = User::updateOrCreate([
        'id' => $User->id,
        'name' => $User->name,
        'email' => $User->email,
        'email_verified_at'=>now(),
       'avatar'=>$User->avatar,
       'nickname' => $User->nickname,

    ]);

    Auth::login($new_user);

    auth()->user()->assignRole('job_seeker');

    $token = $new_user->createToken('api_token');

    return response()->json([
        'message' => 'Login successful',
        
    [ 
       'id' => $User->id,
        'name' => $User->name,
        'email' => $User->email,
       'avatar'=>$User->avatar,
      'role'=>'job seeker',
      'email_verified_at'=>now(),
      'nickname' => $User->nickname,
    ],
        'token'=>$token->plainTextToken
    ], 200);
}


    }










