<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260407144853 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE page DROP FOREIGN KEY `FK_140AB6205582E9C0`');
        $this->addSql('DROP INDEX IDX_140AB6205582E9C0 ON page');
        $this->addSql('ALTER TABLE page DROP bloc_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE page ADD bloc_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE page ADD CONSTRAINT `FK_140AB6205582E9C0` FOREIGN KEY (bloc_id) REFERENCES bloc (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_140AB6205582E9C0 ON page (bloc_id)');
    }
}
