<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250713125453 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE employe_projet (employe_id INT NOT NULL, projet_id INT NOT NULL, INDEX IDX_3E3387501B65292 (employe_id), INDEX IDX_3E338750C18272 (projet_id), PRIMARY KEY(employe_id, projet_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE employe_projet ADD CONSTRAINT FK_3E3387501B65292 FOREIGN KEY (employe_id) REFERENCES employes (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE employe_projet ADD CONSTRAINT FK_3E338750C18272 FOREIGN KEY (projet_id) REFERENCES projets (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('DROP TABLE employes_projets');
        $this->addSql('ALTER TABLE dashboard RENAME INDEX fk_5c94fff8a76ed395 TO IDX_5C94FFF8A76ED395');
        $this->addSql('ALTER TABLE employes DROP INDEX user_id, ADD UNIQUE INDEX UNIQ_A94BC0F0A76ED395 (user_id)');
        $this->addSql('ALTER TABLE employes CHANGE adresse adresse LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE employes RENAME INDEX departement_id TO IDX_A94BC0F0CCF9E01E');
        $this->addSql('ALTER TABLE presence RENAME INDEX employe_id TO IDX_6977C7A51B65292');
        $this->addSql('DROP INDEX employe_id ON profile');
        $this->addSql('ALTER TABLE profile CHANGE bio bio LONGTEXT DEFAULT NULL, ADD PRIMARY KEY (employe_id)');
        $this->addSql('ALTER TABLE projets CHANGE description description LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE users CHANGE roles roles JSON NOT NULL, CHANGE created_at created_at DATETIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, roles JSON NOT NULL, password VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, headers LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, queue_name VARCHAR(190) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E016BA31DB (delivered_at), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E0FB7336F0 (queue_name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = MyISAM COMMENT = \'\' ');
        $this->addSql('CREATE TABLE employes_projets (employes_id INT NOT NULL, projets_id INT NOT NULL, INDEX IDX_EBC2AC2F597A6CB7 (projets_id), INDEX IDX_EBC2AC2FF971F91F (employes_id), PRIMARY KEY(employes_id, projets_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE employe_projet DROP FOREIGN KEY FK_3E3387501B65292');
        $this->addSql('ALTER TABLE employe_projet DROP FOREIGN KEY FK_3E338750C18272');
        $this->addSql('DROP TABLE employe_projet');
        $this->addSql('ALTER TABLE `admin` DROP FOREIGN KEY FK_880E0D76A76ED395');
        $this->addSql('ALTER TABLE users CHANGE roles roles JSON NOT NULL COMMENT \'Stores roles like ["ROLE_ADMIN", "ROLE_HR", "ROLE_EMPLOYEE"]\', CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE dashboard DROP FOREIGN KEY FK_5C94FFF8A76ED395');
        $this->addSql('ALTER TABLE dashboard RENAME INDEX idx_5c94fff8a76ed395 TO FK_5C94FFF8A76ED395');
        $this->addSql('ALTER TABLE projets CHANGE description description TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE employes DROP INDEX UNIQ_A94BC0F0A76ED395, ADD INDEX user_id (user_id)');
        $this->addSql('ALTER TABLE employes DROP FOREIGN KEY FK_A94BC0F0A76ED395');
        $this->addSql('ALTER TABLE employes DROP FOREIGN KEY FK_A94BC0F0CCF9E01E');
        $this->addSql('ALTER TABLE employes CHANGE adresse adresse TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE employes RENAME INDEX idx_a94bc0f0ccf9e01e TO departement_id');
        $this->addSql('ALTER TABLE profile DROP INDEX primary, ADD UNIQUE INDEX employe_id (employe_id)');
        $this->addSql('ALTER TABLE profile DROP FOREIGN KEY FK_8157AA0F1B65292');
        $this->addSql('ALTER TABLE profile CHANGE bio bio TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE presence DROP FOREIGN KEY FK_6977C7A51B65292');
        $this->addSql('ALTER TABLE presence RENAME INDEX idx_6977c7a51b65292 TO employe_id');
    }
}
