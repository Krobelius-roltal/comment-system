<?php

namespace App\Models;

use Database\Factories\CommentFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property string $commentable_type
 * @property int $commentable_id
 * @property int|null $parent_id
 * @property string $content
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 * @property-read News|VideoPost $commentable
 * @property-read Comment|null $parent
 * @property-read Collection<int, Comment> $replies
 *
 * @method static Builder|Comment newQuery()
 * @method static Builder|Comment query()
 * @method static Builder|Comment whereId($value)
 * @method static Builder|Comment whereUserId($value)
 * @method static Builder|Comment whereCommentableType($value)
 * @method static Builder|Comment whereCommentableId($value)
 * @method static Builder|Comment whereParentId($value)
 * @method static Builder|Comment whereContent($value)
 * @method static Builder|Comment whereCreatedAt($value)
 * @method static Builder|Comment whereUpdatedAt($value)
 * @method static Builder|Comment rootComments()
 * @method static Builder|Comment forCommentable(string $type, int $id)
 * @method static Comment create(array $attributes = [])
 * @method static Comment|null find($id)
 * @method static Comment findOrFail($id)
 * @method static Builder|Comment with(array|string $relations)
 * @method bool save(array $options = [])
 * @method bool delete()
 * @method Model load(array|string $relations)
 */
class Comment extends Model
{
    /** @use HasFactory<CommentFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'commentable_type',
        'commentable_id',
        'parent_id',
        'content',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    public function scopeRootComments(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    public function scopeForCommentable(Builder $query, string $type, int $id): Builder
    {
        return $query->where('commentable_type', $type)
            ->where('commentable_id', $id);
    }
}
