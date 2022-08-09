<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220809084625 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE purchase_invoice (id INT AUTO_INCREMENT NOT NULL, partner_id INT DEFAULT NULL, delivery_address_id INT DEFAULT NULL, partner_main_city_id INT DEFAULT NULL, date DATETIME NOT NULL, invoice_number INT NOT NULL, reference VARCHAR(255) DEFAULT NULL, total DOUBLE PRECISION DEFAULT NULL, base_total DOUBLE PRECISION DEFAULT NULL, iva DOUBLE PRECISION DEFAULT NULL, iva21 DOUBLE PRECISION DEFAULT NULL, iva10 DOUBLE PRECISION DEFAULT NULL, iva4 DOUBLE PRECISION DEFAULT NULL, iva0 DOUBLE PRECISION DEFAULT NULL, irpf DOUBLE PRECISION DEFAULT NULL, observations LONGTEXT DEFAULT NULL, partner_name LONGTEXT DEFAULT NULL, partner_cif_nif VARCHAR(255) NOT NULL, partner_main_address VARCHAR(255) DEFAULT NULL, partner_iban VARCHAR(255) DEFAULT NULL, partner_swift VARCHAR(255) DEFAULT NULL, accounting_account INT DEFAULT NULL, enabled TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_8BBFDD3D9393F8FE (partner_id), INDEX IDX_8BBFDD3DEBF23851 (delivery_address_id), INDEX IDX_8BBFDD3DEECAD467 (partner_main_city_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE purchase_invoice ADD CONSTRAINT FK_8BBFDD3D9393F8FE FOREIGN KEY (partner_id) REFERENCES partner (id)');
        $this->addSql('ALTER TABLE purchase_invoice ADD CONSTRAINT FK_8BBFDD3DEBF23851 FOREIGN KEY (delivery_address_id) REFERENCES partner_delivery_address (id)');
        $this->addSql('ALTER TABLE purchase_invoice ADD CONSTRAINT FK_8BBFDD3DEECAD467 FOREIGN KEY (partner_main_city_id) REFERENCES city (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE purchase_invoice');
    }
}
