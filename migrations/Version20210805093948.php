<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210805093948 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE book DROP FOREIGN KEY FK_CBE5A3318FF29546');
        $this->addSql('DROP INDEX IDX_CBE5A3318FF29546 ON book');
        $this->addSql('ALTER TABLE book DROP lend_to_id, DROP type, DROP lend_on');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE book ADD lend_to_id INT DEFAULT NULL, ADD type VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ADD lend_on DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE book ADD CONSTRAINT FK_CBE5A3318FF29546 FOREIGN KEY (lend_to_id) REFERENCES staff (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_CBE5A3318FF29546 ON book (lend_to_id)');
    }
}
