<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call('CommentLikeSeeder');
        $this->call('CommentSeeder');
        $this->call('DeveloperSeeder');
        $this->call('FavoriteSeeder');
        $this->call('FilmSeeder');
        $this->call('FollowingSeeder');
        $this->call('PointSeeder');
        $this->call('ReviewLikeSeeder');
        $this->call('ReviewSeeder');
        $this->call('UserSeeder');
        $this->call('WatchlistSeeder');
    }
}
