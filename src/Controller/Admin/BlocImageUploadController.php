<?php

namespace App\Controller\Admin;

use App\Entity\Media;
use App\Service\UploadManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BlocImageUploadController extends AbstractController
{
    #[Route('/admin/bloc/upload-image', name: 'admin_bloc_upload_image', methods: ['POST'])]
    public function upload(
        Request $request,
        UploadManager $uploadManager,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $file = $request->files->get('upload');

        if (!$file) {
            return new JsonResponse([
                'uploaded' => 0,
                'error' => ['message' => 'Aucun fichier reçu.']
            ], Response::HTTP_BAD_REQUEST);
        }

        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $newFilename = $uploadManager->uploadBlocImage($file);

        $media = new Media();
        $media->setLibelleMedia($originalFilename);
        $media->setChemin($newFilename);

        $entityManager->persist($media);
        $entityManager->flush();

        $url = $request->getSchemeAndHttpHost()
            . $request->getBasePath()
            . '/'
            . $uploadManager->getBlocImagePath($newFilename);

        return new JsonResponse([
            'uploaded' => 1,
            'fileName' => $newFilename,
            'url' => $url,
        ]);
    }
}