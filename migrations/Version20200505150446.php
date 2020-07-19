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
final class Version20200505150446 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE cond (id INT AUTO_INCREMENT NOT NULL, branch_id INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_FAFDE884DCD6CC49 (branch_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cond ADD CONSTRAINT FK_FAFDE884DCD6CC49 FOREIGN KEY (branch_id) REFERENCES branch (id)');
        $this->addSql('ALTER TABLE book ADD cond_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE book ADD CONSTRAINT FK_CBE5A331D5D681D3 FOREIGN KEY (cond_id) REFERENCES cond (id)');
        $this->addSql('CREATE INDEX IDX_CBE5A331D5D681D3 ON book (cond_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE book DROP FOREIGN KEY FK_CBE5A331D5D681D3');
        $this->addSql('DROP TABLE cond');
        $this->addSql('DROP INDEX IDX_CBE5A331D5D681D3 ON book');
        $this->addSql('ALTER TABLE book DROP cond_id');
    }
}
