<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190906215957 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE customer (id INT AUTO_INCREMENT NOT NULL, branch_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, notes LONGTEXT DEFAULT NULL, INDEX IDX_81398E09DCD6CC49 (branch_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, branch_id INT DEFAULT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), INDEX IDX_8D93D649DCD6CC49 (branch_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE genre (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE author (id INT AUTO_INCREMENT NOT NULL, firstname VARCHAR(255) NOT NULL, surname VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE branch (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE book (id INT AUTO_INCREMENT NOT NULL, branch_id INT DEFAULT NULL, author_id INT DEFAULT NULL, genre_id INT DEFAULT NULL, lend_to_id INT DEFAULT NULL, added DATETIME NOT NULL, title VARCHAR(255) NOT NULL, price NUMERIC(10, 2) NOT NULL, sold TINYINT(1) NOT NULL, sold_on DATETIME DEFAULT NULL, removed TINYINT(1) NOT NULL, removed_on DATETIME DEFAULT NULL, release_year INT NOT NULL, type VARCHAR(255) NOT NULL, lend_on DATETIME DEFAULT NULL, INDEX IDX_CBE5A331DCD6CC49 (branch_id), INDEX IDX_CBE5A331F675F31B (author_id), INDEX IDX_CBE5A3314296D31F (genre_id), INDEX IDX_CBE5A3318FF29546 (lend_to_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE customer ADD CONSTRAINT FK_81398E09DCD6CC49 FOREIGN KEY (branch_id) REFERENCES branch (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649DCD6CC49 FOREIGN KEY (branch_id) REFERENCES branch (id)');
        $this->addSql('ALTER TABLE book ADD CONSTRAINT FK_CBE5A331DCD6CC49 FOREIGN KEY (branch_id) REFERENCES branch (id)');
        $this->addSql('ALTER TABLE book ADD CONSTRAINT FK_CBE5A331F675F31B FOREIGN KEY (author_id) REFERENCES author (id)');
        $this->addSql('ALTER TABLE book ADD CONSTRAINT FK_CBE5A3314296D31F FOREIGN KEY (genre_id) REFERENCES genre (id)');
        $this->addSql('ALTER TABLE book ADD CONSTRAINT FK_CBE5A3318FF29546 FOREIGN KEY (lend_to_id) REFERENCES customer (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE book DROP FOREIGN KEY FK_CBE5A3318FF29546');
        $this->addSql('ALTER TABLE book DROP FOREIGN KEY FK_CBE5A3314296D31F');
        $this->addSql('ALTER TABLE book DROP FOREIGN KEY FK_CBE5A331F675F31B');
        $this->addSql('ALTER TABLE customer DROP FOREIGN KEY FK_81398E09DCD6CC49');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649DCD6CC49');
        $this->addSql('ALTER TABLE book DROP FOREIGN KEY FK_CBE5A331DCD6CC49');
        $this->addSql('DROP TABLE customer');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE genre');
        $this->addSql('DROP TABLE author');
        $this->addSql('DROP TABLE branch');
        $this->addSql('DROP TABLE book');
    }
}
