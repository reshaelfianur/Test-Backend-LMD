<?php

namespace App\Http\Middleware;

use Illuminate\Support\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class VerifyApiToken
{
    private $activeToken = 1800; // second

    public function handle(Request $request, Closure $next)
    {
        $token      = $request->bearerToken();
        $tokenArray = explode(':', base64_decode($token));
        $reqCC      = $request->header('cc');

        if (empty($reqCC)) {
            $reqCC = $request->input('cc');
        }

        $cc = json_decode(base64_decode($reqCC));

        try {
            try {
                $tokenArray[1];
            } catch (\Throwable $th) {
                return response()->json([
                    'status'    => 'failed',
                    'message'   => 'Access Denied!',
                ], 401);
            }

            $decrypted = json_decode(base64_decode(Crypt::decryptString($tokenArray[1])));

            if (empty($decrypted->active_date) || empty($decrypted->comp_code)) {
                return response()->json([
                    'data'      => [],
                    'logout'    => true,
                    'status'    => 'failed',
                    'message'   => 'Access Denied!',
                ], 401);
            }

            $date   = Carbon::parse($decrypted->active_date);
            $now    = Carbon::now();

            $diff   = $date->diffInSeconds($now);

            if ($diff > $this->activeToken) {
                return response()->json([
                    'data'      => [],
                    'logout'    => true,
                    'status'    => 'failed',
                    'message'   => 'Your Token is expired!, please re-login.',
                ], 401);
            } elseif ($decrypted->comp_code != $cc) {
                return response()->json([
                    'data'      => [],
                    'logout'    => true,
                    'status'    => 'failed',
                    'message'   => 'Access Denied!',
                ], 401);
            }
        } catch (DecryptException $e) {
            return response()->json([
                'status'    => 'failed',
                'message'   => 'Access Denied!',
            ], 401);
        }

        return $next($request);
    }
}
