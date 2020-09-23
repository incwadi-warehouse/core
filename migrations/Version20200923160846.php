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
final class Version20200923160846 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE book_tag DROP FOREIGN KEY FK_F2F4CE1516A2B381');

        $this->addSql('ALTER TABLE book CHANGE id id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');
        $this->addSql('ALTER TABLE book_tag CHANGE book_id book_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\'');

        $this->addSql('ALTER TABLE book_tag ADD CONSTRAINT FK_F2F4CE1516A2B381 FOREIGN KEY (book_id) REFERENCES book (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE book_tag DROP FOREIGN KEY FK_F2F4CE1516A2B381');

        $this->addSql('ALTER TABLE book CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE book_tag CHANGE book_id book_id INT NOT NULL');

        $this->addSql('ALTER TABLE book_tag ADD CONSTRAINT FK_F2F4CE1516A2B381 FOREIGN KEY (book_id) REFERENCES book (id) ON DELETE CASCADE');
    }
}
