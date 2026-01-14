<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\News;
use App\Models\User;
use App\Models\VideoPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_comment_to_news(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User for News Comment',
            'email' => 'news-comment@example.com',
        ]);

        $news = News::factory()->create([
            'title' => 'Test News for Comment',
            'description' => 'Test Description for Comment',
        ]);

        $response = $this->postJson('/api/v1/comments', [
            'user_id' => $user->id,
            'commentable_type' => 'news',
            'commentable_id' => $news->id,
            'content' => 'Test comment to news',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'user_id',
                'commentable_type',
                'commentable_id',
                'parent_id',
                'content',
                'created_at',
                'updated_at',
                'user',
            ])
            ->assertJson([
                'user_id' => $user->id,
                'commentable_id' => $news->id,
                'content' => 'Test comment to news',
                'parent_id' => null,
            ]);

        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'commentable_id' => $news->id,
            'content' => 'Test comment to news',
        ]);
    }

    public function test_can_create_comment_to_video_post(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User for Video Comment',
            'email' => 'video-comment@example.com',
        ]);

        $videoPost = VideoPost::factory()->create([
            'title' => 'Test Video Post for Comment',
            'description' => 'Test Description for Comment',
        ]);

        $response = $this->postJson('/api/v1/comments', [
            'user_id' => $user->id,
            'commentable_type' => 'video-post',
            'commentable_id' => $videoPost->id,
            'content' => 'Test comment to video post',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'user_id' => $user->id,
                'commentable_id' => $videoPost->id,
                'content' => 'Test comment to video post',
                'parent_id' => null,
            ]);

        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'commentable_id' => $videoPost->id,
            'content' => 'Test comment to video post',
        ]);
    }

    public function test_can_create_reply_to_comment(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User for Reply',
            'email' => 'reply-test@example.com',
        ]);

        $news = News::factory()->create([
            'title' => 'Test News for Reply',
            'description' => 'Test Description for Reply',
        ]);

        $parentComment = Comment::factory()->create([
            'user_id' => $user->id,
            'commentable_type' => News::class,
            'commentable_id' => $news->id,
            'content' => 'Parent comment for reply test',
        ]);

        $response = $this->postJson('/api/v1/comments', [
            'user_id' => $user->id,
            'commentable_type' => 'news',
            'commentable_id' => $news->id,
            'parent_id' => $parentComment->id,
            'content' => 'Reply to comment',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'parent_id' => $parentComment->id,
                'content' => 'Reply to comment',
            ]);

        $this->assertDatabaseHas('comments', [
            'parent_id' => $parentComment->id,
            'content' => 'Reply to comment',
        ]);
    }

    public function test_can_get_comments_list(): void
    {
        $news = News::factory()->create([
            'title' => 'Test News for Comments List',
            'description' => 'Test Description for Comments List',
        ]);

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        Comment::factory()->count(3)->create([
            'user_id' => $user->id,
            'commentable_type' => News::class,
            'commentable_id' => $news->id,
        ]);

        $response = $this->getJson("/api/v1/comments?commentable_type=news&commentable_id={$news->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'limit',
                'next_cursor',
                'has_more',
            ]);

        $data = $response->json();
        $this->assertCount(3, $data['data']);
        $this->assertFalse($data['has_more']);
    }

    public function test_can_get_comment(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $news = News::factory()->create([
            'title' => 'Test News',
            'description' => 'Test Description',
        ]);

        $comment = Comment::factory()->create([
            'user_id' => $user->id,
            'commentable_type' => News::class,
            'commentable_id' => $news->id,
            'content' => 'Test comment content',
        ]);

        $response = $this->getJson("/api/v1/comments/{$comment->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'user_id',
                'content',
                'user',
                'commentable',
            ])
            ->assertJson([
                'id' => $comment->id,
                'content' => 'Test comment content',
            ]);
    }

    public function test_can_update_comment(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $news = News::factory()->create([
            'title' => 'Test News',
            'description' => 'Test Description',
        ]);

        $comment = Comment::factory()->create([
            'user_id' => $user->id,
            'commentable_type' => News::class,
            'commentable_id' => $news->id,
            'content' => 'Original content',
        ]);

        $response = $this->putJson("/api/v1/comments/{$comment->id}", [
            'content' => 'Updated content',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'content' => 'Updated content',
            ]);

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'content' => 'Updated content',
        ]);
    }

    public function test_can_delete_comment(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $news = News::factory()->create([
            'title' => 'Test News',
            'description' => 'Test Description',
        ]);

        $comment = Comment::factory()->create([
            'user_id' => $user->id,
            'commentable_type' => News::class,
            'commentable_id' => $news->id,
            'content' => 'Comment to delete',
        ]);

        $response = $this->deleteJson("/api/v1/comments/{$comment->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('comments', [
            'id' => $comment->id,
        ]);
    }

    public function test_comment_validation(): void
    {
        $response = $this->postJson('/api/v1/comments', [
            'commentable_type' => 'news',
            'commentable_id' => 1,
            'content' => 'Test content',
        ]);
        $response->assertStatus(422);

        $response = $this->postJson('/api/v1/comments', [
            'user_id' => 1,
            'commentable_id' => 1,
            'content' => 'Test content',
        ]);
        $response->assertStatus(422);

        $response = $this->postJson('/api/v1/comments', [
            'user_id' => 1,
            'commentable_type' => 'news',
            'commentable_id' => 1,
        ]);
        $response->assertStatus(422);

        $response = $this->postJson('/api/v1/comments', [
            'user_id' => 1,
            'commentable_type' => 'news',
            'commentable_id' => 1,
            'content' => '',
        ]);
        $response->assertStatus(422);
    }

    public function test_cascade_delete(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $news = News::factory()->create([
            'title' => 'Test News',
            'description' => 'Test Description',
        ]);

        $parentComment = Comment::factory()->create([
            'user_id' => $user->id,
            'commentable_type' => News::class,
            'commentable_id' => $news->id,
            'content' => 'Parent comment',
        ]);

        $reply1 = Comment::factory()->create([
            'user_id' => $user->id,
            'commentable_type' => News::class,
            'commentable_id' => $news->id,
            'parent_id' => $parentComment->id,
            'content' => 'Reply 1',
        ]);

        $reply2 = Comment::factory()->create([
            'user_id' => $user->id,
            'commentable_type' => News::class,
            'commentable_id' => $news->id,
            'parent_id' => $parentComment->id,
            'content' => 'Reply 2',
        ]);

        $response = $this->deleteJson("/api/v1/comments/{$parentComment->id}");
        $response->assertStatus(204);

        $this->assertDatabaseMissing('comments', [
            'id' => $parentComment->id,
        ]);

        $this->assertDatabaseMissing('comments', [
            'id' => $reply1->id,
        ]);

        $this->assertDatabaseMissing('comments', [
            'id' => $reply2->id,
        ]);
    }

    public function test_comment_cursor_pagination(): void
    {
        $news = News::factory()->create([
            'title' => 'Test News for Pagination',
            'description' => 'Test Description for Pagination',
        ]);

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $comments = Comment::factory()->count(25)->create([
            'user_id' => $user->id,
            'commentable_type' => News::class,
            'commentable_id' => $news->id,
        ]);

        // First page
        $response = $this->getJson("/api/v1/news/{$news->id}?limit=10");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'comments' => [
                    'data',
                    'limit',
                    'next_cursor',
                    'has_more',
                ],
            ]);

        $data = $response->json();
        $this->assertCount(10, $data['comments']['data']);
        $this->assertEquals(10, $data['comments']['limit']);
        $this->assertNotNull($data['comments']['next_cursor']);
        $this->assertTrue($data['comments']['has_more']);

        $firstPageLastId = $data['comments']['data'][9]['id'];

        // Second page using cursor
        $response = $this->getJson("/api/v1/news/{$news->id}?limit=10&cursor={$firstPageLastId}");

        $data = $response->json();
        $this->assertCount(10, $data['comments']['data']);
        $this->assertNotNull($data['comments']['next_cursor']);
        $this->assertTrue($data['comments']['has_more']);

        $secondPageLastId = $data['comments']['data'][9]['id'];

        // Third page (last page)
        $response = $this->getJson("/api/v1/news/{$news->id}?limit=10&cursor={$secondPageLastId}");

        $data = $response->json();
        $this->assertCount(5, $data['comments']['data']);
        $this->assertNull($data['comments']['next_cursor']);
        $this->assertFalse($data['comments']['has_more']);
    }

    public function test_nested_comments_pagination(): void
    {
        $news = News::factory()->create([
            'title' => 'Test News for Nested Pagination',
            'description' => 'Test Description for Nested Pagination',
        ]);

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $parentComment = Comment::factory()->create([
            'user_id' => $user->id,
            'commentable_type' => News::class,
            'commentable_id' => $news->id,
            'content' => 'Parent comment',
        ]);

        Comment::factory()->count(15)->create([
            'user_id' => $user->id,
            'commentable_type' => News::class,
            'commentable_id' => $news->id,
            'parent_id' => $parentComment->id,
        ]);

        $response = $this->getJson("/api/v1/comments?commentable_type=news&commentable_id={$news->id}&parent_id={$parentComment->id}&limit=10");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'limit',
                'next_cursor',
                'has_more',
            ]);

        $data = $response->json();
        $this->assertCount(10, $data['data']);
        $this->assertNotNull($data['next_cursor']);
        $this->assertTrue($data['has_more']);

        $firstPageLastId = $data['data'][9]['id'];

        $response = $this->getJson("/api/v1/comments?commentable_type=news&commentable_id={$news->id}&parent_id={$parentComment->id}&limit=10&cursor={$firstPageLastId}");

        $data = $response->json();
        $this->assertCount(5, $data['data']);
        $this->assertNull($data['next_cursor']);
        $this->assertFalse($data['has_more']);
    }
}
