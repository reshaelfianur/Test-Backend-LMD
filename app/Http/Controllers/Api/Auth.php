<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth as VendorAuth;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

use App\Models\User as mUser;
use App\Models\Auth_attempt;

class Auth extends Controller
{
    private $_maxAttempt    = 7;

    public function signIn(Request $req)
    {
        $validate = Validator::make($req->all(), [
            'username_or_email' => 'required',
            'password'          => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'data'      => null,
                'status'    => false,
                'message'   => $validate->errors(),
            ], 200);
        } else {
            $credentials    = request(['password']);
            $fieldType      = filter_var($req->username_or_email, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
            $credentials    = Arr::add($credentials, $fieldType, $req->username_or_email);
            $credentials    = Arr::add($credentials, 'user_status', 1);

            $user = mUser::where($fieldType, $req->username_or_email)->first();

            if (!VendorAuth::attempt($credentials) || empty($user) || !Hash::check($req->password, $user->password, [])) {
                return response()->json([
                    'data'      => null,
                    'status'    => false,
                    'message'   => 'Password does not match with your username or email!',
                ], 200);
            }

            return response()->json([
                'status'    => true,
                'message'   => 'Signin successfully!',
                'data'      => [
                    'user'          => [
                        'user_id'           => $user->user_id,
                        'user_full_name'    => $user->user_full_name,
                        'username'          => $user->username,
                        'email'             => $user->email,
                    ],
                    'token'         =>  $user->createToken('token-auth')->plainTextToken,
                    'token_type'    => 'Bearer',
                ]
            ], 200);
        }
    }

    public function signOut(Request $req)
    {
        $user = $req->user();
        $user->currentAccessToken()->delete();

        return response()->json([
            'data'      => null,
            'status'    => true,
            'message'   => 'Signout successfully',
        ], 200);
    }

    public function signOutAll(Request $req)
    {
        $user = $req->user();
        $user->tokens()->delete();

        $respon = [
            'data'      => null,
            'status'    => true,
            'message'   => 'Signout all successfully',
        ];
        return response()->json($respon, 200);
    }

    public function verifyToken(Request $req)
    {
        if (auth('sanctum')->check()) {
            return response()->json([
                'status'    => 'success',
                'message'   => 'Authenticated.'
            ], 200);
        } else {
            return response()->json([
                'status'    => 'failed',
                'message'   => 'Unauthenticated.'
            ], 401);
        }
    }

    public function checkAuthAttempt(Request $req)
    {
        if (empty($req->header('Captcha')) || empty($req->header('IPAddress')) || empty($req->header('User-Agent'))) {
            return false;
        }

        $AuthAttempt = Auth_attempt::where('ip_address', $req->header('IPAddress'))
            ->where('user_agent', $req->header('User-Agent'))
            ->first();

        if (empty($AuthAttempt)) {
            $captcha = Str::random(32);

            Auth_attempt::create([
                'ip_address'    => $req->header('IPAddress'),
                'user_agent'    => $req->header('User-Agent'),
                'captcha'       => $captcha,
                'attempt'       => 0,
            ]);

            return $captcha;
        } else {
            if ($AuthAttempt->captcha == $req->header('Captcha')) {
                if ($AuthAttempt->attempt >= $this->_maxAttempt) {
                    return false;
                }
                $newCaptcha = Str::random(32);

                $AuthAttempt = Auth_attempt::where('captcha', $AuthAttempt->captcha)->update([
                    'captcha'       => $newCaptcha,
                    'attempt'       => $AuthAttempt->attempt + 1,
                ]);

                return $newCaptcha;
            } else {
                return false;
            }
        }
    }

    public function resetAuthAttempt($captcha)
    {
        $AuthAttempt = Auth_attempt::where('captcha', $captcha)->first();

        if (!empty($AuthAttempt)) {
            return Auth_attempt::where('captcha', $AuthAttempt->captcha)->update([
                'attempt'   => 0,
            ]);
        }
    }
}
