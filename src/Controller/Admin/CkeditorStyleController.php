<?php

namespace App\Controller\Admin;

use App\Entity\Parametre;
use App\Repository\ParametreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CkeditorStyleController extends AbstractController
{
    #[Route('/admin/ckeditor/dynamic-style.css', name: 'admin_ckeditor_dynamic_style', methods: ['GET'])]
    public function index(ParametreRepository $parametreRepository): Response
    {
        $themeCss = $this->readCssFileFromParametre(
            $parametreRepository,
            Parametre::CODE_THEME_CSS
        );

        $customCss = $this->readCssFileFromParametre(
            $parametreRepository,
            Parametre::CODE_CUSTOM_CSS
        );

        $content = <<<CSS
/* CSS dynamique chargé dans CKEditor */
/* Ordre : CSS du thème puis CSS personnalisé */

/* ===== THEME CSS ===== */
{$themeCss}

/* ===== CUSTOM CSS ===== */
{$customCss}
CSS;

        return new Response($content, Response::HTTP_OK, [
            'Content-Type' => 'text/css; charset=UTF-8',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    private function readCssFileFromParametre(
        ParametreRepository $parametreRepository,
        string $codeParametre
    ): string {
        $nomFichier = trim((string) $parametreRepository->findValueByCode($codeParametre, ''));

        if ($nomFichier === '') {
            return '';
        }

        $nomFichier = basename($nomFichier);

        $cheminDossier = $this->getParameter('app.uploads.styles_dir');
        $cheminFichier = $cheminDossier . DIRECTORY_SEPARATOR . $nomFichier;

        $dossierReel = realpath($cheminDossier);
        $fichierReel = realpath($cheminFichier);

        if (
            $dossierReel === false
            || $fichierReel === false
            || !str_starts_with($fichierReel, $dossierReel)
            || strtolower(pathinfo($fichierReel, PATHINFO_EXTENSION)) !== 'css'
            || !is_file($fichierReel)
        ) {
            return '';
        }

        return file_get_contents($fichierReel) ?: '';
    }
}