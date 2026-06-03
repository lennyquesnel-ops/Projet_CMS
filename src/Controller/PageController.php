<?php

namespace App\Controller;

use App\Repository\PageRepository;
use App\Service\StaticPageGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PageController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function home(PageRepository $pageRepository): Response
    {
        $slug = $pageRepository->findHomepageSlug();

        if ($slug === null) {
            throw $this->createNotFoundException('Aucune page n\'est disponible.');
        }

        return $this->redirectToRoute('app_page_show', [
            'slug' => $slug,
        ]);
    }

    #[Route('/page/{slug}', name: 'app_page_show')]
    public function show(
        string $slug,
        PageRepository $pageRepository,
        StaticPageGenerator $staticPageGenerator
    ): Response {
        $page = $pageRepository->findOneBy([
            'slug' => $slug,
        ]);

        if (!$page) {
            throw $this->createNotFoundException('Page introuvable');
        }

        if ($staticPageGenerator->hasCache($page)) {
            return new Response($staticPageGenerator->readCache($page), Response::HTTP_OK, [
                'Content-Type' => 'text/html; charset=UTF-8',
                'X-ATAIS-Cache' => 'HIT',
            ]);
        }

        $pageWithBlocs = $pageRepository->findOneBySlugWithBlocs($slug);

        if (!$pageWithBlocs) {
            throw $this->createNotFoundException('Page introuvable');
        }

        return new Response($staticPageGenerator->generate($pageWithBlocs, '/page/' . trim($slug, '/')), Response::HTTP_OK, [
            'Content-Type' => 'text/html; charset=UTF-8',
            'X-ATAIS-Cache' => 'MISS',
        ]);
    }
}