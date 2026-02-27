<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Handle login for both web and api.
     */
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string'
            ]);

            if ($validator->fails()) {
                return $this->errorResponse('Validasi gagal.', false, $validator->errors()->all(), 422);
            }

            $credentials = $validator->validated();
            $type = $request->input('type'); // menangkap tipe 'web' atau 'api'
            $auth = ($type === 'web') ? Auth::guard('web') : Auth::guard('api');

            // Cek apakah email ada
            $user = User::where('email', $credentials['email'])->first();
            if (!$user || !Hash::check($credentials['password'], $user->password)) {
                return $this->errorResponse('Email atau Password tidak sesuai.', false, [], 401);
            }

            // Attempt login
            if (!$token = $auth->attempt($credentials)) {
                return $this->errorResponse('Gagal login.', false, [], 401);
            }

            if ($type === 'web') {
                $request->session()->regenerate();

                // redirect according to role (trainer should go to trainer dashboard)
                if ($user->hasRole('User:Trainer')) {
                    $redirect = route('trainer.dashboard');
                } else {
                    $redirect = route('home');
                }

                return $this->successResponse('Login berhasil.', ['direct' => $redirect]);
            } else {
                return $this->successResponse('Login berhasil.', [
                    'payload' => $this->respondWithToken($token, $auth),
                    'user' => $user->only(['name', 'email']),
                    'version' => '-'
                ]);
            }
        } catch (\Throwable $th) {
            Log::error($th);
            return $this->errorResponse('Terjadi kesalahan: ' . $th->getMessage(), false, null, 500);
        }
    }

    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
            ]);

            if ($validator->fails()) {
                // Return validation errors as JSON
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'data'    => $validator->errors()
                ], 422);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'email_verified_at' => now(),
            ]);

            // Assign a default role according to request (or Member if not provided).
            // Existing implementation always gives Member; if you want to register a trainer
            // you must pass a `role` value and the caller must be authorised to create trainers
            // (for example admin only). For now public register still defaults to member.
            $role = $request->input('role', 'User:Member');
            if (!in_array($role, ['User:Member','User:Trainer'])) {
                $role = 'User:Member';
            }
            $user->assignRole($role);
            // Note: do not write non-enum values into the `users.role` column.
            // The database `role` column is an enum(['member','staff','owner','admin']).
            // We rely on Spatie roles (e.g. 'User:Trainer') for authorization.


            // Log the user in
            Auth::guard('web')->login($user);

            $request->session()->regenerate();

            // Return success response with redirect
            return response()->json([
                'success' => true,
                'message' => 'Registration successful',
                'data' => [
                    'direct' => route('home')
                ]
            ]);

        } catch (\Throwable $th) {
            Log::error($th);
            return response()->json([
                'success' => false,
                'message' => 'Registration failed. Please try again.',
                'data' => null
            ], 500);
        }
    }

    /**
     * Logout for web and API.
     */
    public function logout(Request $request)
    {
        $type = $request->input('type');

        if ($type === 'web') {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login');
        } else {
            try {
                Auth::guard('api')->logout();
                return $this->successResponse('Logout berhasil!');
            } catch (\Throwable $e) {
                return $this->errorResponse('Gagal logout', false, null, 500);
            }
        }
    }

    /**
     * Refresh token untuk API.
     */
    public function refresh(Request $request)
    {
        try {
            $auth = Auth::guard('api');
            $newToken = $auth->refresh();

            return $this->successResponse('Token diperbarui.', [
                'payload' => $this->respondWithToken($newToken, $auth)
            ]);
        } catch (\Throwable $th) {
            Log::error($th);
            return $this->errorResponse('Gagal refresh token.', false, null, 500);
        }
    }

    /**
     * Format token response.
     */
    protected function respondWithToken($token, $auth)
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $auth->factory()->getTTL() * 60
        ];
    }
}
