<?php

namespace App\Services\Pas;

use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class PasAuthenticator
{
    /** @return array<string, mixed> */
    public function authenticate(string $username, string $password): array
    {
        if ($adminIdentity = $this->authenticateConfiguredAdmin($username, $password)) {
            return $adminIdentity;
        }

        return match (config('services.pas.driver')) {
            'api' => $this->authenticateViaApi($username, $password),
            'database' => $this->authenticateViaDatabase($username, $password),
            'mock' => $this->authenticateMock($username, $password),
            default => throw new PasAuthenticationException('Driver autentikasi PAS tidak dikenal.'),
        };
    }

    /** @return array<string, mixed> */
    private function authenticateViaDatabase(string $username, string $password): array
    {
        $login = trim($username);
        $connection = (string) config('services.pas.database_connection', 'pas');

        try {
            $lecturer = DB::connection($connection)->table('dosen')
                ->leftJoin('program_studi', 'program_studi.jurusan_id', '=', 'dosen.jurusan_id')
                ->where(function ($query) use ($login): void {
                    $query->where('dosen.nidn', $login)
                        ->orWhere('dosen.kd_dosen', $login)
                        ->orWhere('dosen.email', $login);
                })
                ->select([
                    'dosen.dosen_id', 'dosen.nidn', 'dosen.kd_dosen', 'dosen.nama',
                    'dosen.email', 'dosen.password', 'dosen.status_dosen',
                    'program_studi.nama as study_program',
                ])->first();

            if ($lecturer) {
                $this->verifyDatabasePassword($password, $lecturer->password);

                return [
                    'pas_user_id' => 'PAS-DOSEN-'.$lecturer->dosen_id,
                    'username' => $login,
                    'name' => $lecturer->nama,
                    'email' => $lecturer->email,
                    'identity_number' => filled($lecturer->nidn) ? $lecturer->nidn : $lecturer->kd_dosen,
                    'unit' => $lecturer->status_dosen,
                    'study_program' => $lecturer->study_program,
                    'roles' => ['dosen'],
                    'is_active' => true,
                    'pas_password_changed' => ! hash_equals(Str::lower((string) $lecturer->email), Str::lower($login)),
                ];
            }

            $student = DB::connection($connection)->table('mahasiswa')
                ->leftJoin('program_studi', 'program_studi.jurusan_id', '=', 'mahasiswa.jurusan_id')
                ->where(function ($query) use ($login): void {
                    $query->where('mahasiswa.nim', $login)
                        ->orWhere('mahasiswa.email', $login);
                })
                ->select([
                    'mahasiswa.mahasiswa_id', 'mahasiswa.nim', 'mahasiswa.nama',
                    'mahasiswa.email', 'mahasiswa.password', 'mahasiswa.status_mhs',
                    'program_studi.nama as study_program',
                ])->first();

            if ($student) {
                $this->verifyDatabasePassword($password, $student->password);

                return [
                    'pas_user_id' => 'PAS-MAHASISWA-'.$student->mahasiswa_id,
                    'username' => $login,
                    'name' => $student->nama,
                    'email' => $student->email,
                    'identity_number' => $student->nim,
                    'unit' => 'Mahasiswa',
                    'study_program' => $student->study_program,
                    'roles' => ['mahasiswa'],
                    'is_active' => Str::lower((string) $student->status_mhs) === 'aktif',
                    'pas_password_changed' => ! hash_equals(Str::lower((string) $student->email), Str::lower($login)),
                ];
            }
        } catch (QueryException) {
            throw new PasAuthenticationException('Database PAS sedang tidak dapat dihubungi.');
        }

        throw new PasAuthenticationException('Username atau password PAS tidak sesuai.');
    }

    private function verifyDatabasePassword(string $plainPassword, ?string $hashedPassword): void
    {
        if (! filled($hashedPassword) || ! Hash::check($plainPassword, $hashedPassword)) {
            throw new PasAuthenticationException('Username atau password PAS tidak sesuai.');
        }
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

    /** @return array<string, mixed>|null */
    private function authenticateConfiguredAdmin(string $username, string $password): ?array
    {
        if (! config('services.pas.local_admin_enabled')) {
            return null;
        }

        if (! hash_equals((string) config('services.pas.mock_username'), $username) ||
            ! hash_equals((string) config('services.pas.mock_password'), $password)) {
            return null;
        }

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
            'pas_password_changed' => false,
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
