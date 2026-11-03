<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Pas\PasAuthenticationException;
use App\Services\Pas\PasAuthenticator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PasLoginController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request, PasAuthenticator $authenticator): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string', 'max:100'],
            'password' => ['required', 'string', 'max:255'],
        ]);

        try {
            $identity = $authenticator->authenticate($credentials['username'], $credentials['password']);
        } catch (PasAuthenticationException $exception) {
            return back()
                ->withInput($request->only('username'))
                ->withErrors(['username' => $exception->getMessage()]);
        }

        if (! $identity['is_active']) {
            return back()->withErrors(['username' => 'Akun PAS Anda sedang tidak aktif.']);
        }

        $user = User::updateOrCreate(
            ['pas_user_id' => $identity['pas_user_id']],
            [...$identity, 'account_source' => 'pas', 'pas_synced_at' => now()],
        );

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
