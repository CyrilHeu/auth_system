<?php

declare(strict_types=1);

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

final class RegistryRepository
{
    public function findActiveByEmail(string $email): ?array
    {
        $row = DB::table('backoffice_registry')
            ->select([
                'id',
                'email',
                'firebase_project_key',
                'restaurant_id',
                'firebase_uid',
                'is_active',
            ])
            ->whereRaw('LOWER(email) = LOWER(?)', [trim($email)])
            ->where('is_active', 1)
            ->first();

        return $row !== null ? (array) $row : null;
    }

    public function attachFirebaseUid(int $registryId, string $firebaseUid): void
    {
        DB::table('backoffice_registry')
            ->where('id', $registryId)
            ->whereNull('firebase_uid')
            ->update(['firebase_uid' => $firebaseUid]);
    }
}
