<?php

namespace App\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20181127171435 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sale_request_has_delivery_note (id INT AUTO_INCREMENT NOT NULL, sale_request_id INT DEFAULT NULL, sale_delivery_note_id INT DEFAULT NULL, reference VARCHAR(255) DEFAULT NULL, total_hours_morning DOUBLE PRECISION DEFAULT NULL, price_hour_morning DOUBLE PRECISION DEFAULT NULL, amount_morning DOUBLE PRECISION DEFAULT NULL, total_hours_afternoon DOUBLE PRECISION DEFAULT NULL, price_hour_afternoon DOUBLE PRECISION DEFAULT NULL, amount_afternoon DOUBLE PRECISION DEFAULT NULL, total_hours_night DOUBLE PRECISION DEFAULT NULL, price_hour_night DOUBLE PRECISION DEFAULT NULL, amount_night DOUBLE PRECISION DEFAULT NULL, total_hours_early_morning DOUBLE PRECISION DEFAULT NULL, price_hour_early_morning DOUBLE PRECISION DEFAULT NULL, amount_early_morning DOUBLE PRECISION DEFAULT NULL, total_hours_displacement DOUBLE PRECISION DEFAULT NULL, price_hour_displacement DOUBLE PRECISION DEFAULT NULL, amount_displacement DOUBLE PRECISION DEFAULT NULL, iva_type DOUBLE PRECISION DEFAULT NULL, retention_type DOUBLE PRECISION DEFAULT NULL, enabled TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_6A207146BF2A5CE1 (sale_request_id), INDEX IDX_6A2071469C1DE698 (sale_delivery_note_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sale_request_has_delivery_note ADD CONSTRAINT FK_6A207146BF2A5CE1 FOREIGN KEY (sale_request_id) REFERENCES sale_request (id)');
        $this->addSql('ALTER TABLE sale_request_has_delivery_note ADD CONSTRAINT FK_6A2071469C1DE698 FOREIGN KEY (sale_delivery_note_id) REFERENCES sale_delivery_note (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE sale_request_has_delivery_note');
    }
}
