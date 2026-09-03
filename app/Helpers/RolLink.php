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

    /**
     * Parse trait string into a human-readable description
     */
    public static function parseTraits(?string $traits, bool $brief = false): string
    {
        if ($traits === null || trim($traits) === '') {
            return '';
        }

        if (!class_exists('\cTraitEffects') || !function_exists('init_traits')) {
            if (file_exists(base_path('RulesSrc/global.php'))) {
                require_once base_path('RulesSrc/global.php');
                if (function_exists('application_start')) {
                    application_start();
                }
            }
        }

        if (function_exists('init_traits')) {
            global $aTraitDescriptions;
            if (empty($aTraitDescriptions)) {
                init_traits();
            }
        }

        try {
            $desc = \cTraitEffects::StatGetTraitsDescription($traits, $brief);
            $desc = trim($desc);
            if ($desc === '' || str_contains($desc, 'ERROR!!!')) {
                return e($traits);
            }
            if ($brief) {
                return $desc;
            }
            $lines = array_filter(array_map('trim', preg_split('/(\\\\n|\r\n|\n|\r)/', $desc)));
            if (count($lines) <= 1) {
                return implode('', $lines);
            }
            return implode('<br/>', $lines);
        } catch (\Throwable $e) {
            return e($traits);
        }
    }

    /**
     * Parse natural attacks string into human-readable descriptions
     */
    public static function parseNaturalAttacks(?string $natAttacks, int $sizeClass = 0): string
    {
        if ($natAttacks === null || trim($natAttacks) === '') {
            return '';
        }

        if (!class_exists('\cCreature')) {
            if (file_exists(base_path('RulesSrc/global.php'))) {
                require_once base_path('RulesSrc/global.php');
                if (function_exists('application_start')) {
                    application_start();
                }
            }
        }

        try {
            $desc = \cCreature::GetNaturalAttacksDescription($natAttacks, $sizeClass);
            $desc = trim($desc);
            if ($desc === '') {
                return e($natAttacks);
            }
            $lines = array_filter(array_map('trim', preg_split('/(\\\\n|\r\n|\n|\r)/', $desc)));
            if (count($lines) <= 1) {
                return implode('', $lines);
            }
            return implode('<br/>', $lines);
        } catch (\Throwable $e) {
            return e($natAttacks);
        }
    }

    /**
     * Format text by converting C-style escape sequences (\n, \t, etc.) into HTML
     */
    public static function formatText(?string $str): string
    {
        if ($str === null || trim($str) === '') {
            return '';
        }

        // Normalize returns and escapes
        $str = str_replace(["\\r\\n", "\r\n", "\\r", "\r"], "\n", $str);
        $str = str_replace(["\\t", "\t"], "\t", $str);
        $str = str_replace(["\\n", "\n"], "\n", $str);
        $str = str_replace(['\"', "\\'"], ['"', "'"], $str);

        // Escape HTML special characters to be XSS-safe, then convert newlines to <br/> and tabs to &emsp;
        $escaped = e($str);
        $escaped = str_replace("\t", '&emsp;&emsp;', $escaped);
        $escaped = nl2br($escaped, false);

        return $escaped;
    }

    /**
     * Clean and truncate a text snippet for single/double-line table displays
     */
    public static function cleanSnippet(?string $str, int $limit = 140): string
    {
        if ($str === null || trim($str) === '') {
            return '—';
        }

        $str = str_replace(["\\r\\n", "\r\n", "\\r", "\r", "\\n", "\n", "\\t", "\t"], ' ', $str);
        $str = str_replace(['\"', "\\'"], ['"', "'"], $str);
        $str = preg_replace('/\s+/', ' ', trim($str));

        return \Illuminate\Support\Str::limit($str, $limit);
    }
}
