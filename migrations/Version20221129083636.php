<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221129083636 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sale_invoice ADD sale_invoice_generated_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sale_invoice ADD CONSTRAINT FK_E57BD5D66B5E6DF6 FOREIGN KEY (sale_invoice_generated_id) REFERENCES sale_invoice (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E57BD5D66B5E6DF6 ON sale_invoice (sale_invoice_generated_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sale_invoice DROP FOREIGN KEY FK_E57BD5D66B5E6DF6');
        $this->addSql('DROP INDEX UNIQ_E57BD5D66B5E6DF6 ON sale_invoice');
        $this->addSql('ALTER TABLE sale_invoice DROP sale_invoice_generated_id');
    }
}
