<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function __construct(protected ActivityLogService $activityLogService) {}

    /**
     * @OA\Post(
     *     path="/api/auth/register",
     *     summary="Registrasi pengguna baru",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password","password_confirmation"},
     *             @OA\Property(property="name",     type="string",  example="Budi Santoso"),
     *             @OA\Property(property="email",    type="string",  format="email", example="budi@example.com"),
     *             @OA\Property(property="password", type="string",  format="password", example="password123"),
     *             @OA\Property(property="password_confirmation", type="string", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Registrasi berhasil",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="user",  ref="#/components/schemas/UserResource"),
     *             @OA\Property(property="token", type="string")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validasi gagal")
     * )
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'user',
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        $this->activityLogService->log('REGISTER', "User baru terdaftar: {$user->email}", $user->id);

        return response()->json([
            'message' => 'Registrasi berhasil.',
            'user'    => new UserResource($user),
            'token'   => $token,
        ], 201);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     summary="Login dan dapatkan Sanctum token",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email",    type="string", format="email", example="ichsan@keluargakas.app"),
     *             @OA\Property(property="password", type="string", format="password", example="password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login berhasil",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="user",  ref="#/components/schemas/UserResource"),
     *             @OA\Property(property="token", type="string")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Kredensial tidak valid")
     * )
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            $this->activityLogService->log(
                'SUSPICIOUS_ACTIVITY',
                "Percobaan login gagal untuk email: {$request->email}",
            );

            return response()->json([
                'message' => 'Email atau password tidak valid.',
            ], 401);
        }

        // Hapus token lama agar tidak menumpuk
        $user->tokens()->delete();

        $token = $user->createToken('api-token')->plainTextToken;

        $this->activityLogService->log('LOGIN', "User login: {$user->email}", $user->id);

        return response()->json([
            'message' => 'Login berhasil.',
            'user'    => new UserResource($user),
            'token'   => $token,
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/logout",
     *     summary="Logout dan revoke token aktif",
     *     tags={"Auth"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="Logout berhasil")
     * )
     */
    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();

        // Hanya revoke token yang digunakan saat ini
        $user->currentAccessToken()->delete();

        $this->activityLogService->log('LOGOUT', "User logout: {$user->email}", $user->id);

        return response()->json(['message' => 'Logout berhasil.']);
    }

    /**
     * @OA\Get(
     *     path="/api/auth/me",
     *     summary="Profil user yang sedang login",
     *     tags={"Auth"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Data user",
     *         @OA\JsonContent(ref="#/components/schemas/UserResource")
     *     )
     * )
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json(new UserResource($request->user()));
    }
}
