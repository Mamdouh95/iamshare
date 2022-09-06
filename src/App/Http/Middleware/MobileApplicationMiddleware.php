<?php

namespace Mamdouh\Iamshare\App\Http\Middleware;

use RNCryptor\RNCryptor\Decryptor;
use Closure;
use Illuminate\Support\Facades\Http;

class MobileApplicationMiddleware
{
    public function handle($request, Closure $next)
    {
        $appNid = $request->header('x-app-nid', NULL);

        $userModel = config('iamshare.model');

        $nationalKey = config('iamshare.national_key', 'national_id');

        $userNationalId = NULL;

        try {
            if ($nationalId = (new Decryptor())->decrypt($appNid, 'iam.media.gov.sa')) {
                $userNationalId = $nationalId;
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = $userModel::query()->where($nationalKey, $userNationalId)->first();

        if (is_null($user)) {
            try {
                $request = Http::post(config('iamshare.get_user_url'), ['national_id' => $appNid]);

                $data = $request->json();

                $merged = array_merge($data, self::merge($data));

                $merged = array_merge($merged, config('iamshare.default_merge'));

                $user = $userModel::create($merged);

            } catch (\Exception $e) {
                return response()->json(['message' => 'User not in system, to be fixed.'], 401);
            }
        }

        $request->merge(['user' => $user]);

        return $next($request);
    }

    public static function merge($responseArr)
    {
        $mergable = config('iamshare.user_merge');
        $arr = [];

        foreach ($mergable as $key => $value) {
            $arr[$key] = $responseArr[$value] ?? NULL;
        }

        return $arr;
    }
}

