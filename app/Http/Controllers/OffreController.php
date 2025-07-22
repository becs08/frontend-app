<?php

namespace App\Http\Controllers;

use App\Services\ApiClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class OffreController extends Controller
{
    private $apiClient;

    public function __construct(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'categorie', 'localisation', 'type_offre', 'page']);

        $response = $this->apiClient->getOffres($filters);
        $categoriesResponse = $this->apiClient->getCategories();

        if (!$response->successful()) {
            return back()->withErrors(['error' => 'Erreur lors du chargement des offres']);
        }

        $data = $response->json();
        $categories = $categoriesResponse->successful() ? $categoriesResponse->json() : [];

        return view('offres.index', [
            'offres' => $data,
            'categories' => $categories,
            'filters' => $filters
        ]);
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
}
