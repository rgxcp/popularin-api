<?php

use App\CommentLike;
use Illuminate\Database\Seeder;

class CommentLikeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(CommentLike::class, 10)->create();
    }
}
