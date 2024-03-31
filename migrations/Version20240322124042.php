<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240322124042 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD image VARCHAR(255) NOT NULL, CHANGE first_name first_name VARCHAR(255) NOT NULL, CHANGE last_name last_name VARCHAR(255) NOT NULL, CHANGE cin cin VARCHAR(255) NOT NULL, CHANGE email email VARCHAR(255) NOT NULL, CHANGE age age INT NOT NULL, CHANGE phone_number phone_number INT NOT NULL, CHANGE address address VARCHAR(255) NOT NULL, CHANGE role role VARCHAR(255) NOT NULL, CHANGE password password VARCHAR(255) NOT NULL, CHANGE date date DATE NOT NULL, CHANGE dob dob DATE NOT NULL, CHANGE status status VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `user` DROP image, CHANGE first_name first_name VARCHAR(255) DEFAULT NULL, CHANGE last_name last_name VARCHAR(255) DEFAULT NULL, CHANGE cin cin VARCHAR(255) DEFAULT NULL, CHANGE email email VARCHAR(255) DEFAULT NULL, CHANGE age age INT DEFAULT NULL, CHANGE phone_number phone_number INT DEFAULT NULL, CHANGE address address VARCHAR(255) DEFAULT NULL, CHANGE role role VARCHAR(255) DEFAULT NULL, CHANGE password password VARCHAR(255) DEFAULT NULL, CHANGE date date DATE DEFAULT NULL, CHANGE dob dob DATE DEFAULT NULL, CHANGE status status VARCHAR(255) DEFAULT NULL');
    }
}
