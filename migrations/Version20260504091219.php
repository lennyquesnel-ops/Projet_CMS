<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260504091219 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE page_bloc (id INT AUTO_INCREMENT NOT NULL, ordre INT DEFAULT 1 NOT NULL, page_id INT NOT NULL, bloc_id INT NOT NULL, INDEX IDX_40BC8980C4663E4 (page_id), INDEX IDX_40BC89805582E9C0 (bloc_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE page_bloc ADD CONSTRAINT FK_40BC8980C4663E4 FOREIGN KEY (page_id) REFERENCES page (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE page_bloc ADD CONSTRAINT FK_40BC89805582E9C0 FOREIGN KEY (bloc_id) REFERENCES bloc (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bloc DROP FOREIGN KEY `FK_C778955AC4663E4`');
        $this->addSql('DROP INDEX IDX_C778955AC4663E4 ON bloc');
        $this->addSql('ALTER TABLE bloc DROP ordre, DROP page_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE page_bloc DROP FOREIGN KEY FK_40BC8980C4663E4');
        $this->addSql('ALTER TABLE page_bloc DROP FOREIGN KEY FK_40BC89805582E9C0');
        $this->addSql('DROP TABLE page_bloc');
        $this->addSql('ALTER TABLE bloc ADD ordre INT NOT NULL, ADD page_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE bloc ADD CONSTRAINT `FK_C778955AC4663E4` FOREIGN KEY (page_id) REFERENCES page (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_C778955AC4663E4 ON bloc (page_id)');
    }
}
