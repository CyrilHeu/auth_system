<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\FirebaseAuthService;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

final class EnsureBackofficeAuthenticated
{
    public function __construct(private FirebaseAuthService $firebaseAuth)
    {
    }

    public function handle(Request $request, Closure $next): Response|RedirectResponse
    {
        $auth = $request->session()->get('backoffice_auth');

        if (!is_array($auth)) {
            return redirect()->route('login');
        }

        $now = now()->timestamp;
        $idleExpiresAt = (int) ($auth['idle_expires_at'] ?? 0);

        if ($idleExpiresAt <= $now) {
            $request->session()->forget('backoffice_auth');

            return redirect()->route('login')
                ->withErrors(['email' => 'Votre session a expiré après une période d’inactivité.']);
        }

        try {
            $expiresAt = (int) ($auth['expires_at'] ?? 0);

            if ($expiresAt <= $now + 120) {
                $refreshToken = trim((string) ($auth['refresh_token'] ?? ''));
                $webApiKey = trim((string) ($auth['web_api_key'] ?? ''));

                if ($refreshToken === '' || $webApiKey === '') {
                    throw new \RuntimeException('Jeton Firebase incomplet.');
                }

                $refreshed = $this->firebaseAuth->refreshToken($webApiKey, $refreshToken);
                $auth['id_token'] = $refreshed['id_token'];
                $auth['refresh_token'] = $refreshed['refresh_token'];
                $auth['expires_at'] = $now + (int) $refreshed['expires_in'];
            }

            $stayConnected = (bool) ($auth['stay_connected'] ?? false);
            $auth['last_activity_at'] = $now;
            $auth['idle_expires_at'] = $stayConnected
                ? $now + 60 * 60 * 24 * 30
                : $now + 60 * 60;

            $request->session()->put('backoffice_auth', $auth);
        } catch (Throwable $exception) {
            Log::warning('[BACKOFFICE TOKEN] '.$exception->getMessage());
            $request->session()->forget('backoffice_auth');

            return redirect()->route('login')
                ->withErrors(['email' => 'Votre connexion a expiré. Veuillez vous reconnecter.']);
        }

        return $next($request);
    }
}
