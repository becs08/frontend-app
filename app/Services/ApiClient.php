<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class ApiClient
{
    private $baseUrl;
    private $timeout;

    public function __construct()
    {
        $this->baseUrl = config('app.api_base_url', 'http://localhost:8000/api');
        $this->timeout = config('app.api_timeout', 30);
    }

    private function makeRequest($method, $endpoint, $data = [], $headers = [])
    {
        $token = Session::get('api_token');

        if ($token) {
            $headers['Authorization'] = 'Bearer ' . $token;
        }

        try {
            $startTime = microtime(true);
            Log::info("Starting API request: {$method} {$this->baseUrl}{$endpoint}");
            
            $response = Http::timeout($this->timeout)
                ->withHeaders($headers)
                ->$method($this->baseUrl . $endpoint, $data);

            $endTime = microtime(true);
            $duration = $endTime - $startTime;
            
            Log::info("API request completed: {$method} {$endpoint} - Status: {$response->status()} - Duration: {$duration}s");
            
            if ($duration > 0.5) {
                Log::warning("Slow API request detected: {$method} {$endpoint} took {$duration} seconds");
            }

            return $response;
        } catch (\Exception $e) {
            Log::error('API Request failed: ' . $e->getMessage());
            return Http::response(['error' => 'Erreur de connexion'], 500);
        }
    }

    // Auth methods
    public function login($email, $password)
    {
        return $this->makeRequest('post', '/login', [
            'email' => $email,
            'password' => $password
        ]);
    }

    public function register($userData)
    {
        return $this->makeRequest('post', '/register', $userData);
    }

    public function logout()
    {
        return $this->makeRequest('post', '/logout');
    }

    public function getUser()
    {
        return $this->makeRequest('get', '/user');
    }

    public function getProfile()
    {
        return $this->makeRequest('get', '/profile');
    }

    public function updateProfile($data)
    {
        return $this->makeRequest('put', '/profile', $data);
    }

    public function getDashboardStats()
    {
        return $this->makeRequest('get', '/dashboard/stats');
    }

    // Offres methods
    public function getOffres($filters = [])
    {
        return $this->makeRequest('get', '/offres?' . http_build_query($filters));
    }

    public function getOffre($id)
    {
        return $this->makeRequest('get', "/offres/{$id}");
    }

    public function createOffre($data)
    {
        return $this->makeRequest('post', '/offres', $data);
    }

    public function updateOffre($id, $data)
    {
        return $this->makeRequest('put', "/offres/{$id}", $data);
    }

    public function deleteOffre($id)
    {
        return $this->makeRequest('delete', "/offres/{$id}");
    }

    public function getMesOffres($page = 1)
    {
        return $this->makeRequest('get', "/mes-offres?page={$page}");
    }

    public function getCategories()
    {
        return $this->makeRequest('get', '/categories');
    }

    // Demandes methods
    public function createDemande($offreId, $data)
    {
        return $this->makeRequest('post', "/offres/{$offreId}/demandes", $data);
    }

    public function updateDemande($id, $data)
    {
        return $this->makeRequest('put', "/demandes/{$id}", $data);
    }

    public function getMesDemandes($page = 1)
    {
        return $this->makeRequest('get', "/mes-demandes?page={$page}");
    }

    public function getDemandesRecues($page = 1)
    {
        return $this->makeRequest('get', "/demandes-recues?page={$page}");
    }
}
