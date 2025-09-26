<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250926120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add mail_reservation field to branch table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE branch ADD mail_reservation TEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE branch DROP COLUMN mail_reservation');
    }
}
