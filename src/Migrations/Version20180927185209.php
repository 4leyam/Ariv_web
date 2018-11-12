<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180927185209 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE nzela_user ADD id_agence_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE nzela_user ADD CONSTRAINT FK_4808100057108F2A FOREIGN KEY (id_agence_id) REFERENCES agences (id)');
        $this->addSql('CREATE INDEX IDX_4808100057108F2A ON nzela_user (id_agence_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE nzela_user DROP FOREIGN KEY FK_4808100057108F2A');
        $this->addSql('DROP INDEX IDX_4808100057108F2A ON nzela_user');
        $this->addSql('ALTER TABLE nzela_user DROP id_agence_id');
    }
}
