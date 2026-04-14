<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class UploadManager
{
    public function __construct(
        private readonly SluggerInterface $slugger,
        private readonly string $blocsUploadDir,
        private readonly string $blocsUploadPath,
    ) {
    }

    public function uploadBlocImage(UploadedFile $file): string
    {
        $extension = $file->guessExtension() ?: 'bin';
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = (string) $this->slugger->slug($originalFilename);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$extension;

        if (!is_dir($this->blocsUploadDir)) {
            mkdir($this->blocsUploadDir, 0775, true);
        }

        $file->move($this->blocsUploadDir, $newFilename);

        return $newFilename;
    }

    public function deleteBlocImage(?string $filename): void
    {
        if (!$filename) {
            return;
        }

        $fullPath = $this->blocsUploadDir.'/'.$filename;

        if (is_file($fullPath)) {
            unlink($fullPath);
        }
    }

    public function getBlocImagePath(string $filename): string
    {
        return $this->blocsUploadPath.'/'.$filename;
    }

    public function getBlocImageDir(): string
    {
        return $this->blocsUploadDir;
    }

    public function getBlocImageBasePath(): string
    {
        return $this->blocsUploadPath;
    }
}