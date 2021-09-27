<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210924135651 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE sale_invoice_sale_delivery_note');
        $this->addSql('ALTER TABLE sale_delivery_note ADD sale_invoice_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sale_delivery_note ADD CONSTRAINT FK_3FA6B46AD4DD15B9 FOREIGN KEY (sale_invoice_id) REFERENCES sale_invoice (id)');
        $this->addSql('CREATE INDEX IDX_3FA6B46AD4DD15B9 ON sale_delivery_note (sale_invoice_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sale_invoice_sale_delivery_note (sale_invoice_id INT NOT NULL, sale_delivery_note_id INT NOT NULL, INDEX IDX_D6833BD79C1DE698 (sale_delivery_note_id), INDEX IDX_D6833BD7D4DD15B9 (sale_invoice_id), PRIMARY KEY(sale_invoice_id, sale_delivery_note_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE sale_invoice_sale_delivery_note ADD CONSTRAINT FK_D6833BD79C1DE698 FOREIGN KEY (sale_delivery_note_id) REFERENCES sale_delivery_note (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sale_invoice_sale_delivery_note ADD CONSTRAINT FK_D6833BD7D4DD15B9 FOREIGN KEY (sale_invoice_id) REFERENCES sale_invoice (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sale_delivery_note DROP FOREIGN KEY FK_3FA6B46AD4DD15B9');
        $this->addSql('DROP INDEX IDX_3FA6B46AD4DD15B9 ON sale_delivery_note');
        $this->addSql('ALTER TABLE sale_delivery_note DROP sale_invoice_id');
    }
}
