<?php

namespace App\Controller\Admin;

use App\Entity\Media;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class BlocImageUploadController extends AbstractController
{
    #[Route('/admin/bloc/upload-image', name: 'admin_bloc_upload_image', methods: ['POST'])]
    public function upload(
        Request $request,
        SluggerInterface $slugger,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $file = $request->files->get('upload');

        if (!$file) {
            return new JsonResponse([
                'uploaded' => 0,
                'error' => ['message' => 'Aucun fichier reçu.']
            ], Response::HTTP_BAD_REQUEST);
        }

        $extension = $file->guessExtension() ?: 'bin';
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = (string) $slugger->slug($originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $extension;

        $uploadDir = $this->getParameter('kernel.project_dir') . '/public/uploads/blocs';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }

        $file->move($uploadDir, $newFilename);

        $media = new Media();
        $media->setLibelleMedia($originalFilename);
        $media->setChemin($newFilename);

        $entityManager->persist($media);
        $entityManager->flush();

        $url = $request->getSchemeAndHttpHost()
            . $request->getBasePath()
            . '/uploads/blocs/'
            . $newFilename;

        return new JsonResponse([
            'uploaded' => 1,
            'fileName' => $newFilename,
            'url' => $url,
        ]);
    }
}