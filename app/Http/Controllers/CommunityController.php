<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

use App\Models\User;
use App\Models\Community;
use App\Http\Requests\Community\StoreRequest;
use App\Http\Requests\Community\UpdateRequest;
use App\Http\Resources\UserCollection;
use App\Http\Resources\CommunityResource;
use App\Http\Resources\CommunityCollection;
use App\Http\Resources\PostCollection;

class CommunityController extends Controller
{
    /**
     * Create a new CommunityController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', 
            ['except' => [
                'index', 
                'show', 
                'getSubscribers', 
                'getModerators', 
                'getBans', 
                'getPosts'
                ]
            ]
        );
    }

    /**
     * Display a listing of the resource.
     * 
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(new CommunityCollection(Community::all()));
    }

    /**
     * Store a newly created resource in storage.
     * 
     * @param  \App\Http\Requests\Community\StoreRequest  $request
     * 
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        $community = auth()->user()->communities()->create($request->validated());

        $community->subscribers()->attach(auth()->id());
        $community->moderators()->attach(auth()->id());

        return response()->json(new CommunityResource($community), 201);
    }

    /**
     * Display the specified resource.
     * 
     * @param  \App\Models\Community  $community
     * 
     * @return \Illuminate\Http\Response
     */
    public function show(Community $community)
    {
        return response()->json(new CommunityResource($community), 200);
    }

    /**
     * Update the specified resource in storage.
     * 
     * @param  \App\Http\Requests\Community\UpdateRequest  $request
     * @param  \App\Models\Community  $community
     * 
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, Community $community)
    {
        $response = Gate::inspect('update', $community);

        if ($response->denied()) {
            return response()->json(['error' => $response->message()], 403);
        } 

        $community->update($request->validated());

        return response()->json(new CommunityResource($community), 200);
    }

    /**
     * Remove the specified resource from storage.
     * 
     * @param  \App\Models\Community  $community
     * 
     * @return \Illuminate\Http\Response
     */
    public function destroy(Community $community)
    {
        $response = Gate::inspect('delete', $community);

        if ($response->denied()) {
            return response()->json(['error' => $response->message()], 403);
        }

        // $community->moderators()->detach();
        // $community->subscribers()->detach();
    
        $community->delete();
    
        return response()->json(null, 204);
    }

    /**
     * Subscribe to the specified community.
     * 
     * @param  \App\Models\Community  $community
     * 
     * @return \Illuminate\Http\Response
     */
    public function subscribe(Community $community)
    {
        $user = auth()->user();

        // Check if the user is banned from the community
        if ($user->isBannedFrom($community)) {
            return response()->json(['error' => 'You are banned from this community.'], 400);
        }

        // Check if the user is already subscribed to the community
        if ($user->isSubscribedTo($community)) {
            return response()->json(['error' => 'You are already subscribed to this community.'], 409);
        }

        $user->subscriptions()->attach($community->id);

        return response()->json(null, 204);
    }

    /**
     * Unsubscribe from the specified community.
     * 
     * @param  \App\Models\Community  $community
     * 
     * @return \Illuminate\Http\Response
     */
    public function unsubscribe(Community $community)
    {
        $user = auth()->user();

        if (!$user->isSubscribedTo($community)) {
            return response()->json(['error' => 'User is not subscribed to the community'], 400);
        }

        $user->subscriptions()->detach($community->id);

        if ($user->isModeratorOf($community)) {
            $community->moderators()->detach($user->id);
        }

        return response()->json(null, 204);
    }

    /**
     * Add a moderator to the specified community.
     */
    public function addModerator($community, $user)
    {
        // Check if the community exists
        $community = Community::where('name', $community)->firstOrFail();

        // Check if the user exists
        $user = User::where('username', $user)->firstOrFail();

        $response = Gate::inspect('addModerator', $community);

        if ($response->denied()) {
            return response()->json(['error' => $response->message()], 403);
        }

        // Check if the user is banned from the community
        if ($user->isBannedFrom($community)) {
            return response()->json(['error' => 'User is banned from the community'], 400);
        }

        // Check if the user to be added as a moderator is subscribed to the community
        if (!$user->isSubscribedTo($community)) {
            return response()->json(['error' => 'User must be subscribed to the community'], 400);
        }

        // Check if the user is already a moderator of the community
        if ($user->isModeratorOf($community)) {
            return response()->json(['error' => 'User is already a moderator of the community'], 409);
        }

        // Add the user as a moderator of the community
        $community->moderators()->attach($user->id);

        return response()->json(null, 204);
    }

