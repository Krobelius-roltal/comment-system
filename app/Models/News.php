<?php

namespace App\Models;

use Database\Factories\NewsFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $title
 * @property string $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Comment> $comments
 *
 * @method static Builder|News newQuery()
 * @method static Builder|News query()
 * @method static Builder|News whereId($value)
 * @method static Builder|News whereTitle($value)
 * @method static Builder|News whereDescription($value)
 * @method static Builder|News whereCreatedAt($value)
 * @method static Builder|News whereUpdatedAt($value)
 * @method static News create(array $attributes = [])
 * @method static News|null find($id)
 * @method static News findOrFail($id)
 * @method static Builder|News with(array|string $relations)
 */
class News extends Model
{
    /** @use HasFactory<NewsFactory> */
    use HasFactory;

    protected $fillable = ['title', 'description'];

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
