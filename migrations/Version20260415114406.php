<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260415114406 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE element_menu DROP FOREIGN KEY `FK_3E3B1B6ACCD7E912`');
        $this->addSql('ALTER TABLE element_menu ADD CONSTRAINT FK_3E3B1B6ACCD7E912 FOREIGN KEY (menu_id) REFERENCES menu (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE element_menu DROP FOREIGN KEY FK_3E3B1B6ACCD7E912');
        $this->addSql('ALTER TABLE element_menu ADD CONSTRAINT `FK_3E3B1B6ACCD7E912` FOREIGN KEY (menu_id) REFERENCES menu (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
