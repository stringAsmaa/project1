<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class post extends Model
{
    use HasFactory;

    protected $fillable=[
        'post',
        'title',
        'time_schedule',
        'location_job',
        'type_job'
    ];


    public function user(){
        return $this->belongsToMany(User::class,'savepost')->withTimestamps();}








}
