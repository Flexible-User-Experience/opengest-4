<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220907143430 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cost_center (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, enabled TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE purchase_invoice (id INT AUTO_INCREMENT NOT NULL, partner_id INT DEFAULT NULL, enterprise_id INT DEFAULT NULL, delivery_address_id INT DEFAULT NULL, partner_main_city_id INT DEFAULT NULL, date DATETIME NOT NULL, invoice_number INT NOT NULL, reference VARCHAR(255) DEFAULT NULL, total DOUBLE PRECISION DEFAULT NULL, base_total DOUBLE PRECISION DEFAULT NULL, iva DOUBLE PRECISION DEFAULT NULL, iva21 DOUBLE PRECISION DEFAULT NULL, iva10 DOUBLE PRECISION DEFAULT NULL, iva4 DOUBLE PRECISION DEFAULT NULL, iva0 DOUBLE PRECISION DEFAULT NULL, irpf DOUBLE PRECISION DEFAULT NULL, observations LONGTEXT DEFAULT NULL, partner_name LONGTEXT DEFAULT NULL, partner_cif_nif VARCHAR(255) NOT NULL, partner_main_address VARCHAR(255) DEFAULT NULL, partner_iban VARCHAR(255) DEFAULT NULL, partner_swift VARCHAR(255) DEFAULT NULL, accounting_account INT DEFAULT NULL, enabled TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_8BBFDD3D9393F8FE (partner_id), INDEX IDX_8BBFDD3DA97D1AC3 (enterprise_id), INDEX IDX_8BBFDD3DEBF23851 (delivery_address_id), INDEX IDX_8BBFDD3DEECAD467 (partner_main_city_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE purchase_invoice_due_date (id INT AUTO_INCREMENT NOT NULL, purchase_invoice_id INT DEFAULT NULL, enterprise_transfer_account_id INT DEFAULT NULL, amount DOUBLE PRECISION DEFAULT NULL, date DATETIME NOT NULL, payment_date DATETIME DEFAULT NULL, paid TINYINT(1) NOT NULL, enabled TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_289F7A4B25BAA637 (purchase_invoice_id), INDEX IDX_289F7A4BB73AF0C6 (enterprise_transfer_account_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE purchase_invoice_line (id INT AUTO_INCREMENT NOT NULL, purchase_invoice_id INT DEFAULT NULL, purchase_item_id INT DEFAULT NULL, operator_id INT DEFAULT NULL, vehicle_id INT DEFAULT NULL, sale_delivery_note_id INT DEFAULT NULL, cost_center_id INT DEFAULT NULL, units DOUBLE PRECISION DEFAULT NULL, price_unit DOUBLE PRECISION NOT NULL, total DOUBLE PRECISION DEFAULT NULL, base_total DOUBLE PRECISION DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, iva DOUBLE PRECISION NOT NULL, irpf DOUBLE PRECISION NOT NULL, enabled TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_17CB213325BAA637 (purchase_invoice_id), INDEX IDX_17CB21339B59827 (purchase_item_id), INDEX IDX_17CB2133584598A3 (operator_id), INDEX IDX_17CB2133545317D1 (vehicle_id), INDEX IDX_17CB21339C1DE698 (sale_delivery_note_id), INDEX IDX_17CB2133E28F9437 (cost_center_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE purchase_item (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, enabled TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE purchase_invoice ADD CONSTRAINT FK_8BBFDD3D9393F8FE FOREIGN KEY (partner_id) REFERENCES partner (id)');
        $this->addSql('ALTER TABLE purchase_invoice ADD CONSTRAINT FK_8BBFDD3DA97D1AC3 FOREIGN KEY (enterprise_id) REFERENCES enterprise (id)');
        $this->addSql('ALTER TABLE purchase_invoice ADD CONSTRAINT FK_8BBFDD3DEBF23851 FOREIGN KEY (delivery_address_id) REFERENCES partner_delivery_address (id)');
        $this->addSql('ALTER TABLE purchase_invoice ADD CONSTRAINT FK_8BBFDD3DEECAD467 FOREIGN KEY (partner_main_city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE purchase_invoice_due_date ADD CONSTRAINT FK_289F7A4B25BAA637 FOREIGN KEY (purchase_invoice_id) REFERENCES purchase_invoice (id)');
        $this->addSql('ALTER TABLE purchase_invoice_due_date ADD CONSTRAINT FK_289F7A4BB73AF0C6 FOREIGN KEY (enterprise_transfer_account_id) REFERENCES enterprise_transfer_account (id)');
        $this->addSql('ALTER TABLE purchase_invoice_line ADD CONSTRAINT FK_17CB213325BAA637 FOREIGN KEY (purchase_invoice_id) REFERENCES purchase_invoice (id)');
        $this->addSql('ALTER TABLE purchase_invoice_line ADD CONSTRAINT FK_17CB21339B59827 FOREIGN KEY (purchase_item_id) REFERENCES purchase_item (id)');
        $this->addSql('ALTER TABLE purchase_invoice_line ADD CONSTRAINT FK_17CB2133584598A3 FOREIGN KEY (operator_id) REFERENCES operator (id)');
        $this->addSql('ALTER TABLE purchase_invoice_line ADD CONSTRAINT FK_17CB2133545317D1 FOREIGN KEY (vehicle_id) REFERENCES vehicle (id)');
        $this->addSql('ALTER TABLE purchase_invoice_line ADD CONSTRAINT FK_17CB21339C1DE698 FOREIGN KEY (sale_delivery_note_id) REFERENCES sale_delivery_note (id)');
        $this->addSql('ALTER TABLE purchase_invoice_line ADD CONSTRAINT FK_17CB2133E28F9437 FOREIGN KEY (cost_center_id) REFERENCES cost_center (id)');
        $this->addSql('ALTER TABLE partner ADD cost_accounting_account INT DEFAULT NULL, ADD default_iva DOUBLE PRECISION DEFAULT NULL, ADD default_irpf DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE sale_invoice_due_date ADD enterprise_transfer_account_id INT DEFAULT NULL, ADD payment_date DATETIME DEFAULT NULL, ADD paid TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE sale_invoice_due_date ADD CONSTRAINT FK_49B5AB7AB73AF0C6 FOREIGN KEY (enterprise_transfer_account_id) REFERENCES enterprise_transfer_account (id)');
        $this->addSql('CREATE INDEX IDX_49B5AB7AB73AF0C6 ON sale_invoice_due_date (enterprise_transfer_account_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE purchase_invoice_line DROP FOREIGN KEY FK_17CB2133E28F9437');
        $this->addSql('ALTER TABLE purchase_invoice_due_date DROP FOREIGN KEY FK_289F7A4B25BAA637');
        $this->addSql('ALTER TABLE purchase_invoice_line DROP FOREIGN KEY FK_17CB213325BAA637');
        $this->addSql('ALTER TABLE purchase_invoice_line DROP FOREIGN KEY FK_17CB21339B59827');
        $this->addSql('DROP TABLE cost_center');
        $this->addSql('DROP TABLE purchase_invoice');
        $this->addSql('DROP TABLE purchase_invoice_due_date');
        $this->addSql('DROP TABLE purchase_invoice_line');
        $this->addSql('DROP TABLE purchase_item');
        $this->addSql('ALTER TABLE partner DROP cost_accounting_account, DROP default_iva, DROP default_irpf');
        $this->addSql('ALTER TABLE sale_invoice_due_date DROP FOREIGN KEY FK_49B5AB7AB73AF0C6');
        $this->addSql('DROP INDEX IDX_49B5AB7AB73AF0C6 ON sale_invoice_due_date');
        $this->addSql('ALTER TABLE sale_invoice_due_date DROP enterprise_transfer_account_id, DROP payment_date, DROP paid');
    }
}
