<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Mail\resetPassword;
use App\Models\code;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;
// use Spatie\Permission\Models\Permission;
// use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request,$role):JsonResponse
   
{

    $request->authenticate();
      $user=$request->user();
  
    //للتحقق من نوع المستخدم لهذا الحساب هل هو مطابق لنوع
    //  المستخدم في عملية تسجيل الدخول
    if($user->role !=$role){
        return response()->json([
            'user'=>null,
            'message'=>'not found',
            
                    ]); 
    }
    $user->tokens()->delete();
$token = $user->createToken('api_token')->plainTextToken;

   
    return response()->json([
        'user'=>$user,
        'token'=>$token,
        
                ]);

}


public function forgot_password(Request $request):JsonResponse{
    $validator = Validator::make($request->all(), [
        'email' => ['required'],
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 422);
    }



$user=User::where('email',$request['email'])->first();


if($user){
$call = new code();
$code=$call->generate_code();

DB::table('codes')->insert([
'user_id'=>$user->id,
    'code'=>$code,
    'expiry_at'=>$call->expiry_at
]);


mail::to($request['email'])->send(new resetPassword($user->name,$code,$call->expiry_at));

// $token =$user->createToken('api_token')->plainTextToken;
return response()->json([
    'message'=>'success'
        ]);



}
else{
    return response()->json([
'mesaage'=>'email is wrong'
    ]);
}

}

public function code_4_password(Request $request):JsonResponse{
    $validator = Validator::make($request->all(), [
        'code' => ['required','digits:5'],
       
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 422);
    }



DB::table('codes')->where('expiry_at', '<', now())->delete();
$code=DB::table('codes')->where('code',$request['code'])->first();
// $user=DB::table('users')->where('id',auth()->user()->id)->first();

if($code!==null){
    return response()->json([
        'mesaage'=>'success now you can reset password'
            ]);

}



if(!$code){
    return response()->json([
        'mesaage'=>'code is wrong'
            ]);  

}
}





public function reset_password(Request $request):JsonResponse{
    $validator = Validator::make($request->all(), [
'email'=>['required'],
        'password' => ['required', 'confirmed', Rules\Password::defaults()]
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 422);
    }

    DB::table('users')->where('email',$request->email)->update([
        'password' => Hash::make($request->password)
    ]);

    return response()->json([
        'message' => 'Password reset successful'
    ]);
}



public function resend_code4password(Request $request){
    $request->validate([
        'email'=>['required']
    ]);


        $user=User::where('email',$request['email'])->first();

       if($user==null){
        return response()->json([
            'message'=>'the email is wrong!'
        ]);
       }

       if($user->email_verified_at !=null){

        return response()->json([
    
        'The email has already been verified'
        ]);
    }


    $call = new code();
$code=$call->generate_code();

DB::table('codes')->insert([
    'user_id'=>$user->id,
    'code'=>$code,
    'expiry_at'=>$call->expiry_at,
    ]);

mail::to($user->email)->send(new resetPassword($user->name,$code,$call->expiry_at));

}




    public function destroy(Request $request):JsonResponse
     { 

          //حالة البرييز
       
             Auth::user()->currentAccessToken()->delete();
        return response()->json([
          
                'message' => 'log out successfully'
            ]
        );

          
        //حالة التوكين غلط
            return response()->json([
                'message' => 'invalid token']);
            
      }}
       


     

