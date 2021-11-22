<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211122102446 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE vehicle_special_permit (id INT AUTO_INCREMENT NOT NULL, vehicle_id INT DEFAULT NULL, additional_vehicle VARCHAR(255) DEFAULT NULL, additional_registration_number VARCHAR(255) DEFAULT NULL, expedition_date DATE NOT NULL, expiry_date DATE NOT NULL, total_length DOUBLE PRECISION DEFAULT NULL, total_height DOUBLE PRECISION DEFAULT NULL, total_width DOUBLE PRECISION DEFAULT NULL, maximum_weight DOUBLE PRECISION DEFAULT NULL, number_of_axes INT DEFAULT NULL, `load` DOUBLE PRECISION DEFAULT NULL, expedient_number VARCHAR(255) DEFAULT NULL, route VARCHAR(255) DEFAULT NULL, notes VARCHAR(255) DEFAULT NULL, route_image VARCHAR(255) DEFAULT NULL, enabled TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_F3CDB4AB545317D1 (vehicle_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE vehicle_special_permit ADD CONSTRAINT FK_F3CDB4AB545317D1 FOREIGN KEY (vehicle_id) REFERENCES vehicle (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE vehicle_special_permit');
    }
}
