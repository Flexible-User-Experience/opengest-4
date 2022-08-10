<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220810075413 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE purchase_invoice_due_date (id INT AUTO_INCREMENT NOT NULL, purchase_invoice_id INT DEFAULT NULL, enterprise_transfer_account_id INT DEFAULT NULL, amount DOUBLE PRECISION DEFAULT NULL, date DATETIME NOT NULL, payment_date DATETIME DEFAULT NULL, paid TINYINT(1) NOT NULL, enabled TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_289F7A4B25BAA637 (purchase_invoice_id), INDEX IDX_289F7A4BB73AF0C6 (enterprise_transfer_account_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE purchase_invoice_line (id INT AUTO_INCREMENT NOT NULL, purchase_invoice_id INT DEFAULT NULL, purchase_item_id INT DEFAULT NULL, operator_id INT DEFAULT NULL, vehicle_id INT DEFAULT NULL, sale_delivery_note_id INT DEFAULT NULL, cost_center_id INT DEFAULT NULL, units DOUBLE PRECISION DEFAULT NULL, price_unit DOUBLE PRECISION NOT NULL, total DOUBLE PRECISION DEFAULT NULL, base_total DOUBLE PRECISION DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, iva DOUBLE PRECISION NOT NULL, irpf DOUBLE PRECISION NOT NULL, enabled TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_17CB213325BAA637 (purchase_invoice_id), INDEX IDX_17CB21339B59827 (purchase_item_id), INDEX IDX_17CB2133584598A3 (operator_id), INDEX IDX_17CB2133545317D1 (vehicle_id), INDEX IDX_17CB21339C1DE698 (sale_delivery_note_id), INDEX IDX_17CB2133E28F9437 (cost_center_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE purchase_invoice_due_date ADD CONSTRAINT FK_289F7A4B25BAA637 FOREIGN KEY (purchase_invoice_id) REFERENCES purchase_invoice (id)');
        $this->addSql('ALTER TABLE purchase_invoice_due_date ADD CONSTRAINT FK_289F7A4BB73AF0C6 FOREIGN KEY (enterprise_transfer_account_id) REFERENCES enterprise_transfer_account (id)');
        $this->addSql('ALTER TABLE purchase_invoice_line ADD CONSTRAINT FK_17CB213325BAA637 FOREIGN KEY (purchase_invoice_id) REFERENCES purchase_invoice (id)');
        $this->addSql('ALTER TABLE purchase_invoice_line ADD CONSTRAINT FK_17CB21339B59827 FOREIGN KEY (purchase_item_id) REFERENCES purchase_item (id)');
        $this->addSql('ALTER TABLE purchase_invoice_line ADD CONSTRAINT FK_17CB2133584598A3 FOREIGN KEY (operator_id) REFERENCES operator (id)');
        $this->addSql('ALTER TABLE purchase_invoice_line ADD CONSTRAINT FK_17CB2133545317D1 FOREIGN KEY (vehicle_id) REFERENCES vehicle (id)');
        $this->addSql('ALTER TABLE purchase_invoice_line ADD CONSTRAINT FK_17CB21339C1DE698 FOREIGN KEY (sale_delivery_note_id) REFERENCES sale_delivery_note (id)');
        $this->addSql('ALTER TABLE purchase_invoice_line ADD CONSTRAINT FK_17CB2133E28F9437 FOREIGN KEY (cost_center_id) REFERENCES cost_center (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE purchase_invoice_due_date');
        $this->addSql('DROP TABLE purchase_invoice_line');
    }
}
