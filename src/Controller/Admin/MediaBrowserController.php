<?php

namespace App\Controller\Admin;

use App\Repository\MediaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MediaBrowserController extends AbstractController
{
#[Route('/admin/media-browser', name: 'admin_media_browser', methods: ['GET'])]
    public function index(MediaRepository $mediaRepository): Response
    {
        $medias = $mediaRepository->findBy([], ['id' => 'DESC']);

        return $this->render('admin/media_browser.html.twig', [
            'medias' => $medias,
        ]);
    }
}