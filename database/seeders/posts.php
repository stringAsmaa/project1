<?php

namespace Database\Seeders;

use App\Models\post;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class posts extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        

        $post1 = post::create([
            'id'=>1,
            'user_id'=>'2',
            'title'=>'Marketing',
            'schedule'=>'full time',
            'location'=>'Remote',
            'type_job'=>'Marketing',
            'company_name'=>'AMANـGroup',
            'salary'=>'100000',
            'image'=>'post_image/',
            'description'=>'We are seeking an IT Sales Engineer to join our dynamic team. 
            The ideal candidate will have a background in sales
             and a strong technical knowledge of IT products and services.',
            'accept'=>'1'

            ]);

            $post11 = post::create([
                'id'=>2,
                'user_id'=>'2',
                'title'=>'Translations',
                'schedule'=>'full time',
                'location'=>'on site',
                'type_job'=>'Translations',
                'company_name'=>'AMANـGroup',
                'salary'=>'180000',
                'image'=>'post_image/',
                'description'=>'We are seeking an Translator   to join our dynamic team. 
                The ideal candidate will have a background in translation
                 and a strong  knowledge of  multi languages.
                  Work location in Hama.',
                'accept'=>'1'
    
                ]);





            $post2 = post::create([
                'id'=>3,
                'user_id'=>'3',
                'title'=>'Software Development',
                'schedule'=>'part time',
                'location'=>'on site',
                'type_job'=>'Software Development',
                'company_name'=>'Jeddahart',
                'salary'=>'3500000',
                'image'=>'post_image/',
                'description'=>'Requirements:
* +3 years of professional software engineering experience.
* Solid experience in React life cycle.
* Solid experience in ReactJS.
* Solid experience in React query.
* Solid experience in Redux Toolkit.
* Solid experience in Socket.IO
* Good experience in JS, CSS, Material ui, and HTML.
 Work location in Latakia.',
                'accept'=>'1'
    
                ]);

                $post22= post::create([
                    'id'=>4,
                    'user_id'=>'3',
                    'title'=>'Research and Development',
                    'schedule'=>'part time',
                    'location'=>'on site',
                    'type_job'=>'Research and Development',
                    'company_name'=>'Jeddahart',
                    'salary'=>'900000',
                    'image'=>'post_image/',
                    'description'=>'Requirement:
    +3 years of professional Research and Development experience.
     Work location in Aleppo.',
                    'accept'=>'1'
        
                    ]);


                $post3 = post::create([
                    'id'=>5,
                    'user_id'=>'4',
                    'title'=>'Administration',
                    'schedule'=>'full time',
                    'location'=>'Remote',
                    'type_job'=>'Administration',
                    'company_name'=>'LARSA',
                    'salary'=>'3500000',
                    'image'=>'post_image/',
                    'description'=>'LARSA Technologies is seeking a highly skilled and experienced Server Administrator
                     to join our dynamic team. The 
                    ideal candidate will have extensive knowledge and hands-on 
                    experience in managing and maintaining server infrastructures
                     with a focus on Kubernetes, Linux, microservices, Docker, 
                     GitHub, DNS, cPanel, security, and firewalls.',
                    'accept'=>'1'
                    ]);



                    $post33 = post::create([
                        'id'=>6,
                        'user_id'=>'4',
                        'title'=>'Graphic Design',
                        'schedule'=>'part time',
                        'location'=>'on site',
                        'type_job'=>'Graphic Design',
                        'company_name'=>'LARSA',
                        'salary'=>'3500000',
                        'image'=>'post_image/',
                        'description'=>'(WE are hiring Graphic design for social media ) 
Must be creative & self motivated.
❇️using Photoshop & Illustrator. 
❇️Creative social design. 
❇️Designing 2D videos 
❇️Fluent in English and prefer Arabic language and Urdu or Hindi languages.
Work location in Damascus.',
                        'accept'=>'1'
                        ]);











    }
}
