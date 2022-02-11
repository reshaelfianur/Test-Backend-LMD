<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyAccessRights
{
    public function handle(Request $request, Closure $next)
    {
        $accessRights = $request->header('accessRights');

        if (empty($accessRights)) {
            return response()->json([
                'message'   => 'Access Denied!',
                'data'      => [],
                'status'    => 'failed'
            ], 401);
        }

        $accessRights   = json_decode(base64_decode($accessRights));
        $class          = currentRoute('class');
        $method         = currentRoute('method');

        if ($accessRights->role->id != 1) {
            if ($accessRights->role->id == 2 && $class != 'user' && $class != 'company') {
                return response()->json([
                    'message'   => 'Access Denied!',
                    'data'      => [],
                    'status'    => 'failed'
                ], 401);
            } else if ($accessRights->role->id == 3 && (($class == 'user' && !empty('user_is_ho')) || ($class == 'company' && !empty('comp_is_ho')))) {
                return response()->json([
                    'message'   => 'Access Denied!',
                    'data'      => [],
                    'status'    => 'failed'
                ], 401);
            } else if ($accessRights->role->id != 2 && $accessRights->role->id != 3) {
            }
        }

        return $next($request);
    }
}
