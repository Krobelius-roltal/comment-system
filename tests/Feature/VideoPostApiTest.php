<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\User;
use App\Models\VideoPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VideoPostApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @dataProvider videoPostCreationProvider
     */
    public function test_can_create_video_post(string $title, string $description): void
    {
        $videoPostData = [
            'title' => $title,
            'description' => $description,
        ];

        $response = $this->postJson('/api/v1/video-posts', $videoPostData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'title',
                'description',
                'created_at',
                'updated_at',
            ]);

        $this->assertDatabaseHas('video_posts', $videoPostData);
    }

    public static function videoPostCreationProvider(): array
    {
        return [
            'valid video post 1' => [
                'title' => 'Test Video Post 1',
                'description' => 'Test Description 1',
            ],
            'valid video post 2' => [
                'title' => 'Test Video Post 2',
                'description' => 'Test Description 2',
            ],
        ];
    }

    public function test_can_get_video_post_with_comments(): void
    {
        $videoPost = VideoPost::factory()->create([
            'title' => 'Test Video Post for Comments',
            'description' => 'Test Description for Comments',
        ]);

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $comments = Comment::factory()->count(5)->create([
            'user_id' => $user->id,
            'commentable_type' => VideoPost::class,
            'commentable_id' => $videoPost->id,
        ]);

        $response = $this->getJson("/api/v1/video-posts/{$videoPost->id}?limit=10&offset=0");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'title',
                'description',
                'created_at',
                'updated_at',
                'comments' => [
                    'data',
                    'total',
                    'limit',
                    'offset',
                    'has_more',
                ],
            ]);

        $data = $response->json();
        $this->assertCount(5, $data['comments']['data']);
        $this->assertEquals(5, $data['comments']['total']);
    }

    public function test_video_post_validation(): void
    {
        $response = $this->postJson('/api/v1/video-posts', [
            'description' => 'Test Description',
        ]);
        $response->assertStatus(422);

        $response = $this->postJson('/api/v1/video-posts', [
            'title' => 'Test Title',
        ]);
        $response->assertStatus(422);

        $response = $this->postJson('/api/v1/video-posts', [
            'title' => '',
            'description' => 'Test Description',
        ]);
        $response->assertStatus(422);
    }

    public function test_get_nonexistent_video_post(): void
    {
        $response = $this->getJson('/api/v1/video-posts/99999');

        $response->assertStatus(404);
    }
}
