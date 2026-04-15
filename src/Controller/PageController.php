<?php

namespace App\Controller;

use App\Repository\PageRepository;
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
        PageRepository $pageRepository
    ): Response {
        $page = $pageRepository->findOneBySlugWithBlocs($slug);

        if (!$page) {
            throw $this->createNotFoundException('Page introuvable');
        }

        return $this->render('page/show.html.twig', [
            'page' => $page,
        ]);
    }
}