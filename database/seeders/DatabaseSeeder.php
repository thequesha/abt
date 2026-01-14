<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\News;
use App\Models\User;
use App\Models\VideoPost;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = User::factory(20)->create();

        $news = News::factory(50)->create();
        $videoPosts = VideoPost::factory(50)->create();

        $allCommentables = collect();
        $news->each(fn($item) => $allCommentables->push(['type' => News::class, 'id' => $item->id]));
        $videoPosts->each(fn($item) => $allCommentables->push(['type' => VideoPost::class, 'id' => $item->id]));

        $rootComments = collect();
        for ($i = 0; $i < 300; $i++) {
            $commentable = $allCommentables->random();
            $comment = Comment::factory()->create([
                'user_id' => $users->random()->id,
                'commentable_type' => $commentable['type'],
                'commentable_id' => $commentable['id'],
                'parent_id' => null,
            ]);
            $rootComments->push($comment);
        }

        for ($i = 0; $i < 200; $i++) {
            $parentComment = $rootComments->random();
            Comment::factory()->create([
                'user_id' => $users->random()->id,
                'commentable_type' => $parentComment->commentable_type,
                'commentable_id' => $parentComment->commentable_id,
                'parent_id' => $parentComment->id,
            ]);
        }
    }
}
