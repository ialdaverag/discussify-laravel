<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Gate;

use App\Models\User;
use App\Models\Community;

use Illuminate\Http\Request;

use App\Http\Requests\CommunityStoreRequest;
use App\Http\Requests\CommunityUpdateRequest;
use App\Http\Resources\CommunityResource;

class CommunityController extends Controller
{
    /**
     * Create a new CommunityController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['index', 'show']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Community::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CommunityStoreRequest $request)
    {
        $community = auth()->user()->communities()->create($request->validated());
        
        $community->moderators()->attach(auth()->id());
        $community->subscribers()->attach(auth()->id());

        return response()->json(new CommunityResource($community), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Community $community)
    {
        return $community;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CommunityUpdateRequest $request, Community $community)
    {
        if (Gate::denies('update-community', $community)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $community->update($request->validated());

        return response()->json(new CommunityResource($community), 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Community $community)
    {
        if (Gate::denies('delete-community', $community)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $community->moderators()->detach();
        $community->subscribers()->detach();
    
        $community->delete();
    
        return response()->json(null, 204);
    }

    /**
     * Subscribe to the specified community.
     */
    public function subscribe(Community $community)
    {
        $user = auth()->user();

        if ($user->isSubscribedTo($community)) {
            return response()->json(['error' => 'User is already subscribed to the community'], 400);
        }

        $user->subscriptions()->attach($community->id);

        return response()->json(null, 204);
    }

    /**
     * Unsubscribe from the specified community.
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

        if (Gate::denies('add-moderator', $community)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Check if the user to be added as a moderator is subscribed to the community
        if (!$user->isSubscribedTo($community)) {
            return response()->json(['error' => 'User must be subscribed to the community'], 400);
        }

        // Check if the user is already a moderator of the community
        if ($user->isModeratorOf($community)) {
            return response()->json(['error' => 'User is already a moderator of the community'], 400);
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

        if (Gate::denies('remove-moderator', $community)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Check if the user is a moderator of the community
        if (!$user->isModeratorOf($community)) {
            return response()->json(['error' => 'User is not a moderator of the community'], 400);
        }

        // Remove the user as a moderator of the community
        $community->moderators()->detach($user->id);

        return response()->json(null, 204);
    }
}