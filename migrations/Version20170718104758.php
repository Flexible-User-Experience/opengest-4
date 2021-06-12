<?php

namespace DoctrineMigrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170718104758 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE enterprise (id INT AUTO_INCREMENT NOT NULL, city_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, tax_identification_number VARCHAR(255) NOT NULL, business_name VARCHAR(255) NOT NULL, address VARCHAR(255) DEFAULT NULL, phone1 VARCHAR(255) DEFAULT NULL, phone2 VARCHAR(255) DEFAULT NULL, phone3 VARCHAR(255) DEFAULT NULL, fax VARCHAR(255) DEFAULT NULL, email VARCHAR(255) NOT NULL, www VARCHAR(255) DEFAULT NULL, logo VARCHAR(255) NOT NULL, deed_of_incorporation VARCHAR(255) NOT NULL, tax_identification_number_card VARCHAR(255) NOT NULL, tc1receipt VARCHAR(255) NOT NULL, tc2receipt VARCHAR(255) NOT NULL, ss_registration VARCHAR(255) NOT NULL, ss_payment_certificate VARCHAR(255) NOT NULL, rc1insurance VARCHAR(255) NOT NULL, rc2insurance VARCHAR(255) NOT NULL, rc_receipt VARCHAR(255) NOT NULL, prevention_service_contract VARCHAR(255) NOT NULL, prevention_service_invoice VARCHAR(255) NOT NULL, prevention_service_receipt VARCHAR(255) NOT NULL, occupational_accidents_insurance VARCHAR(255) NOT NULL, occupational_receipt VARCHAR(255) NOT NULL, labor_risk_assessment VARCHAR(255) NOT NULL, security_plan VARCHAR(255) NOT NULL, rea_certificate VARCHAR(255) NOT NULL, oil_certificate VARCHAR(255) NOT NULL, gencat_payment_certificate VARCHAR(255) NOT NULL, deeds_of_powers VARCHAR(255) NOT NULL, iae_registration VARCHAR(255) NOT NULL, iae_receipt VARCHAR(255) NOT NULL, mutual_partnership VARCHAR(255) NOT NULL, enabled TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_B1B36A038BAC62AF (city_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE enterprise ADD CONSTRAINT FK_B1B36A038BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE enterprise');
    }
}
