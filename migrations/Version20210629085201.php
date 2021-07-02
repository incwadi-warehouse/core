<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210629085201 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE format (id INT AUTO_INCREMENT NOT NULL, branch_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_DEBA72DFDCD6CC49 (branch_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE format ADD CONSTRAINT FK_DEBA72DFDCD6CC49 FOREIGN KEY (branch_id) REFERENCES branch (id)');
        $this->addSql('ALTER TABLE book ADD format_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE book ADD CONSTRAINT FK_CBE5A331D629F605 FOREIGN KEY (format_id) REFERENCES format (id)');
        $this->addSql('CREATE INDEX IDX_CBE5A331D629F605 ON book (format_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE book DROP FOREIGN KEY FK_CBE5A331D629F605');
        $this->addSql('DROP TABLE format');
        $this->addSql('DROP INDEX IDX_CBE5A331D629F605 ON book');
        $this->addSql('ALTER TABLE book DROP format_id');
    }
}
