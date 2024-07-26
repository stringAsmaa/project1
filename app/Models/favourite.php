<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class favourite extends Model
{
    use HasFactory;


    protected $fillable=[
'message_id',
// 'user_id'

    ];


    public function user(){
        return $this->belongsToMany(User::class,'favourite_user')->withTimestamps();}













}
