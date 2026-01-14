<?php

namespace Database\Factories;

use App\Models\News;
use App\Models\User;
use App\Models\VideoPost;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $commentableTypes = [News::class, VideoPost::class];
        $commentableType = fake()->randomElement($commentableTypes);

        return [
            'user_id' => User::factory(),
            'commentable_type' => $commentableType,
            'commentable_id' => $commentableType::factory(),
            'parent_id' => null,
            'body' => fake()->paragraph(),
        ];
    }

    public function forCommentable(string $type, int $id): static
    {
        return $this->state(fn(array $attributes) => [
            'commentable_type' => $type,
            'commentable_id' => $id,
        ]);
    }

    public function asReplyTo(int $parentId): static
    {
        return $this->state(fn(array $attributes) => [
            'parent_id' => $parentId,
        ]);
    }
}
