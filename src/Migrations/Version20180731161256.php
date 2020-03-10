<?php

namespace DoctrineMigrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180731161256 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE partner (id INT AUTO_INCREMENT NOT NULL, enterprise_id INT DEFAULT NULL, class_id INT DEFAULT NULL, type_id INT DEFAULT NULL, transfer_account_id INT DEFAULT NULL, main_city_id INT DEFAULT NULL, secondary_city_id INT DEFAULT NULL, cif_nif VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, notes TEXT DEFAULT NULL, main_address VARCHAR(255) DEFAULT NULL, secondary_address VARCHAR(255) DEFAULT NULL, phone_number1 VARCHAR(255) DEFAULT NULL, phone_number2 VARCHAR(255) DEFAULT NULL, phone_number3 VARCHAR(255) DEFAULT NULL, phone_number4 VARCHAR(255) DEFAULT NULL, phone_number5 VARCHAR(255) DEFAULT NULL, fax_number1 VARCHAR(255) DEFAULT NULL, fax_number2 VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, www VARCHAR(255) DEFAULT NULL, discount DOUBLE PRECISION DEFAULT NULL, code INT DEFAULT NULL, provider_reference VARCHAR(255) DEFAULT NULL, reference VARCHAR(255) DEFAULT NULL, iva_tax_free TINYINT(1) DEFAULT NULL, iban VARCHAR(255) DEFAULT NULL, swift VARCHAR(255) DEFAULT NULL, bank_code VARCHAR(255) DEFAULT NULL, office_number VARCHAR(255) DEFAULT NULL, control_digit VARCHAR(255) DEFAULT NULL, account_number VARCHAR(255) DEFAULT NULL, enabled TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_312B3E16A97D1AC3 (enterprise_id), INDEX IDX_312B3E16EA000B10 (class_id), INDEX IDX_312B3E16C54C8C93 (type_id), INDEX IDX_312B3E1641A66153 (transfer_account_id), INDEX IDX_312B3E167FE7CB46 (main_city_id), INDEX IDX_312B3E164EB2946F (secondary_city_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE partner ADD CONSTRAINT FK_312B3E16A97D1AC3 FOREIGN KEY (enterprise_id) REFERENCES enterprise (id)');
        $this->addSql('ALTER TABLE partner ADD CONSTRAINT FK_312B3E16EA000B10 FOREIGN KEY (class_id) REFERENCES partner_class (id)');
        $this->addSql('ALTER TABLE partner ADD CONSTRAINT FK_312B3E16C54C8C93 FOREIGN KEY (type_id) REFERENCES partner_type (id)');
        $this->addSql('ALTER TABLE partner ADD CONSTRAINT FK_312B3E1641A66153 FOREIGN KEY (transfer_account_id) REFERENCES enterprise_transfer_account (id)');
        $this->addSql('ALTER TABLE partner ADD CONSTRAINT FK_312B3E167FE7CB46 FOREIGN KEY (main_city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE partner ADD CONSTRAINT FK_312B3E164EB2946F FOREIGN KEY (secondary_city_id) REFERENCES city (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE partner');
    }
}
