<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class EnsureCatalogSeeded
{
    /**
     * One-shot runtime catalogue import. When SEED_CATALOG_ONCE is enabled and
     * the components table holds no scraped (PCPP-*) rows, import the current
     * market catalogue so fresh deployments that skip build-time composer
     * scripts still ship the full part list. Fail-safe: never breaks a request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (env('SEED_CATALOG_ONCE') === '1') {
            try {
                $hasScraped = DB::table('components')->where('sku', 'like', 'PCPP-%')->exists();

                if (! $hasScraped) {
                    (new \Database\Seeders\ScrapedCatalogSeeder())->runFast();
                }
            } catch (QueryException $e) {
                report($e);
            }
        }

        return $next($request);
    }
}
