<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230821124825 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE garden (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(128) NOT NULL, description VARCHAR(1000) NOT NULL, address VARCHAR(240) NOT NULL, postal_code INT NOT NULL, city VARCHAR(128) NOT NULL, water TINYINT(1) NOT NULL, tool TINYINT(1) NOT NULL, shed TINYINT(1) NOT NULL, cultivation TINYINT(1) NOT NULL, surface INT NOT NULL, phone_access TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(64) NOT NULL, password VARCHAR(64) NOT NULL, email VARCHAR(255) NOT NULL, phone VARCHAR(64) DEFAULT NULL, role VARCHAR(128) NOT NULL, avatar VARCHAR(128) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE garden');
        $this->addSql('DROP TABLE user');
    }
}
