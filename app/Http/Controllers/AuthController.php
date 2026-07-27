<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Repositories\RegistryRepository;
use App\Services\FirebaseAuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use RuntimeException;
use Throwable;

final class AuthController extends Controller
{
    public function showLogin(Request $request): View|RedirectResponse
    {
        if ($request->session()->has('backoffice_auth')) {
            return redirect()->route('app');
        }

        return view('auth.login', [
            'rememberedEmail' => (string) $request->cookie('backoffice_email', ''),
        ]);
    }

    public function login(
        Request $request,
        RegistryRepository $registry,
        FirebaseAuthService $firebaseAuth,
    ): RedirectResponse {
        $credentials = $request->validate([
            'email' => ['required', 'email:rfc', 'max:254'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Adresse e-mail obligatoire.',
            'email.email' => 'Adresse e-mail incorrecte.',
            'password.required' => 'Mot de passe obligatoire.',
        ]);

        try {
            $registryUser = $registry->findActiveByEmail($credentials['email']);

            if ($registryUser === null) {
                throw new RuntimeException('Adresse e-mail ou mot de passe incorrect.');
            }

            $projectKey = trim((string) ($registryUser['firebase_project_key'] ?? ''));
            $project = config('firebase.projects.'.$projectKey);

            if (!is_array($project)) {
                throw new RuntimeException('Le projet Firebase associé est inconnu.');
            }

            $webApiKey = trim((string) ($project['web_api_key'] ?? ''));
            $projectId = trim((string) ($project['project_id'] ?? ''));
            $restaurantId = trim((string) ($registryUser['restaurant_id'] ?? ''));

            if ($webApiKey === '' || $projectId === '' || $restaurantId === '') {
                throw new RuntimeException('La configuration du compte est incomplète.');
            }

            $firebaseLogin = $firebaseAuth->signIn(
                $webApiKey,
                $credentials['email'],
                $credentials['password'],
            );

            $registeredUid = trim((string) ($registryUser['firebase_uid'] ?? ''));
            $firebaseUid = trim((string) $firebaseLogin['uid']);

            if ($registeredUid !== '' && !hash_equals($registeredUid, $firebaseUid)) {
                throw new RuntimeException('Ce compte Firebase ne correspond pas au registre.');
            }

            if ($registeredUid === '') {
                $registry->attachFirebaseUid((int) $registryUser['id'], $firebaseUid);
            }

            $request->session()->regenerate();

            $now = now()->timestamp;
            $stayConnected = $request->boolean('stay_connected');

            $request->session()->put('backoffice_auth', [
                'registry_id' => (int) $registryUser['id'],
                'firebase_uid' => $firebaseUid,
                'email' => (string) ($registryUser['email'] ?? $credentials['email']),
                'firebase_project_key' => $projectKey,
                'project_id' => $projectId,
                'web_api_key' => $webApiKey,
                'restaurant_id' => $restaurantId,
                'id_token' => (string) $firebaseLogin['id_token'],
                'refresh_token' => (string) $firebaseLogin['refresh_token'],
                'expires_at' => $now + (int) $firebaseLogin['expires_in'],
                'authenticated_at' => $now,
                'stay_connected' => $stayConnected,
                'last_activity_at' => $now,
                'idle_expires_at' => $stayConnected
                    ? $now + 60 * 60 * 24 * 30
                    : $now + 60 * 60,
            ]);

            if ($request->boolean('remember_credentials')) {
                Cookie::queue(cookie(
                    'backoffice_email',
                    $credentials['email'],
                    60 * 24 * 365,
                    config('session.path', '/'),
                    null,
                    true,
                    true,
                    false,
                    'lax',
                ));
            } else {
                Cookie::queue(Cookie::forget('backoffice_email', config('session.path', '/')));
            }

            return redirect()->route('app');
        } catch (Throwable $exception) {
            Log::warning('[BACKOFFICE AUTH] '.$exception->getMessage());

            $request->session()->forget('backoffice_auth');

            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => $exception instanceof RuntimeException
                        ? $exception->getMessage()
                        : 'Une erreur technique empêche la connexion.',
                ]);
        }
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('backoffice_auth');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
