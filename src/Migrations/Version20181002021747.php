<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181002021747 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE localisation_departs DROP FOREIGN KEY FK_71821A1BC68BE09C');
        $this->addSql('DROP TABLE localisation');
        $this->addSql('DROP TABLE localisation_departs');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE localisation (id INT AUTO_INCREMENT NOT NULL, zone_ville VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE localisation_departs (localisation_id INT NOT NULL, departs_id INT NOT NULL, INDEX IDX_71821A1BC68BE09C (localisation_id), INDEX IDX_71821A1B6D127C3 (departs_id), PRIMARY KEY(localisation_id, departs_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE localisation_departs ADD CONSTRAINT FK_71821A1B6D127C3 FOREIGN KEY (departs_id) REFERENCES departs (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE localisation_departs ADD CONSTRAINT FK_71821A1BC68BE09C FOREIGN KEY (localisation_id) REFERENCES localisation (id) ON DELETE CASCADE');
    }
}
