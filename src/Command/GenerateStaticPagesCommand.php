<?php

namespace App\Command;

use App\Service\StaticPageGenerator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:static:generate',
    description: 'Génère les pages publiques en fichiers HTML statiques.'
)]
class GenerateStaticPagesCommand extends Command
{
    public function __construct(
        private readonly StaticPageGenerator $staticPageGenerator,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Génération du cache statique');

        $generatedFiles = $this->staticPageGenerator->generateAll();

        if ($generatedFiles === []) {
            $io->warning('Aucune page statique n’a été générée. Vérifie qu’il existe au moins une page en base de données.');

            return Command::SUCCESS;
        }

        $io->success(sprintf('%d fichier(s) généré(s).', count($generatedFiles)));
        $io->listing($generatedFiles);

        return Command::SUCCESS;
    }
}