<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class RulesController extends Controller
{
    private function renderChapter(string $slug, int $chapterNum, string $view, string $title): Response
    {
        $tmpFile = "/tmp/rold20_cache/rule_{$slug}.html";
        $cachedFile = storage_path("framework/cache/pages/rule_{$slug}.html");
        
        $html = null;
        if (file_exists($tmpFile)) {
            $html = file_get_contents($tmpFile);
        } elseif (file_exists($cachedFile)) {
            $html = file_get_contents($cachedFile);
            if (is_dir('/tmp/rold20_cache')) {
                @file_put_contents($tmpFile, $html);
            }
        } else {
            if (!function_exists('show_agecategories') && file_exists(base_path('RulesSrc/global.php'))) {
                require_once base_path('RulesSrc/global.php');
                if (function_exists('application_start')) {
                    application_start();
                }
            }
            $html = view($view, ['chapter' => $chapterNum, 'title' => $title])->render();
            if (!is_dir(dirname($cachedFile))) {
                @mkdir(dirname($cachedFile), 0777, true);
            }
            @file_put_contents($cachedFile, $html);
            if (is_dir('/tmp/rold20_cache')) {
                @file_put_contents($tmpFile, $html);
            }
        }

        $etag = '"rule-' . $slug . '-' . md5($html) . '"';
        if (request()->header('If-None-Match') === $etag) {
            return response('', 304, [
                'ETag' => $etag,
                'Cache-Control' => 'no-cache, must-revalidate',
            ]);
        }

        $acceptEncoding = request()->header('Accept-Encoding', '');
        if (str_contains($acceptEncoding, 'gzip') && function_exists('gzencode')) {
            $gzipped = gzencode($html, 6);
            return response($gzipped, 200, [
                'Content-Type' => 'text/html; charset=UTF-8',
                'Content-Encoding' => 'gzip',
                'Content-Length' => strlen($gzipped),
                'ETag' => $etag,
                'Cache-Control' => 'no-cache, must-revalidate',
            ]);
        }

        return response($html, 200, [
            'Content-Type' => 'text/html; charset=UTF-8',
            'Content-Length' => strlen($html),
            'ETag' => $etag,
            'Cache-Control' => 'no-cache, must-revalidate',
        ]);
    }

    public function intro(): Response
    {
        return $this->renderChapter('intro', 1, 'rules.intro', 'Introduction');
    }

    public function core(): Response
    {
        return $this->renderChapter('core', 2, 'rules.core', 'Core Mechanics');
    }

    public function chargen(): Response
    {
        return $this->renderChapter('chargen', 3, 'rules.chargen', 'Character Generation');
    }

    public function encounters(): Response
    {
        return $this->renderChapter('engagement', 4, 'rules.encounters', 'Rules of Engagement');
    }

    public function engagement(): Response
    {
        return $this->encounters();
    }

    public function combat(): Response
    {
        return $this->renderChapter('combat', 5, 'rules.combat', 'Rules of Combat');
    }

    public function magic(): Response
    {
        return $this->renderChapter('magic', 6, 'rules.magic', 'Rules of Magic');
    }

    public function environment(): Response
    {
        return $this->renderChapter('environment', 7, 'rules.environment', 'Rules of Environment');
    }

    public function culture(): Response
    {
        return $this->renderChapter('culture', 8, 'rules.culture', 'Rules of Culture');
    }

    public function indexList(): Response
    {
        return $this->renderChapter('index', 15, 'rules.index', 'Rules Index');
    }

    public function printable(): Response
    {
        @set_time_limit(300);
        return $this->renderChapter('printable', 0, 'rules.printable', 'Complete Ruleset - Printable');
    }
}
