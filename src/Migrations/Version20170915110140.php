<?php

namespace DoctrineMigrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170915110140 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE operator CHANGE hour_cost hour_cost VARCHAR(255) DEFAULT NULL, CHANGE address address VARCHAR(255) DEFAULT NULL, CHANGE enterprise_mobile enterprise_mobile VARCHAR(255) DEFAULT NULL, CHANGE own_phone own_phone VARCHAR(255) DEFAULT NULL, CHANGE own_mobile own_mobile VARCHAR(255) DEFAULT NULL, CHANGE profile_photo_image profile_photo_image VARCHAR(255) DEFAULT NULL, CHANGE tax_identification_number_image tax_identification_number_image VARCHAR(255) DEFAULT NULL, CHANGE driving_license_image driving_license_image VARCHAR(255) DEFAULT NULL, CHANGE cranes_operator_license_image cranes_operator_license_image VARCHAR(255) DEFAULT NULL, CHANGE medical_check_image medical_check_image VARCHAR(255) DEFAULT NULL, CHANGE epis_image epis_image VARCHAR(255) DEFAULT NULL, CHANGE training_document_image training_document_image VARCHAR(255) DEFAULT NULL, CHANGE information_image information_image VARCHAR(255) DEFAULT NULL, CHANGE use_of_machinery_authorization_image use_of_machinery_authorization_image VARCHAR(255) DEFAULT NULL, CHANGE discharge_social_security_image discharge_social_security_image VARCHAR(255) DEFAULT NULL, CHANGE employment_contract_image employment_contract_image VARCHAR(255) DEFAULT NULL, CHANGE shoe_size shoe_size INT DEFAULT NULL, CHANGE jerseyt_size jerseyt_size INT DEFAULT NULL, CHANGE jacket_size jacket_size INT DEFAULT NULL, CHANGE t_shirt_size t_shirt_size INT DEFAULT NULL, CHANGE pant_size pant_size INT DEFAULT NULL, CHANGE working_dress_size working_dress_size INT DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE operator CHANGE hour_cost hour_cost VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE address address VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE enterprise_mobile enterprise_mobile VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE own_phone own_phone VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE own_mobile own_mobile VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE profile_photo_image profile_photo_image VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE tax_identification_number_image tax_identification_number_image VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE driving_license_image driving_license_image VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE cranes_operator_license_image cranes_operator_license_image VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE medical_check_image medical_check_image VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE epis_image epis_image VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE training_document_image training_document_image VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE information_image information_image VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE use_of_machinery_authorization_image use_of_machinery_authorization_image VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE discharge_social_security_image discharge_social_security_image VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE employment_contract_image employment_contract_image VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE shoe_size shoe_size INT NOT NULL, CHANGE jerseyt_size jerseyt_size INT NOT NULL, CHANGE jacket_size jacket_size INT NOT NULL, CHANGE t_shirt_size t_shirt_size INT NOT NULL, CHANGE pant_size pant_size INT NOT NULL, CHANGE working_dress_size working_dress_size INT NOT NULL');
    }
}
