<?php
// app/Http/Middleware/FrontendAuth.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Services\ApiClient;

class FrontendAuth
{
    private $apiClient;

    public function __construct(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    public function handle(Request $request, Closure $next)
    {
        if (!Session::has('api_token')) {
            return redirect()->route('login');
        }

        // Vérifier la validité du token
        $response = $this->apiClient->getUser();

        if (!$response->successful()) {
            Session::forget(['api_token', 'user']);
            return redirect()->route('login')
                ->withErrors(['error' => 'Session expirée, veuillez vous reconnecter']);
        }

        // Mettre à jour les infos utilisateur en session
        Session::put('user', $response->json());

        return $next($request);
    }
}
