<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AccountTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('account_types')->insert([
            'name' => 'Compte Cheque',
            'status' => true,
        ]);

        DB::table('account_types')->insert([
            'name' => 'Compte Epargne',
            'status' => true,
        ]);
    }
}
