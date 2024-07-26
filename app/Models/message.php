<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class message extends Model
{
    use HasFactory;

protected $fillable=[
    'message',
    'user_id',
    'to_id'
];

public function users(){
    return $this->belongsToMany(User::class,'favourite')->withTimestamps();}


}
