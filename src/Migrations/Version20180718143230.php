<?php

namespace DoctrineMigrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180718143230 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE enterprise_group_bounty (id INT AUTO_INCREMENT NOT NULL, enterprise_id INT DEFAULT NULL, `group` VARCHAR(255) NOT NULL, normal_hour DOUBLE PRECISION DEFAULT \'0\', extra_normal_hour DOUBLE PRECISION DEFAULT \'0\', extra_extra_hour DOUBLE PRECISION DEFAULT \'0\', road_normal_hour DOUBLE PRECISION DEFAULT \'0\', road_extra_hour DOUBLE PRECISION DEFAULT \'0\', awaiting_hour DOUBLE PRECISION DEFAULT \'0\', negative_hour DOUBLE PRECISION DEFAULT \'0\', transfer_hour DOUBLE PRECISION DEFAULT \'0\', lunch DOUBLE PRECISION DEFAULT \'0\', dinner DOUBLE PRECISION DEFAULT \'0\', over_night DOUBLE PRECISION DEFAULT \'0\', exta_night DOUBLE PRECISION DEFAULT \'0\', diet DOUBLE PRECISION DEFAULT \'0\', international_lunch DOUBLE PRECISION DEFAULT \'0\', international_dinner DOUBLE PRECISION DEFAULT \'0\', truck_output DOUBLE PRECISION DEFAULT \'0\', car_output DOUBLE PRECISION DEFAULT \'0\', enabled TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_C29186DBA97D1AC3 (enterprise_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE enterprise_group_bounty ADD CONSTRAINT FK_C29186DBA97D1AC3 FOREIGN KEY (enterprise_id) REFERENCES enterprise (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE enterprise_group_bounty');
    }
}
