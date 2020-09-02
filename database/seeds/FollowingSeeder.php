<?php

use App\Following;
use Illuminate\Database\Seeder;

class FollowingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Following::class, 10)->create();
    }
}
