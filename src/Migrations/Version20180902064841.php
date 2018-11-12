<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180902064841 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE agences (id INT AUTO_INCREMENT NOT NULL, nom_agence VARCHAR(255) NOT NULL, adresse_agence VARCHAR(255) NOT NULL, contact_agence VARCHAR(255) NOT NULL, email_agence VARCHAR(255) NOT NULL, plus_info LONGTEXT NOT NULL, avis INT DEFAULT NULL, agence_logo VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commentaires (id INT AUTO_INCREMENT NOT NULL, agence_id INT NOT NULL, user_name VARCHAR(255) NOT NULL, commentaire LONGTEXT NOT NULL, avis INT NOT NULL, INDEX IDX_D9BEC0C4D725330D (agence_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE departs (id INT AUTO_INCREMENT NOT NULL, agence_id INT NOT NULL, formalite TIME NOT NULL, depart TIME NOT NULL, origine VARCHAR(255) NOT NULL, destination VARCHAR(255) NOT NULL, place_init INT NOT NULL, tarif_adult INT NOT NULL, tarif_enfant INT NOT NULL, date_depart DATETIME NOT NULL, image_bus VARCHAR(255) NOT NULL, valide TINYINT(1) NOT NULL, place_restante INT NOT NULL, INDEX IDX_15CE7982D725330D (agence_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE nzela_user (id INT AUTO_INCREMENT NOT NULL, id_agence_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, emai_id VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, role INT NOT NULL, telephone VARCHAR(255) NOT NULL, code_inscription VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_4808100057108F2A (id_agence_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE commentaires ADD CONSTRAINT FK_D9BEC0C4D725330D FOREIGN KEY (agence_id) REFERENCES agences (id)');
        $this->addSql('ALTER TABLE departs ADD CONSTRAINT FK_15CE7982D725330D FOREIGN KEY (agence_id) REFERENCES agences (id)');
        $this->addSql('ALTER TABLE nzela_user ADD CONSTRAINT FK_4808100057108F2A FOREIGN KEY (id_agence_id) REFERENCES agences (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE commentaires DROP FOREIGN KEY FK_D9BEC0C4D725330D');
        $this->addSql('ALTER TABLE departs DROP FOREIGN KEY FK_15CE7982D725330D');
        $this->addSql('ALTER TABLE nzela_user DROP FOREIGN KEY FK_4808100057108F2A');
        $this->addSql('DROP TABLE agences');
        $this->addSql('DROP TABLE commentaires');
        $this->addSql('DROP TABLE departs');
        $this->addSql('DROP TABLE nzela_user');
    }
}
