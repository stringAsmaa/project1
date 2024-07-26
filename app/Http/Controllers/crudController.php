<?php
namespace App\Http\Controllers;

use App\Mail\sendCv;
use App\Mail\suggested;
use App\Models\cv;
use App\Models\experience;
use App\Models\location;
use App\Models\post;
use App\Models\salary;
use App\Models\savepost;
use App\Models\search;
use App\Models\skill;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class crudController extends Controller
{
    

public function create_post(Request $request){


$validator=Validator::make($request->all(),[
    'company_name'=>['required'],
    'description'=>['required'],
     'title'=>['required'],
     'schedule'=>['required'],
     'location'=>['required'],
     'type_job'=>['required'],
     'salary'=>['required'],
     'image'=>['required','mimes:png,jpg,jpeg,bmp,sav'],

]
);
$not_have_enough_balance=DB::table('wallets')->where('user_id',auth()->user()->id)->where('balance','<',10)->first();

if($not_have_enough_balance){
    return response()->json([
        'message'=>'you not have enough balance!'
    ]);
}

if($validator->fails()){
    return response()->json([
        'message' => 'Validation failed',
        'errors' => $validator->errors()
    ], 422);
}



if($request['image']!=null){

    $image=$request->file('image');
    $fileName=null;
    if($request->hasFile('image')){
    
        $fileName = time() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('post_image'), $fileName);
        $fileName='post_image/'.$fileName;
       
    }}



$create=DB::table('posts')->insert([
'user_id'=>auth()->user()->id,
'description'=>$request['description'],
'title'=>$request['title'],
'schedule'=>$request['schedule'],
'location'=>$request['location'],
'type_job'=>$request['type_job'],
'salary'=>$request['salary'],
'company_name'=>$request['company_name'],
'image'=>asset($fileName),
'created_at'=>now(),
'updated_at'=>now()

]);




if($create){
    return response()->json([
'message'=>'ok, wait for accept it',
'post'=> DB::table('posts')->where('created_at',now())->get()
    ]);
}

else{
    return response()->json([
        'message'=>'fail'
        
            ]);
}



}



public function edit_post(Request $request){


    $validator=Validator::make($request->all(),[
        'id'=>['required'],
        'company_name'=>['nullable'],
        'description'=>['nullable'],
         'title'=>['nullable'],
         'schedule'=>['nullable'],
         'location'=>['nullable'],
         'type_job'=>['nullable'],
         'salary'=>['nullable'],
         'image'=>['nullable','mimes:png,jpg,jpeg,bmp,sav'],

        ]
        );
        
        if($validator->fails()){
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }


//to find the post requirment 
$post=post::find($request['id']);


if($post->accept=='1' && $post->user_id==auth()->user()->id){


$post->description = $request->input('description', $post->description);
$post->title = $request->input('title', $post->title);
$post->schedule = $request->input('schedule', $post->schedule);
$post->location = $request->input('location', $post->location);
$post->type_job = $request->input('type_job', $post->type_job);
$post->company_name = $request->input('company_name', $post->company_name);
$post->salary = $request->input('salary', $post->salary);
$post->updated_at = now();



if($request['image']!=null){

    $image=$request->file('image');
    $fileName=null;
    if($request->hasFile('image')){
    
        $fileName = time() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('post_image'), $fileName);
        $fileName='post_image/'.$fileName;
       $post->image=$fileName;
      
    }}

    $post->save();

return response()->json([
    'message'=>'edited successfuly',
    'post'=>DB::table('posts')->where('updated_at',now())->first()
    
        ]);

}

    return response()->json([
        'message'=>'fail'
        
            ]);


}



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

$delete=DB::table('posts')->where('user_id',auth()->user()->id)
->where('accept','1')->where('id',$request['id'])->delete();


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


