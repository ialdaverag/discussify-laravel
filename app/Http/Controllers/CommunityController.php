<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Gate;

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

        return response()->json(null, 204);
    }
}
