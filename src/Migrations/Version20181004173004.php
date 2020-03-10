<?php

namespace App\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20181004173004 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sale_delivery_note (id INT AUTO_INCREMENT NOT NULL, enterprise_id INT DEFAULT NULL, partner_id INT DEFAULT NULL, building_site_id INT DEFAULT NULL, order_id INT DEFAULT NULL, collection_document_id INT DEFAULT NULL, activity_line_id INT DEFAULT NULL, date DATETIME NOT NULL, delivery_note_number INT NOT NULL, base_amount DOUBLE PRECISION NOT NULL, discount DOUBLE PRECISION DEFAULT NULL, collection_term INT DEFAULT NULL, wont_be_invoiced TINYINT(1) NOT NULL, enabled TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_3FA6B46AA97D1AC3 (enterprise_id), INDEX IDX_3FA6B46A9393F8FE (partner_id), INDEX IDX_3FA6B46AC1759B6C (building_site_id), INDEX IDX_3FA6B46A8D9F6D38 (order_id), INDEX IDX_3FA6B46A5F95E4B6 (collection_document_id), INDEX IDX_3FA6B46A4D2903A2 (activity_line_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sale_delivery_note ADD CONSTRAINT FK_3FA6B46AA97D1AC3 FOREIGN KEY (enterprise_id) REFERENCES enterprise (id)');
        $this->addSql('ALTER TABLE sale_delivery_note ADD CONSTRAINT FK_3FA6B46A9393F8FE FOREIGN KEY (partner_id) REFERENCES partner (id)');
        $this->addSql('ALTER TABLE sale_delivery_note ADD CONSTRAINT FK_3FA6B46AC1759B6C FOREIGN KEY (building_site_id) REFERENCES partner_building_site (id)');
        $this->addSql('ALTER TABLE sale_delivery_note ADD CONSTRAINT FK_3FA6B46A8D9F6D38 FOREIGN KEY (order_id) REFERENCES partner_order (id)');
        $this->addSql('ALTER TABLE sale_delivery_note ADD CONSTRAINT FK_3FA6B46A5F95E4B6 FOREIGN KEY (collection_document_id) REFERENCES collection_document_type (id)');
        $this->addSql('ALTER TABLE sale_delivery_note ADD CONSTRAINT FK_3FA6B46A4D2903A2 FOREIGN KEY (activity_line_id) REFERENCES activity_line (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE sale_delivery_note');
    }
}
