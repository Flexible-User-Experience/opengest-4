<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211219182038 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE sale_invoice_due_date (id INT AUTO_INCREMENT NOT NULL, sale_invoice_id INT DEFAULT NULL, amount DOUBLE PRECISION DEFAULT NULL, date DATETIME NOT NULL, enabled TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_49B5AB7AD4DD15B9 (sale_invoice_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sale_invoice_due_date ADD CONSTRAINT FK_49B5AB7AD4DD15B9 FOREIGN KEY (sale_invoice_id) REFERENCES sale_invoice (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE sale_invoice_due_date');
    }
}
