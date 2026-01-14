<?php

namespace Tests\Feature;

use App\Models\News;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @dataProvider newsCreationProvider
     */
    public function test_can_create_news(string $title, string $description): void
    {
        $newsData = [
            'title' => $title,
            'description' => $description,
        ];

        $response = $this->postJson('/api/v1/news', $newsData);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'id',
                     'title',
                     'description',
                     'created_at',
                     'updated_at'
                 ]);

        $this->assertDatabaseHas('news', $newsData);
    }

    public static function newsCreationProvider(): array
    {
        return [
            'valid news 1' => [
                'title' => 'Test News 1',
                'description' => 'Test Description 1',
            ],
            'valid news 2' => [
                'title' => 'Test News 2',
                'description' => 'Test Description 2',
            ],
        ];
    }

    public function test_can_get_news_with_comments(): void
    {
        $news = News::factory()->create([
            'title' => 'Test News for Comments',
            'description' => 'Test Description for Comments',
        ]);

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $comments = \App\Models\Comment::factory()->count(5)->create([
            'user_id' => $user->id,
            'commentable_type' => News::class,
            'commentable_id' => $news->id,
        ]);

        $response = $this->getJson("/api/v1/news/{$news->id}?limit=10&offset=0");

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
                         'has_more'
                     ]
                 ]);

        $data = $response->json();
        $this->assertCount(5, $data['comments']['data']);
        $this->assertEquals(5, $data['comments']['total']);
    }

    public function test_news_validation(): void
    {
        // Тест без title
        $response = $this->postJson('/api/v1/news', [
            'description' => 'Test Description',
        ]);
        $response->assertStatus(422);

        // Тест без description
        $response = $this->postJson('/api/v1/news', [
            'title' => 'Test Title',
        ]);
        $response->assertStatus(422);

        // Тест с пустым title
        $response = $this->postJson('/api/v1/news', [
            'title' => '',
            'description' => 'Test Description',
        ]);
        $response->assertStatus(422);
    }

    public function test_get_nonexistent_news(): void
    {
        $response = $this->getJson('/api/v1/news/99999');

        $response->assertStatus(404);
    }
}
