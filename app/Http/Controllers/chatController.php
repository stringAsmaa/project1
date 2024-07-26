<?php

namespace App\Http\Controllers;

use App\Events\chat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class chatController extends Controller
{


    public function send(Request $request){
       
broadcast(new chat($request->input('message'), $request->input('to_id')));

DB::table('messages')->insert([
'message'=>$request->input('message'),
'to_id'=>$request->input('to_id'),
'user_id'=>  Auth::user()->id,
'created_at' => now(), // تاريخ الإنشاء
        'updated_at' => now() 
]);


return response()->json([
    'message'=>'success'
]);

    }


public function show_messages(Request $request){

    //كلشي رسائل انا ارسلتها او تلقيتها
$show=DB::table('messages')->where('user_id',Auth::user()->id)
->orWhere('to_id',Auth::user()->id)->get();

return response()->json([
    'message'=>$show
]);

}

public function search(Request $request){
//البحث عن جهة اتصال عن طريق الاسم او البحث عن رسالة
$request->validate([
    'name' => ['string','required_if:message,=,null'],
    'message' => ['string','required_if:name,=,null']
]);

$search_contact_person=[];
$search_messages=[];

if($request->has('name')){

 $search= DB::table('messages')->where('user_id',Auth::user()->id)
->orWhere('to_id',Auth::user()->id)->pluck('id');

if($search){
    //whereIn يقوم بالمقارنة بين اكثر من قيمة 
    //whereNotIn يرجع السجلات التي لا تحتوي على القيمة التي يتم مقارنتها 
    $search_contact_person= DB::table('users')->whereIn('id',$search)
    ->whereRaw("LOWER(name) LIKE '%".strtolower($request['name'])."%'")
    ->whereNotIn('id', [Auth::user()->id])->get();

}


}

if($request->has('message')){
//هون لازم أأكد عليه انه يرجعلي الرسائل يلي انا ارسلتها او استقبلتها فقط
$search_messages=DB::table('messages')->where(function($query){

$query->where('user_id',Auth::user()->id)
->orWhere('to_id',Auth::user()->id);
})->where('message', 'LIKE', '%'.$request['message'].'%')->get();
}

return response()->json([
    'contact_person'=>$search_contact_person,
    'messages'=>$search_messages
]);





}


public function favourite(Request $request){
//وضع رسائل في المفضلة
$request->validate([

'message_id'=>['required']

]);
 


  $user=DB::table('messages')->where('id',$request['message_id'])
 ->where(function ($query) {
    $query->where('user_id', Auth::user()->id)
          ->orWhere('to_id', Auth::user()->id);
})->first();

if($user){

DB::table('favourites')->insert([

'user_id'=>Auth::user()->id,
'message_id'=>$request['message_id']

]);



return response()->json([
'message'=>'success',
]);
}

return response()->json([
   'message'=> 'not fouund message'
]);



}



public function get_favourite(Request $request){
//عرض رسائل المفضلة

    $favourite=DB::table('favourites')->where('favourites.user_id',Auth::user()->id)
    ->join('messages','favourites.message_id','=','messages.id')
    ->select('message','messages.user_id')->get();


if($favourite){
    return response()->json([
        'favourite'=>$favourite
    ]);
}

return response()->json([
    'message'=>'not found messages'
]);

}







}
