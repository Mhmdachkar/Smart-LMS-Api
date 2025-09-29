<?php
// File: app/Http/Controllers/Api/V1/AuthController.php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\V1\RegisterRequest;
use App\Http\Resources\V1\AuthResource;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends BaseApiController
{
    /**
     * Register a new user
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            // Create the user
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'role' => $request->role ?? 'student',
                'status' => 'active',
            ]);

            // Create user profile
            UserProfile::create([
                'user_id' => $user->id,
                'bio' => $request->bio,
                'timezone' => $request->timezone ?? 'UTC',
                'language' => $request->language ?? 'en',
                'preferences' => [
                    'email_notifications' => true,
                    'marketing_emails' => false,
                    'course_updates' => true,
                ],
                'social_links' => $request->social_links ?? [],
            ]);

            // Load user with profile for the response
            $user->load('profile');

            // Generate JWT token
            $token = JWTAuth::fromUser($user);

            DB::commit();

            // Return success response with user data and token
            return $this->success(
                new AuthResource([
                    'user' => $user,
                    'token' => $token,
                    'token_type' => 'bearer',
                    'expires_in' => auth('api')->factory()->getTTL() * 60, // Convert to seconds
                ]),
                'User registered successfully',
                201
            );

        } catch (\Exception $e) {
            DB::rollBack();
            
            return $this->error(
                'Registration failed. Please try again.',
                500,
                config('app.debug') ? ['exception' => $e->getMessage()] : null
            );
        }
    }

    /**
     * User login
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        try {
            if (!$token = auth('api')->attempt($credentials)) {
                throw ValidationException::withMessages([
                    'email' => ['Invalid credentials provided.'],
                ]);
            }

            $user = auth('api')->user();
            
            // Check if user is active
            if (!$user->isActive()) {
                auth('api')->logout();
                return $this->error('Account is suspended or inactive.', 403);
            }

            // Update last login timestamp
            $user->update(['last_login_at' => now()]);
            
            // Load user relationships
            $user->load('profile');

            return $this->success(
                new AuthResource([
                    'user' => $user,
                    'token' => $token,
                    'token_type' => 'bearer',
                    'expires_in' => auth('api')->factory()->getTTL() * 60,
                ]),
                'Login successful'
            );

        } catch (ValidationException $e) {
            return $this->error('Invalid credentials', 422, $e->errors());
        } catch (\Exception $e) {
            return $this->error(
                'Login failed. Please try again.',
                500,
                config('app.debug') ? ['exception' => $e->getMessage()] : null
            );
        }
    }

    /**
     * Get current authenticated user
     */
    public function me(): JsonResponse
    {
        try {
            $user = auth('api')->user();
            
            if (!$user) {
                return $this->error('User not found', 404);
            }

            $user->load('profile');

            return $this->success(
                new AuthResource([
                    'user' => $user,
                    'token' => null, // Don't send token in me endpoint
                    'token_type' => 'bearer',
                    'expires_in' => null,
                ]),
                'User data retrieved successfully'
            );

        } catch (\Exception $e) {
            return $this->error(
                'Failed to retrieve user data',
                500,
                config('app.debug') ? ['exception' => $e->getMessage()] : null
            );
        }
    }

    /**
     * User logout
     */
    public function logout(): JsonResponse
    {
        try {
            auth('api')->logout();

            return $this->success(
                null,
                'Successfully logged out'
            );

        } catch (\Exception $e) {
            return $this->error(
                'Logout failed',
                500,
                config('app.debug') ? ['exception' => $e->getMessage()] : null
            );
        }
    }

    /**
     * Refresh JWT token
     */
    public function refresh(): JsonResponse
    {
        try {
            $newToken = auth('api')->refresh();
            $user = auth('api')->user();
            $user->load('profile');

            return $this->success(
                new AuthResource([
                    'user' => $user,
                    'token' => $newToken,
                    'token_type' => 'bearer',
                    'expires_in' => auth('api')->factory()->getTTL() * 60,
                ]),
                'Token refreshed successfully'
            );

        } catch (\Exception $e) {
            return $this->error(
                'Token refresh failed',
                500,
                config('app.debug') ? ['exception' => $e->getMessage()] : null
            );
        }
    }
}