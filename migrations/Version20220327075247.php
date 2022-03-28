<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220327075247 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sale_invoice ADD partner_main_city_id INT DEFAULT NULL, ADD partner_name LONGTEXT DEFAULT NULL, ADD partner_cif_nif VARCHAR(255) NOT NULL, ADD partner_main_address VARCHAR(255) DEFAULT NULL, ADD partner_iban VARCHAR(255) DEFAULT NULL, ADD partner_swift VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE sale_invoice ADD CONSTRAINT FK_E57BD5D6EECAD467 FOREIGN KEY (partner_main_city_id) REFERENCES city (id)');
        $this->addSql('CREATE INDEX IDX_E57BD5D6EECAD467 ON sale_invoice (partner_main_city_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sale_invoice DROP FOREIGN KEY FK_E57BD5D6EECAD467');
        $this->addSql('DROP INDEX IDX_E57BD5D6EECAD467 ON sale_invoice');
        $this->addSql('ALTER TABLE sale_invoice DROP partner_main_city_id, DROP partner_name, DROP partner_cif_nif, DROP partner_main_address, DROP partner_iban, DROP partner_swift');
    }
}
