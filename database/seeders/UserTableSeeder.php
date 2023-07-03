<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            "first_name"     => ucwords("Kunozga"),
            "middle_name"    => ucwords("Calvin"),
            "last_name"      => ucwords("Mlowoka"),
            "email"         => "kunozgamlowoka@gmail.com",
            "phone_number"  => "997748584",
            "password"      => bcrypt("12345678"),
            "national_id"   => "HKAMJAZU",
            "role_id"       => 1,
        ]);

        User::create([
            "first_name"     => ucwords("Maya"),
            "middle_name"    => ucwords("Chiso"),
            "last_name"      => ucwords("Msonkho"),
            "email"         => "maya@gmail.com",
            "phone_number"  => "886027878",
            "password"      => bcrypt("12345678"),
            "national_id"   => "JGAMSYZH",
            "role_id"       => 2,
        ]);
    }
}
