<?php

namespace App\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180925152549 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sale_tariff (id INT AUTO_INCREMENT NOT NULL, enterprise_id INT DEFAULT NULL, year INT NOT NULL, tonnage VARCHAR(255) NOT NULL, price_hour DOUBLE PRECISION DEFAULT NULL, minium_hours DOUBLE PRECISION DEFAULT NULL, minium_holiday_hours DOUBLE PRECISION DEFAULT NULL, displacement DOUBLE PRECISION DEFAULT NULL, increase_for_holidays DOUBLE PRECISION DEFAULT NULL, enabled TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_E0042EBFA97D1AC3 (enterprise_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sale_tariff ADD CONSTRAINT FK_E0042EBFA97D1AC3 FOREIGN KEY (enterprise_id) REFERENCES enterprise (id)');
        $this->addSql('CREATE TABLE sale_request (id INT AUTO_INCREMENT NOT NULL, enterprise_id INT DEFAULT NULL, partner_id INT DEFAULT NULL, invoice_to_id INT DEFAULT NULL, vehicle_id INT DEFAULT NULL, operator_id INT DEFAULT NULL, tariff_id INT DEFAULT NULL, attended_by_id INT DEFAULT NULL, secondary_vehicle_id INT DEFAULT NULL, service_description LONGTEXT NOT NULL, height INT DEFAULT NULL, distance INT DEFAULT NULL, weight DOUBLE PRECISION DEFAULT NULL, place LONGTEXT DEFAULT NULL, utensils VARCHAR(255) DEFAULT NULL, observations LONGTEXT DEFAULT NULL, request_date DATE NOT NULL, request_time TIME NOT NULL, service_date DATE NOT NULL, service_time TIME NOT NULL, hour_price DOUBLE PRECISION DEFAULT NULL, minium_hours DOUBLE PRECISION DEFAULT NULL, displacement DOUBLE PRECISION DEFAULT NULL, enabled TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_4E894D0DA97D1AC3 (enterprise_id), INDEX IDX_4E894D0D9393F8FE (partner_id), INDEX IDX_4E894D0DDEF1AD0 (invoice_to_id), INDEX IDX_4E894D0D545317D1 (vehicle_id), INDEX IDX_4E894D0D584598A3 (operator_id), INDEX IDX_4E894D0D92348FD2 (tariff_id), INDEX IDX_4E894D0D4A5CE02 (attended_by_id), INDEX IDX_4E894D0D7D15FA0C (secondary_vehicle_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sale_request ADD CONSTRAINT FK_4E894D0DA97D1AC3 FOREIGN KEY (enterprise_id) REFERENCES enterprise (id)');
        $this->addSql('ALTER TABLE sale_request ADD CONSTRAINT FK_4E894D0D9393F8FE FOREIGN KEY (partner_id) REFERENCES partner (id)');
        $this->addSql('ALTER TABLE sale_request ADD CONSTRAINT FK_4E894D0DDEF1AD0 FOREIGN KEY (invoice_to_id) REFERENCES partner (id)');
        $this->addSql('ALTER TABLE sale_request ADD CONSTRAINT FK_4E894D0D545317D1 FOREIGN KEY (vehicle_id) REFERENCES vehicle (id)');
        $this->addSql('ALTER TABLE sale_request ADD CONSTRAINT FK_4E894D0D584598A3 FOREIGN KEY (operator_id) REFERENCES operator (id)');
        $this->addSql('ALTER TABLE sale_request ADD CONSTRAINT FK_4E894D0D92348FD2 FOREIGN KEY (tariff_id) REFERENCES sale_tariff (id)');
        $this->addSql('ALTER TABLE sale_request ADD CONSTRAINT FK_4E894D0D4A5CE02 FOREIGN KEY (attended_by_id) REFERENCES admin_user (id)');
        $this->addSql('ALTER TABLE sale_request ADD CONSTRAINT FK_4E894D0D7D15FA0C FOREIGN KEY (secondary_vehicle_id) REFERENCES vehicle (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE sale_request');
        $this->addSql('DROP TABLE sale_tariff');
    }
}
