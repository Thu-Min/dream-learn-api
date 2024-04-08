<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\User;
use App\Mail\EmailVerify;
use Laravel\Passport\Token;
use Illuminate\Http\Request;
use App\Models\VerificationCode;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\Api\SignInRequest;
use App\Http\Requests\Api\SignUpRequest;
use Laravel\Socialite\Facades\Socialite;
use GuzzleHttp\Exception\ClientException;
use App\Http\Requests\Api\VerifyCodeRequest;

class AuthController extends Controller
{
    public function socialRedirect($provider)
    {
        return Socialite::driver($provider)->stateless()->redirect();
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
        $data = [
            'data' => $new_user,
            'token' => $token
        ];

        return $this->apiResponse(true, 'Login with socail success', $data, 200);
    }

    public function signUp(SignUpRequest $request)
    {
        $input = $request->only('name', 'user_name', 'email', 'password');
        $input['password'] = Hash::make($input['password']);

        $user = User::create($input);
        $code = VerificationCode::generateVerificationCode(6, $user->id);

        Mail::to($user->email)->send(new EmailVerify($code));

        $token = $user->createToken('api-access-token')->accessToken;

        $data = [
            'data' => $user,
            'token' => $token
        ];

        return $this->apiResponse(true, 'Signup with email success', $data, 200);
    }

    public function signIn(SignInRequest $request)
    {
        $email = $request->email;
        $password = $request->password;

        $user = User::where('email', $email)->first();

        $check = Hash::check($password, $user->password);

        if($check === true) {
            $token = $user->createToken('api-access-token')->accessToken;

            $data = [
                'data' => $user,
                'token' => $token
            ];

            return $this->apiResponse(true, 'Sign in with email success', $data, 200);
        }

        return $this->apiResponse(false, 'Credentials are not correct', '', 403);
    }

    public function signOut()
    {
        $user = Auth::user();

        Token::where('user_id', $user->id)->delete();

        return $this->apiResponse(true, 'Sign out success', '', 200);
    }

    public function requestVerifyEmail()
    {
        $user = Auth::user();

        if($user->email_verified === false) {
            $code = VerificationCode::generateVerificationCode(6, $user->id);

            Mail::to($user->email)->send(new EmailVerify($code));

            return $this->apiResponse(true, 'Email verification code has been sent to your email', '', 200);
        }

        return $this->apiResponse(false, 'Your email is already verified', '', 400);
    }

    public function verifyEmail(VerifyCodeRequest $request)
    {
        $code = $request->code;
        $user = Auth::user();
        $verification_code = VerificationCode::where('user_id', $user->id)->first();

        $check_code = Hash::check($code, $verification_code->code);

        if($check_code === true) {
            if(Carbon::now()->lte($verification_code->expire_at)) {
                $user->update([
                    'email_verified' => true,
                    'email_verified_at' => Carbon::now()
                ]);

                $verification_code->delete();

                return $this->apiResponse(true, 'Email verification success', '', 200);
            }

            return $this->apiResponse(false, 'Verification code expired', '', 403);
        }

        return $this->apiResponse(false, 'Verification code incorrect', '', 403);
    }
}
