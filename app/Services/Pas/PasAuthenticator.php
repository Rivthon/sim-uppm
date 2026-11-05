<?php

namespace App\Services\Pas;

use App\Models\User;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class PasAuthenticator
{
    /** @return array<string, mixed> */
    public function authenticate(string $username, string $password): array
    {
        return match (config('services.pas.driver')) {
            'api' => $this->authenticateViaApi($username, $password),
            'mock' => $this->authenticateMock($username, $password),
            default => throw new PasAuthenticationException('Driver autentikasi PAS tidak dikenal.'),
        };
    }

    /** @return array<string, mixed> */
    private function authenticateViaApi(string $username, string $password): array
    {
        $endpoint = config('services.pas.endpoint');

        if (! $endpoint) {
            throw new PasAuthenticationException('Endpoint autentikasi PAS belum dikonfigurasi.');
        }

        try {
            $request = Http::acceptJson()
                ->asJson()
                ->timeout(config('services.pas.timeout'));

            if ($token = config('services.pas.token')) {
                $request = $request->withToken($token);
            }

            $response = $request->post($endpoint, [
                config('services.pas.username_field') => $username,
                config('services.pas.password_field') => $password,
            ]);
        } catch (ConnectionException) {
            throw new PasAuthenticationException('Layanan PAS sedang tidak dapat dihubungi.');
        }

        if ($response->status() === 401 || $response->status() === 422) {
            throw new PasAuthenticationException('Username atau password PAS tidak sesuai.');
        }

        if (! $response->successful()) {
            throw new PasAuthenticationException('PAS menolak permintaan login. Silakan coba kembali.');
        }

        $payload = $response->json('data') ?? $response->json('user') ?? $response->json();

        if (! is_array($payload)) {
            throw new PasAuthenticationException('Format respons PAS tidak valid.');
        }

        return $this->normalize($payload, $username);
    }

    /** @return array<string, mixed> */
    private function authenticateMock(string $username, string $password): array
    {
        if (! app()->environment(['local', 'testing'])) {
            throw new PasAuthenticationException('Username atau password PAS tidak sesuai.');
        }

        if (hash_equals((string) config('services.pas.mock_username'), $username) &&
            hash_equals((string) config('services.pas.mock_password'), $password)) {
            return [
                'pas_user_id' => 'PAS-DEMO-001',
                'username' => $username,
                'name' => 'Administrator UPPM',
                'email' => 'uppm@kampus.ac.id',
                'identity_number' => 'UPPM001',
                'unit' => 'Unit Penelitian dan Pengabdian kepada Masyarakat',
                'study_program' => null,
                'roles' => ['admin_uppm'],
                'is_active' => true,
            ];
        }

        $user = User::query()
            ->where('username', $username)
            ->orWhere(function ($query) use ($username): void {
                $query->where('pas_password_changed', true)
                    ->where('identity_number', $username);
            })
            ->first();

        if (! $user || ! $user->password || ! Hash::check($password, $user->password)) {
            throw new PasAuthenticationException('Username atau password PAS tidak sesuai.');
        }

        return [
            'pas_user_id' => $user->pas_user_id,
            'username' => $user->username,
            'name' => $user->name,
            'email' => $user->email,
            'identity_number' => $user->identity_number,
            'unit' => $user->unit,
            'study_program' => $user->study_program,
            'roles' => $user->roles,
            'is_active' => $user->is_active,
            'pas_password_changed' => $user->pas_password_changed,
        ];
    }

    /** @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    private function normalize(array $payload, string $fallbackUsername): array
    {
        $externalId = Arr::get($payload, 'id')
            ?? Arr::get($payload, 'user_id')
            ?? Arr::get($payload, 'pas_user_id');

        if (! $externalId) {
            throw new PasAuthenticationException('Respons PAS tidak memiliki ID pengguna.');
        }

        $roles = Arr::get($payload, 'roles', Arr::get($payload, 'role', ['dosen']));
        $roles = collect(is_array($roles) ? $roles : [$roles])
            ->filter()
            ->map(fn ($role) => Str::of((string) $role)->lower()->replace([' ', '-'], '_')->value())
            ->map(fn (string $role): string => match ($role) {
                'student' => 'mahasiswa',
                'lecturer' => 'dosen',
                default => $role,
            })
            ->unique()
            ->values()
            ->all();
        $passwordChanged = $this->passwordWasChanged($payload);
        $pasUsername = (string) (Arr::get($payload, 'username') ?? $fallbackUsername);
        $identityNumber = $this->identityNumber($payload, $roles);
        $syncedUsername = $passwordChanged && filled($identityNumber) ? $identityNumber : $pasUsername;

        return [
            'pas_user_id' => (string) $externalId,
            'username' => $syncedUsername,
            'name' => (string) (Arr::get($payload, 'name') ?? Arr::get($payload, 'nama') ?? $fallbackUsername),
            'email' => Arr::get($payload, 'email'),
            'identity_number' => $identityNumber,
            'unit' => Arr::get($payload, 'unit') ?? Arr::get($payload, 'unit_kerja'),
            'study_program' => Arr::get($payload, 'study_program') ?? Arr::get($payload, 'prodi'),
            'roles' => $roles,
            'is_active' => (bool) Arr::get($payload, 'is_active', Arr::get($payload, 'aktif', true)),
            'pas_password_changed' => $passwordChanged,
        ];
    }

    /** @param array<string, mixed> $payload
     * @param  list<string>  $roles
     */
    private function identityNumber(array $payload, array $roles): ?string
    {
        $identity = match (true) {
            in_array('mahasiswa', $roles, true) => Arr::get($payload, 'nim')
                ?? Arr::get($payload, 'identity_number'),
            in_array('dosen', $roles, true) => Arr::get($payload, 'nidn')
                ?? Arr::get($payload, 'kode_dosen')
                ?? Arr::get($payload, 'lecturer_code')
                ?? Arr::get($payload, 'identity_number')
                ?? Arr::get($payload, 'nip'),
            default => Arr::get($payload, 'identity_number') ?? Arr::get($payload, 'nip'),
        };

        return filled($identity) ? (string) $identity : null;
    }

    /** @param array<string, mixed> $payload */
    private function passwordWasChanged(array $payload): bool
    {
        if (filled(Arr::get($payload, 'password_changed_at'))) {
            return true;
        }

        $value = Arr::get($payload, 'password_changed')
            ?? Arr::get($payload, 'is_password_changed')
            ?? Arr::get($payload, 'has_changed_password')
            ?? Arr::get($payload, 'ganti_password')
            ?? Arr::get($payload, 'status_ganti_password');

        if (is_bool($value)) {
            return $value;
        }

        return in_array(Str::lower(trim((string) $value)), ['1', 'true', 'yes', 'ya', 'sudah', 'changed', 'diganti'], true);
    }
}
