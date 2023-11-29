<?php

namespace App\Http\Controllers;

use App\Models\Community;
use Illuminate\Http\Request;

use App\Http\Requests\CommunityStoreRequest;
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
    public function update(Request $request, Community $community)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Community $community)
    {
        //
    }
}