    /**
     * Remove a moderator from the specified community.
     */
    public function removeModerator($community, $user)
    {
        // Check if the community exists
        $community = Community::where('name', $community)->firstOrFail();

        // Check if the user exists
        $user = User::where('username', $user)->firstOrFail();

        // Check if the user is the owner of the community
        $response = Gate::inspect('removeModerator', $community);

        if ($response->denied()) {
            return response()->json(['error' => $response->message()], 403);
        }

        // Check if the user is the owner of the community
        if ($user->id === $community->owner_id) {
            return response()->json(['error' => 'User is the owner of the community'], 400);
        }

        // Check if the user is a moderator of the community
        if (!$user->isModeratorOf($community)) {
            return response()->json(['error' => 'User is not a moderator of the community'], 400);
        }

        // Remove the user as a moderator of the community
        $community->moderators()->detach($user->id);

        return response()->json(null, 204);
    }

    /**
     * Ban a user from the specified community.
     */
    public function banUser($community, $user)
    {
        // Check if the community exists
        $community = Community::where('name', $community)->firstOrFail();

        // Check if the user exists
        $user = User::where('username', $user)->firstOrFail();

        // Check if the user is a moderator of the community
        $response = Gate::inspect('banUser', $community);

        if ($response->denied()) {
            return response()->json(['error' => $response->message()], 403);
        }

        // Check if the user is already banned from the community
        if ($user->isBannedFrom($community)) {
            return response()->json(['error' => 'User is already banned from the community'], 400);
        }

        // Check if the user is a subscriber of the community
        if (!$user->isSubscribedTo($community)) {
            return response()->json(['error' => 'User is not subscribed to the community'], 400);
        }

        // User cannot ban themselves
        if ($user->id === auth()->id()) {
            return response()->json(['error' => 'User cannot ban themselves'], 400);
        }

        // Cannot ban the owner of the community
        if ($user->id === $community->owner_id) {
            return response()->json(['error' => 'User cannot ban the owner of the community'], 400);
        }

        // Remove moderator privileges from the user if they are a moderator of the community
        if ($user->isModeratorOf($community)) {
            $community->moderators()->detach($user->id);
        }

        // Ban the user from the community
        $community->bans()->attach($user->id);

        return response()->json(null, 204);
    }

    /**
     * Unban a user from the specified community.
     */
    public function unbanUser($community, $user)
    {
        // Check if the community exists
        $community = Community::where('name', $community)->firstOrFail();

        // Check if the user exists
        $user = User::where('username', $user)->firstOrFail();

        // Check if the user is a moderator of the community
        $response = Gate::inspect('unbanUser', $community);

        if ($response->denied()) {
            return response()->json(['error' => $response->message()], 403);
        }

        // Check if the user is banned from the community
        if (!$user->isBannedFrom($community)) {
            return response()->json(['error' => 'User is not banned from the community'], 400);
        }

        // Unban the user from the community
        $community->bans()->detach($user->id);

        return response()->json(null, 204);
    }

    /**
     * Get the subscribers of the specified community.
     */
    public function getSubscribers(Community $community)
    {
        return response()->json(new UserCollection($community->subscribers));
    }

    /**
     * Get the moderators of the specified community.
     */
    public function getModerators(Community $community)
    {
        return response()->json(new UserCollection($community->moderators));
    }

    /**
     * Get the bans of the specified community.
     */
    public function getBans(Community $community)
    {
        return response()->json(new UserCollection($community->bans));
    }

    /**
     * Get the posts of the specified community.
     */
    public function getPosts(Community $community)
    {
        return response()->json(new PostCollection($community->posts));
    }
}