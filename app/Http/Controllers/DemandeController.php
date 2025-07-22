<?php

namespace App\Http\Controllers;

use App\Services\ApiClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class DemandeController extends Controller
{
    private $apiClient;

    public function __construct(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    public function store(Request $request, $offreId)
    {
        $validated = $request->validate([
            'message' => 'required|string',
            'prix_propose' => 'nullable|numeric|min:0',
        ]);

        $response = $this->apiClient->createDemande($offreId, $validated);

        if (!$response->successful()) {
            $error = $response->json()['message'] ?? 'Erreur lors de l\'envoi de la demande';
            return back()->withErrors(['error' => $error]);
        }

        return back()->with('success', 'Votre demande a été envoyée avec succès !');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'statut' => 'required|in:acceptee,refusee',
            'message_reponse' => 'nullable|string',
        ]);

        $response = $this->apiClient->updateDemande($id, $validated);

        if (!$response->successful()) {
            return back()->withErrors(['error' => 'Erreur lors de la réponse']);
        }

        return back()->with('success', 'Réponse envoyée avec succès !');
    }

    public function mesDemandes(Request $request)
    {
        if (!Session::has('api_token')) {
            return redirect()->route('login');
        }

        $page = $request->get('page', 1);
        $response = $this->apiClient->getMesDemandes($page);

        if (!$response->successful()) {
            return back()->withErrors(['error' => 'Erreur lors du chargement']);
        }

        $data = $response->json();

        return view('demandes.mes-demandes', ['demandes' => $data]);
    }

    public function demandesRecues(Request $request)
    {
        if (!Session::has('api_token')) {
            return redirect()->route('login');
        }

        $page = $request->get('page', 1);
        $response = $this->apiClient->getDemandesRecues($page);

        if (!$response->successful()) {
            return back()->withErrors(['error' => 'Erreur lors du chargement']);
        }

        $data = $response->json();

        return view('demandes.demandes-recues', ['demandes' => $data]);
    }
}
