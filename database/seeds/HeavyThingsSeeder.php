<?php
/**
 * Created by PhpStorm.
 * User: mohammad
 * Date: 10/26/18
 * Time: 12:42 PM
 */
use Illuminate\Database\Seeder;

class HeavyThingsSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        DB::table('heavy_things')->insert([
            'id' => 3,
            'name' => 'یخچال ساید بای ساید',
           
            'price' => 100000,
            'status' => 1
        ]);

        DB::table('heavy_things')->insert([
            'id' => 4,
            'name' => 'گاو صندوق',
            
            'price' => 150000,
            'status' => 1
        ]);

        DB::table('heavy_things')->insert([
            'id' => 5,
            'name' => 'میز شیشیه ای شش نفره به بالا',
           
            'price' => 100000,
            'status' => 1
        ]);

        DB::table('heavy_things')->insert([
            'id' => 6,
            'name' => 'پیانو',
            
            'price' => 450000,
            'status' => 1
        ]);
    }
}