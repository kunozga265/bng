<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Plot;
use App\Models\Site;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Site::factory(5)->create();
        Plot::factory(100)->create();
        $this->call(UserTableSeeder::class);
        $this->call(RoleTableSeeder::class);
    }
}
