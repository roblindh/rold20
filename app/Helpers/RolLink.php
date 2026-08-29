<?php
declare(strict_types=1);

namespace App\Helpers;

class RolLink
{
    /**
     * Generate a link to a rules chapter/anchor
     */
    public static function rule(string $target, ?string $label = null, ?string $anchor = null): string
    {
        $label = $label ?? ucfirst($target);
        $url = route("rules.$target", [], false);
        if ($anchor) {
            $url .= '#' . ltrim($anchor, '#');
        }
        return '<a class="rule-link text-indigo-600 hover:text-indigo-800 font-medium underline decoration-dotted" href="' . e($url) . '">' . e($label) . '</a>';
    }

    /**
     * Generate a link to a reference page/item with tooltip metadata
     */
    public static function reference(string $type, string $name, ?string $label = null): string
    {
        $label = $label ?? $name;
        $url = route("reference.$type.show", ['name' => urlencode($name)], false);
        return '<a class="ref-link text-amber-700 hover:text-amber-900 font-semibold underline" data-ref-type="' . e($type) . '" data-ref-name="' . e($name) . '" href="' . e($url) . '">' . e($label) . '</a>';
    }

    /**
     * Auto-detect and transform standard rule & spell terms in text to links
     */
    public static function autoLink(string $html): string
    {
        // Replace common tags like [Su, MR, AoO] with styled badges
        $html = preg_replace_callback('/\[([A-Za-z0-9_,\s\-\/]+)\]/', function ($m) {
            $badges = explode(',', $m[1]);
            $out = [];
            foreach ($badges as $b) {
                $b = trim($b);
                $out[] = '<span class="inline-block bg-slate-100 text-slate-700 text-xs px-1.5 py-0.5 rounded border border-slate-300 font-mono">' . e($b) . '</span>';
            }
            return implode(' ', $out);
        }, $html);

        return $html;
    }
}
