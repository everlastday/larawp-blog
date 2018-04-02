<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // reset the users table
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('users')->truncate();

        // generate 3 users/author
        DB::table('users')->insert([
            [
                'name' => "John Doe",
                'slug' => "john-doe",
                'email' => "johndoe@test.com",
                'password' => bcrypt('123'),
            ],
            [
                'name' => "Jane Dee",
                'slug' => "jane-dee",
                'email' => "janedee@test.com",
                'password' => bcrypt('123'),
            ],
            [
                'name' => "Edo Masaru",
                'slug' => "edo-masaru",
                'email' => "edoma@test.com",
                'password' => bcrypt('123'),
            ]
        ]);
    }
}
