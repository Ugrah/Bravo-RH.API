<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    protected $users = [
        [
            'civility' => 'M', 
            'name' => 'Rimbaut', 
            'firstname' => 'Mathieu',
            'email' => 'mathieu@test.fr',
            'phone' => '0600000001',
            'phone_prefix' => '33',
            'phone_operator' => 'Free Mobile',
            'login' => 'FIRSTUSER',
            'password' => 'passwordtest'
        ],

    ];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->delete();

        foreach($this->users as $user) {
            DB::table('users')->insert([
                'civility' => $user['civility'],
                'name' => $user['name'],
                'firstname' => $user['firstname'],
                'email' => $user['email'],
                'phone' => $user['phone'],
                'phone_prefix' => $user['phone_prefix'],
                'phone_operator' => $user['phone_operator'],
                'login' => $user['login'],
                'password' => bcrypt($user['password']),
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ]);
        }
    }
}
