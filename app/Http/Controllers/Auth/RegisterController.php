<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $data = $request->only('username','email','password');
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        $user->token = $user->createToken('Api Token')->plainTextToken;

        return ApiResponse::sendResponse(201, "Created Successfully", new UserResource($user));
    }
}
