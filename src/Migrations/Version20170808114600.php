<?php

namespace App\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170808114600 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE operator (id INT AUTO_INCREMENT NOT NULL, enterprise_id INT DEFAULT NULL, city_id INT DEFAULT NULL, tax_identification_number VARCHAR(255) NOT NULL, banc_account_number VARCHAR(255) NOT NULL, social_security_number VARCHAR(255) NOT NULL, hour_cost VARCHAR(255) NOT NULL, surname1 VARCHAR(255) NOT NULL, surname2 VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, enterprise_mobile VARCHAR(255) NOT NULL, own_phone VARCHAR(255) NOT NULL, own_mobile VARCHAR(255) NOT NULL, brith_date DATETIME NOT NULL, registration_date DATETIME NOT NULL, profile_photo_image VARCHAR(255) NOT NULL, tax_identification_number_image VARCHAR(255) NOT NULL, driving_license_image VARCHAR(255) NOT NULL, cranes_operator_license_image VARCHAR(255) NOT NULL, medical_check_image VARCHAR(255) NOT NULL, epis_image VARCHAR(255) NOT NULL, training_document_image VARCHAR(255) NOT NULL, information_image VARCHAR(255) NOT NULL, use_of_machinery_authorization_image VARCHAR(255) NOT NULL, discharge_social_security_image VARCHAR(255) NOT NULL, employment_contract_image VARCHAR(255) NOT NULL, has_car_driving_license TINYINT(1) NOT NULL, has_lorry_driving_license TINYINT(1) NOT NULL, has_towing_driving_license TINYINT(1) NOT NULL, has_crane_driving_license TINYINT(1) NOT NULL, shoe_size INT NOT NULL, jerseyt_size INT NOT NULL, jacket_size INT NOT NULL, t_shirt_size INT NOT NULL, pant_size INT NOT NULL, working_dress_size INT NOT NULL, enabled TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_D7A6A7815E237E06 (name), INDEX IDX_D7A6A781A97D1AC3 (enterprise_id), INDEX IDX_D7A6A7818BAC62AF (city_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE operator ADD CONSTRAINT FK_D7A6A781A97D1AC3 FOREIGN KEY (enterprise_id) REFERENCES enterprise (id)');
        $this->addSql('ALTER TABLE operator ADD CONSTRAINT FK_D7A6A7818BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE enterprise CHANGE tc2receipt tc2receipt VARCHAR(255) NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE operator');
        $this->addSql('ALTER TABLE enterprise CHANGE tc2receipt tc2receipt VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci');
    }
}
