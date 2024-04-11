<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240401115047 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comment_post DROP FOREIGN KEY comment_post_ibfk_1');
        $this->addSql('ALTER TABLE image_psot DROP FOREIGN KEY image_psot_ibfk_1');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY post_ibfk_1');
        $this->addSql('ALTER TABLE product_images DROP FOREIGN KEY product_images_ibfk_1');
        $this->addSql('ALTER TABLE reaction_post DROP FOREIGN KEY reaction_post_ibfk_1');
        $this->addSql('ALTER TABLE response DROP FOREIGN KEY response_ibfk_1');
        $this->addSql('ALTER TABLE transactions DROP FOREIGN KEY transactions_ibfk_1');
        $this->addSql('ALTER TABLE transactions DROP FOREIGN KEY transactions_ibfk_2');
        $this->addSql('DROP TABLE chat');
        $this->addSql('DROP TABLE comment_post');
        $this->addSql('DROP TABLE contracts');
        $this->addSql('DROP TABLE favorite');
        $this->addSql('DROP TABLE image_psot');
        $this->addSql('DROP TABLE post');
        $this->addSql('DROP TABLE products');
        $this->addSql('DROP TABLE product_images');
        $this->addSql('DROP TABLE reaction_post');
        $this->addSql('DROP TABLE reclamation');
        $this->addSql('DROP TABLE response');
        $this->addSql('DROP TABLE ticket');
        $this->addSql('DROP TABLE transactions');
        $this->addSql('DROP TABLE user');
        $this->addSql('ALTER TABLE abonnement ADD vehicule_image VARCHAR(255) NOT NULL, DROP dateDebut, DROP dateFin, DROP image, CHANGE Type_Abonnement type_abonnement VARCHAR(255) NOT NULL, CHANGE nom nom VARCHAR(255) NOT NULL, CHANGE prenom prenom VARCHAR(255) NOT NULL');
        $this->addSql('DROP INDEX NomStation ON station');
        $this->addSql('ALTER TABLE station ADD nom_station VARCHAR(255) NOT NULL, ADD address_station VARCHAR(255) NOT NULL, DROP NomStation, DROP AddressStation, CHANGE Type_Vehicule type_vehicule VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE transport DROP FOREIGN KEY id_station_depart');
        $this->addSql('ALTER TABLE transport DROP FOREIGN KEY id_station_arrive');
        $this->addSql('DROP INDEX Reference ON transport');
        $this->addSql('DROP INDEX id_station_depart ON transport');
        $this->addSql('DROP INDEX id_station_arrive ON transport');
        $this->addSql('ALTER TABLE transport CHANGE Type_Vehicule type_vehicule VARCHAR(255) DEFAULT NULL, CHANGE Reference reference VARCHAR(255) DEFAULT NULL, CHANGE Vehicule_Image vehicule_image VARCHAR(255) NOT NULL, CHANGE Heure heure VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE chat (idChat INT AUTO_INCREMENT NOT NULL, idSender INT NOT NULL, idReciver INT NOT NULL, message TEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, msgState INT NOT NULL, timestamp DATETIME DEFAULT \'current_timestamp()\' NOT NULL, PRIMARY KEY(idChat)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE comment_post (idComment INT AUTO_INCREMENT NOT NULL, caption VARCHAR(200) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, dateComment DATETIME DEFAULT \'current_timestamp()\' NOT NULL, idPost INT NOT NULL, idCompte INT NOT NULL, INDEX idPost (idPost), INDEX idCompte (idCompte), PRIMARY KEY(idComment)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE contracts (idContract INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_general_ci`, effectiveDate DATETIME DEFAULT \'current_timestamp()\', terminationDate DATETIME DEFAULT \'NULL\', purpose TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, termsAndConditions TEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, paymentMethod VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_general_ci`, recivinglocation TEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, PRIMARY KEY(idContract)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE favorite (idFavorite INT AUTO_INCREMENT NOT NULL, idUser INT NOT NULL, specifications TEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, PRIMARY KEY(idFavorite)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE image_psot (idImg INT AUTO_INCREMENT NOT NULL, path VARCHAR(200) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, idPost INT NOT NULL, INDEX idPost (idPost), PRIMARY KEY(idImg)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE post (id INT AUTO_INCREMENT NOT NULL, compte INT NOT NULL, date_post DATETIME DEFAULT \'current_timestamp()\' NOT NULL, caption VARCHAR(200) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_general_ci`, nbReactions INT NOT NULL, INDEX compte (compte), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE products (idProd INT AUTO_INCREMENT NOT NULL, idUser INT NOT NULL, name VARCHAR(200) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, descreption TEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, isDeleted TINYINT(1) DEFAULT NULL, price DOUBLE PRECISION NOT NULL, quantity DOUBLE PRECISION NOT NULL, state TINYINT(1) NOT NULL, timestamp DATETIME DEFAULT \'current_timestamp()\' NOT NULL, type VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, category VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, PRIMARY KEY(idProd)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE product_images (idImage INT AUTO_INCREMENT NOT NULL, idProduct INT NOT NULL, path VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, INDEX idProduct (idProduct), PRIMARY KEY(idImage)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE reaction_post (idReactionPost INT AUTO_INCREMENT NOT NULL, idPost INT NOT NULL, idCompte INT NOT NULL, type VARCHAR(10) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, INDEX idCompte (idCompte), INDEX idPost (idPost), PRIMARY KEY(idReactionPost)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE reclamation (idReclamtion INT AUTO_INCREMENT NOT NULL, privateKey VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, date DATETIME DEFAULT \'current_timestamp()\' NOT NULL, subject VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, description VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, imagePath VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, INDEX privateKey (privateKey), PRIMARY KEY(idReclamtion)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE response (id INT AUTO_INCREMENT NOT NULL, id_reclamation INT NOT NULL, privateKey VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, description VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, reponse VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, INDEX id_reclamation (id_reclamation), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE ticket (idTicket INT AUTO_INCREMENT NOT NULL, customName VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, date DATETIME DEFAULT \'current_timestamp()\' NOT NULL, type VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, ticketNumber INT NOT NULL, PRIMARY KEY(idTicket)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE transactions (idTransaction INT AUTO_INCREMENT NOT NULL, idProd INT NOT NULL, idContract INT NOT NULL, idSeller INT NOT NULL, idBuyer INT NOT NULL, pricePerUnit DOUBLE PRECISION NOT NULL, quantity INT NOT NULL, transactionMode VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, timeStamp DATETIME DEFAULT \'current_timestamp()\' NOT NULL, INDEX idProd (idProd), INDEX idContract (idContract), PRIMARY KEY(idTransaction)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE user (idUser INT AUTO_INCREMENT NOT NULL, cin VARCHAR(10) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_general_ci`, firstName VARCHAR(20) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_general_ci`, lastname VARCHAR(20) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_general_ci`, email VARCHAR(30) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_general_ci`, age INT DEFAULT NULL, num INT DEFAULT NULL, adresse VARCHAR(30) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_general_ci`, role VARCHAR(20) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_general_ci`, droit_acces INT DEFAULT NULL, password VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_general_ci`, photos VARCHAR(200) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_general_ci`, date DATE DEFAULT \'NULL\', dob DATE DEFAULT \'NULL\', status VARCHAR(30) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_general_ci`, idMunicipalite INT DEFAULT NULL, IsConnected TINYINT(1) DEFAULT NULL, gender VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_general_ci`, INDEX idMunicipalite (idMunicipalite), PRIMARY KEY(idUser)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE comment_post ADD CONSTRAINT comment_post_ibfk_1 FOREIGN KEY (idCompte) REFERENCES user (idUser) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE image_psot ADD CONSTRAINT image_psot_ibfk_1 FOREIGN KEY (idPost) REFERENCES post (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT post_ibfk_1 FOREIGN KEY (compte) REFERENCES user (idUser) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_images ADD CONSTRAINT product_images_ibfk_1 FOREIGN KEY (idProduct) REFERENCES products (idProd) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reaction_post ADD CONSTRAINT reaction_post_ibfk_1 FOREIGN KEY (idCompte) REFERENCES user (idUser) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE response ADD CONSTRAINT response_ibfk_1 FOREIGN KEY (id_reclamation) REFERENCES reclamation (idReclamtion) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE transactions ADD CONSTRAINT transactions_ibfk_1 FOREIGN KEY (idProd) REFERENCES products (idProd) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE transactions ADD CONSTRAINT transactions_ibfk_2 FOREIGN KEY (idContract) REFERENCES contracts (idContract) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('ALTER TABLE abonnement ADD dateDebut DATETIME DEFAULT \'current_timestamp(6)\' NOT NULL, ADD dateFin DATE DEFAULT \'NULL\', ADD image VARCHAR(255) DEFAULT \'NULL\', DROP vehicule_image, CHANGE nom nom VARCHAR(100) DEFAULT \'NULL\', CHANGE prenom prenom VARCHAR(100) DEFAULT \'NULL\', CHANGE type_abonnement Type_Abonnement VARCHAR(100) NOT NULL');
        $this->addSql('ALTER TABLE station ADD NomStation VARCHAR(100) NOT NULL, ADD AddressStation VARCHAR(100) NOT NULL, DROP nom_station, DROP address_station, CHANGE type_vehicule Type_Vehicule VARCHAR(100) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX NomStation ON station (NomStation, AddressStation)');
        $this->addSql('ALTER TABLE transport CHANGE type_vehicule Type_Vehicule VARCHAR(100) NOT NULL, CHANGE reference Reference VARCHAR(100) NOT NULL, CHANGE vehicule_image Vehicule_Image VARCHAR(100) NOT NULL, CHANGE heure Heure VARCHAR(100) NOT NULL');
        $this->addSql('ALTER TABLE transport ADD CONSTRAINT id_station_depart FOREIGN KEY (Station_depart) REFERENCES station (idStation) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE transport ADD CONSTRAINT id_station_arrive FOREIGN KEY (Station_arrive) REFERENCES station (idStation) ON DELETE CASCADE');
        $this->addSql('CREATE UNIQUE INDEX Reference ON transport (Reference)');
        $this->addSql('CREATE INDEX id_station_depart ON transport (Station_depart)');
        $this->addSql('CREATE INDEX id_station_arrive ON transport (Station_arrive)');
    }
}
