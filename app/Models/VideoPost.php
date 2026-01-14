<?php

namespace App\Models;

use Database\Factories\VideoPostFactory;
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
 * @method static Builder|VideoPost newQuery()
 * @method static Builder|VideoPost query()
 * @method static Builder|VideoPost whereId($value)
 * @method static Builder|VideoPost whereTitle($value)
 * @method static Builder|VideoPost whereDescription($value)
 * @method static Builder|VideoPost whereCreatedAt($value)
 * @method static Builder|VideoPost whereUpdatedAt($value)
 * @method static VideoPost create(array $attributes = [])
 * @method static VideoPost|null find($id)
 * @method static VideoPost findOrFail($id)
 * @method static Builder|VideoPost with(array|string $relations)
 */
class VideoPost extends Model
{
    /** @use HasFactory<VideoPostFactory> */
    use HasFactory;

    protected $fillable = ['title', 'description'];

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
