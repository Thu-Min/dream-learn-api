<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Mail\EmailVerify;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\Api\SignUpRequest;
use Laravel\Socialite\Facades\Socialite;
use GuzzleHttp\Exception\ClientException;

class AuthController extends Controller
{
    public function socialRedirect($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function socialCallback($provider)
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

        $token = $new_user->createToken('api-access-token')->accessToken;

        return response()->json([
            'message' => 'Success',
            'token' => $token,
        ], 200);
    }

    public function signUp(SignUpRequest $request)
    {
        $input = $request->only('name', 'user_name', 'email', 'password');
        $input['password'] = Hash::make($input['password']);

        $user = User::create($input);

        Mail::to($user->email)->send(new EmailVerify());

        $token = $user->createToken('api-access-token')->accessToken;

        $data = [
            'data' => $user,
            'token' => $token
        ];


        return response()->json($data, 200);
    }
}
