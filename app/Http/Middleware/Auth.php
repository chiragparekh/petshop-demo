<?php

namespace App\Http\Middleware;

use App\Enums\UserType;
use App\Models\User;
use App\Services\JwtService;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Lcobucci\JWT\UnencryptedToken;
use Symfony\Component\HttpFoundation\Response;

class Auth
{
    public function __construct(private JwtService $jwtService)
    {
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $userType = 'user'): Response
    {
        $token = $request->bearerToken();

        $token = $this->jwtService->parse($token);

        if(! $token) {
            return $this->unauthorized();
        }

        $login = $this->login($token, $userType);

        if(! $login) {
            return $this->unauthorized();
        }

        return $next($request);
    }

    private function login(UnencryptedToken $token, string $userType): bool
    {
        $userQuery = User::whereUuid($token->claims()->get('userUuid'));

        if($userType === 'admin') {
            $userQuery->where('is_admin', 1);
        } else {
            $userQuery->where('is_admin', 0);
        }

        $user = $userQuery->first();

        if(! $user) {
            return false;
        }

        \Illuminate\Support\Facades\Auth::login($user);

        return true;
    }

    private function unauthorized()
    {
        return new JsonResponse([
            'message' => 'You are not authorized'
        ], 401);
    }
}
