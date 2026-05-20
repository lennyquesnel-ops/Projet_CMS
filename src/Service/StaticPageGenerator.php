<?php

namespace App\Service;

use App\Entity\Page;
use App\Repository\PageRepository;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;

class StaticPageGenerator
{
    private string $staticDir;

    public function __construct(
        private readonly Environment $twig,
        private readonly PageRepository $pageRepository,
        private readonly RequestStack $requestStack,
        #[Autowire('%kernel.project_dir%')]
        private readonly string $projectDir,
    ) {
        $this->staticDir = $this->projectDir . '/public/static';
    }

    /**
     * @return array<int, string> Liste des fichiers générés.
     */
    public function generateAll(): array
    {
        $this->clearStaticDirectory();

        $generatedFiles = [];
        $pages = $this->findPagesWithBlocs();
        $homepageSlug = $this->pageRepository->findHomepageSlug();

        foreach ($pages as $page) {
            $slug = $page->getSlug();

            if ($slug === null || trim($slug) === '') {
                continue;
            }

            $slug = trim($slug, '/');

            $html = $this->renderPage('/page/' . $slug, $page);
            $filePath = $this->staticDir . '/page/' . $slug . '/index.html';

            $this->writeFile($filePath, $html);
            $generatedFiles[] = $this->makeRelativePath($filePath);

            if ($slug === $homepageSlug) {
                $homeHtml = $this->renderPage('/', $page);
                $homeFilePath = $this->staticDir . '/index.html';

                $this->writeFile($homeFilePath, $homeHtml);
                $generatedFiles[] = $this->makeRelativePath($homeFilePath);
            }
        }

        return $generatedFiles;
    }

    public function clearStaticDirectory(): void
    {
        if (is_dir($this->staticDir)) {
            $this->removeDirectory($this->staticDir);
        }

        mkdir($this->staticDir, 0775, true);
    }

    private function renderPage(string $pathInfo, Page $page): string
    {
        $request = Request::create($pathInfo, 'GET');
        $this->requestStack->push($request);

        try {
            return $this->twig->render('page/show.html.twig', [
                'page' => $page,
            ]);
        } finally {
            $this->requestStack->pop();
        }
    }

    /**
     * @return array<int, Page>
     */
    private function findPagesWithBlocs(): array
    {
        return $this->pageRepository
            ->createQueryBuilder('p')
            ->leftJoin('p.pageBlocs', 'pb')
            ->addSelect('pb')
            ->leftJoin('pb.bloc', 'b')
            ->addSelect('b')
            ->orderBy('p.id', 'ASC')
            ->addOrderBy('pb.ordre', 'ASC')
            ->getQuery()
            ->getResult();
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
}