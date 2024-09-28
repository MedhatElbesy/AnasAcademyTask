<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(LoginRequest $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return ApiResponse::sendResponse(401,'Invalid credentials');
        }

        $user = Auth::user();
        $user->token = $user->createToken('auth_token')->plainTextToken;

        return ApiResponse::sendResponse(200, 'login success', new UserResource($user));
    }
}
