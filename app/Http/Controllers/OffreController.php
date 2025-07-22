<?php

namespace App\Http\Controllers;

use App\Services\ApiClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class OffreController extends Controller
{
    private $apiClient;

    public function __construct(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }
    
    /**
     * Optimized index method using concurrent API requests and caching
     */
    public function indexOptimized(Request $request)
    {
        $startTime = microtime(true);
        
        $filters = $request->only(['search', 'categorie', 'localisation', 'type_offre', 'page']);
        
        // Check if categories are cached
        $categories = Cache::remember('categories', 3600, function () {
            $baseUrl = config('app.api_base_url', 'http://localhost:8000/api');
            $timeout = config('app.api_timeout', 30);
            $token = Session::get('api_token');
            $headers = $token ? ['Authorization' => 'Bearer ' . $token] : [];
            
            $response = Http::timeout($timeout)
                ->withHeaders($headers)
                ->get($baseUrl . '/categories');
                
            return $response->successful() ? $response->json() : [];
        });
        
        // Prepare API request for offres
        $baseUrl = config('app.api_base_url', 'http://localhost:8000/api');
        $timeout = config('app.api_timeout', 30);
        $token = Session::get('api_token');
        $headers = $token ? ['Authorization' => 'Bearer ' . $token] : [];
        
        // Get offres
        $offresResponse = Http::timeout($timeout)
            ->withHeaders($headers)
            ->get($baseUrl . '/offres?' . http_build_query($filters));
        
        \Log::info('API requests took: ' . (microtime(true) - $startTime) . ' seconds');
        
        // Check if offres request was successful
        if (!$offresResponse->successful()) {
            return back()->withErrors(['error' => 'Erreur lors du chargement des offres']);
        }
        
        $data = $offresResponse->json();
        
        $totalTime = microtime(true) - $startTime;
        \Log::info('Total indexOptimized execution time: ' . $totalTime . ' seconds');
        
        return view('offres.index', [
            'offres' => $data,
            'categories' => $categories,
            'filters' => $filters
        ]);
    }

    public function index(Request $request)
    {
        $startTime = microtime(true);
        
        $filters = $request->only(['search', 'categorie', 'localisation', 'type_offre', 'page']);

        // Log start of API calls
        \Log::info('Starting offres.index API calls');
        
        $offresStartTime = microtime(true);
        $response = $this->apiClient->getOffres($filters);
        $offresEndTime = microtime(true);
        \Log::info('getOffres API call took: ' . ($offresEndTime - $offresStartTime) . ' seconds');
        
        $categoriesStartTime = microtime(true);
        $categoriesResponse = $this->apiClient->getCategories();
        $categoriesEndTime = microtime(true);
        \Log::info('getCategories API call took: ' . ($categoriesEndTime - $categoriesStartTime) . ' seconds');

        if (!$response->successful()) {
            return back()->withErrors(['error' => 'Erreur lors du chargement des offres']);
        }

        $data = $response->json();
        $categories = $categoriesResponse->successful() ? $categoriesResponse->json() : [];

        $viewStartTime = microtime(true);
        $result = view('offres.index', [
            'offres' => $data,
            'categories' => $categories,
            'filters' => $filters
        ]);
        $viewEndTime = microtime(true);
        \Log::info('View rendering took: ' . ($viewEndTime - $viewStartTime) . ' seconds');
        
        $totalTime = microtime(true) - $startTime;
        \Log::info('Total offres.index execution time: ' . $totalTime . ' seconds');
        
        return $result;
    }

    public function show($id)
    {
        $response = $this->apiClient->getOffre($id);

        if (!$response->successful()) {
            if ($response->status() === 404) {
                abort(404);
            }
            return back()->withErrors(['error' => 'Erreur lors du chargement de l\'offre']);
        }

        $data = $response->json();

        return view('offres.show', $data);
    }

    public function create()
    {
        if (!Session::has('api_token')) {
            return redirect()->route('login');
        }

        $categoriesResponse = $this->apiClient->getCategories();
        $categories = $categoriesResponse->successful() ? $categoriesResponse->json() : [];

        return view('offres.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'categorie' => 'required|string',
            'prix_min' => 'nullable|numeric|min:0',
            'prix_max' => 'nullable|numeric|min:0|gte:prix_min',
            'localisation' => 'required|string|max:255',
            'type_offre' => 'required|in:service,produit,formation',
            'date_expiration' => 'nullable|date|after:today',
        ]);

        $response = $this->apiClient->createOffre($validated);

        if (!$response->successful()) {
            $errors = $response->json()['errors'] ?? ['error' => 'Erreur lors de la création'];
            return back()->withErrors($errors)->withInput();
        }

        $data = $response->json();

        return redirect()->route('offres.show', $data['offre']['id'])
            ->with('success', 'Offre créée avec succès !');
    }

    public function edit($id)
    {
        if (!Session::has('api_token')) {
            return redirect()->route('login');
        }

        $response = $this->apiClient->getOffre($id);
        $categoriesResponse = $this->apiClient->getCategories();

        if (!$response->successful()) {
            return back()->withErrors(['error' => 'Offre non trouvée']);
        }

        $data = $response->json();

        if (!$data['can_edit']) {
            abort(403);
        }

        $categories = $categoriesResponse->successful() ? $categoriesResponse->json() : [];

        return view('offres.edit', [
            'offre' => $data['offre'],
            'categories' => $categories
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'categorie' => 'required|string',
            'prix_min' => 'nullable|numeric|min:0',
            'prix_max' => 'nullable|numeric|min:0|gte:prix_min',
            'localisation' => 'required|string|max:255',
            'type_offre' => 'required|in:service,produit,formation',
            'date_expiration' => 'nullable|date|after:today',
            'statut' => 'nullable|in:active,suspendue',
        ]);

        $response = $this->apiClient->updateOffre($id, $validated);

        if (!$response->successful()) {
            $errors = $response->json()['errors'] ?? ['error' => 'Erreur lors de la mise à jour'];
            return back()->withErrors($errors)->withInput();
        }

        return redirect()->route('offres.show', $id)
            ->with('success', 'Offre mise à jour avec succès !');
    }

    public function destroy($id)
    {
        $response = $this->apiClient->deleteOffre($id);

        if (!$response->successful()) {
            return back()->withErrors(['error' => 'Erreur lors de la suppression']);
        }

        return redirect()->route('offres.index')
            ->with('success', 'Offre supprimée avec succès !');
    }

    public function mesOffres(Request $request)
    {
        if (!Session::has('api_token')) {
            return redirect()->route('login');
        }

        $page = $request->get('page', 1);
        $response = $this->apiClient->getMesOffres($page);

        if (!$response->successful()) {
            return back()->withErrors(['error' => 'Erreur lors du chargement']);
        }

        $data = $response->json();

        return view('offres.mes-offres', ['offres' => $data]);
    }
    
    /**
     * Clear categories cache
     */
    public function clearCategoriesCache()
    {
        Cache::forget('categories');
        return response()->json(['message' => 'Categories cache cleared']);
    }
}
