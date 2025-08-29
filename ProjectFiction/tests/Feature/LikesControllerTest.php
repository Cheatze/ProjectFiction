<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Story;
use App\Models\Like;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Event;
use Illuminate\Testing\TestResponse;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Requests\LikeRequest;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Http\Controllers\LikesController;
use App\Http\Requests\DeleteRequest;
use App\Enums\Genre;
use App\Events\StoryViewed;
use App\Events\StoryPosted;
use PHPUnit\Framework\Attributes\Test;

class LikesControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * The verified user used for testing.
     *
     * @var User
     */
    protected User $verifiedUser;

    /**
     * The unverified user used for testing.
     *
     * @var User
     */
    protected User $unverifiedUser;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->verifiedUser = User::factory()->create();
        $this->unverifiedUser = User::factory()->unverified()->create();
    }

    /**
     * Test that a verified user can like a story.
     */
    #[Test]
    public function verified_user_can_like_a_story(): void
    {
        $this->actingAs($this->verifiedUser);
        $story = Story::factory()->create(['likes' => 0, 'score' => 0]);

        $response = $this->post(route('stories.like', ['story' => $story->id]));

        $response->assertSessionHas('success', 'Story liked successfully!');
        $response->assertRedirect();
        $this->assertDatabaseHas('likes', [
            'user_id' => $this->verifiedUser->id,
            'story_id' => $story->id,
        ]);

        $story->refresh();
        $this->assertEquals(1, $story->likes);
        $this->assertEquals(10, $story->score);
    }

    /**
     * Test that a verified user can unlike a story they have liked.
     */
    #[Test]
    public function verified_user_can_unlike_a_story(): void
    {
        $this->actingAs($this->verifiedUser);
        $story = Story::factory()->create(['likes' => 1, 'score' => 10]);
        $story->likers()->attach($this->verifiedUser->id);

        $response = $this->post(route('stories.dislike', ['story' => $story->id]));

        $response->assertSessionHas('success', 'Story unliked successfully!');
        $response->assertRedirect();

        $this->assertDatabaseMissing('likes', [
            'user_id' => $this->verifiedUser->id,
            'story_id' => $story->id,
        ]);

        $story->refresh();
        $this->assertEquals(0, $story->likes);
        $this->assertEquals(0, $story->score);
    }

    /**
     * Test that a user cannot like a story twice.
     */
    #[Test]
    public function user_cannot_like_a_story_twice(): void
    {
        $this->actingAs($this->verifiedUser);
        $story = Story::factory()->create();

        // First like
        $this->post(route('stories.like', ['story' => $story->id]));

        // Second attempt to like
        $response = $this->post(route('stories.like', ['story' => $story->id]));

        $response->assertSessionHas('error', 'You have already liked this story.');
        $response->assertRedirect();

        $this->assertCount(1, $story->likers()->get());
        $story->refresh();
        $this->assertEquals(1, $story->likes);
    }

    /**
     * Test that a user cannot unlike a story they haven't liked.
     */
    #[Test]
    public function user_cannot_unlike_a_story_they_haven_t_liked(): void
    {
        $this->actingAs($this->verifiedUser);
        $story = Story::factory()->create();

        $response = $this->post(route('stories.dislike', ['story' => $story->id]));

        $response->assertSessionHas('error', 'You have not liked this story yet.');
        $response->assertRedirect();

        $this->assertDatabaseMissing('likes', [
            'user_id' => $this->verifiedUser->id,
            'story_id' => $story->id,
        ]);
    }

    /**
     * Test that guests cannot like a story.
     */
    #[Test]
    public function guest_cannot_like_a_story(): void
    {
        $story = Story::factory()->create();
        $response = $this->post(route('stories.like', ['story' => $story->id]));
        $response->assertRedirect('/login');

        $this->assertDatabaseMissing('likes', [
            'user_id' => null,
            'story_id' => $story->id,
        ]);
    }

    /**
     * Test that guests cannot unlike a story.
     */
    #[Test]
    public function guest_cannot_unlike_a_story(): void
    {
        $story = Story::factory()->create();
        $response = $this->post(route('stories.dislike', ['story' => $story->id]));
        $response->assertRedirect('/login');

        $this->assertDatabaseMissing('likes', [
            'user_id' => null,
            'story_id' => $story->id,
        ]);
    }

    /**
     * Test that an unverified user cannot like a story.
     */
    #[Test]
    public function unverified_user_cannot_like_a_story(): void
    {
        $this->actingAs($this->unverifiedUser);
        $story = Story::factory()->create();

        $response = $this->post(route('stories.like', ['story' => $story->id]));

        $response->assertRedirect(route('verification.notice'));
        $this->assertDatabaseMissing('likes', [
            'user_id' => $this->unverifiedUser->id,
            'story_id' => $story->id,
        ]);
    }

    /**
     * Test that an unverified user cannot unlike a story.
     */
    #[Test]
    public function unverified_user_cannot_unlike_a_story(): void
    {
        $this->actingAs($this->unverifiedUser);
        $story = Story::factory()->create();
        $story->likers()->attach($this->unverifiedUser->id);

        $response = $this->post(route('stories.dislike', ['story' => $story->id]));

        $response->assertRedirect(route('verification.notice'));
        $this->assertDatabaseHas('likes', [
            'user_id' => $this->unverifiedUser->id,
            'story_id' => $story->id,
        ]);
    }

    /**
     * Test that liking a non-existent story returns a 404.
     */
    #[Test]
    public function liking_a_non_existent_story_returns_404(): void
    {
        $this->actingAs($this->verifiedUser);
        $response = $this->post(route('stories.like', ['story' => 9999]));
        $response->assertNotFound();
    }

    /**
     * Test that unliking a non-existent story returns a 404.
     */
    #[Test]
    public function unliking_a_non_existent_story_returns_404(): void
    {
        $this->actingAs($this->verifiedUser);
        $response = $this->post(route('stories.dislike', ['story' => 9999]));
        $response->assertNotFound();
    }
}
