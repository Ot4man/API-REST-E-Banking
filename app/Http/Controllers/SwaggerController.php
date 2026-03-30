<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *      title="AlMadar Bank API",
 *      version="1.0.0",
 *      description="E-Banking API documentation for AlMadar Bank."
 * )
 * @OA\Server(
 *      url="http://127.0.0.1:8000",
 *      description="Local Development Server"
 * )
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     in="header",
 *     name="Authorization"
 * )
 */
class SwaggerController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/auth/register",
     *     summary="Inscription",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password","password_confirmation","date_naissance"},
     *             @OA\Property(property="name", type="string", example="otman"),
     *             @OA\Property(property="email", type="string", format="email", example="otman@gmail.com"),
     *             @OA\Property(property="password", type="string", format="password"),
     *             @OA\Property(property="password_confirmation", type="string", format="password"),
     *             @OA\Property(property="date_naissance", type="string", format="date", example="1995-12-01"),
     *             @OA\Property(property="role", type="string", enum={"user", "admin"})
     *         )
     *     ),
     *     @OA\Response(response=201, description="Success"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function register() {}

    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     summary="Connexion (JWT)",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="otman@gmail.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Authorized"),
     *     @OA\Response(response=422, description="Invalid credentials")
     * )
     */
    public function login() {}

    /**
     * @OA\Post(
     *     path="/api/auth/logout",
     *     summary="Déconnexion",
     *     tags={"Authentication"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Logged out")
     * )
     */
    public function logout() {}

    /**
     * @OA\Post(
     *     path="/api/auth/refresh",
     *     summary="Refresh Token",
     *     tags={"Authentication"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Token refreshed")
     * )
     */
    public function refresh() {}

    /**
     * @OA\Get(
     *     path="/api/users/me",
     *     summary="Profil connecté",
     *     tags={"Profile"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function me() {}

    /**
     * @OA\Get(
     *     path="/api/accounts",
     *     summary="Lister les comptes",
     *     tags={"Accounts"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="List of accounts")
     * )
     */
    public function listAccounts() {}

    /**
     * @OA\Post(
     *     path="/api/accounts",
     *     summary="Créer un compte",
     *     tags={"Accounts"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"type"},
     *             @OA\Property(property="type", type="string", enum={"courant", "epargne", "mineur"}),
     *             @OA\Property(property="overdraft_limit", type="number", example=500),
     *             @OA\Property(property="interest_rate", type="number", example=3.5),
     *             @OA\Property(property="guardian_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Account created")
     * )
     */
    public function createAccount() {}

    /**
     * @OA\Post(
     *     path="/api/transfers",
     *     summary="Initier un virement",
     *     tags={"Transfers"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"from_account_id", "to_account_id", "amount"},
     *             @OA\Property(property="from_account_id", type="integer"),
     *             @OA\Property(property="to_account_id", type="integer"),
     *             @OA\Property(property="amount", type="number")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Transfer success"),
     *     @OA\Response(response=422, description="Transfer fail")
     * )
     */
    public function transfer() {}
}
