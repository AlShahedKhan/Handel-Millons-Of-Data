<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class UserController extends Controller
{
    public function index()
    {
        $users = Cache::store('redis')->remember('users_all', 60, function () {
            return User::take(10000)->get();
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Users retrieved successfully.',
            'data' => $users,
        ]);
    }

     public function testRedis()
    {
        try {
            // Test Redis connection
            Redis::set('test_key', 'Hello Redis!');
            $value = Redis::get('test_key');

            // Test Cache with Redis
            Cache::store('redis')->put('test_cache', 'Hello Cache!', 60);
            $cacheValue = Cache::store('redis')->get('test_cache');

            return response()->json([
                'status' => 'success',
                'message' => 'Redis is working!',
                'data' => [
                    'redis_test' => $value,
                    'cache_test' => $cacheValue,
                    'redis_connection' => Redis::connection()->ping()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Redis connection failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
