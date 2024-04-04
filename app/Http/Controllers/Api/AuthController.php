<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use GuzzleHttp\Exception\ClientException;

class AuthController extends Controller
{
    public function redirect($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback($provider)
    {
        $user = Socialite::driver($provider)->stateless()->user();

        $new_user = User::firstOrCreate(
            [
                'email' => $user->getEmail()
            ],
            [
                'name' => $user->getName(),
                'avatar' => $user->getAvatar(),
                'email_verified' => true,
                'email_verified_at' => now(),
            ]
        );

        $new_user->authProviders()->updateOrCreate(
            [
                'provider' => $provider,
                'provider_id' => $user->getId(),
            ]
        );

        $token = $new_user->createToken('API Access Token')->accessToken;

        // $token = $new_user->createToken('token-name')->plainTextToken;

        return response()->json([
            'message' => 'Success',
            'token' => $token,
        ], 200);
    }
}
