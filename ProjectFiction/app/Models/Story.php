<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;

class Story extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'synopsis',
        'genre',
        'content',
        'user_id', // Make sure user_id is mass assignable
    ];


    /**
     * Scope a query to select orderBy and paginate
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return void
     */
    protected function scopeselectOrderPaginate(Builder $query): void
    {
        $query->with('user')
            ->select('id', 'title', 'genre', 'synopsis', 'user_id')
            ->orderBy('id', 'desc');
    }

        /**
     * Scope a query to include stories with their authors and basic fields.
     */
    public function scopeWithAuthor(Builder $query): void
    {
        $query->with('user')
            ->select('id', 'title', 'genre', 'synopsis', 'user_id')
            ->orderBy('id', 'desc');
    }

    /**
     * Get the user that wrote the story.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
