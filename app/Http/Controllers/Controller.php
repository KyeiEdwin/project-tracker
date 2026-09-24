<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class Controller
{
    use AuthorizesRequests;

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function projectOptions(): array
    {
        return $this->accessibleProjectsQuery()
            ->orderBy('name')
            ->get()
            ->map(fn (Project $project) => $project->toInertia())
            ->values()
            ->all();
    }

    protected function accessibleProjectsQuery(?User $user = null): Builder
    {
        $user ??= request()->user();

        if ($user?->role?->name === 'admin') {
            return Project::query();
        }

        return Project::query()->where(function (Builder $query) use ($user): void {
            $query->where('owner_id', $user?->id)
                ->orWhereHas('users', fn (Builder $users) => $users->whereKey($user?->id));
        });
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function teamMemberOptions(): array
    {
        return TeamMember::query()
            ->orderBy('name')
            ->get()
            ->map(fn (TeamMember $member) => $member->toInertia())
            ->values()
            ->all();
    }

    /**
     * @param  callable(mixed): array<string, mixed>  $mapper
     * @return array{data: array<int, array<string, mixed>>, pagination: array<string, int|null>}
     */
    protected function inertiaPage(Builder $query, callable $mapper, int $perPage = 25): array
    {
        /** @var LengthAwarePaginator $paginator */
        $paginator = $query->paginate($perPage)->withQueryString();

        return [
            'data' => $paginator->getCollection()->map($mapper)->values()->all(),
            'pagination' => [
                'currentPage' => $paginator->currentPage(),
                'lastPage' => $paginator->lastPage(),
                'perPage' => $paginator->perPage(),
                'total' => $paginator->total(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
        ];
    }
}
