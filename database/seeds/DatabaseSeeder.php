<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        config(['app.seeding' => true]);
         $this->call(AdminUserSeeder::class);
         $this->call(RolesTableSeeder::class);

        if (!App::environment('codecanyon')) {
            $this->call(JobCategorySeeder::class);
            $this->call(SkillSeeder::class);
            $this->call(LocationSeeder::class);
            $this->call(JobSeeder::class);
            $this->call(TeamSeeder::class);
            $this->call(RoleSeeder::class);
            $this->call(JobApplicationSeeder::class);
            $this->call(ZoomDatabaseSeeder::class);
        }
        config(['app.seeding' => false]);
    }
}
