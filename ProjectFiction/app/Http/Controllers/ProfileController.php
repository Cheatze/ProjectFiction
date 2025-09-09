<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Story;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Services\StoryService;
use App\Services\SubscriptionService;

class ProfileController extends Controller
{
    /**
     * Shows the private profile of a user with their stories.
     * @param \App\Models\User $user
     * @return \Illuminate\Contracts\View\View
     */
    public function showPrivateProfile(User $user, StoryService $storyService)
    {
        log::channel('users')->info('User accessed private profile', ['user_id' => $user->id]);

        $list = $storyService->getUserStories($user);

        log::channel('users')->info('Showing private profile stories list', ['user_id' => $user->id, 'count' => $list->count()]);

        return view('privateProfile')
            ->with('user', $user)
            ->with('stories', $list);
    }

    /**
     * Shows the public profile of a user with their stories.
     * @param \App\Models\User $user
     * @return \Illuminate\Contracts\View\View
     */
    public function showProfile(User $user, StoryService $storyService, SubscriptionService $subscriptionService)
    {
        log::channel('users')->info('User accessed public profile', ['user_id' => $user->id]);

        $id = $user->id;

        $currentUser = Auth::user();

        $list = $storyService->getUserStories($user);

        log::channel('users')->info('Showing public profile stories list', ['user_id' => $user->id, 'count' => $list->count()]);

        $isSubscribed = false;
        if ($currentUser) {
            $isSubscribed = $subscriptionService->isSubscribed($currentUser, $user->id);
            log::channel('users')->info('Checking subscription status', ['user_id' => $currentUser->id, 'subscribed_to' => $user->id, 'is_subscribed' => $isSubscribed]);
        }

        return view('profile')
            ->with('user', $user)
            ->with('stories', $list)
            ->with('isSubscribed', $isSubscribed);
    }
}
