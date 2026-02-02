<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260202210440 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE feature (id_feature INT AUTO_INCREMENT NOT NULL, nom_feature VARCHAR(255) NOT NULL, description_feature LONGTEXT DEFAULT NULL, type_feature VARCHAR(255) DEFAULT NULL, limite INT DEFAULT NULL, statut TINYINT(1) NOT NULL, date_creation DATETIME NOT NULL, PRIMARY KEY(id_feature)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE package (id_package INT AUTO_INCREMENT NOT NULL, nom_package VARCHAR(255) NOT NULL, description_package LONGTEXT DEFAULT NULL, type_package VARCHAR(255) DEFAULT NULL, prix DOUBLE PRECISION NOT NULL, devise VARCHAR(50) NOT NULL, duree INT DEFAULT NULL, unite_duree VARCHAR(50) DEFAULT NULL, essai_gratuit TINYINT(1) NOT NULL, statut TINYINT(1) NOT NULL, date_creation DATETIME NOT NULL, PRIMARY KEY(id_package)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE feature');
        $this->addSql('DROP TABLE package');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
