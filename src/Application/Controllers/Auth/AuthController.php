<?php

namespace Src\Application\Controllers\Auth;

use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;
use Src\Application\Controllers\Controller;

class AuthController extends Controller
{
    public function __construct()
    {
        // By default we are using here auth:api middleware
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    /**
     * @OA\Post(
     * path="/api/v1/auth/login",
     * summary="Login by email and password",
     * description="Login by email, password",
     * operationId="api.v1.auth.login",
     * tags={"Auth"},
     *
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *
     *    @OA\JsonContent(
     *       required={"email","password"},
     *
     *       @OA\Property(property="email", type="string", format="email", example="example@example.com"),
     *       @OA\Property(property="password", type="string", format="password", example="PassWord12345"),
     *    ),
     * ),
     *
     * @OA\Response(
     *    response=401,
     *    description="Wrong credentials response",
     *
     *    @OA\JsonContent(
     *
     *       @OA\Property(property="error", type="string", example="Usuario ou senha invalidos")
     *        )
     *     )
     * )
     */
    public function login(): JsonResponse
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken(
            $token
        ); // If all credentials are correct - we are going to generate a new access token and send it back on response
    }

    /**
     * @OA\Post(
     * path="/api/v1/auth/user-info",
     * summary="Get logged user informations",
     * description="Get logged user informations",
     * operationId="api.v1.auth.user-info",
     * tags={"Auth"},
     *
     * @OA\Response(
     *    response=401,
     *    description="Wrong credentials response",
     *
     *    @OA\JsonContent(
     *
     *       @OA\Property(property="error", type="string", example="Usuario ou senha invalidos")
     *        )
     *     ),
     *
     * @OA\Response(response="200", description="Get logged user informations")
     * ),
     */
    public function me()
    {
        // Here we just get information about current user
        return response()->json(auth()->user());
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/logout",
     *     summary="Logout",
     *     description="Finish current session",
     *     operationId="api.v1.auth.logout",
     *     tags={"Auth"},
     *     security={{ "bearer": {} }},
     *
     *     @OA\Response(response="200", description="Successfully logged out"),
     *     @OA\Response(response="401", description="Unauthorized"),
     * )
     */
    public function logout()
    {
        auth()->logout(); // This is just logout function that will destroy access token of current user

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/refresh",
     *     summary="Refesh token",
     *     description="Refresh token",
     *     operationId="api.v1.auth.refresh",
     *     tags={"Auth"},
     *     security={{ "bearer": {} }},
     *
     *     @OA\Response(response="200", description="Given new access token"),
     *     @OA\Response(response="401", description="Unauthorized"),
     * )
     */
    public function refresh()
    {
        // When access token will be expired, we are going to generate a new one wit this function
        // and return it here in response
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string  $token
     * @return JsonResponse
     */
    protected function respondWithToken($token)
    {
        // This function is used to make JSON response with new
        // access token of current user
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ]);
    }
}
