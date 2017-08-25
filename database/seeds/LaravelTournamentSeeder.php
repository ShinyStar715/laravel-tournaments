<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        Model::unguard();

        $this->command->info('Seeding...');

        setFKCheckOff();

        DB::table('competitor')->truncate();
        DB::table('tournament')->truncate();
        DB::table('category')->truncate();
        DB::table('users')->truncate();
        DB::table('venue')->truncate();

        $this->call(VenueSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(TournamentSeeder::class);
        $this->call(CompetitorSeeder::class);

        $this->command->info('All tables seeded!');

        setFKCheckOn();

        Model::reguard();
    }
}
