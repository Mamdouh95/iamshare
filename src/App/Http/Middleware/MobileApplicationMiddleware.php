<?php

namespace Mamdouh\Iamshare\App\Http\Middleware;

use Carbon\Carbon;
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

        $decryptionKey = config('iamshare.decryption_key');

        $userNationalId = NULL;

        try {
            $key = (new Decryptor())->decrypt($appNid, $decryptionKey);

            $arr = explode('|', $key);

            $expired = Carbon::parse($arr[1])->isAfter(now());

            if (!$expired) {
                return response()->json(['message' => 'Token expired, Please re-login.'], 401);
            }

            $userNationalId = $arr[0];

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
                return response()->json(['message' => 'User fetching from IAM failed.'], 401);
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

