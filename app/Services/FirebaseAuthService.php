<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

final class FirebaseAuthService
{
    public function signIn(string $webApiKey, string $email, string $password): array
    {
        try {
            $response = Http::acceptJson()
                ->asJson()
                ->connectTimeout(8)
                ->timeout(15)
                ->post(
                    'https://identitytoolkit.googleapis.com/v1/accounts:signInWithPassword?key='.rawurlencode($webApiKey),
                    [
                        'email' => $email,
                        'password' => $password,
                        'returnSecureToken' => true,
                    ]
                );
        } catch (ConnectionException $exception) {
            throw new RuntimeException('Firebase est actuellement inaccessible.', 0, $exception);
        }

        if (!$response->successful()) {
            $firebaseError = (string) $response->json('error.message', 'AUTHENTICATION_FAILED');
            throw new RuntimeException($this->translateSignInError($firebaseError));
        }

        $idToken = trim((string) $response->json('idToken', ''));
        $uid = trim((string) $response->json('localId', ''));

        if ($idToken === '' || $uid === '') {
            throw new RuntimeException('Réponse Firebase incomplète.');
        }

        return [
            'id_token' => $idToken,
            'refresh_token' => trim((string) $response->json('refreshToken', '')),
            'uid' => $uid,
            'email' => trim((string) $response->json('email', $email)),
            'expires_in' => max(1, (int) $response->json('expiresIn', 3600)),
        ];
    }

    public function refreshToken(string $webApiKey, string $refreshToken): array
    {
        try {
            $response = Http::asForm()
                ->connectTimeout(8)
                ->timeout(15)
                ->post(
                    'https://securetoken.googleapis.com/v1/token?key='.rawurlencode($webApiKey),
                    [
                        'grant_type' => 'refresh_token',
                        'refresh_token' => $refreshToken,
                    ]
                );
        } catch (ConnectionException $exception) {
            throw new RuntimeException('Firebase est actuellement inaccessible.', 0, $exception);
        }

        if (!$response->successful()) {
            throw new RuntimeException('Le renouvellement de la connexion Firebase a échoué.');
        }

        $idToken = trim((string) $response->json('id_token', ''));
        if ($idToken === '') {
            throw new RuntimeException('Firebase n’a retourné aucun nouveau jeton.');
        }

        return [
            'id_token' => $idToken,
            'refresh_token' => trim((string) $response->json('refresh_token', $refreshToken)),
            'expires_in' => max(1, (int) $response->json('expires_in', 3600)),
            'firebase_uid' => trim((string) $response->json('user_id', '')),
        ];
    }

    public function sendPasswordReset(string $webApiKey, string $email): void
    {
        try {
            $response = Http::acceptJson()
                ->asJson()
                ->withHeaders(['X-Firebase-Locale' => 'fr'])
                ->connectTimeout(8)
                ->timeout(15)
                ->post(
                    'https://identitytoolkit.googleapis.com/v1/accounts:sendOobCode?key='.rawurlencode($webApiKey),
                    [
                        'requestType' => 'PASSWORD_RESET',
                        'email' => $email,
                    ]
                );
        } catch (ConnectionException $exception) {
            throw new RuntimeException('Firebase est actuellement inaccessible.', 0, $exception);
        }

        if (!$response->successful()) {
            throw new RuntimeException('La demande de réinitialisation n’a pas pu être envoyée.');
        }
    }

    private function translateSignInError(string $error): string
    {
        return match ($error) {
            'EMAIL_NOT_FOUND', 'INVALID_PASSWORD', 'INVALID_LOGIN_CREDENTIALS' =>
                'Adresse e-mail ou mot de passe incorrect.',
            'USER_DISABLED' => 'Ce compte Firebase est désactivé.',
            'TOO_MANY_ATTEMPTS_TRY_LATER' => 'Trop de tentatives. Réessaie ultérieurement.',
            default => 'La connexion Firebase a échoué.',
        };
    }
}
