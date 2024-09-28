<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    public function logout(Request $request){

        $request->user()->currentAccessToken()->delete();
        return ApiResponse::sendResponse(200,'logout Success');
    }
}
