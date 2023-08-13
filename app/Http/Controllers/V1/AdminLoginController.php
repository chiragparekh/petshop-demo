<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\AdminLoginRequest;
use App\Models\User;
use App\Services\JwtService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminLoginController extends Controller
{
    public function __construct(private JwtService $jwtService)
    {
    }

    /**
     * Handle the incoming request.
     */
    public function __invoke(AdminLoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        if(! Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password'], 'is_admin' => 1])) {
            return new JsonResponse([
                'message' => 'Invalid login credentials.'
            ], 401);
        }

        return new JsonResponse([
            'token' => $this->jwtService->generate(Auth::user()->uuid)
        ]);
    }
}
