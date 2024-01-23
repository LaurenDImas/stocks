<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Store;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CreateUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            "name"       => "Admin",
            "email"      => "admin@gmail.com",
            "password"   => bcrypt("password"),
            "birthdate"  => "1990-01-01",
            "role"       => "admin",
            "store_id"   => null
        ]);

        $items = [
            [
                'name' => 'Gandaria City 2'
            ],[
                'name' => 'Pondok Indah Mall'
            ],[
                'name' => 'Radio Dalam 1'
            ],[
                'name' => 'Radio Dalam 2'
            ],[
                'name' => 'Pondok Pinang'
            ],[
                'name' => 'Lebak Bulus'
            ],
        ];

        collect($items)->each(function ($result) { 
            $item = Store::create($result); 
            User::create([
                "name"       => "User ".$result['name'],
                "email"      => str_replace(' ','_',strtolower($result['name']))."@gmail.com",
                "password"   => bcrypt("password"),
                "birthdate"  => "1990-01-01",
                "role"       => "user",
                "store_id"   => $item->id
            ]);
        });
    }
}
