<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211104153954 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE vehicle DROP FOREIGN KEY FK_1B80E4869C7DE094');
        $this->addSql('DROP INDEX IDX_1B80E4869C7DE094 ON vehicle');
        $this->addSql('ALTER TABLE vehicle ADD chassis_brand VARCHAR(255) NOT NULL, ADD vehicle_brand VARCHAR(255) DEFAULT NULL, ADD vehicle_model VARCHAR(255) DEFAULT NULL, ADD serial_number VARCHAR(255) DEFAULT NULL, DROP vehicle_category_id, DROP description, DROP position, CHANGE short_description chassis_number VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE vehicle ADD vehicle_category_id INT DEFAULT NULL, ADD short_description VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, ADD description TEXT CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, ADD position INT DEFAULT 1 NOT NULL, DROP chassis_brand, DROP chassis_number, DROP vehicle_brand, DROP vehicle_model, DROP serial_number');
        $this->addSql('ALTER TABLE vehicle ADD CONSTRAINT FK_1B80E4869C7DE094 FOREIGN KEY (vehicle_category_id) REFERENCES vehicle_category (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_1B80E4869C7DE094 ON vehicle (vehicle_category_id)');
    }
}
