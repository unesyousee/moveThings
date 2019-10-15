<?php

use Illuminate\Database\Seeder;
use App\Role;
use App\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
/*        DB::table('users')->insert([
            'first_name' => str_random(10),
            'last_name' => str_random(10),
            'phone' => str_random(10),
            'email' => str_random(10).'@gmail.com',
            'address' => str_random(10),
            'profile_pic' => str_random(10),
            'password' => bcrypt('secret'),
            'share_code' => str_random(10),
            'is_first' => 1,
            'player_id' => 1,
            'status' => 1,
        ]);*/
        $role_employee = Role::where('name', 'employee')->first();
        $role_manager  = Role::where('name', 'manager')->first();
        $employee = new User();
        $employee->first_name = 'منشی';
        $employee->last_name = 'منشی زاده';
        $employee->phone = '09999999999';
        $employee->email = 'monshi@example.com';
        $employee->password = bcrypt('secret');
        $employee->save();
        $employee->roles()->attach($role_employee);
        $manager = new User();
        $manager->first_name = 'مدیر';
        $manager->last_name = 'مدیرزاده';
        $manager->phone = '0912111111';
        $manager->email = 'manager@example.com';
        $manager->password = bcrypt('secret');
        $manager->save();
        $manager->roles()->attach($role_manager);
    }
}
