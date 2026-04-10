<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class BlocImageUploadController extends AbstractController
{
    #[Route('/admin/bloc/upload-image', name: 'admin_bloc_upload_image', methods: ['POST'])]
    public function upload(Request $request, SluggerInterface $slugger): JsonResponse
    {
        $file = $request->files->get('upload');

        if (!$file) {
            return new JsonResponse([
                'uploaded' => 0,
                'error' => ['message' => 'Aucun fichier reçu.']
            ], Response::HTTP_BAD_REQUEST);
        }

        $extension = $file->guessExtension() ?: 'bin';
        $safeFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $slugger->slug($safeFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $extension;

        $uploadDir = $this->getParameter('kernel.project_dir') . '/public/uploads/blocs';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }

        $file->move($uploadDir, $newFilename);

        return new JsonResponse([
            'uploaded' => 1,
            'fileName' => $newFilename,
            'url' => '/uploads/blocs/' . $newFilename,
        ]);
    }
}