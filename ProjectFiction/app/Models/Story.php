<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

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
     * Get the user that wrote the story.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
