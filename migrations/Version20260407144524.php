<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260407144524 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE bloc_media (bloc_id INT NOT NULL, media_id INT NOT NULL, INDEX IDX_6E8CC6D75582E9C0 (bloc_id), INDEX IDX_6E8CC6D7EA9FDD75 (media_id), PRIMARY KEY (bloc_id, media_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE bloc_media ADD CONSTRAINT FK_6E8CC6D75582E9C0 FOREIGN KEY (bloc_id) REFERENCES bloc (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bloc_media ADD CONSTRAINT FK_6E8CC6D7EA9FDD75 FOREIGN KEY (media_id) REFERENCES media (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bloc ADD element_menu_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE bloc ADD CONSTRAINT FK_C778955A3B3CA946 FOREIGN KEY (element_menu_id) REFERENCES element_menu (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C778955A3B3CA946 ON bloc (element_menu_id)');
        $this->addSql('ALTER TABLE element_menu ADD menu_id INT DEFAULT NULL, ADD page_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE element_menu ADD CONSTRAINT FK_3E3B1B6ACCD7E912 FOREIGN KEY (menu_id) REFERENCES menu (id)');
        $this->addSql('ALTER TABLE element_menu ADD CONSTRAINT FK_3E3B1B6AC4663E4 FOREIGN KEY (page_id) REFERENCES page (id)');
        $this->addSql('CREATE INDEX IDX_3E3B1B6ACCD7E912 ON element_menu (menu_id)');
        $this->addSql('CREATE INDEX IDX_3E3B1B6AC4663E4 ON element_menu (page_id)');
        $this->addSql('ALTER TABLE page ADD bloc_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE page ADD CONSTRAINT FK_140AB6205582E9C0 FOREIGN KEY (bloc_id) REFERENCES bloc (id)');
        $this->addSql('CREATE INDEX IDX_140AB6205582E9C0 ON page (bloc_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bloc_media DROP FOREIGN KEY FK_6E8CC6D75582E9C0');
        $this->addSql('ALTER TABLE bloc_media DROP FOREIGN KEY FK_6E8CC6D7EA9FDD75');
        $this->addSql('DROP TABLE bloc_media');
        $this->addSql('ALTER TABLE bloc DROP FOREIGN KEY FK_C778955A3B3CA946');
        $this->addSql('DROP INDEX UNIQ_C778955A3B3CA946 ON bloc');
        $this->addSql('ALTER TABLE bloc DROP element_menu_id');
        $this->addSql('ALTER TABLE element_menu DROP FOREIGN KEY FK_3E3B1B6ACCD7E912');
        $this->addSql('ALTER TABLE element_menu DROP FOREIGN KEY FK_3E3B1B6AC4663E4');
        $this->addSql('DROP INDEX IDX_3E3B1B6ACCD7E912 ON element_menu');
        $this->addSql('DROP INDEX IDX_3E3B1B6AC4663E4 ON element_menu');
        $this->addSql('ALTER TABLE element_menu DROP menu_id, DROP page_id');
        $this->addSql('ALTER TABLE page DROP FOREIGN KEY FK_140AB6205582E9C0');
        $this->addSql('DROP INDEX IDX_140AB6205582E9C0 ON page');
        $this->addSql('ALTER TABLE page DROP bloc_id');
    }
}
