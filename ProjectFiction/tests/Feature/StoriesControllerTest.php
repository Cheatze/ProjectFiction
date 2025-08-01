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

    /** @test */
    public function guests_cannot_access_write_story_page()
    {
        $response = $this->get(route('show.write'));
        $response->assertRedirect('/login'); // Assuming it redirects to login
    }

    /** @test */
    public function authenticated_and_verified_users_can_access_write_story_page()
    {
        $this->actingAs($this->verifiedUser); //

        $response = $this->get(route('show.write'));
        $response->assertOk();
        $response->assertViewIs('write');
    }

    /** @test */
    public function unverified_users_cannot_access_write_story_page()
    {
        $this->actingAs($this->unverifiedUser); //

        $response = $this->get(route('show.write'));
        $response->assertRedirect(route('verification.notice'));
    }

    /** @test */
    public function it_displays_a_paginated_list_of_new_stories()
    {
        Story::factory()->count(20)->create();

        $response = $this->get(route('show.newest'));

        $response->assertOk();
        $response->assertViewIs('browse');
        $response->assertViewHas('stories');
        $this->assertCount(15, $response->viewData('stories')); // Default pagination is 15
    }

    /** @test */
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

    /** @test */
    public function it_handles_invalid_genre_in_show_genre()
    {
        // Attempt to access a genre that doesn't exist in the enum
        $response = $this->get('/stories/invalidgenre');
        $response->assertNotFound(); // Or whatever error handling Laravel provides for invalid enum routes
    }

    /** @test */
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

}
