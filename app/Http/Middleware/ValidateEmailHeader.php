<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ValidateEmailHeader
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $email = $request->header('X-User-Email');
        if (!empty($email)) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new ValidationException('Invalid email format provided.');
            }
            // Retrieve the user from the database
            $user = $this->getUserFromEmail($email);
            // Attach the user model to the request but INTERNALLY ONLY
            $request->setUserResolver(function () use ($user) {
                return $user;
            });
        }
        return $next($request);
    }

    /**
     * Retrieves a user from the database based on the provided email.
     *
     * @param string $email The email of the user to retrieve.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If the user with the provided email does not exist.
     * @return \App\Models\User The user object.
     */
    protected function getUserFromEmail(string $email): User
    {
        try {
            $user = User::where('email', $email)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException('User for the provided email does not exist.');
        }
        return $user;
    }
}
