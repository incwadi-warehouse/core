<?php

declare(strict_types=1);

/*
 * This script is part of incwadi/core
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210322173016 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reservation (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', branch_id INT NOT NULL, created_at DATETIME NOT NULL, collection DATETIME DEFAULT NULL, notes LONGTEXT DEFAULT NULL, INDEX IDX_42C84955DCD6CC49 (branch_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955DCD6CC49 FOREIGN KEY (branch_id) REFERENCES branch (id)');
        $this->addSql('ALTER TABLE book ADD reservation_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE book ADD CONSTRAINT FK_CBE5A331B83297E7 FOREIGN KEY (reservation_id) REFERENCES reservation (id)');
        $this->addSql('CREATE INDEX IDX_CBE5A331B83297E7 ON book (reservation_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE book DROP FOREIGN KEY FK_CBE5A331B83297E7');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP INDEX IDX_CBE5A331B83297E7 ON book');
        $this->addSql('ALTER TABLE book DROP reservation_id');
    }
}
