<?php

namespace DoctrineMigrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170918074738 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE enterprise CHANGE logo logo VARCHAR(255) DEFAULT NULL, CHANGE deed_of_incorporation deed_of_incorporation VARCHAR(255) DEFAULT NULL, CHANGE tax_identification_number_card tax_identification_number_card VARCHAR(255) DEFAULT NULL, CHANGE tc1receipt tc1receipt VARCHAR(255) DEFAULT NULL, CHANGE tc2receipt tc2receipt VARCHAR(255) DEFAULT NULL, CHANGE ss_registration ss_registration VARCHAR(255) DEFAULT NULL, CHANGE ss_payment_certificate ss_payment_certificate VARCHAR(255) DEFAULT NULL, CHANGE rc1insurance rc1insurance VARCHAR(255) DEFAULT NULL, CHANGE rc2insurance rc2insurance VARCHAR(255) DEFAULT NULL, CHANGE rc_receipt rc_receipt VARCHAR(255) DEFAULT NULL, CHANGE prevention_service_contract prevention_service_contract VARCHAR(255) DEFAULT NULL, CHANGE prevention_service_invoice prevention_service_invoice VARCHAR(255) DEFAULT NULL, CHANGE prevention_service_receipt prevention_service_receipt VARCHAR(255) DEFAULT NULL, CHANGE occupational_accidents_insurance occupational_accidents_insurance VARCHAR(255) DEFAULT NULL, CHANGE occupational_receipt occupational_receipt VARCHAR(255) DEFAULT NULL, CHANGE labor_risk_assessment labor_risk_assessment VARCHAR(255) DEFAULT NULL, CHANGE security_plan security_plan VARCHAR(255) DEFAULT NULL, CHANGE rea_certificate rea_certificate VARCHAR(255) DEFAULT NULL, CHANGE oil_certificate oil_certificate VARCHAR(255) DEFAULT NULL, CHANGE gencat_payment_certificate gencat_payment_certificate VARCHAR(255) DEFAULT NULL, CHANGE deeds_of_powers deeds_of_powers VARCHAR(255) DEFAULT NULL, CHANGE iae_registration iae_registration VARCHAR(255) DEFAULT NULL, CHANGE iae_receipt iae_receipt VARCHAR(255) DEFAULT NULL, CHANGE mutual_partnership mutual_partnership VARCHAR(255) DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE enterprise CHANGE logo logo VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE deed_of_incorporation deed_of_incorporation VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE tax_identification_number_card tax_identification_number_card VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE tc1receipt tc1receipt VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE tc2receipt tc2receipt VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE ss_registration ss_registration VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE ss_payment_certificate ss_payment_certificate VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE rc1insurance rc1insurance VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE rc2insurance rc2insurance VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE rc_receipt rc_receipt VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE prevention_service_contract prevention_service_contract VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE prevention_service_invoice prevention_service_invoice VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE prevention_service_receipt prevention_service_receipt VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE occupational_accidents_insurance occupational_accidents_insurance VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE occupational_receipt occupational_receipt VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE labor_risk_assessment labor_risk_assessment VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE security_plan security_plan VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE rea_certificate rea_certificate VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE oil_certificate oil_certificate VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE gencat_payment_certificate gencat_payment_certificate VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE deeds_of_powers deeds_of_powers VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE iae_registration iae_registration VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE iae_receipt iae_receipt VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, CHANGE mutual_partnership mutual_partnership VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci');
    }
}
