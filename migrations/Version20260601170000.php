<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260601170000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout du dossier de cache sur les pages.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE page ADD cache_directory VARCHAR(255) DEFAULT NULL');
        $this->addSql('UPDATE page SET cache_directory = TRIM(BOTH "/" FROM slug) WHERE cache_directory IS NULL OR cache_directory = ""');
        $this->addSql('ALTER TABLE page CHANGE cache_directory cache_directory VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_PAGE_CACHE_DIRECTORY ON page (cache_directory)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX UNIQ_PAGE_CACHE_DIRECTORY ON page');
        $this->addSql('ALTER TABLE page DROP cache_directory');
    }
}