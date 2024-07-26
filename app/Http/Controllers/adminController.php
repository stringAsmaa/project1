<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class adminController extends Controller
{
    
    public function delete_post(Request $request){


        $validator=Validator::make($request->all(),[
            'id'=>['required'],
            ]
            );
            
            if($validator->fails()){
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
    

$delete=DB::table('posts')->where('id',$request['id'])->delete();


if($delete){
    return response()->json([
'message'=>'deleted successfuly'

    ]);
}

else{
    return response()->json([
        'message'=>'fail'
        
            ]);
}


    }


    public function banUser(Request $request, User $user)
    {
        $validator=Validator::make($request->all(),[
            'id'=>['required'],
            ]
            );
            
            if($validator->fails()){
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
         $user=User::where('id',$request['id'])->first();

         $user->status = User::STATUS_BANNED;
         $user->tokens()->delete();
        $user->save();
    
        return response()->json([
            'message'=>'user banned successfuly'
            
                ]);    }



                public function actUser(Request $request, User $user)
                {
                    $validator=Validator::make($request->all(),[
                        'id'=>['required'],
                        ]
                        );
                        
                        if($validator->fails()){
                            return response()->json([
                                'message' => 'Validation failed',
                                'errors' => $validator->errors()
                            ], 422);
                        }
                     $user=User::where('id',$request['id'])->first();
            
                     $user->status = User::STATUS_ACTIVE;
                    $token= $user->createToken('api_token');
                    $user->save();
                
                    return response()->json([
                        'message'=>'user active successfuly',
                        'user'=>$user,
                        'token'=>$token->plainTextToken
                            ]);    }



public function statistics(){


$posts_today=DB::table('posts')->where('created_at','>=',Carbon::now()->subHours(24))->get();

$posts_last_week=DB::table('posts')->where('created_at','>=',Carbon::now()->subWeek())->get();

$posts_updated_today=DB::table('posts')->where('updated_at','>=',Carbon::now()->subHours(24))->get();

$posts_updated_last_week=DB::table('posts')->where('updated_at','>=',Carbon::now()->subWeek())->get();

$Users_banned_today=DB::table('users')->where('status','banned')
->where('updated_at','>=',Carbon::now()->subHours(24))->get();

$Users_banned_last_week=DB::table('users')->where('status','banned')
->where('updated_at','>=',Carbon::now()->subWeek())->get();


return response()->json([
'posts inserted todady:'=>$posts_today,
'posts inserted last week:'=>$posts_last_week,
'posts updated todady:'=>$posts_updated_today,
'posts updated  last week:'=>$posts_updated_last_week,
'users banned  todady:'=>$Users_banned_today,
'users banned  last week:'=>$Users_banned_last_week,

]);

}



public function show_not_accoet_posts(){

return response()->json([

    'not accept posts'=>DB::table('posts')->where('accept','0')->get()
]);

}


public function accept_post(Request $request){

    $validator=Validator::make($request->all(),[
        'id'=>['required'],
        ]
        );
        
        if($validator->fails()){
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

$accept=DB::table('posts')->where('id',$request['id'])->where('accept','1')->first();
         if($accept!==null){

            return response()->json([
                'message'=>'the post has already accepted!'
            ]);
         }

         $find_user=DB::table('posts')->where('id',$request['id'])->first();
         $get_amount=DB::table('wallets')->where('user_id',$find_user->user_id)->first();

if($get_amount->balance<10){
    return response()->json([
        'message'=>'not have enough balance to accept it!'
    ]);
}


        DB::table('posts')->where('id',$request['id'])->update([
            'accept'=>'1'
        ]);
       
        $discount_amount=DB::table('wallets')->where('user_id',$find_user->user_id)->update([

            'balance'=>$get_amount->balance-10
        ]);

        return response()->json([
            'message'=>'success'
        ]);
}

public function show_all_companies(){

return response()->json([
    'companies'=>DB::table('users')->where('role','company')->get()
]);
}

public function login(LoginRequest $request,$role):JsonResponse
   
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



public function insert_balance(Request $request){

    $validator=Validator::make($request->all(),[
        'id'=>['required'],
        'balance' => ['required','min:10','Integer'],
        ]
        );
       
        if($validator->fails()){
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        } 

$user=DB::table('users')->where('id',$request->id)->where('role','company')
->first();

if($user!==null){
    //في راتب مسبقا ف نحدث له الراتب الجديد
$company_insert_balance=DB::table('wallets')
->where('user_id',$request->id)->first();


if($company_insert_balance!==null){

    DB::table('wallets')->where('user_id',$request->id)->update([
        'balance'=>$request['balance']+ $company_insert_balance->balance,
        'updated_at'=>now(),

    ]);
    return response()->json([
        'message' => 'balance updated successfully',
        
    ]);
    }
// في حال ما كان في راتب ف رح نضيفله راتب لاول مرة
    DB::table('wallets')->insert([
        'user_id'=>$request->id,
        'balance'=>$request['balance'],
        'created_at'=>now(),
        'updated_at'=>now(),

    ]);
    return response()->json([
        'message' => 'balance inserted successfully',
        
    ]);

}

return response()->json([
    'message' => 'company not found!',
    
]);


}









}
