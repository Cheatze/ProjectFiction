<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Story;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubmitStoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test successful story submission.
     *
     * @return void
     */
    public function test_submit_story_success()
    {
        // Create a user and authenticate
        $user = User::factory()->create();

        $this->actingAs($user);

        $postData = [
            'title' => 'Test Story Title',
            'synopsis' => 'This is a valid synopsis with more than 25 characters.',
            'genre' => 'Fiction',
            'story' => str_repeat('This is the story content. ', 30), // > 500 chars
        ];

        $response = $this->post(route('write'), $postData);

        $response->assertRedirect(route('index'));
        $response->assertSessionHas('success', 'Story submitted successfully!');

        $this->assertDatabaseHas('stories', [
            'title' => $postData['title'],
            'synopsis' => $postData['synopsis'],
            'genre' => $postData['genre'],
            'content' => $postData['story'],
            'user_id' => $user->id,
        ]);
    }

    /**
     * Test story submission validation failure.
     *
     * @return void
     */
    public function test_submit_story_validation_failure()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        // Missing required fields
        $postData = [
            'title' => '',
            'synopsis' => 'short',
            'genre' => 'InvalidGenre',
            'story' => 'short',
        ];

        $response = $this->from(route('write'))->post(route('write'), $postData);

        $response->assertRedirect(route('write'));
        $response->assertSessionHasErrors(['title', 'synopsis', 'genre', 'story']);
    }
}
