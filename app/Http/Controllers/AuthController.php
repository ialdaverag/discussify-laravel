<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

use App\Models\User;
use App\Http\Requests\SignupRequest;
use App\Http\Resources\UserResource;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['signup']]);
    }

    public function signup(SignupRequest $request)
    {
        $user = User::create($request->validated());

        $userResource = new UserResource($user);

        return response()->json($userResource, 201);
    }
}