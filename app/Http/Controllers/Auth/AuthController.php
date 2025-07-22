<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\ApiClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    private $apiClient;

    public function __construct(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $response = $this->apiClient->login($request->email, $request->password);

        if (!$response->successful()) {
            $errors = $response->json()['errors'] ?? ['email' => ['Les informations de connexion sont incorrectes.']];
            return back()->withErrors($errors)->withInput();
        }

        $data = $response->json();
        
        \Log::info('API Auth Response:', ['data' => $data, 'status' => $response->status()]);

        if (!isset($data['token']) || !isset($data['user'])) {
            return back()->withErrors(['email' => ['Format de réponse invalide de l\'API.']])->withInput();
        }

        Session::put('api_token', $data['token']);
        Session::put('user', $data['user']);

        return redirect()->intended('/dashboard');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'user_type' => 'required|in:demandeur,offreur',
        ]);

        $response = $this->apiClient->register($validated);

        if (!$response->successful()) {
            $errors = $response->json()['errors'] ?? ['email' => ['Erreur lors de l\'inscription.']];
            return back()->withErrors($errors)->withInput();
        }

        $data = $response->json();
        
        \Log::info('API Auth Response:', ['data' => $data, 'status' => $response->status()]);

        if (!isset($data['token']) || !isset($data['user'])) {
            return back()->withErrors(['email' => ['Format de réponse invalide de l\'API.']])->withInput();
        }

        Session::put('api_token', $data['token']);
        Session::put('user', $data['user']);

        return redirect('/dashboard');
    }

    public function logout(Request $request)
    {
        $this->apiClient->logout();

        Session::forget(['api_token', 'user']);

        return redirect('/');
    }
}
