<?php

use App\Role;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->truncate();

        // Create Admin role
        $admin = new Role();
        $admin->name = 'admin';
        $admin->display_name = 'Admin';
        $admin->save();

        // Create Editor role
        $edit = new Role();
        $edit->name = "editor";
        $edit->display_name = "Editor";
        $edit->save();

        // Create Author role
        $author = new Role();
        $author->name = "author";
        $author->display_name = "Author";
        $author->save();

        // Attach the roles
        // first user as admin
        $user1 = User::find(1);
        $user1->detachRole($admin);
        $user1->attachRole($admin);

        // second user as editor
        $user2 = User::find(2);
        $user2->detachRole($edit);
        $user2->attachRole($edit);

        // Third user as author
        $user3 = User::find(3);
        $user3->detachRole($author);
        $user3->attachRole($author);

    }
}