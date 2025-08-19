<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

//use Illuminate\Contracts\Auth\CanResetPassword;

class User extends Authenticatable implements MustVerifyEmail//, CanResetPassword
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the stories for the user.
     */
    public function stories()
    {
        return $this->hasMany(Story::class);
    }

    /**
     * One user can like many stories.
     * A story can be liked by many users.
     * @return BelongsToMany<Story, User, \Illuminate\Database\Eloquent\Relations\Pivot>
     */
    public function likedStories(): BelongsToMany
    {
        return $this->belongsToMany(Story::class, 'likes');
    }

    /**
     * The users that this user is subscribing to.
     */
    public function subscribedTo()
    {
        return $this->belongsToMany(User::class, 'subscriptions', 'subscriber_id', 'subscribed_to_id');
    }

    /**
     * The users that are subscribing to this user.
     */
    public function subscribers()
    {
        return $this->belongsToMany(User::class, 'subscriptions', 'subscribed_to_id', 'subscriber_id');
    }
}
