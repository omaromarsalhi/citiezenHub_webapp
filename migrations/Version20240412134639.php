<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240412134639 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE image_psot (id INT AUTO_INCREMENT NOT NULL, post_id INT NOT NULL, path VARCHAR(255) DEFAULT NULL, INDEX IDX_5BFB815A4B89032C (post_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE image_psot ADD CONSTRAINT FK_5BFB815A4B89032C FOREIGN KEY (post_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE post ADD nb_reactions INT DEFAULT NULL, DROP nbReactions, CHANGE date_post date_post DATETIME DEFAULT NULL, CHANGE caption caption VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE image_psot DROP FOREIGN KEY FK_5BFB815A4B89032C');
        $this->addSql('DROP TABLE image_psot');
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('ALTER TABLE post ADD nbReactions INT NOT NULL, DROP nb_reactions, CHANGE date_post date_post DATETIME DEFAULT \'current_timestamp()\' NOT NULL, CHANGE caption caption VARCHAR(255) DEFAULT \'NULL\'');
    }
}
