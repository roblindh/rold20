<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    /**
     * Full Search Results Page
     */
    public function search(Request $request): View
    {
        $q = trim((string)$request->input('q', ''));
        $category = $request->input('category');

        $query = DB::table('search_index');

        if (!empty($q)) {
            $query->where(function ($queryBuilder) use ($q) {
                $queryBuilder->where('title', 'like', "%{$q}%")
                             ->orWhere('content', 'like', "%{$q}%");
            });

            // Order by relevance: exact match > Table: match > prefix match > title substring > content
            $query->orderByRaw("CASE 
                WHEN LOWER(title) = LOWER(?) THEN 1
                WHEN LOWER(title) = LOWER(?) THEN 2
                WHEN LOWER(title) LIKE LOWER(?) THEN 3
                WHEN LOWER(title) LIKE LOWER(?) THEN 4
                ELSE 5 
            END", [$q, "Table: {$q}", "{$q}%", "%{$q}%"]);
        }

        if (!empty($category)) {
            $query->where('category', 'like', "%{$category}%");
        }

        $results = $query->paginate(20)->withPath('/search')->withQueryString();
        $results->getCollection()->transform(function ($item) {
            $item->url = $this->formatUrl($item->url);
            return $item;
        });

        $categories = DB::table('search_index')->distinct()->pluck('category');

        return view('search.index', compact('q', 'category', 'results', 'categories'));
    }

    /**
     * Instant search suggestions for Ctrl+K modal
     */
    public function suggestions(Request $request): JsonResponse
    {
        $q = trim((string)$request->input('q', ''));
        if (mb_strlen($q) < 2) {
            return response()->json([]);
        }

        $query = DB::table('search_index')
            ->where(function ($queryBuilder) use ($q) {
                $queryBuilder->where('title', 'like', "%{$q}%")
                             ->orWhere('content', 'like', "%{$q}%");
            });

        // Relevance ordering: exact title > Table: title > prefix match > title substring > content
        $query->orderByRaw("CASE 
            WHEN LOWER(title) = LOWER(?) THEN 1
            WHEN LOWER(title) = LOWER(?) THEN 2
            WHEN LOWER(title) LIKE LOWER(?) THEN 3
            WHEN LOWER(title) LIKE LOWER(?) THEN 4
            ELSE 5 
        END", [$q, "Table: {$q}", "{$q}%", "%{$q}%"]);

        $results = $query->limit(10)->get(['title', 'category', 'url', 'snippet']);

        $results->transform(function ($item) {
            $item->url = $this->formatUrl($item->url);
            return $item;
        });

        return response()->json($results);
    }

    /**
     * Convert any absolute URL or localhost reference to a root-relative path
     */
    private function formatUrl(?string $url): string
    {
        if (empty($url)) {
            return '/';
        }

        $parsed = parse_url($url);
        $path = $parsed['path'] ?? '/';
        if (!empty($parsed['query'])) {
            $path .= '?' . $parsed['query'];
        }
        if (!empty($parsed['fragment'])) {
            $path .= '#' . $parsed['fragment'];
        }

        return $path;
    }
}
