<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181002023226 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE location (id INT AUTO_INCREMENT NOT NULL, ville VARCHAR(255) NOT NULL, pays VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE departs ADD origine_id INT NOT NULL, ADD destination_id INT NOT NULL');
        $this->addSql('ALTER TABLE departs ADD CONSTRAINT FK_15CE798287998E FOREIGN KEY (origine_id) REFERENCES location (id)');
        $this->addSql('ALTER TABLE departs ADD CONSTRAINT FK_15CE7982816C6140 FOREIGN KEY (destination_id) REFERENCES location (id)');
        $this->addSql('CREATE INDEX IDX_15CE798287998E ON departs (origine_id)');
        $this->addSql('CREATE INDEX IDX_15CE7982816C6140 ON departs (destination_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE departs DROP FOREIGN KEY FK_15CE798287998E');
        $this->addSql('ALTER TABLE departs DROP FOREIGN KEY FK_15CE7982816C6140');
        $this->addSql('DROP TABLE location');
        $this->addSql('DROP INDEX IDX_15CE798287998E ON departs');
        $this->addSql('DROP INDEX IDX_15CE7982816C6140 ON departs');
        $this->addSql('ALTER TABLE departs DROP origine_id, DROP destination_id');
    }
}
