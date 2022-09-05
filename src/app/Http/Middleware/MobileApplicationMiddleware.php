<?php

namespace Mamdouh\Iamshare\App\Http\Middleware;

use Mamdouh\Iamshare\App\Models\User;
use RNCryptor\RNCryptor\Decryptor;
use Closure;

class MobileApplicationMiddleware
{
    protected $nationalKey = 'national_id';

    public function handle($request, Closure $next)
    {
        $appNid = $request->header('x-app-nid', NULL);

        $userNationalId = NULL;

        try {
            if ($nationalId = (new Decryptor())->decrypt($appNid, 'iam.media.gov.sa')) {
                $userNationalId = $nationalId;
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = User::where($this->nationalKey, $userNationalId)->first();

        if (is_null($user)) {
            return response()->json(['message' => 'User not in system, to be fixed.'], 401);
        }

        $request->merge(['user' => $user]);

        return $next($request);
    }
}

