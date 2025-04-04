<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\Clinic;
use App\Models\User;
use App\Models\Category;
use App\Models\DocSession;
use App\Models\Nurse;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Contracts\Session\Session;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $nurses = [

            [
                'name' => 'Test User6',
                'email' => 'test6@example.com',
                'password' => Hash::make('password6'),
                'role'=>'nurse'
            ],
            [
                'name' => 'Test User7',
                'email' => 'test7@example.com',
                'password' => Hash::make('password7'),
                'role'=>'nurse'
            ]
            ];

        $doctors=[[
            'name' => 'Test User1',
            'email' => 'test1@example.com',
            'password' => Hash::make('password1'),
            'role'=>'doctor'
        ],
        [
            'name' => 'Test User2',
            'email' => 'test2@example.com',
            'password' => Hash::make('password2'),
            'role'=>'doctor'
        ],
        [
            'name' => 'Test User3',
            'email' => 'test3@example.com',
            'password' => Hash::make('password3'),
            'role'=>'doctor'
        ],
        [
            'name' => 'Test User4',
            'email' => 'test4@example.com',
            'password' => Hash::make('password4'),
            'role'=>'doctor'
        ],
        [
            'name' => 'Test User5',
            'email' => 'test5@example.com',
            'password' => Hash::make('password5'),
            'role'=>'doctor'
        ]];
        $categories=[
            ['name'=>'Psycology'],
            [ 'name'=>'oral'],
            ['name'=>'eye surgan'],
            [ 'name'=>'heart specialist'],
            [ 'name'=>'Neurology']
             ];
    
           foreach($categories as $category)
           {
            Category::create($category);
    
           }  
       foreach($doctors as $doctor)
       {
        User::create($doctor);

       } 
       foreach($nurses as $nurse)
       {
        User::create($nurse);

       } 
       
   

      
       for($i=1;$i<6;$i++)
       {
        Doctor::create([
          'user_id'=>$i,
          'category_id'=>$i,
          'image'=>'doc'.$i.'.jpg'
        ]);
        }
        $clinics=[
            ['name'=>'nawaloka','image'=>'clinic1.jpg' ,'description'=>'Nawaloka Family Health Clinic provides comprehensive medical care for individuals and families. Our team of experienced doctors and nurses offers services such as general check-ups, vaccinations, chronic disease management, and minor procedures. We prioritize patient-centered care, ensuring a comfortable and supportive environment for all our visitors.

'],
            [ 'name'=>'nawapuloka','image'=>'clinic2.jpg','description'=>'nawapuloka Family Health Clinic provides comprehensive medical care for individuals and families. Our team of experienced doctors and nurses offers services such as general check-ups, vaccinations, chronic disease management, and minor procedures. We prioritize patient-centered care, ensuring a comfortable and supportive environment for all our visitors.

'],

             ];
        
        foreach($clinics as $clinic)
        {
            Clinic::create($clinic);

        } 
        
        $date = '2025-04-26';
        $i=0;
        for($j=1;$j<6;$j++)
        {
          for($k=1;$k<6;$k++ ) 
          {$i++;
            $newDate = Carbon::parse($date)->addDays($i);
            DocSession::create(['clinic_id'=>$k,'doctor_id'=>$j,'date'=>$newDate,'availability'=>'upcoming']);
          } 
        }

        for($i=1;$i<6;$i++)
        {
         Nurse::create(['user_id'=>$i+5,'clinic_id'=>$i]);
        }
        
    }
}
