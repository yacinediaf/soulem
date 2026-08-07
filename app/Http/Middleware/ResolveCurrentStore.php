<?php

namespace App\Http\Middleware;

use App\Models\Store;
use App\ValueObjects\CurrentStore;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveCurrentStore
{
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();
        $baseDomain = config('app.domain', 'localhost');

        $subdomain = str_replace('.'.$baseDomain, '', $host);

        if ($subdomain === $host || $subdomain === '') {
            return $next($request);
        }

        $store = Store::where('slug', $subdomain)
            ->with('plan')
            ->first();

        if (! $store) {
            abort(404, 'Store not found.');
        }

        app()->instance(CurrentStore::class, new CurrentStore($store));

        return $next($request);
    }
}
