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

    public function test_pagination_first_page(): void
    {
        $news = News::factory()->create([
            'title' => 'Test News for Pagination',
            'description' => 'Test Description',
        ]);

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        Comment::factory()->count(15)->create([
            'user_id' => $user->id,
            'commentable_type' => News::class,
            'commentable_id' => $news->id,
        ]);

        $limit = 10;
        $offset = 0;

        $query = $news->comments();
        $total = $query->count();

        $comments = $query->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->offset($offset)
            ->get();

        $hasMore = ($offset + $limit) < $total;

        $this->assertCount(10, $comments);
        $this->assertEquals(15, $total);
        $this->assertTrue($hasMore);
    }

    public function test_pagination_with_offset(): void
    {
        $news = News::factory()->create([
            'title' => 'Test News for Pagination',
            'description' => 'Test Description',
        ]);

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        Comment::factory()->count(15)->create([
            'user_id' => $user->id,
            'commentable_type' => News::class,
            'commentable_id' => $news->id,
        ]);

        $limit = 10;
        $offset = 10;

        $query = $news->comments();
        $total = $query->count();

        $comments = $query->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->offset($offset)
            ->get();

        $hasMore = ($offset + $limit) < $total;

        $this->assertCount(5, $comments);
        $this->assertEquals(15, $total);
        $this->assertFalse($hasMore);
    }

    public function test_pagination_has_more(): void
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
        $offset = 0;

        $query = $news->comments();
        $total = $query->count();

        $hasMore = ($offset + $limit) < $total;

        $this->assertEquals(25, $total);
        $this->assertTrue($hasMore);
    }

    public function test_pagination_last_page(): void
    {
        $news = News::factory()->create([
            'title' => 'Test News for Pagination',
            'description' => 'Test Description',
        ]);

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        Comment::factory()->count(15)->create([
            'user_id' => $user->id,
            'commentable_type' => News::class,
            'commentable_id' => $news->id,
        ]);

        $limit = 10;
        $offset = 10;

        $query = $news->comments();
        $total = $query->count();

        $hasMore = ($offset + $limit) < $total;

        $this->assertEquals(15, $total);
        $this->assertFalse($hasMore);
    }

    public function test_pagination_limit(): void
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
        $offset = 0;

        $query = $news->comments();
        $comments = $query->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->offset($offset)
            ->get();

        $this->assertCount(3, $comments);
        $this->assertLessThanOrEqual($limit, $comments->count());
    }

    public function test_pagination_max_limit(): void
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

        $limit = min(150, 100); // Максимальный лимит 100
        $offset = 0;

        $query = $news->comments();
        $comments = $query->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->offset($offset)
            ->get();

        $this->assertCount(100, $comments);
        $this->assertLessThanOrEqual(100, $comments->count());
    }

    public function test_pagination_total_count(): void
    {
        $news = News::factory()->create([
            'title' => 'Test News for Pagination',
            'description' => 'Test Description',
        ]);

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $expectedCount = 30;
        Comment::factory()->count($expectedCount)->create([
            'user_id' => $user->id,
            'commentable_type' => News::class,
            'commentable_id' => $news->id,
        ]);

        $query = $news->comments();
        $total = $query->count();

        $this->assertEquals($expectedCount, $total);
    }
}
