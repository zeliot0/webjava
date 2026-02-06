<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260205150000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create goal table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE goal (
            id_goa INT AUTO_INCREMENT NOT NULL,
            title_goa VARCHAR(100) NOT NULL,
            description_goa VARCHAR(255) NOT NULL,
            date_debut_goa DATE DEFAULT NULL,
            date_final_goa DATE DEFAULT NULL,
            status_goa VARCHAR(20) NOT NULL,
            progress_goa DOUBLE PRECISION DEFAULT NULL,
            category_goa VARCHAR(50) NOT NULL,
            priority_goa VARCHAR(20) NOT NULL,
            notes_goa LONGTEXT DEFAULT NULL,
            color_goa VARCHAR(7) DEFAULT NULL,
            PRIMARY KEY(id_goa)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE goal');
    }
}

