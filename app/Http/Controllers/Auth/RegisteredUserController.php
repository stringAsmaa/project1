<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\verification;
use App\Models\code;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Traits\HasRoles;

class RegisteredUserController extends Controller
{
    /**
     * @throws \Illuminate\Validation\ValidationException
     */



    //     public function store(Request $request,$role):JsonResponse
    //     {

    //         $request->validate([
    //             'name' => ['required', 'string', 'min:3','max:255'],
    //             'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
    //             'password' => ['required', Rules\Password::defaults()],
    //             'number'=>['nullable','digits:10'],
    //            'address'=>['nullable'],

    //         ]);



    // $name=$request['name'];

    // $new_code = new code();


    // $code=$new_code->generate_code();




    // $create_user = User::create([
    //     'name' =>  $request->name,
    //     'email' => $request->email,
    //     'password' => Hash::make($request->password),
    //     'avatar'=>'public/default/default_image.jpg',
    //     'role'=>$role,
    //     'number'=>$request->input('number',null)
    // ]);

    // if($role=='company'){
    //     $create_user->assignRole('company');
    //     $create_user->address=$request['address'];
    //     $create_user->save();
    // }

    // if($role=='job_seeker'){
    //     $create_user->assignRole('job_seeker');
    // }





    // $add_code=DB::table('codes')->insert([
    //     'user_id'=>$create_user->id,
    // 'code'=>$code,
    // 'expiry_at'=>$new_code->expiry_at
    // ]);

    // $expiry_at=$new_code->expiry_at;
    // mail::to($request['email'])->send(new verification($name,$code,$expiry_at));

    // return response()->json([
    //     'message'=>'user add successfully now verify your email'
    // ]);

    //     }


    public function store(Request $request, $role): JsonResponse
    {

        $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', Rules\Password::defaults()],
            'number' => ['nullable', 'digits:10'],
            'address' => ['nullable'],
            'Creation_Date' => ['nullable'],
            'location' => ['nullable'],

        ]);



        $name = $request['name'];

        $new_code = new code();


        $code = $new_code->generate_code();




        $create_user = User::create([
            'name' =>  $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'avatar' => asset('default/default_image.jpg'),
            'role' => $role,
            'number' => $request->input('number', null)
        ]);

        if ($role == 'company') {
            $create_user->assignRole('company');
            $create_user->Creation_Date = $request['Creation_Date'];
            $create_user->location = $request['location'];

            $create_user->save();
        }

        if ($role == 'job_seeker') {
            $create_user->assignRole('job_seeker');
        }





        $add_code = DB::table('codes')->insert([
            'user_id' => $create_user->id,
            'code' => $code,
            'expiry_at' => $new_code->expiry_at
        ]);

        $expiry_at = $new_code->expiry_at;
        mail::to($request['email'])->send(new verification($name, $code, $expiry_at));

        return response()->json([
            'message' => 'user add successfully now verify your email'
        ]);
    }




    public function put_code(Request $request)
    {


        $request->validate([
            'code' => ['required', 'digits:5']
        ]);


        //   الكود منتهي الصلاحية
        if (code::where('expiry_at', '<', now())) {
            DB::table('codes')->where('expiry_at', '<', now())->delete();
        }


        $code = code::where('code', $request['code'])->first();



        //اذا موجود الكود
        if ($code !== null) {
            $user = user::where('id', $code->user_id)->first();

            // اذا المستخدم مأكد حسابه من قبل
            if ($user->email_verified_at != null) {
                return response()->json([

                    'The email has already been verified'
                ]);
            }

            $role = code::where('code', $request['code'])
                ->join('users', 'user_id', 'users.id')->select('role')->first();



            event(new Registered($user));
            Auth::login($user);




            // $token =$user->createToken('api_token')->plainTextToken;
            $user = DB::table('users')->where('id', $code->user_id)->update([
                'email_verified_at' => now()
            ]);
            $user = user::where('id', $code->user_id)->first();
            return response()->json([
                'user' => $user,
                // 'token'=>$token,

            ]);
        }


        //اذا مو موجود الكود
        if (!$code) {
            return response()->json([
                'message' => 'The code is wrong',
            ]);
        }
    }



    public function resend_code4verify(Request $request)
    {


        $request->validate([
            'email' => ['required']
        ]);


        $user = User::where('email', $request['email'])->first();

        if ($user == null) {
            return response()->json([

                'The email is wrong'
            ]);
        }

        if ($user->email_verified_at != null) {

            return response()->json([

                'The email has already been verified'
            ]);
        } else {
            $new_code = new code();
            $code = $new_code->generate_code();

            DB::table('codes')->insert([
                'user_id' => $user->id,
                'code' => $code,
                'expiry_at' => $new_code->expiry_at,
            ]);

            mail::to($user->email)->send(new verification($user->name, $code, $new_code->expiry_at));
        }
    }
}
