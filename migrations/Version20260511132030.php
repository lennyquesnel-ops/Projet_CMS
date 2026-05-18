<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260511132030 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE profil_acces (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(100) NOT NULL, description LONGTEXT DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE profil_acces_page (profil_acces_id INT NOT NULL, page_id INT NOT NULL, INDEX IDX_6D66BF4E0ECCA00 (profil_acces_id), INDEX IDX_6D66BF4C4663E4 (page_id), PRIMARY KEY (profil_acces_id, page_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user_profil_acces (user_id INT NOT NULL, profil_acces_id INT NOT NULL, INDEX IDX_1E349C56A76ED395 (user_id), INDEX IDX_1E349C56E0ECCA00 (profil_acces_id), PRIMARY KEY (user_id, profil_acces_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE profil_acces_page ADD CONSTRAINT FK_6D66BF4E0ECCA00 FOREIGN KEY (profil_acces_id) REFERENCES profil_acces (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE profil_acces_page ADD CONSTRAINT FK_6D66BF4C4663E4 FOREIGN KEY (page_id) REFERENCES page (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_profil_acces ADD CONSTRAINT FK_1E349C56A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_profil_acces ADD CONSTRAINT FK_1E349C56E0ECCA00 FOREIGN KEY (profil_acces_id) REFERENCES profil_acces (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE profil_acces_page DROP FOREIGN KEY FK_6D66BF4E0ECCA00');
        $this->addSql('ALTER TABLE profil_acces_page DROP FOREIGN KEY FK_6D66BF4C4663E4');
        $this->addSql('ALTER TABLE user_profil_acces DROP FOREIGN KEY FK_1E349C56A76ED395');
        $this->addSql('ALTER TABLE user_profil_acces DROP FOREIGN KEY FK_1E349C56E0ECCA00');
        $this->addSql('DROP TABLE profil_acces');
        $this->addSql('DROP TABLE profil_acces_page');
        $this->addSql('DROP TABLE user_profil_acces');
    }
}
