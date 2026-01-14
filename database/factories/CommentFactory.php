<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\News;
use App\Models\User;
use App\Models\VideoPost;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Comment>
 */
class CommentFactory extends Factory
{
    protected $model = Comment::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'commentable_type' => News::class,
            'commentable_id' => News::factory(),
            'parent_id' => null,
            'content' => fake()->paragraph(),
        ];
    }

    public function forVideoPost(?VideoPost $videoPost = null): static
    {
        return $this->state(fn(array $attributes) => [
            'commentable_type' => VideoPost::class,
            'commentable_id' => $videoPost?->id ?? VideoPost::factory(),
        ]);
    }

    public function forNews(?News $news = null): static
    {
        return $this->state(fn(array $attributes) => [
            'commentable_type' => News::class,
            'commentable_id' => $news?->id ?? News::factory(),
        ]);
    }

    public function replyTo(?Comment $parent = null): static
    {
        return $this->state(fn(array $attributes) => [
            'parent_id' => $parent?->id ?? Comment::factory(),
        ]);
    }
}
