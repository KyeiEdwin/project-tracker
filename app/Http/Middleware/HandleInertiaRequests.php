<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user('web');
        $teamMember = $request->user('team_member');
        $permissions = [];

        if ($user) {
            $permissions = $user->loadMissing('role.permissions')
                ->role?->permissions->pluck('name')->values()->all() ?? [];
        } elseif ($teamMember) {
            $permissions = $teamMember->loadMissing('memberRole.permissions')
                ->memberRole?->permissions->pluck('name')->values()->all() ?? [];
        }

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user,
                'teamMember' => $teamMember,
                'permissions' => $permissions,
            ],
            'isAuthPage' => $request->routeIs('login'),
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
        ];
    }
}
