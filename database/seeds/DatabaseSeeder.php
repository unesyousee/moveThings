<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
         
        $this->call(UsersTableSeeder::class);
        $this->call(RoleTableSeeder::class);
        /*DB::table('carriers')->insert([
            'id' => 1,
            'name' => 'نیسان',
           
            'price' => 700000,
            'picture_enable' => 'http://nobaartest.ir/img/cars/neysan_enable.png',
            'picture_disable' => 'http://nobaartest.ir/img/cars/neysan_disable.png',
            'status' => 1
        ]);

        DB::table('carriers')->insert([
            'id' => 2,
            'name' => 'کامیون',
            
            'price' => 1800000,
            'picture_enable' => 'http://nobaartest.ir/img/cars/kamyun_enable.png',
            'picture_disable' => 'http://nobaartest.ir/img/cars/kamyun_disable.png',
            'status' => 1
        ]);

        DB::table('carriers')->insert([
            'id' => 3,
            'name' => 'وانت',
         
            'price' => 600000,
            'picture_enable' => 'http://nobaartest.ir/img/cars/vanet_enable.png',
            'picture_disable' => 'http://nobaartest.ir/img/cars/vanet_disable.png',
            'status' => 1
        ]);

        DB::table('carriers')->insert([
            'id' => 5,
            'name' => 'کارگر',
            
            'price' => 75000,
            'picture_enable' => 'http://nobaartest.ir/img/cars/kargar_enable.png',
            'picture_disable' => 'http://nobaartest.ir/img/cars/kargar_disable.png',
            'status' => 1
        ]);

        DB::table('carriers')->insert([
            'id' => 4,
            'name' => 'ماشین بار',
            
            'price' => 10000,
            'picture_enable' => 'http://nobaartest.ir/img/cars/vanet_enable.png',
            'picture_disable' => 'http://nobaartest.ir/img/cars/vanet_disable.png',
            'status' => 0
        ]);*/
         
    }
}
