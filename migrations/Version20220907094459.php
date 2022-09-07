<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220907094459 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sale_invoice_due_date ADD enterprise_transfer_account_id INT DEFAULT NULL, ADD payment_date DATETIME DEFAULT NULL, ADD paid TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE sale_invoice_due_date ADD CONSTRAINT FK_49B5AB7AB73AF0C6 FOREIGN KEY (enterprise_transfer_account_id) REFERENCES enterprise_transfer_account (id)');
        $this->addSql('CREATE INDEX IDX_49B5AB7AB73AF0C6 ON sale_invoice_due_date (enterprise_transfer_account_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sale_invoice_due_date DROP FOREIGN KEY FK_49B5AB7AB73AF0C6');
        $this->addSql('DROP INDEX IDX_49B5AB7AB73AF0C6 ON sale_invoice_due_date');
        $this->addSql('ALTER TABLE sale_invoice_due_date DROP enterprise_transfer_account_id, DROP payment_date, DROP paid');
    }
}
