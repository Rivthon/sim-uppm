<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'pas_user_id',
    'username',
    'name',
    'email',
    'identity_number',
    'unit',
    'study_program',
    'roles',
    'account_source',
    'is_active',
    'pas_synced_at',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'roles' => 'array',
            'is_active' => 'boolean',
            'pas_synced_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function hasRole(string $role): bool
    {
        return in_array($role, $this->roles ?? [], true);
    }

    public function roleLabel(): string
    {
        return collect($this->roles ?? [])
            ->map(fn (string $role) => str($role)->replace('_', ' ')->title())
            ->join(', ');
    }

    public function ledProposals(): HasMany
    {
        return $this->hasMany(Proposal::class, 'lead_id');
    }
}
