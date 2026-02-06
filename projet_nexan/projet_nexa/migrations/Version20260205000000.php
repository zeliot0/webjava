<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260205000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add photo_p to produit';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE produit ADD photo_p VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE produit DROP photo_p');
    }
}

