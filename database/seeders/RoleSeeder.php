<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
            'id' => 1,
            'name' => "Super Admin",
            'guard_name' => 'ROLE_SUPER_ADMIN',
        ]);
        DB::table('roles')->insert([
            'id' => 2,
            'name' => "Admin",
            'guard_name' => 'ROLE_ADMIN',
        ]);
        DB::table('roles')->insert([
            'id' => 3,
            'name' => "Accounts Manager",
            'guard_name' => 'ROLE_ACCOUNTS_MANAGER',
        ]);
        DB::table('roles')->insert([
            'id' => 4,
            'name' => "Client",
            'guard_name' => 'ROLE_CLIENT',
        ]);
    }
}
