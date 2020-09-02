<?php

use App\Watchlist;
use Illuminate\Database\Seeder;

class WatchlistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Watchlist::class, 10)->create();
    }
}
