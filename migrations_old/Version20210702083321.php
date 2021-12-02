<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210702083321 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sale_service_tariff (id INT AUTO_INCREMENT NOT NULL, activity_line_id INT DEFAULT NULL, description VARCHAR(255) NOT NULL, enabled TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_DE484AC4D2903A2 (activity_line_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sale_service_tariff ADD CONSTRAINT FK_DE484AC4D2903A2 FOREIGN KEY (activity_line_id) REFERENCES activity_line (id)');
        $this->addSql('ALTER TABLE sale_request ADD building_site_id INT DEFAULT NULL, ADD service_id INT DEFAULT NULL, ADD status INT NOT NULL, CHANGE service_time service_time TIME DEFAULT NULL');
        $this->addSql('ALTER TABLE sale_request ADD CONSTRAINT FK_4E894D0DC1759B6C FOREIGN KEY (building_site_id) REFERENCES partner_building_site (id)');
        $this->addSql('ALTER TABLE sale_request ADD CONSTRAINT FK_4E894D0DED5CA9E6 FOREIGN KEY (service_id) REFERENCES sale_service_tariff (id)');
        $this->addSql('CREATE INDEX IDX_4E894D0DC1759B6C ON sale_request (building_site_id)');
        $this->addSql('CREATE INDEX IDX_4E894D0DED5CA9E6 ON sale_request (service_id)');
        $this->addSql('ALTER TABLE sale_tariff ADD sale_service_tariff_id INT DEFAULT NULL, ADD partner_id INT DEFAULT NULL, ADD partner_building_site_id INT DEFAULT NULL, ADD date DATE DEFAULT NULL, ADD increase_for_holidays_percentage DOUBLE PRECISION DEFAULT NULL, CHANGE tonnage tonnage VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE sale_tariff ADD CONSTRAINT FK_E0042EBF4EAB1A17 FOREIGN KEY (sale_service_tariff_id) REFERENCES sale_service_tariff (id)');
        $this->addSql('ALTER TABLE sale_tariff ADD CONSTRAINT FK_E0042EBF9393F8FE FOREIGN KEY (partner_id) REFERENCES partner (id)');
        $this->addSql('ALTER TABLE sale_tariff ADD CONSTRAINT FK_E0042EBF69A34E47 FOREIGN KEY (partner_building_site_id) REFERENCES partner_building_site (id)');
        $this->addSql('CREATE INDEX IDX_E0042EBF4EAB1A17 ON sale_tariff (sale_service_tariff_id)');
        $this->addSql('CREATE INDEX IDX_E0042EBF9393F8FE ON sale_tariff (partner_id)');
        $this->addSql('CREATE INDEX IDX_E0042EBF69A34E47 ON sale_tariff (partner_building_site_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sale_request DROP FOREIGN KEY FK_4E894D0DED5CA9E6');
        $this->addSql('ALTER TABLE sale_tariff DROP FOREIGN KEY FK_E0042EBF4EAB1A17');
        $this->addSql('DROP TABLE sale_service_tariff');
        $this->addSql('ALTER TABLE sale_request DROP FOREIGN KEY FK_4E894D0DC1759B6C');
        $this->addSql('DROP INDEX IDX_4E894D0DC1759B6C ON sale_request');
        $this->addSql('DROP INDEX IDX_4E894D0DED5CA9E6 ON sale_request');
        $this->addSql('ALTER TABLE sale_request DROP building_site_id, DROP service_id, DROP status, CHANGE service_time service_time TIME NOT NULL');
        $this->addSql('ALTER TABLE sale_tariff DROP FOREIGN KEY FK_E0042EBF9393F8FE');
        $this->addSql('ALTER TABLE sale_tariff DROP FOREIGN KEY FK_E0042EBF69A34E47');
        $this->addSql('DROP INDEX IDX_E0042EBF4EAB1A17 ON sale_tariff');
        $this->addSql('DROP INDEX IDX_E0042EBF9393F8FE ON sale_tariff');
        $this->addSql('DROP INDEX IDX_E0042EBF69A34E47 ON sale_tariff');
        $this->addSql('ALTER TABLE sale_tariff DROP sale_service_tariff_id, DROP partner_id, DROP partner_building_site_id, DROP date, DROP increase_for_holidays_percentage, CHANGE tonnage tonnage VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`');
    }
}
