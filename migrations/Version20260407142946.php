<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260407142946 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE media_bloc DROP FOREIGN KEY `FK_43F4A895582E9C0`');
        $this->addSql('ALTER TABLE media_bloc DROP FOREIGN KEY `FK_43F4A89EA9FDD75`');
        $this->addSql('DROP TABLE media_bloc');
        $this->addSql('ALTER TABLE bloc DROP id_bloc');
        $this->addSql('ALTER TABLE contact DROP id_contact');
        $this->addSql('ALTER TABLE element_menu DROP FOREIGN KEY `FK_3E3B1B6A5582E9C0`');
        $this->addSql('ALTER TABLE element_menu DROP FOREIGN KEY `FK_3E3B1B6AC4663E4`');
        $this->addSql('ALTER TABLE element_menu DROP FOREIGN KEY `FK_3E3B1B6ACCD7E912`');
        $this->addSql('DROP INDEX IDX_3E3B1B6AC4663E4 ON element_menu');
        $this->addSql('DROP INDEX IDX_3E3B1B6ACCD7E912 ON element_menu');
        $this->addSql('DROP INDEX UNIQ_3E3B1B6A5582E9C0 ON element_menu');
        $this->addSql('ALTER TABLE element_menu DROP id_element_menu, DROP menu_id, DROP bloc_id, DROP page_id');
        $this->addSql('ALTER TABLE media DROP id_media');
        $this->addSql('ALTER TABLE menu DROP id_menu');
        $this->addSql('ALTER TABLE page DROP FOREIGN KEY `FK_140AB6205582E9C0`');
        $this->addSql('DROP INDEX IDX_140AB6205582E9C0 ON page');
        $this->addSql('ALTER TABLE page DROP id_page, DROP bloc_id');
        $this->addSql('ALTER TABLE parametre DROP id_parametre');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE media_bloc (media_id INT NOT NULL, bloc_id INT NOT NULL, INDEX IDX_43F4A895582E9C0 (bloc_id), INDEX IDX_43F4A89EA9FDD75 (media_id), PRIMARY KEY (media_id, bloc_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE media_bloc ADD CONSTRAINT `FK_43F4A895582E9C0` FOREIGN KEY (bloc_id) REFERENCES bloc (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE media_bloc ADD CONSTRAINT `FK_43F4A89EA9FDD75` FOREIGN KEY (media_id) REFERENCES media (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bloc ADD id_bloc INT NOT NULL');
        $this->addSql('ALTER TABLE contact ADD id_contact INT NOT NULL');
        $this->addSql('ALTER TABLE element_menu ADD id_element_menu INT NOT NULL, ADD menu_id INT DEFAULT NULL, ADD bloc_id INT DEFAULT NULL, ADD page_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE element_menu ADD CONSTRAINT `FK_3E3B1B6A5582E9C0` FOREIGN KEY (bloc_id) REFERENCES bloc (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE element_menu ADD CONSTRAINT `FK_3E3B1B6AC4663E4` FOREIGN KEY (page_id) REFERENCES page (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE element_menu ADD CONSTRAINT `FK_3E3B1B6ACCD7E912` FOREIGN KEY (menu_id) REFERENCES menu (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_3E3B1B6AC4663E4 ON element_menu (page_id)');
        $this->addSql('CREATE INDEX IDX_3E3B1B6ACCD7E912 ON element_menu (menu_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3E3B1B6A5582E9C0 ON element_menu (bloc_id)');
        $this->addSql('ALTER TABLE media ADD id_media INT NOT NULL');
        $this->addSql('ALTER TABLE menu ADD id_menu INT NOT NULL');
        $this->addSql('ALTER TABLE page ADD id_page SMALLINT NOT NULL, ADD bloc_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE page ADD CONSTRAINT `FK_140AB6205582E9C0` FOREIGN KEY (bloc_id) REFERENCES bloc (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_140AB6205582E9C0 ON page (bloc_id)');
        $this->addSql('ALTER TABLE parametre ADD id_parametre INT NOT NULL');
    }
}
