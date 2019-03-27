<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        foreach (['test1', 'test2', 'test3'] as $name) {

            \App\User::create([
                'name' => $name,
                'email' => $name . '@test.te',
                'password' => \Illuminate\Support\Facades\Hash::make('test1'),
            ]);
        }
    }
}
