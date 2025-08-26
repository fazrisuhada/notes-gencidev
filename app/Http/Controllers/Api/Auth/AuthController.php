<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Helpers\ApiResponse;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        try {
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $token = Auth::guard('api')->login($user);

            return ApiResponse::success(
                'Registrasi berhasil',
                201,
                [
                    'name'  => $user->name,
                    'email' => $user->email,
                ],
                [
                    'token'      => $token,
                    'token_type' => 'bearer',
                    'expires_in' => auth('api')->factory()->getTTL() * 60
                ]
            );
        } catch (\Exception $e) {
            return ApiResponse::error('Registration failed', 500, [
                'exception' => $e->getMessage(),
            ]);
        }
    }

    public function login(LoginRequest $request)
    {
        try {
            $credentials = $request->only('email', 'password');

            if (!$token = Auth::guard('api')->attempt($credentials)) {
                return ApiResponse::error('Invalid credentials', 401);
            }

            $user = Auth::guard('api')->user();            

            return ApiResponse::success(
                'Login berhasil',
                200,
                [
                    'name'  => $user->name,
                    'email' => $user->email,
                ],
                [
                    'token'      => $token,
                    'token_type' => 'bearer',
                    'expires_in' => auth('api')->factory()->getTTL() * 60
                ]
            );
        } catch (\Exception $e) {
            return ApiResponse::error('Login failed', 500, [
                'exception' => $e->getMessage(),
            ]);
        }
    }

    public function logout()
    {
        try {
            Auth::guard('api')->logout();

            return ApiResponse::success('Logout berhasil', 200);
        } catch (\Exception $e) {
            return ApiResponse::error('Logout failed', 500, [
                'exception' => $e->getMessage(),
            ]);
        }
    }
}
