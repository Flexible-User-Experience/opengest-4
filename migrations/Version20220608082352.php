<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220608082352 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sale_delivery_note ADD delivery_address_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sale_delivery_note ADD CONSTRAINT FK_3FA6B46AEBF23851 FOREIGN KEY (delivery_address_id) REFERENCES partner_delivery_address (id)');
        $this->addSql('CREATE INDEX IDX_3FA6B46AEBF23851 ON sale_delivery_note (delivery_address_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sale_delivery_note DROP FOREIGN KEY FK_3FA6B46AEBF23851');
        $this->addSql('DROP INDEX IDX_3FA6B46AEBF23851 ON sale_delivery_note');
        $this->addSql('ALTER TABLE sale_delivery_note DROP delivery_address_id');
    }
}