public function search(Request $request){
    $validator=Validator::make($request->all(),[
        'search'=>['required'],
        
        ]
        );
        
        if($validator->fails()){
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }


$compare_job='%'.$request['search'].'%';
$compare_company='%'.$request['search'].'%';



$search_job=DB::table('posts')->where('accept','1')->where(function ($query) use ($compare_job){
$query->where('title','LIKE',$compare_job);
// ->orWhere('schedule','LIKE',$compare_job)
// ->orWhere('location','LIKE',$compare_job)
// ->orWhere('type_job','LIKE',$compare_job)
// ->orWhere('company_name','LIKE',$compare_job);
})->get();


$search_company= DB::table('users')
->where('role', 'company')
->where(function ($query) use ($compare_company) {
    $query->where('location', 'LIKE', $compare_company)
        ->orWhere('name', 'LIKE', $compare_company)
         ->orWhere('Creation_Date', 'LIKE', $compare_company)
          ->orWhere('schedule', 'LIKE', $compare_company);
})->get();


if(count($search_job) > 0 || count($search_company) > 0){
    return response()->json([
'posts'=>$search_job,
'companies'=>$search_company


    ]);
}
else{
    return response()->json([
        'message'=>'fail'
        
            ]);
}
}





public function show_posts(){

$show=DB::table('posts')->where('accept','1')->get();

return response()->json([
    'posts'=>$show]);

}

