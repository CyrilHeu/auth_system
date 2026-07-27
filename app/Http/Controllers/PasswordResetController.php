<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Repositories\RegistryRepository;
use App\Services\FirebaseAuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

final class PasswordResetController extends Controller
{
    public function show(): View
    {
        return view('auth.forgot-password');
    }

    public function send(
        Request $request,
        RegistryRepository $registry,
        FirebaseAuthService $firebaseAuth,
    ): RedirectResponse {
        $validated = $request->validate([
            'email' => ['required', 'email:rfc', 'max:254'],
        ], [
            'email.required' => 'Adresse e-mail obligatoire.',
            'email.email' => 'Adresse e-mail incorrecte.',
        ]);

        $genericMessage = 'Si cette adresse correspond à un compte autorisé, un e-mail de réinitialisation vient d’être envoyé.';

        try {
            $registryUser = $registry->findActiveByEmail($validated['email']);

            if ($registryUser !== null) {
                $projectKey = trim((string) ($registryUser['firebase_project_key'] ?? ''));
                $project = config('firebase.projects.'.$projectKey);
                $webApiKey = is_array($project)
                    ? trim((string) ($project['web_api_key'] ?? ''))
                    : '';

                if ($webApiKey !== '') {
                    $firebaseAuth->sendPasswordReset($webApiKey, $validated['email']);
                }
            }
        } catch (Throwable $exception) {
            Log::warning('[BACKOFFICE PASSWORD RESET] '.$exception->getMessage());
        }

        return back()->with('status', $genericMessage);
    }
}
