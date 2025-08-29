<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Story;
use App\Models\User;
use App\Enums\Genre;
use Illuminate\Support\Facades\Event;
use App\Events\StoryPosted;
use App\Events\StoryViewed;
use Illuminate\Support\Facades\Session;
use PHPUnit\Framework\Attributes\Test;

class StoriesControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        // Create a user that is verified by default as per the UserFactory
        $this->verifiedUser = User::factory()->create(); //
        // Create an unverified user for specific tests
        $this->unverifiedUser = User::factory()->unverified()->create();
    }

    /**
     * Test that guests cannot access the write story page
     * @return void
     */
    #[Test]
    public function guests_cannot_access_write_story_page()
    {
        $response = $this->get(route('show.write'));
        $response->assertRedirect('/login'); // Assuming it redirects to login
    }

    /**
     * Test that authenticated and verified users can access the write story page
     * @return void
     */
    #[Test]
    public function authenticated_and_verified_users_can_access_write_story_page()
    {
        $this->actingAs($this->verifiedUser); //

        $response = $this->get(route('show.write'));
        $response->assertOk();
        $response->assertViewIs('write');
    }

    #[Test]
    public function unverified_users_cannot_access_write_story_page()
    {
        $this->actingAs($this->unverifiedUser); //

        $response = $this->get(route('show.write'));
        $response->assertRedirect(route('verification.notice'));
    }

    #[Test]
    public function it_displays_a_paginated_list_of_new_stories()
    {
        Story::factory()->count(20)->create();

        $response = $this->get(route('show.newest'));

        $response->assertOk();
        $response->assertViewIs('browse');
        $response->assertViewHas('stories');
        $this->assertCount(15, $response->viewData('stories')); // Default pagination is 15
    }

    #[Test]
    public function it_displays_paginated_stories_by_genre()
    {
        Story::factory()->count(5)->create(['genre' => Genre::Fantasy]);
        Story::factory()->count(10)->create(['genre' => Genre::ScienceFiction]);

        $response = $this->get(route('stories.genre', ['genre' => Genre::Fantasy->value]));

        $response->assertOk();
        $response->assertViewIs('browse');
        $response->assertViewHas('stories');
        $this->assertCount(5, $response->viewData('stories'));
        $response->viewData('stories')->each(function ($story) {
            $this->assertEquals("Fantasy", $story->genre);
        });
    }

    #[Test]
    public function it_handles_invalid_genre_in_show_genre()
    {
        // Attempt to access a genre that doesn't exist in the enum
        $response = $this->get('/stories/invalidgenre');
        $response->assertNotFound(); // Or whatever error handling Laravel provides for invalid enum routes
    }

    #[Test]
    public function it_can_search_stories_by_title()
    {
        Story::factory()->create(['title' => 'The Great Adventure']);
        Story::factory()->create(['title' => 'A Small Tale']);

        $response = $this->get(route('show.search', ['search' => 'great']));

        $response->assertOk();
        $response->assertViewIs('browse');
        $response->assertViewHas('stories');
        $this->assertCount(1, $response->viewData('stories'));
        $this->assertEquals('The Great Adventure', $response->viewData('stories')->first()->title);
    }

    #[Test]
    public function it_can_search_stories_by_synopsis()
    {
        Story::factory()->create(['synopsis' => 'A story about a brave knight.']);
        Story::factory()->create(['synopsis' => 'A simple description.']);

        $response = $this->get(route('show.search', ['search' => 'brave knight']));

        $response->assertOk();
        $response->assertViewIs('browse');
        $response->assertViewHas('stories');
        $this->assertCount(1, $response->viewData('stories'));
        $this->assertEquals('A story about a brave knight.', $response->viewData('stories')->first()->synopsis);
    }

    #[Test]
    public function search_requires_a_search_term()
    {
        $response = $this->get(route('show.search', ['search' => '']));
        $response->assertSessionHasErrors('search'); // Assuming SearchRequest validates this
    }

    #[Test]
    public function it_displays_a_single_story()
    {
        $story = Story::factory()->create(['content' => '<p>This is <strong>bold</strong> content.</p>']);

        $response = $this->get(route('stories.read', ['id' => $story->id]));

        $response->assertOk();
        $response->assertViewIs('read');
        $response->assertViewHas('story');
        $this->assertEquals($story->id, $response->viewData('story')->id);
        // Test that content is purified
        $this->assertEquals('<p>This is <strong>bold</strong> content.</p>', $response->viewData('story')->content);
    }

    #[Test]
    public function it_returns_404_for_non_existent_story()
    {
        $response = $this->get(route('stories.read', ['id' => 99999]));
        $response->assertNotFound();
    }

    #[Test]
    public function authenticated_and_verified_user_can_submit_a_story()
    {
        Event::fake(); // Prevent actual event dispatch during testing
        $this->actingAs($this->verifiedUser); //

        $storyData = [
            'title' => 'My New Story',
            'synopsis' => $this->faker->sentence(30), // Ensure min:25
            'genre' => Genre::Fantasy->value,
            'story' => $this->faker->paragraphs(10, true), // Ensure min:500 characters
        ];

        $response = $this->post(route('write'), $storyData);

        $response->assertRedirect(route('index'));
        $response->assertSessionHas('success', 'Story submitted successfully!');
        $this->assertDatabaseHas('stories', [
            'title' => 'My New Story',
            'user_id' => $this->verifiedUser->id, //
            'genre' => Genre::Fantasy->value,
        ]);
        Event::assertDispatched(StoryPosted::class, function ($event) {
            return $event->story->user_id === $this->verifiedUser->id; //
        });
    }

    #[Test]
    public function unverified_user_cannot_submit_a_story()
    {
        $this->actingAs($this->unverifiedUser); //

        $storyData = [
            'title' => 'My New Story',
            'synopsis' => $this->faker->sentence(30),
            'genre' => Genre::Fantasy->value,
            'story' => $this->faker->paragraphs(10, true),
        ];

        $response = $this->post(route('write'), $storyData);
        $response->assertRedirect(route('verification.notice'));
        $this->assertDatabaseMissing('stories', ['title' => 'My New Story']);
    }

    #[Test]
    public function submit_story_requires_valid_data()
    {
        $this->actingAs($this->verifiedUser); //

        $response = $this->post(route('write'), [
            'title' => '', // Invalid
            'synopsis' => 'Too short', // Invalid
            'genre' => 'InvalidGenre', // Invalid
            'story' => 'Too short', // Invalid
        ]);

        $response->assertSessionHasErrors(['title', 'synopsis', 'genre', 'story']);
    }

    #[Test]
    public function submit_story_purifies_content()
    {
        $this->actingAs($this->verifiedUser); //

        $maliciousContent = '<script>alert("Hacked!");</script><p>Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.</p>';
        $expectedCleanContent = '<p>Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.Clean content.</p>';

        $storyData = [
            'title' => 'Story with Malicious Content',
            'synopsis' => $this->faker->sentence(30),
            'genre' => Genre::Fantasy->value,
            'story' => $maliciousContent,
        ];

        $this->post(route('write'), $storyData);

        $this->assertDatabaseHas('stories', [
            'title' => 'Story with Malicious Content',
            'content' => $expectedCleanContent,
        ]);
    }

    #[Test]
    public function authenticated_user_can_delete_their_own_story()
    {
        $this->actingAs($this->verifiedUser); //
        $story = Story::factory()->create(['user_id' => $this->verifiedUser->id]); //

        $response = $this->post(route('delete', ['story' => $story->id]));

        $response->assertRedirect(); // Typically redirects back
        $this->assertDatabaseMissing('stories', ['id' => $story->id]);
    }

    #[Test]
    public function authenticated_user_cannot_delete_another_users_story()
    {
        $user2 = User::factory()->create(); // This user will be verified by default
        $this->actingAs($this->verifiedUser); //
        $storyOfUser2 = Story::factory()->create(['user_id' => $user2->id]); //

        $response = $this->post(route('delete', ['story' => $storyOfUser2->id]));

        $response->assertForbidden(); // Assumes a 403 Forbidden due to policy
        $this->assertDatabaseHas('stories', ['id' => $storyOfUser2->id]);
    }

    #[Test]
    public function guest_cannot_delete_a_story()
    {
        $story = Story::factory()->create();

        $response = $this->post(route('delete', ['story' => $story->id]));

        $response->assertRedirect('/login'); // Redirects to login
        $this->assertDatabaseHas('stories', ['id' => $story->id]);
    }

    /**
     * Test that unverified users cannot delete a story
     * @return void
     */
    #[Test]
    public function unverified_user_cannot_delete_a_story()
    {
        $this->actingAs($this->unverifiedUser); //
        $story = Story::factory()->create(['user_id' => $this->unverifiedUser->id]); //

        $response = $this->post(route('delete', ['story' => $story->id]));
        $response->assertRedirect(route('verification.notice'));
        $this->assertDatabaseHas('stories', ['id' => $story->id]);
    }

    /**
     * Test that viewing a story dispatches the StoryViewed event
     *
     * @return void
     */
    #[Test]
    public function show_story_dispatches_story_viewed_event()
    {
        Event::fake(); // Don't run the actual listeners

        $story = Story::factory()->create();

        $this->get(route('stories.read', ['id' => $story->id]));

        Event::assertDispatched(StoryViewed::class, function ($event) use ($story) {
            return $event->story->id === $story->id;
        });
    }

    /**
     * Test that viewing a story increments views and score only once per session
     *
     * @return void
     */
    #[Test]
    public function viewing_a_story_increments_views_and_score_once_per_session()
    {
        $story = Story::factory()->create(['views' => 0, 'score' => 0]);

        // First view, should increment
        $response1 = $this->get(route('stories.read', ['id' => $story->id]));
        $response1->assertOk();

        // Refresh the story model from the database
        $story->refresh();

        $this->assertEquals(1, $story->views);
        $this->assertEquals(1, $story->score);

        // Second view in the same session, should NOT increment
        $response2 = $this->withSession(['story_viewed_' . $story->id => true])->get(route('stories.read', ['id' => $story->id]));
        $response2->assertOk();

        // Refresh the story model again
        $story->refresh();

        $this->assertEquals(1, $story->views);
        $this->assertEquals(1, $story->score);
    }

}
