<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Game; // Asegurarse de que esta línea esté aquí
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Verificar si el usuario tiene partidas activas como anfitrión
        if (Game::where('host_id', Auth::id())
            ->whereIn('status', ['waiting', 'in_progress'])
            ->exists()) {
            return back()->with('error', 'No puedes cerrar sesión mientras seas anfitrión de una partida activa. Por favor, termina o abandona tus partidas primero.');
        }
        
        // Verificar si el usuario está en cualquier partida activa (como invitado)
        if (Game::where(function ($query) {
                $query->where('host_id', Auth::id())
                    ->orWhere('guest_id', Auth::id());
            })
            ->whereIn('status', ['waiting', 'in_progress'])
            ->exists()) {
            return back()->with('error', 'No puedes cerrar sesión mientras estés en una partida activa. Por favor, termina o abandona tu partida actual primero.');
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