//عرض بوستات الشركة المطلوبة
public function show_my_posts(Request $request){

    $validator=Validator::make($request->all(),[
       
        'id'=>['required']
        ]
        );
        
        if($validator->fails()){
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
    $show=DB::table('posts')->where('user_id',$request['id'])
    ->where('accept','1')->get();

    return response()->json([
        'posts'=>$show]);
    
    }

public function show_post_id(Request $request){
    $validator=Validator::make($request->all(),[
       
        'id'=>['required']
        ]
        );
        
        if($validator->fails()){
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $show=DB::table('posts')->where('id',$request['id'])
        ->where('accept','1')->get();
    if($show!==null){
        return response()->json([
            'posts'=>$show]);
    }
    else{
    return response()->json([
        'posts'=>'not found']);
    }


}


public function show_posts_company(){

$show=DB::table('posts')->where('user_id',auth()->user()->id)->where('accept','1')->get();


return response()->json([
    'posts'=>$show]);
}





public function show_profile(){


    return response()->json([
        'user information'=>DB::table('users')->where('id',auth()->user()->id)
        ->select('id','name','nickname','email','number','avatar','location')->first(),

        'cv'=>DB::table('cvs')->where('user_id',auth()->user()->id)->select('id','user_id','file','github','linkedIn')->first(),

        'skills'=>DB::table('skills')->where('user_id',auth()->user()->id)->select('id','user_id','name')->get(),

       'locations'=>DB::table('locations')->where('user_id',auth()->user()->id)->select('id','user_id','name')->get(),

       'experience year'=>DB::table('experiences')->where('user_id',auth()->user()->id)->select('id','user_id','name')->get(),
        
        'range'=>DB::table('salaries')->where('user_id',auth()->user()->id)->select('id','user_id','range')->first()

    ]);
    
    }

public function update_profile(Request $request){


    $validator=Validator::make($request->all(),[
        'name'=>['nullable'],
'email'=>['nullable','string', 'lowercase', 'email', 'max:255', Rule::unique('users')->ignore(auth()->user()->id) ],//ضفنا هالشرط الجديد مشان اذا بعتنا نفس ايميل المستخدم القديم ما يعترض على انه لازم يكون فريد
        'number'=>['nullable','digits:10'],
        'avatar'=>['nullable','mimes:png,jpg,jpeg,bmp,sav'],
        'file'=>['nullable'],
         'github'=>['nullable'],
         'linkedIn'=>['nullable']
        ]
        );
        
        if($validator->fails()){
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
//الحصول على معلومات المستخدم قبل التعديل
 $find_user=User::find(auth()->user()->id);
 //في حال في ايميل نسند قيمته الجديدة
 $email = $request->input('email');
 if ($email !== null) {
    $find_user->email = $email;
}

$name=$request->input('name');
if ($name !== null) {
    $find_user->name = $name;
}


$number=$request->input('number');
if ($number !== null) {
    $find_user->number = $number;
}
//1



    if($request['avatar']!=null){

    $image=$request->file('avatar');
    $fileName=null;
    if($request->hasFile('avatar')){
    
        $fileName = time() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('image'), $fileName);
        $fileName='image/'.$fileName;
        $find_user->avatar=asset($fileName);
    }}
    $find_user->save();

  
// الحصول على معلومات السيفي قبل التعديل
 $find_cv = cv::where('user_id', auth()->user()->id)->first();

//2

$file = $request->input('file');
$github = $request->input('github');
$linkedIn = $request->input('linkedIn');

if($file!==null && $github!==null && $linkedIn!==null && $find_cv==null){
    DB::table('cvs')->where('user_id',auth()->user()->id)->insert([
        'user_id'=>auth()->user()->id,
        'file'=>$file,
        'github'=>$github,
        'linkedIn'=>$linkedIn
    ]);
}
 if ($file !== null) {
    if($find_cv!==null)
    {$find_cv->file = $file;
    $find_cv->save();
    }
   
}
 if ($github !== null) {
    if($find_cv!==null){
    $find_cv->github = $github;
    $find_cv->save();
    }
}
if ($linkedIn !== null) {
    if($find_cv!==null){
   $find_cv->linkedIn = $linkedIn;
   $find_cv->save();
    }
}




if($find_user || $find_cv){
return response()->json([

'message'=>'Profile updated successfully',
'profile'=>$find_user,
'cv'=>DB::table('cvs')->where('user_id',auth()->user()->id)->first()

]);
}

else {
    return response()->json([
        'message' => 'User not found'
    ], 404);



}


}




public function insert_cv(Request $request){

    $validator=Validator::make($request->all(),[
        'file'=>['required'],
        'github'=>['required'],
        'linkedIn'=>['required'],

        ]
        );
       
        if($validator->fails()){
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        } 

     $already_have_cv=DB::table('cvs')->where('user_id',auth()->user()->id)->first();
     // يعني عندو سيفي ف بس نحدثه بالقيمة الجديدة
     if($already_have_cv !==null){
DB::table('cvs')->where('user_id',auth()->user()->id)->update([
    'file'=>$request['file'],
    'github'=>$request['github'],
    'linkedIn'=>$request['linkedIn'],
    'updated_at'=>now()

]);
return response()->json([

    'message'=>'cv updated successfully'
]);
     }

     DB::table('cvs')->insert([
        'user_id'=>auth()->user()->id,
        'file'=>$request['file'],
        'github'=>$request['github'],
        'linkedIn'=>$request['linkedIn'],
        'created_at'=>now(),
        'updated_at'=>now()
     ]);
     return response()->json([

        'message'=>'cv inserted successfully'
    ]);

}



public function save_posts(Request $request){


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
//في حال ما كان المنشور بالمفضلة نضيفه
$post=DB::table('posts')->where('id',$request['id'])->where('accept','1')->first();


$saveposts=DB::table('saveposts')->where('post_id',$request['id'])
->where('user_id',auth()->user()->id)->first();


//في حال كان موجود بالمفضلة نغير قيمته 
if($saveposts!==null){
if($saveposts->is_fav==1){
    DB::table('saveposts')->where('post_id',$request['id'])
    ->where('user_id',auth()->user()->id)->update([
        'is_fav'=>0
    ]);
} 
elseif($saveposts->is_fav==0){
    DB::table('saveposts')->where('post_id',$request['id'])
    ->where('user_id',auth()->user()->id)->update([
        'is_fav'=>1
    ]);
}
$save_post=DB::table('posts')->where('posts.id',$request['id'])
->join('saveposts','posts.id','=','saveposts.post_id')
->select('posts.id','title','schedule','location','type_job','company_name','salary','image','description','is_fav')->first();

//لحذف كلشي بوستات غير مفضلة
DB::table('saveposts')->where('is_fav',0)->delete();


return response()->json([
    'post'=>$save_post
    
   
]);

}

//البوست المطلوب موجود لذا نضيفه بالمفضلة
if($post!==null){
    DB::table('saveposts')->Insert([
'user_id'=>auth()->user()->id,
'post_id'=>$request['id'],
'is_fav'=>1,
'created_at'=>now(),
'updated_at'=>now()

    ]);
    return response()->json([
        'post'=>DB::table('posts')->where('posts.id',$request['id'])
        ->join('saveposts','posts.id','=','saveposts.post_id')
        ->select('posts.id','title','schedule','location','type_job','company_name','salary','image','description','is_fav')->first()
        
       
    ]);
        
        
}

return response()->json([
'message'=>'not found'

]);

}





public function show_save_posts(){

  $saveposts=savepost::where('user_id',auth()->user()->id)->where('is_fav',1)->pluck('post_id');
$user=auth()->user()->id;

if($saveposts!==null){

return response()->json([
    'posts'=>DB::table('posts')->whereIn('posts.id',$saveposts)
    ->join('saveposts','posts.id','=','post_id')
   ->where('saveposts.user_id',$user)
->select('posts.id','title','schedule','location','type_job','company_name','salary','image','description','is_fav')->get()]) ;

}


}



public function suggested_posts(){
  
    $user=user::where('id',auth()->user()->id)->first();
   //اذا المستخدم موجود
   if($user!==null){
      $skill= skill::where('user_id',$user->id)->select('name')->get()
      ->pluck('name')->toArray();//لحتى تتحول لمصفوفة لازم نعمل بلوك
   


   $location=location::where('user_id',$user->id)->select('name')->get()
   ->pluck('name')->toArray();



   $orWhereConditions = []; // متغير لتجميع الشروط
   
   foreach($skill as $skill_name){
       $item=  '%' . $skill_name . '%';
   
       $orWhereConditions[] = function($query) use ($item) {
                           $query->where('accept','1')
                           ->where('created_at','>',Carbon::now()->subHours(72))
                           ->where('type_job','LIKE', $item);};
                         
   }

   foreach($location as $location_name){
    $item=  '%' . $location_name . '%';

    $orWhereConditions[] = function($query) use ($item) {
                        $query->where('accept','1')
                        ->where('created_at','>',Carbon::now()->subHours(72))
                        ->where('description','LIKE', $item);};
                      
}


   $posts = Post::where(function($query) use ($orWhereConditions) {
       foreach($orWhereConditions as $condition) {
           $query->orWhere($condition);
       }
   })->get();

   foreach($posts as $post){
    // يتم ارسال اشعارات للبوستات المقترحة من ساعتين على الاكثر
   if($post->created_at->diffInHours(carbon::now()) <= 2){
    mail::to(auth()->user()->email)->send(new suggested(auth()->user()->name));
    // return '1';
   }
   }
   return response()->json([
   'suggested posts'=> $posts
   ]);
   
   }
   return response()->json([
    'message'=> 'user not found'
    ]);
   
   }

public function category(){
   
   $full_time= DB::table('posts')->where('accept','1')->where('schedule','full time')->get();
   $part_time= DB::table('posts')->where('accept','1')->where('schedule','part time')->get();
   $remote= DB::table('posts')->where('accept','1')->where('location','remote')->get();
   $on_site= DB::table('posts')->where('accept','1')->where('location','on site')->get();

   
   
   
return response()->json([
    'full time'=>$full_time,
    'part time'=> $part_time,
    'remote'=>$remote,
    'on site'=>$on_site
]);}





//////////////////////////////////////

public function insert_skill_location_experince(Request $request):JsonResponse{

    $validator=Validator::make($request->all(),[
        'skills' => 'required|array',
        'locations' => 'required|array',
        'experience' => 'required',
        'range'=>'required'
         ]);
       
        if($validator->fails()){
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        } 

   $exist=DB::table('skills')->where('user_id',auth()->user()->id)->get();
   if(count($exist)>0){
    return response()->json([
        'message'=>'you have already add this informations!'
    ]);
   }



        //اضافة مهارات المستخدم
    $skills = $request->input('skills');
    $userId = auth()->user()->id;
    
    $dataToInsert = [];
    foreach ($skills as $skill) {
        $dataToInsert[] = [
            'user_id' => $userId,
            'name' => $skill
        ];
    }

    DB::table('skills')->insert($dataToInsert);

//اضافة مواقع المستخدم

$locations = $request->input('locations');


$dataToInsert = [];
foreach ($locations as $location) {
    $dataToInsert[] = [
        'user_id' => $userId,
        'name' => $location
    ];
}

DB::table('locations')->insert($dataToInsert);

//اضافة سنوات خبرة المستخدم

$experiences = $request->input('experience');


$has_already_experience=DB::table('experiences')
->where('user_id',$userId)->first();
if($has_already_experience==null){
DB::table('experiences')->insert([
    'user_id'=>$userId,
    'name'=> $experiences
]);

}
DB::table('experiences')->update([
               
    'name'=> $experiences
]);

//اضافة راتب المستخدم

$range=$request->input('range');
$already_have_range=DB::table('salaries')->where('user_id',$userId)->first();
if($already_have_range==null){
    DB::table('salaries')->insert([
        'user_id'=>$userId,
         'range'=>$range
    ]);
}
DB::table('salaries')->update([
               
    'range'=> $range
]);

    return response()->json([
        'message' => 'Skills & locations & experience years & range inserted successfully'
    ]);




}



public function show_all_skills_locations_experience():JsonResponse{

    return response()->json([
    'skills'=> skill::getEnumValues(),
   'locations'=> location::getEnumValues(),
    'experience years' =>  experience::getEnumValues(),
        'all ranges'=>salary::getEnumValues()
    ]);

}



public function choose_a_post(Request $request){
    $validator=Validator::make($request->all(),[
        'id' => ['required','exists:posts,id'],
        ]
        );
       
        if($validator->fails()){
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
       // نتأكد ان البوست المختار موافق عليه من قبل الادمن
        $is_accepted=DB::table('posts')
        ->where('id',$request['id'])->where('accept','1')->first();

      //نتأكد ان المستخدم ما طلب البوست نفسه مرتين
        $already_choose=DB::table('request_jobs')
        ->where('user_id',auth()->user()->id)
        ->where('post_id',$request['id'])->first();

if($is_accepted!==null && $already_choose==null){
   DB::table('request_jobs')->insert([
'user_id'=>auth()->user()->id,
'post_id'=>$request['id']

   ]);

return response()->json([
    'message'=>'success'
]);
}
//البوست مختاره من قبل
if($already_choose!==null){
    return response()->json([
        'message'=>'this post already has choosen!'
    ]);

}
 // في حال اختار بوست ما وافق عليه الادمن لسا
return response()->json([
    'message'=>'this post is invalid!'
]);

}




public function list_orders(Request $request){
    $validator=Validator::make($request->all(),[
        'id' => ['required',
        // هذا الشرط لحتى يتم التحقق ان البوست المختار موجود و ينتمي لصاحب الشركة 
         Rule::exists('posts','id')->where(function($query){
            $query->where('user_id',auth()->user()->id);
         })     
    ],
        ]
        );
       
        if($validator->fails()){
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
       
     $request_jobs= DB::table('request_jobs')
    ->where('post_id',$request['id'])->get()->pluck('user_id')->toArray();


$users=DB::table('users')->whereIn('users.id',$request_jobs)->join('experiences','users.id','=','experiences.user_id')
->join('salaries','users.id','=','salaries.user_id')
->select('users.name','email','number','avatar','status','experiences.name as experience','range')->get();


return response()->json([
    'orders'=>$users,

]);

}


// public function filter_orders(Request $request,$filter){

// //لحتى نتأكد من ارجاع الطلبات للبوستات الخاصة بالشركة نفسها وليس شركة اخرى
//    $users=DB::table('posts')->where('posts.user_id',auth()->user()->id)
// ->join('request_jobs','posts.id','=','request_jobs.post_id')
// ->select('request_jobs.user_id')->get()->pluck('user_id')->toArray();

// if($filter=='experience'){
// // فلترة بناء على سنين الخبرة من الاكثر الى الاقل
// return response()->json([
//    'orders'=> DB::table('experiences')->whereIn('user_id',$users)
// ->orderBy('experiences.name','desc')->join('users','experiences.user_id','=','users.id')
// ->select('users.name','email','number','avatar')
// ->get()]);
// }

// //الفلترة بناء على االراتب
// if($filter=='salary'){
//     $validator=Validator::make($request->all(),[
//         'range' => ['required'],
//         ]
//         );
       
//         if($validator->fails()){
//             return response()->json([
//                 'message' => 'Validation failed',
//                 'errors' => $validator->errors()
//             ], 422);
//         }


//         $salaries=DB::table('salaries')->whereIn('user_id',$users)
//      ->where('range',$request->range)->get()->pluck('user_id')->toArray();

//         return response()->json([
//           'orders'=>  DB::table('users')->whereIn('id',$salaries)
//         ->select('name','email','number','avatar')
//         ->get()]);
// }

// //الفلترة بناء على اماكن العمل 

// if($filter=='location'){

//     $validator=Validator::make($request->all(),[
//         'location' => ['required'],
//         ]
//         );
       
//         if($validator->fails()){
//             return response()->json([
//                 'message' => 'Validation failed',
//                 'errors' => $validator->errors()
//             ], 422);
//         }


//         $locations=DB::table('locations')->whereIn('user_id',$users)
//      ->where('name',$request->location)->get()->pluck('user_id')->toArray();

//      return response()->json([
//         'orders'=>  DB::table('users')->whereIn('id',$locations)
//       ->select('name','email','number','avatar')
//       ->get()]);


// }




// }



public function filter_orders(Request $request,$filter){

    //لحتى نتأكد من ارجاع الطلبات للبوستات الخاصة بالشركة نفسها وليس شركة اخرى
       $users=DB::table('posts')->where('posts.user_id',auth()->user()->id)
    ->join('request_jobs','posts.id','=','request_jobs.post_id')
    ->select('request_jobs.user_id')->get()->pluck('user_id')->toArray();
    
    if($filter=='experience'){
    // فلترة بناء على سنين الخبرة من الاكثر الى الاقل
    return response()->json([
       'orders'=> DB::table('experiences')->whereIn('user_id',$users)
    ->orderBy('experiences.name','desc')->join('users','experiences.user_id','=','users.id')
    ->select('users.name','email','number','avatar')
    ->get()]);
    }
    
    //الفلترة بناء على االراتب
    if($filter=='salary'){
        $validator=Validator::make($request->all(),[
            'range' => ['required'],
            ]
            );
           
            if($validator->fails()){
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
    
    
            $salaries=DB::table('salaries')->whereIn('user_id',$users)
         ->where('range',$request->range)->get()->pluck('user_id')->toArray();
    
            return response()->json([
              'orders'=>  DB::table('users')->whereIn('id',$salaries)
            ->select('name','email','number','avatar')
            ->get()]);
    }
    
    //الفلترة بناء على اماكن العمل 
    
    if($filter=='location'){
    
        $validator=Validator::make($request->all(),[
            'location' => ['required'],
            ]
            );
           
            if($validator->fails()){
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
    
    
            $locations=DB::table('locations')->whereIn('user_id',$users)
         ->where('name',$request->location)->get()->pluck('user_id')->toArray();
    
         return response()->json([
            'orders'=>  DB::table('users')->whereIn('id',$locations)
          ->select('name','email','number','avatar')
          ->get()]);
    
    
    }
    
    
    
    
    }















//عرض شركة معينة عن طريق ال id
public function show_company(Request $request){
    $validator=Validator::make($request->all(),[
        'id' => ['required'],
        ]
        );
       
        if($validator->fails()){
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }


$company=DB::table('users')->where('id',$request['id'])
->where('role','company')->first();
$company = [$company];
if($company!==null){
return response()->json([
    'company'=>$company
]);
}
return response()->json([
    'message'=>'the account not found'
]);

}


public function show_profile_company(){

$user_id=auth()->user()->id;
$company=DB::table('users')->where('id',$user_id)
->select('name','email','number','avatar as image','creation_date','location','role','status')
->first();

$balance=DB::table('wallets')->where('user_id',$user_id)->select('balance')->first();


$postCount = DB::table('posts') // افترض أن اسم جدول المنشورات هو 'posts'
->where('user_id', $user_id)->where('accept','1')
->count();


return response()->json([
'profile information'=>$company ,
'your balance'=> $balance,
'post count' => $postCount 
]);
}


public function update_profile_company(Request $request){

    $validator=Validator::make($request->all(),[
        'name'=>['nullable'],
'email'=>['nullable','string', 'lowercase', 'email', 'max:255', Rule::unique('users')->ignore(auth()->user()->id) ],//ضفنا هالشرط الجديد مشان اذا بعتنا نفس ايميل المستخدم القديم ما يعترض على انه لازم يكون فريد
        'number'=>['nullable','digits:10'],
        'image'=>['nullable','mimes:png,jpg,jpeg,bmp,sav'],
   'location'=>['nullable'],
   'Creation_Date'=>['nullable']

        ]
        );
        
        if($validator->fails()){
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

//الحصول على معلومات المستخدم قبل التعديل
$find_user=User::find(auth()->user()->id);
//في حال في ايميل نسند قيمته الجديدة
$email = $request->input('email');
if ($email !== null) {
   $find_user->email = $email;
}

$name=$request->input('name');
if ($name !== null) {
    $find_user->name = $name;
}


$number=$request->input('number');
if ($number !== null) {
    $find_user->number = $number;
}

$Creation_Date=$request->input('Creation_Date');
if ($Creation_Date !== null) {
    $find_user->Creation_Date = $Creation_Date;
}

$location=$request->input('location');
if ($location !== null) {
    $find_user->location = $location;
}



   if($request['image']!=null){

   $image=$request->file('image');
   $fileName=null;
   if($request->hasFile('image')){
   
       $fileName = time() . '.' . $image->getClientOriginalExtension();
       $image->move(public_path('image'), $fileName);
       $fileName='image/'.$fileName;
       $find_user->avatar=asset($fileName);
   }}
   $find_user->save();

   return response()->json([
    'profile_information'=>DB::table('users')->where('id',$find_user->id)->select( 'id', 'name', 'email', 'number', 'avatar as image', 'location', 'Creation_Date')->first()

   ]);


}



public function id_post_to_get_company(Request $request){
    $validator=Validator::make($request->all(),[
        'id' => ['required'],
        ]
        );
       
        if($validator->fails()){
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

$company=DB::table('posts')->where('id',$request['id'])->where('accept','1')->select('user_id')->get()
->pluck('user_id')->toArray();


return response()->json([

    'company'=>DB::table('users')->where('id',$company)->get()
]);



}












}