<?php

namespace App\Service;

use App\Entity\Page;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;

class StaticPageGenerator
{
    private string $cacheRootDir;

    public function __construct(
        private readonly Environment $twig,
        private readonly RequestStack $requestStack,
        #[Autowire('%kernel.project_dir%')]
        private readonly string $projectDir,
    ) {
        $this->cacheRootDir = $this->projectDir . '/var/page_cache';
    }

    public function hasCache(Page $page): bool
    {
        return is_file($this->getCacheFilePath($page));
    }

    public function readCache(Page $page): string
    {
        $filePath = $this->getCacheFilePath($page);

        if (!is_file($filePath)) {
            throw new \RuntimeException(sprintf('Le fichier de cache "%s" est introuvable.', $filePath));
        }

        return (string) file_get_contents($filePath);
    }

    public function getOrGenerate(Page $page, string $pathInfo): string
    {
        if ($this->hasCache($page)) {
            return $this->readCache($page);
        }

        return $this->generate($page, $pathInfo);
    }

    public function generate(Page $page, string $pathInfo): string
    {
        $html = $this->renderPage($pathInfo, $page);
        $filePath = $this->getCacheFilePath($page);

        $this->writeFile($filePath, $html);

        return $html;
    }

    /**
     * @param iterable<Page> $pages
     *
     * @return array<int, string>
     */
    public function generateAll(iterable $pages): array
    {
        $this->clearAll();

        $generatedFiles = [];

        foreach ($pages as $page) {
            $slug = $page->getSlug();

            if ($slug === null || trim($slug) === '') {
                continue;
            }

            $html = $this->generate($page, '/page/' . trim($slug, '/'));
            $filePath = $this->getCacheFilePath($page);

            if ($html !== '') {
                $generatedFiles[] = $this->makeRelativePath($filePath);
            }
        }

        return $generatedFiles;
    }

    public function clearAll(): void
    {
        if (is_dir($this->cacheRootDir)) {
            $this->removeDirectory($this->cacheRootDir);
        }

        mkdir($this->cacheRootDir, 0775, true);
    }

    public function getCacheFilePath(Page $page): string
    {
        return $this->cacheRootDir . '/' . $this->getSafeCacheDirectory($page) . '/index.html';
    }

    private function renderPage(string $pathInfo, Page $page): string
    {
        $currentRequest = $this->requestStack->getCurrentRequest();

        if ($currentRequest !== null) {
            $uri = $currentRequest->getSchemeAndHttpHost() . $currentRequest->getBaseUrl() . $pathInfo;
        } else {
            $uri = $pathInfo;
        }

        $request = Request::create($uri, 'GET');

        if ($pathInfo === '/') {
            $request->attributes->set('_route', 'app_home');
            $request->attributes->set('_route_params', []);
        } else {
            $request->attributes->set('_route', 'app_page_show');
            $request->attributes->set('_route_params', [
                'slug' => $page->getSlug(),
            ]);
        }

        $this->requestStack->push($request);

        try {
            return $this->twig->render('page/show.html.twig', [
                'page' => $page,
            ]);
        } finally {
            $this->requestStack->pop();
        }
    }

    private function writeFile(string $filePath, string $content): void
    {
        $directory = dirname($filePath);

        if (!is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        file_put_contents($filePath, $content);
    }

    private function removeDirectory(string $directory): void
    {
        $items = scandir($directory);

        if ($items === false) {
            return;
        }

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $path = $directory . '/' . $item;

            if (is_dir($path)) {
                $this->removeDirectory($path);
            } else {
                unlink($path);
            }
        }

        rmdir($directory);
    }

    private function makeRelativePath(string $filePath): string
    {
        return str_replace($this->projectDir . '/', '', $filePath);
    }

    private function getSafeCacheDirectory(Page $page): string
    {
        $directory = $page->getEffectiveCacheDirectory();
        $directory = str_replace('\\', '/', $directory);
        $directory = preg_replace('#/+#', '/', $directory) ?? $directory;
        $directory = trim($directory, '/');

        if ($directory === '') {
            throw new \InvalidArgumentException('Le dossier de cache ne peut pas être vide.');
        }

        if (str_contains($directory, '..')) {
            throw new \InvalidArgumentException('Le dossier de cache ne peut pas contenir "..".');
        }

        if (!preg_match('/^[a-zA-Z0-9_\-\/]+$/', $directory)) {
            throw new \InvalidArgumentException('Le dossier de cache contient des caractères interdits.');
        }

        return $directory;
    }
}