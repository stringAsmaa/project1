<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class rolesandpermissionsseeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();


     // create permissions
     Permission::create(['name' => 'create post']);
     Permission::create(['name' => 'edit post']);
     Permission::create(['name' => 'delete post']);
     Permission::create(['name' => 'show post']);



     
        // create roles and assign existing permissions
        $role1 = Role::create(['name' => 'job_seeker']);
        $role1->givePermissionTo('show post');

$role1->save();

        $role2 = Role::create(['name' => 'company']);
        $role2->givePermissionTo('create post','edit post','delete post','show post');
    
        $role2->save();



        $role3 = Role::create(['name' => 'admin']);

        $role3->givePermissionTo('create post','edit post','delete post','show post');

        $role3->save();


$admin = User::create([
'id'=>1,
'name'=>'asmaa',
'nickname'=>'admin',
'email'=>'asmaaabdaljalil54@gmail.com',
'email_verified_at'=>now(),
'password'=>Hash::make('asmaa6789817'),
'number'=>'0956773151',
'avatar'=>asset('default/default_image.jpg'),
'role'=>'admin'
]);
$token =$admin->createToken('api_token');
$this->command->info('Admin Token: ' . $token->plainTextToken);
$admin->assignRole($role3);



$company1 = User::create([
    'id'=>2,
    'name'=>'AMANـGroup',
    'email'=>'AMANـGroup@gmail.com',
    'email_verified_at'=>now(),
    'password'=>Hash::make('999999999'),
    'number'=>'0986512354',
    'avatar'=>asset('default/default_image.jpg'),
    'role'=>'company'
    ]);



    $company2 = User::create([
        'id'=>3,
        'name'=>'Jeddahart',
        'email'=>'Jeddahart@gmail.com',
        'email_verified_at'=>now(),
        'password'=>Hash::make('444444444'),
        'number'=>'0987654378',
        'avatar'=>asset('default/default_image.jpg'),
        'role'=>'company'
        ]);

        $company3 = User::create([
            'id'=>4,
            'name'=>'LARSA',
            'email'=>'LARSA@gmail.com',
            'email_verified_at'=>now(),
            'password'=>Hash::make('12121212'),
            'number'=>'0912345678',
            'avatar'=>asset('default/default_image.jpg'),
            'role'=>'company'
            ]);

            $user1 = User::create([
                'id'=>5,
                'name'=>'asmaa',
                'email'=>'asmaa@gmail.com',
                'email_verified_at'=>now(),
                'password'=>Hash::make('12345678'),
                'number'=>'0936789817',
                'avatar'=>asset('default/default_image.jpg'),
                'role'=>'job_seeker'
                ]);
                $user2 = User::create([
                    'id'=>6,
                    'name'=>'anas',
                    'email'=>'anas@gmail.com',
                    'email_verified_at'=>now(),
                    'password'=>Hash::make('12345678'),
                    'number'=>'0912121212',
                    'avatar'=>asset('default/default_image.jpg'),
                    'role'=>'job_seeker'
                    ]); 
                     $user1 = User::create([
                        'id'=>7,
                        'name'=>'layan',
                        'email'=>'layan@gmail.com',
                        'email_verified_at'=>now(),
                        'password'=>Hash::make('12345678'),
                        'number'=>'0934343434',
                        'avatar'=>asset('default/default_image.jpg'),
                        'role'=>'job_seeker'
                        ]);
    }
}
