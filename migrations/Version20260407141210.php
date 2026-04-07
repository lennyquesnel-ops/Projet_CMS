<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260407141210 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE bloc (id INT AUTO_INCREMENT NOT NULL, id_bloc INT NOT NULL, type VARCHAR(255) NOT NULL, contenu LONGTEXT NOT NULL, ordre INT NOT NULL, est_visible TINYINT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE contact (id INT AUTO_INCREMENT NOT NULL, id_contact INT NOT NULL, nom VARCHAR(32) NOT NULL, prenom VARCHAR(32) NOT NULL, email VARCHAR(255) NOT NULL, message VARCHAR(255) NOT NULL, date DATE NOT NULL, objet VARCHAR(128) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE element_menu (id INT AUTO_INCREMENT NOT NULL, id_element_menu INT NOT NULL, libelle VARCHAR(255) NOT NULL, ordre INT NOT NULL, est_visible TINYINT NOT NULL, menu_id INT DEFAULT NULL, bloc_id INT DEFAULT NULL, page_id INT DEFAULT NULL, INDEX IDX_3E3B1B6ACCD7E912 (menu_id), UNIQUE INDEX UNIQ_3E3B1B6A5582E9C0 (bloc_id), INDEX IDX_3E3B1B6AC4663E4 (page_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE media (id INT AUTO_INCREMENT NOT NULL, id_media INT NOT NULL, libelle_media VARCHAR(255) NOT NULL, chemin VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE media_bloc (media_id INT NOT NULL, bloc_id INT NOT NULL, INDEX IDX_43F4A89EA9FDD75 (media_id), INDEX IDX_43F4A895582E9C0 (bloc_id), PRIMARY KEY (media_id, bloc_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE menu (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, ordre INT NOT NULL, id_menu INT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE page (id INT AUTO_INCREMENT NOT NULL, id_page SMALLINT NOT NULL, slug VARCHAR(255) NOT NULL, bloc_id INT DEFAULT NULL, INDEX IDX_140AB6205582E9C0 (bloc_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE parametre (id INT AUTO_INCREMENT NOT NULL, url VARCHAR(255) NOT NULL, logo_site VARCHAR(255) NOT NULL, id_parametre INT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, uuid VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_UUID (uuid), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE element_menu ADD CONSTRAINT FK_3E3B1B6ACCD7E912 FOREIGN KEY (menu_id) REFERENCES menu (id)');
        $this->addSql('ALTER TABLE element_menu ADD CONSTRAINT FK_3E3B1B6A5582E9C0 FOREIGN KEY (bloc_id) REFERENCES bloc (id)');
        $this->addSql('ALTER TABLE element_menu ADD CONSTRAINT FK_3E3B1B6AC4663E4 FOREIGN KEY (page_id) REFERENCES page (id)');
        $this->addSql('ALTER TABLE media_bloc ADD CONSTRAINT FK_43F4A89EA9FDD75 FOREIGN KEY (media_id) REFERENCES media (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE media_bloc ADD CONSTRAINT FK_43F4A895582E9C0 FOREIGN KEY (bloc_id) REFERENCES bloc (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE page ADD CONSTRAINT FK_140AB6205582E9C0 FOREIGN KEY (bloc_id) REFERENCES bloc (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE element_menu DROP FOREIGN KEY FK_3E3B1B6ACCD7E912');
        $this->addSql('ALTER TABLE element_menu DROP FOREIGN KEY FK_3E3B1B6A5582E9C0');
        $this->addSql('ALTER TABLE element_menu DROP FOREIGN KEY FK_3E3B1B6AC4663E4');
        $this->addSql('ALTER TABLE media_bloc DROP FOREIGN KEY FK_43F4A89EA9FDD75');
        $this->addSql('ALTER TABLE media_bloc DROP FOREIGN KEY FK_43F4A895582E9C0');
        $this->addSql('ALTER TABLE page DROP FOREIGN KEY FK_140AB6205582E9C0');
        $this->addSql('DROP TABLE bloc');
        $this->addSql('DROP TABLE contact');
        $this->addSql('DROP TABLE element_menu');
        $this->addSql('DROP TABLE media');
        $this->addSql('DROP TABLE media_bloc');
        $this->addSql('DROP TABLE menu');
        $this->addSql('DROP TABLE page');
        $this->addSql('DROP TABLE parametre');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
