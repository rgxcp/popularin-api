<?php

use App\Point;
use Illuminate\Database\Seeder;

class PointSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Point::class, 10)->create();
    }
}
