<?php

namespace Tests\Unit;

use App\Models\Comment;
use App\Models\News;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentPaginationTest extends TestCase
{
    use RefreshDatabase;

    public function test_cursor_pagination_first_page(): void
    {
        $news = News::factory()->create([
            'title' => 'Test News for Pagination',
            'description' => 'Test Description',
        ]);

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $comments = Comment::factory()->count(15)->create([
            'user_id' => $user->id,
            'commentable_type' => News::class,
            'commentable_id' => $news->id,
        ]);

        $limit = 10;

        $query = $news->comments();
        $comments_result = $query->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->limit($limit + 1)
            ->get();

        $has_more = $comments_result->count() > $limit;
        if ($has_more) {
            $comments_result = $comments_result->take($limit);
        }

        $next_cursor = $has_more ? $comments_result->last()->id : null;

        $this->assertCount(10, $comments_result);
        $this->assertTrue($has_more);
        $this->assertNotNull($next_cursor);
    }

    public function test_cursor_pagination_with_cursor(): void
    {
        $news = News::factory()->create([
            'title' => 'Test News for Pagination',
            'description' => 'Test Description',
        ]);

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $comments = Comment::factory()->count(15)->create([
            'user_id' => $user->id,
            'commentable_type' => News::class,
            'commentable_id' => $news->id,
        ]);

        $firstPage = $news->comments()
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();

        $cursor = $firstPage->last()->id;
        $cursor_comment = $news->comments()->find($cursor);

        $limit = 10;

        $query = $news->comments();
        if ($cursor_comment) {
            $query->where(function ($q) use ($cursor_comment, $cursor) {
                $q->where('created_at', '<', $cursor_comment->created_at)
                    ->orWhere(function ($q2) use ($cursor_comment, $cursor) {
                        $q2->where('created_at', $cursor_comment->created_at)
                            ->where('id', '<', $cursor);
                    });
            });
        }

        $comments_result = $query->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->limit($limit + 1)
            ->get();

        $has_more = $comments_result->count() > $limit;
        if ($has_more) {
            $comments_result = $comments_result->take($limit);
        }

        $this->assertCount(5, $comments_result);
        $this->assertFalse($has_more);
    }

    public function test_cursor_pagination_has_more(): void
    {
        $news = News::factory()->create([
            'title' => 'Test News for Pagination',
            'description' => 'Test Description',
        ]);

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        Comment::factory()->count(25)->create([
            'user_id' => $user->id,
            'commentable_type' => News::class,
            'commentable_id' => $news->id,
        ]);

        $limit = 10;

        $query = $news->comments();
        $comments_result = $query->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->limit($limit + 1)
            ->get();

        $has_more = $comments_result->count() > $limit;

        $this->assertTrue($has_more);
    }

    public function test_cursor_pagination_last_page(): void
    {
        $news = News::factory()->create([
            'title' => 'Test News for Pagination',
            'description' => 'Test Description',
        ]);

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $comments = Comment::factory()->count(15)->create([
            'user_id' => $user->id,
            'commentable_type' => News::class,
            'commentable_id' => $news->id,
        ]);

        $firstPage = $news->comments()
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();

        $cursor = $firstPage->last()->id;
        $cursor_comment = $news->comments()->find($cursor);

        $limit = 10;

        $query = $news->comments();
        if ($cursor_comment) {
            $query->where(function ($q) use ($cursor_comment, $cursor) {
                $q->where('created_at', '<', $cursor_comment->created_at)
                    ->orWhere(function ($q2) use ($cursor_comment, $cursor) {
                        $q2->where('created_at', $cursor_comment->created_at)
                            ->where('id', '<', $cursor);
                    });
            });
        }

        $comments_result = $query->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->limit($limit + 1)
            ->get();

        $has_more = $comments_result->count() > $limit;

        $this->assertFalse($has_more);
    }

    public function test_cursor_pagination_limit(): void
    {
        $news = News::factory()->create([
            'title' => 'Test News for Pagination',
            'description' => 'Test Description',
        ]);

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        Comment::factory()->count(5)->create([
            'user_id' => $user->id,
            'commentable_type' => News::class,
            'commentable_id' => $news->id,
        ]);

        $limit = 3;

        $query = $news->comments();
        $comments = $query->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->limit($limit + 1)
            ->get();

        $has_more = $comments->count() > $limit;
        if ($has_more) {
            $comments = $comments->take($limit);
        }

        $this->assertCount(3, $comments);
        $this->assertLessThanOrEqual($limit, $comments->count());
    }

    public function test_cursor_pagination_max_limit(): void
    {
        $news = News::factory()->create([
            'title' => 'Test News for Pagination',
            'description' => 'Test Description',
        ]);

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        Comment::factory()->count(150)->create([
            'user_id' => $user->id,
            'commentable_type' => News::class,
            'commentable_id' => $news->id,
        ]);

        $limit = min(150, 100);

        $query = $news->comments();
        $comments = $query->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->limit($limit + 1)
            ->get();

        $has_more = $comments->count() > $limit;
        if ($has_more) {
            $comments = $comments->take($limit);
        }

        $this->assertCount(100, $comments);
        $this->assertLessThanOrEqual(100, $comments->count());
    }
}
