<?php

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {        
        //cabang alam sutera
        DB::table('users')->insert([
            'staffing_number' => '12345',
            'username' => 'admin',
            'fullname' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('P@ssw0rd12345'),
            'phone_number' => '081223456789',
            'role' => 'admin',
            'branch_id' => 1,
            'status' => '1',
            'created_by' => 'admin',
            'created_at' => '2022-07-20'
        ]);

        DB::table('branches')->insert([
            'branch_code' => '123',
            'branch_name' => 'Test',
            'isDeleted' => 0,
            'user_id' => 1,
            'user_update_id' => 1,
            'created_at' => '2022-07-20',
            'address' =>'test address'
        ]);
    }
}