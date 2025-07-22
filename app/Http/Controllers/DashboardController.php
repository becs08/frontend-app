<?php

namespace App\Http\Controllers;

use App\Services\ApiClient;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
    private $apiClient;

    public function __construct(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    public function index()
    {
        if (!Session::has('api_token')) {
            return redirect()->route('login');
        }

        $response = $this->apiClient->getDashboardStats();

        if (!$response->successful()) {
            return back()->withErrors(['error' => 'Erreur lors du chargement du dashboard']);
        }

        $data = $response->json();

        return view('dashboard', $data);
    }
}
