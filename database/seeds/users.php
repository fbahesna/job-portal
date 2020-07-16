<?php

use Illuminate\Database\Seeder;
use DB;

class users extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Employer',
            'email' => 'employer@gmail.com',
            'password' => 12341234,
        ]);

        DB::table('users')->insert([
            'name' => 'Freelancer',
            'email' => 'freelancer@gmail.com',
            'password' => 43214321,
        ]);
    }
}
