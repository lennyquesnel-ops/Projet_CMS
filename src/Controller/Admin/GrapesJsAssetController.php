<?php

namespace App\Controller\Admin;

use App\Entity\Media;
use App\Repository\MediaRepository;
use App\Service\UploadManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class GrapesJsAssetController extends AbstractController
{
    #[Route('/admin/grapesjs/assets', name: 'admin_grapesjs_assets', methods: ['GET'])]
    public function assets(
        Request $request,
        MediaRepository $mediaRepository,
        UploadManager $uploadManager
    ): JsonResponse {
        $baseUrl = $request->getSchemeAndHttpHost() . $request->getBasePath();
        $medias = $mediaRepository->findBy([], ['id' => 'DESC']);

        $assets = [];

        foreach ($medias as $media) {
            $filename = $media->getChemin();

            if ($filename === null || trim($filename) === '') {
                continue;
            }

            $assets[] = [
                'type' => 'image',
                'src' => $baseUrl . '/' . $uploadManager->getBlocImagePath($filename),
                'name' => $media->getLibelleMedia() ?: $filename,
            ];
        }

        return new JsonResponse($assets);
    }

    #[Route('/admin/grapesjs/upload-image', name: 'admin_grapesjs_upload_image', methods: ['POST'])]
    public function upload(
        Request $request,
        UploadManager $uploadManager,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $files = $this->extractUploadedFiles($request);

        if ($files === []) {
            return new JsonResponse([
                'data' => [],
                'error' => 'Aucun fichier reçu.',
            ], Response::HTTP_BAD_REQUEST);
        }

        $baseUrl = $request->getSchemeAndHttpHost() . $request->getBasePath();
        $assets = [];

        foreach ($files as $file) {
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $newFilename = $uploadManager->uploadBlocImage($file);

            $media = new Media();
            $media->setLibelleMedia($originalFilename);
            $media->setChemin($newFilename);

            $entityManager->persist($media);

            $assets[] = [
                'type' => 'image',
                'src' => $baseUrl . '/' . $uploadManager->getBlocImagePath($newFilename),
                'name' => $originalFilename,
            ];
        }

        $entityManager->flush();

        return new JsonResponse([
            'data' => $assets,
        ]);
    }

    /**
     * @return array<int, UploadedFile>
     */
    private function extractUploadedFiles(Request $request): array
    {
        $uploadedFiles = [];
        $grapesFiles = $request->files->get('files');

        if ($grapesFiles instanceof UploadedFile) {
            $uploadedFiles[] = $grapesFiles;
        }

        if (is_array($grapesFiles)) {
            foreach ($grapesFiles as $file) {
                if ($file instanceof UploadedFile) {
                    $uploadedFiles[] = $file;
                }
            }
        }

        $ckeditorFile = $request->files->get('upload');

        if ($ckeditorFile instanceof UploadedFile) {
            $uploadedFiles[] = $ckeditorFile;
        }

        return $uploadedFiles;
    }
}