<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260505083337 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE parametre ADD code_parametre VARCHAR(255) NOT NULL, ADD description LONGTEXT DEFAULT NULL, ADD valeur_parametre LONGTEXT NOT NULL, DROP url, DROP logo_site');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE parametre ADD logo_site VARCHAR(255) NOT NULL, DROP description, DROP valeur_parametre, CHANGE code_parametre url VARCHAR(255) NOT NULL');
    }
}
