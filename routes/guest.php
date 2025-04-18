<?php

use App\Http\Controllers\adminController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\chatController;
use App\Http\Controllers\crudController;
use App\Http\Controllers\SocialiteController;
use Illuminate\Support\Facades\Route;


//logins
Route::post('/register/{role}', [RegisteredUserController::class, 'store'])
                ->middleware('guest')
                ->name('register');

                
                Route::post('/code', [RegisteredUserController::class, 'put_code'])->middleware('guest');


                Route::post('/resend_code4verify', [RegisteredUserController::class, 'resend_code4verify'])->middleware('guest');



               Route::post('/login/{role}', [AuthenticatedSessionController::class, 'store'])
                ->middleware('guest','status')
                ->name('login');


                Route::post('/forgotPassword', [AuthenticatedSessionController::class, 'forgot_password'])
                ->middleware('guest','status');



                Route::post('/code4password', [AuthenticatedSessionController::class, 'code_4_password']);


                Route::post('/resetPassword', [AuthenticatedSessionController::class, 'reset_password']);

                Route::post('/resend_code4password', [AuthenticatedSessionController::class, 'resend_code4password']);

               Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
                ->middleware('auth:sanctum')
                ->name('logout');




                    //socialite
                  Route::controller(SocialiteController::class)->prefix('auth/google')->group(function(){

                    Route::get('/redirect', 'redirect');
                    Route::get('/callback', 'callBack')->name('auth.callback');
                  });


                       //for chat

                Route::middleware('auth:sanctum')->group(function(){
                    Route::post('/send',[chatController::class,'send']);
                    Route::get('/show-messages',[chatController::class,'show_messages']);
                    Route::post('/search',[chatController::class,'search']);

                    Route::post('/put-favourite',[chatController::class,'favourite']);

                    Route::get('/get-favourite',[chatController::class,'get_favourite']);


                });


                        //crud


                Route::post('/create_post',[crudController::class,'create_post'])
                ->middleware(['auth:sanctum','role:company']);


                Route::post('/edit_post',[crudController::class,'edit_post'])
                ->middleware(['auth:sanctum','role:company']);


                Route::delete('/delete_post',[crudController::class,'delete_post'])
                ->middleware(['auth:sanctum','role:company']);


                Route::post('/search',[crudController::class,'search']);




                Route::get('/show_post',[crudController::class,'show_posts'])
                ->middleware(['auth:sanctum']);


                Route::get('/show_my_posts',[crudController::class,'show_my_posts']);

                Route::get('/show_profile',[crudController::class,'show_profile'])
                ->middleware(['auth:sanctum']);


                Route::post('/update_profile',[crudController::class,'update_profile'])
                ->middleware(['auth:sanctum']);


                Route::post('/insert_cv',[crudController::class,'insert_cv'])
                ->middleware(['auth:sanctum','role:job_seeker']);




                Route::post('/save_posts',[crudController::class,'save_posts'])
                ->middleware(['auth:sanctum','role:job_seeker']);



                Route::get('/show_save_posts',[crudController::class,'show_save_posts'])
                ->middleware(['auth:sanctum','role:job_seeker']);



                Route::get('/suggested_posts',[crudController::class,'suggested_posts'])
                ->middleware(['auth:sanctum']);

                Route::get('/category',[crudController::class,'category']);

                Route::get('/show_company',[crudController::class,'show_company'])
              ;



                Route::post('/insert_skill_location_experince',[crudController::class,'insert_skill_location_experince'])
                ->middleware(['auth:sanctum','role:job_seeker']);

                Route::get('/show_my_skills_locations_experience',[crudController::class,'show_my_skills_locations_experience'])
                ->middleware(['auth:sanctum','role:job_seeker']);

                Route::get('/show_all_skills_locations_experience',[crudController::class,'show_all_skills_locations_experience']);



                Route::post('/choose_a_post',[crudController::class,'choose_a_post'])
                ->middleware(['auth:sanctum','role:job_seeker']);

                Route::get('/list_orders',[crudController::class,'list_orders'])
                ->middleware(['auth:sanctum','role:company']);

                Route::post('/filter_orders/{filter}',[crudController::class,'filter_orders'])
                ->middleware(['auth:sanctum','role:company']);


                Route::get('/show_profile_company',[crudController::class,'show_profile_company'])
                ->middleware(['auth:sanctum','role:company']);


                Route::post('/update_profile_company',[crudController::class,'update_profile_company'])
                ->middleware(['auth:sanctum','role:company']);

                Route::get('/show_posts_company',[crudController::class,'show_posts_company'])
                ->middleware(['auth:sanctum','role:company']);

                Route::get('/show_post_id',[crudController::class,'show_post_id']);
                Route::get('/id_post_to_get_company',[crudController::class,'id_post_to_get_company']);


                //admin
                Route::post('/loginn/{role}',[adminController::class,'login']);

                Route::middleware(['auth:sanctum','role:admin'])->prefix('admin')->group(function(){
                Route::post('delete/post',[adminController::class,'delete_post']);
                Route::post('/banUser',[adminController::class,'banUser']);
                Route::post('/actUser',[adminController::class,'actUser']);
                Route::get('/statistics',[adminController::class,'statistics']);
                Route::get('/show_not_accoet_posts',[adminController::class,'show_not_accoet_posts']);
                Route::post('/accept_post',[adminController::class,'accept_post']);
                Route::get('/show_all_companies',[adminController::class,'show_all_companies']);
                Route::post('/insert_balance',[adminController::class,'insert_balance']);




                });
