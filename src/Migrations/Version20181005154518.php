<?php

namespace App\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20181005154518 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sale_invoice (id INT AUTO_INCREMENT NOT NULL, partner_id INT DEFAULT NULL, series_id INT DEFAULT NULL, date DATETIME NOT NULL, invoice_number INT NOT NULL, type INT NOT NULL, total DOUBLE PRECISION DEFAULT NULL, has_been_counted TINYINT(1) NOT NULL, enabled TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_E57BD5D69393F8FE (partner_id), INDEX IDX_E57BD5D65278319C (series_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sale_invoice ADD CONSTRAINT FK_E57BD5D69393F8FE FOREIGN KEY (partner_id) REFERENCES partner (id)');
        $this->addSql('ALTER TABLE sale_invoice ADD CONSTRAINT FK_E57BD5D65278319C FOREIGN KEY (series_id) REFERENCES sale_invoice_series (id)');
        $this->addSql('ALTER TABLE sale_invoice_series CHANGE prefix prefix VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE sale_delivery_note ADD sale_invoice_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sale_delivery_note ADD CONSTRAINT FK_3FA6B46AD4DD15B9 FOREIGN KEY (sale_invoice_id) REFERENCES sale_invoice (id)');
        $this->addSql('CREATE INDEX IDX_3FA6B46AD4DD15B9 ON sale_delivery_note (sale_invoice_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sale_delivery_note DROP FOREIGN KEY FK_3FA6B46AD4DD15B9');
        $this->addSql('DROP TABLE sale_invoice');
        $this->addSql('DROP INDEX IDX_3FA6B46AD4DD15B9 ON sale_delivery_note');
        $this->addSql('ALTER TABLE sale_delivery_note DROP sale_invoice_id');
        $this->addSql('ALTER TABLE sale_invoice_series CHANGE prefix prefix VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci');
    }
}
