<?php

namespace App\Http\Middleware;

use App\ValueObjects\CurrentStore;
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
        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $request->user(),
            ],
            'currentStore' => function () {
                if (app()->bound(CurrentStore::class)) {
                    $currentStore = app(CurrentStore::class);

                    return [
                        'id' => $currentStore->store->id,
                        'name' => $currentStore->store->name,
                        'slug' => $currentStore->store->slug,
                        'plan' => [
                            'name' => $currentStore->store->plan->name,
                            'slug' => $currentStore->store->plan->slug,
                        ],
                    ];
                }

                return null;
            },
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
        ];
    }
}
