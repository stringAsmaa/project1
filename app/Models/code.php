<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class code extends Model
{
    use HasFactory;


    protected $fillable = [
        'user_id',
        'expiry_at',
        'code',
    ];


    public function generate_code(){
    
        $this->timestamps=false;
    $this->code=rand(10000,99999);
    $this->expiry_at= now()->addMinute(30);
    
    return $this->code;
    }
    

}
