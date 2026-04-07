<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260407145029 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bloc ADD page_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE bloc ADD CONSTRAINT FK_C778955AC4663E4 FOREIGN KEY (page_id) REFERENCES page (id)');
        $this->addSql('CREATE INDEX IDX_C778955AC4663E4 ON bloc (page_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bloc DROP FOREIGN KEY FK_C778955AC4663E4');
        $this->addSql('DROP INDEX IDX_C778955AC4663E4 ON bloc');
        $this->addSql('ALTER TABLE bloc DROP page_id');
    }
}
