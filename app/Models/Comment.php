<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Builder;

class Comment extends Model
{
    /** @use HasFactory<\Database\Factories\CommentFactory> */
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'commentable_type',
        'commentable_id',
        'parent_id',
        'content'
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
    
    // Scope для получения корневых комментариев (без parent_id)
    public function scopeRootComments(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }
    
    // Scope для получения комментариев к конкретному объекту
    public function scopeForCommentable(Builder $query, string $type, int $id): Builder
    {
        return $query->where('commentable_type', $type)
                     ->where('commentable_id', $id);
    }
}
