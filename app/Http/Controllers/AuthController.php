<?php

namespace App\Http\Controllers;

use App\Models\AuthSession;
use App\Models\Role;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Inertia\Inertia;
use Inertia\Response;

class AuthController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => true,
        ]);
    }

    public function register(): Response
    {
        return Inertia::render('Auth/Register', [
            'teams' => Team::query()
                ->where('status', 'active')
                ->orderBy('name')
                ->get(['id', 'name']),
            'jobRoles' => [
                ['value' => 'manager', 'label' => 'Manager'],
                ['value' => 'developer', 'label' => 'Developer'],
                ['value' => 'designer', 'label' => 'Designer'],
                ['value' => 'tester', 'label' => 'Tester'],
                ['value' => 'analyst', 'label' => 'Analyst'],
            ],
        ]);
    }

    public function storeRegistration(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email', 'unique:team_members,email'],
            'team_id' => [
                'required',
                'integer',
                Rule::exists('teams', 'id')->where(fn ($query) => $query->where('status', 'active')),
            ],
            'role' => ['required', 'string', Rule::in(['manager', 'developer', 'designer', 'tester', 'analyst'])],
            'password' => ['required', 'confirmed', PasswordRule::defaults()],
        ]);

        $teamMember = TeamMember::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'team_id' => $data['team_id'],
            'role_id' => Role::query()->where('name', 'team_member')->value('id'),
            'role' => $data['role'],
            'status' => 'active',
        ]);

        Auth::guard('team_member')->login($teamMember);
        $request->session()->regenerate();

        return redirect()->route('team-member.dashboard')
            ->with('success', 'Your team-member account was created.');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['sometimes', 'boolean'],
        ]);

        $remember = (bool) ($credentials['remember'] ?? false);

        if (Auth::guard('web')->attempt(
            ['email' => $credentials['email'], 'password' => $credentials['password'], 'is_active' => true],
            $remember
        )) {
            $request->session()->regenerate();
            $user = $request->user('web');
            $user->forceFill(['last_login_at' => now()])->save();
            AuthSession::query()->updateOrCreate(
                ['id' => $request->session()->getId()],
                ['user_id' => $user->id, 'ip_address' => $request->ip(), 'user_agent' => $request->userAgent(), 'last_activity' => now()]
            );

            if ($user->role?->name !== 'admin' && ! $user->hasVerifiedEmail()) {
                return redirect()->route('verification.notice');
            }

            return redirect()->intended(route('dashboard'));
        }

        if (Auth::guard('team_member')->attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
            'status' => 'active',
        ], $remember)) {
            $request->session()->regenerate();

            return redirect()->intended(route('team-member.dashboard'));
        }

        return back()
            ->withErrors(['email' => 'These credentials do not match our records.'])
            ->onlyInput('email');
    }

    public function destroy(Request $request): RedirectResponse
    {
        AuthSession::query()->whereKey($request->session()->getId())->delete();
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function verify(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('dashboard');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        $request->user()->forceFill(['is_active' => true])->save();

        return redirect()->route('login')->with('success', 'Your email is verified. You can now sign in.');
    }

    public function resendVerification(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('dashboard');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('success', 'Verification link sent.');
    }

    public function verificationNotice(): Response
    {
        return Inertia::render('Auth/VerifyEmail');
    }

    public function forgotPassword(): Response
    {
        return Inertia::render('Auth/ForgotPassword');
    }

    public function resetForm(string $token): Response
    {
        return Inertia::render('Auth/ResetPassword', ['token' => $token, 'email' => request('email')]);
    }

    public function sendResetLink(Request $request): RedirectResponse
    {
        $data = $request->validate(['email' => ['required', 'email']]);
        $status = Password::sendResetLink($data);

        return $status === Password::RESET_LINK_SENT
            ? back()->with('success', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'token' => ['required'], 'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', PasswordRule::defaults()],
        ]);

        $status = Password::reset($data, function (User $user, string $password): void {
            $user->forceFill(['password' => Hash::make($password), 'remember_token' => null])->save();
        });

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('success', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    public function profile(Request $request): Response
    {
        return Inertia::render('Auth/Profile', ['user' => $request->user()]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$request->user()->id],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ]);

        $user = $request->user();
        if ($request->hasFile('avatar')) {
            if ($user->avatar_path) {
                Storage::disk('public')->delete($user->avatar_path);
            }
            $data['avatar_path'] = $request->file('avatar')->store('avatars', 'public');
        }
        unset($data['avatar']);
        if ($data['email'] !== $user->email) {
            $data['email_verified_at'] = null;
        }
        $user->update($data);

        return back()->with('success', 'Profile updated.');
    }

    public function sessions(Request $request): Response
    {
        return Inertia::render('Auth/Sessions', [
            'sessions' => $request->user()->authSessions()->latest('last_activity')->get(),
        ]);
    }

    public function revokeSession(Request $request, AuthSession $session): RedirectResponse
    {
        abort_unless($session->user_id === $request->user()->id, 404);
        $session->delete();

        return back()->with('success', 'Session revoked.');
    }

    public function setAccountStatus(User $user, bool $active): RedirectResponse
    {
        $user->forceFill(['is_active' => $active])->save();

        return back()->with('success', $active ? 'Account activated.' : 'Account deactivated.');
    }
}