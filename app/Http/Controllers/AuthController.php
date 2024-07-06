<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\GetUserByIdAndVerifyJWTToken;
use App\Http\Requests\PasswordRecoveryRequest;
use App\Http\Requests\PasswordResetRequest;
use App\Http\Requests\PasswordResetWithTokenRequest;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\VerifyTokenRequest;
use App\Http\Resources\PasswordRecoveryEmailSentResource;
use App\Http\Resources\PasswordRecoveryResource;
use App\Http\Resources\PasswordRecoverySuccessResource;
use App\Http\Resources\UserLoginResource;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(UserRegisterRequest $request)
    {
        return new UserResource($this->authService->register($request->validated()));
    }

    public function login(UserLoginRequest $request)
    {
        return new UserLoginResource($this->authService->login($request->validated()));
    }

    public function logout(VerifyTokenRequest $request)
    {
        return $this->authService->logout($request->token);
    }

    public function verifyJWT(VerifyTokenRequest $request)
    {
        return new UserResource($this->authService->verifyJWT($request->token));
    }

    public function getUserByIdAndVerifyJWTRequest(GetUserByIdAndVerifyJWTToken $request)
    {
        return new UserResource($this->authService->getUserByIdAndVerifyJWTRequest($request->token, $request->user_id));
    }

    public function verify(VerifyTokenRequest $request)
    {
        return $this->authService->verify($request->token);
    }

    public function passwordRecovery(PasswordRecoveryRequest $request)
    {
        // In development environment, send token back
        // In production, send email with a token
        if (config('app.env') === 'production') {
            // Send email
            $this->authService->sendPasswordRecoveryEmail($request->email);
            return (new PasswordRecoveryEmailSentResource(true))->withoutDataWrapper();
        }
        return new PasswordRecoveryResource($this->authService->passwordRecovery($request->email));
    }

    public function resetPasswordWithToken(PasswordResetWithTokenRequest $request)
    {
        //The rest_token check is performed in the PasswordResetWithTokenRequest
        return (new PasswordRecoverySuccessResource($this->authService->resetPasswordWithToken($request->email, $request->password)))->withoutDataWrapper();
    }

    public function resetPassword(PasswordResetRequest $request)
    {
        return new UserLoginResource($this->authService->resetPassword($request->token, $request->password, $request->current_password));
    }

    public function refresh(VerifyTokenRequest $request)
    {
        return new UserLoginResource($this->authService->refreshJWT($request->token));
    }
}
