<?php

namespace App\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20181011105316 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sale_delivery_note_line (id INT AUTO_INCREMENT NOT NULL, delivery_note_id INT DEFAULT NULL, units DOUBLE PRECISION DEFAULT NULL, price_unit DOUBLE PRECISION NOT NULL, total DOUBLE PRECISION DEFAULT NULL, discount DOUBLE PRECISION DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, iva DOUBLE PRECISION NOT NULL, irpf DOUBLE PRECISION NOT NULL, enabled TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_488879222CF3B78B (delivery_note_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sale_delivery_note_line ADD CONSTRAINT FK_488879222CF3B78B FOREIGN KEY (delivery_note_id) REFERENCES sale_delivery_note (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE sale_delivery_note_line');
    }
}
