<?php

namespace App\Http\Middleware;

use Illuminate\Support\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class GenerateApiToken
{
    public function handle(Request $request, Closure $next)
    {
        $token      = $request->bearerToken();
        $tokenArray = explode(':', base64_decode($token));
        $response   = $next($request);

        $content = json_decode($response->content(), true);

        //Check if the response is JSON
        if (json_last_error() == JSON_ERROR_NONE) {

            try {
                $decrypted = json_decode(base64_decode(Crypt::decryptString($tokenArray[1])));

                $response->setContent(json_encode(array_merge(
                    $content,
                    [
                        'api_token'     => Crypt::encryptString(base64_encode(json_encode([
                            'user_id'       => $decrypted->user_id,
                            'username'      => $decrypted->username,
                            'comp_code'     => $decrypted->comp_code,
                            'active_date'   => Carbon::now()->toDateTimeString(),
                            'random_string' => Str::random(),
                        ])))
                    ]
                )));
            } catch (DecryptException $e) {
            }
        }

        return $response;
    }
}
