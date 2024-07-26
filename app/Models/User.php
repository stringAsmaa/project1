<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable,HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'email_verified_at',
        'avatar',
        'number',
        'role',
        'status',
        'schedule',
        'salary'
       
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];





public function code(): HasMany
{
    return $this->hasMany(code::class);
}




public function post(): HasMany
{
    return $this->hasMany(post::class);
}


public function message(): HasMany
{
    return $this->hasMany(message::class);
}




public function messages(){
    return $this->belongsToMany(message::class,'favourite')->withTimestamps();}


    public function cv():HasOne
    {
        return $this->hasOne(cv::class);}


        public function posts(){
            return $this->belongsToMany(post::class,'savepost')->withTimestamps();}


            public function searches(): HasMany
            {
                return $this->hasMany(search::class);
            }
    
            const STATUS_ACTIVE = 'active';
            const STATUS_BANNED = 'banned';

         public function is_Active(){

return $this->status === self::STATUS_ACTIVE;

         }

        public function is_banned(){

return $this->status === self::STATUS_BANNED;

        }



        public function skill(): HasMany
        {
            return $this->hasMany(skill::class);
        }
        


        public function location(): HasMany
        {
            return $this->hasMany(location::class);
        }

        public function experience(): HasOne
        {
            return $this->hasOne(location::class);
        }

        
        public function request_job(): HasMany
        {
            return $this->hasMany(request_job::class);
        }
}
