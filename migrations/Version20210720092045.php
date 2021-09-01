<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210720092045 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sale_delivery_note ADD sale_service_tariff_id INT DEFAULT NULL, ADD vehicle_id INT DEFAULT NULL, ADD secondary_vehicle_id INT DEFAULT NULL, ADD operator_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sale_delivery_note ADD CONSTRAINT FK_3FA6B46A4EAB1A17 FOREIGN KEY (sale_service_tariff_id) REFERENCES sale_service_tariff (id)');
        $this->addSql('ALTER TABLE sale_delivery_note ADD CONSTRAINT FK_3FA6B46A545317D1 FOREIGN KEY (vehicle_id) REFERENCES vehicle (id)');
        $this->addSql('ALTER TABLE sale_delivery_note ADD CONSTRAINT FK_3FA6B46A7D15FA0C FOREIGN KEY (secondary_vehicle_id) REFERENCES vehicle (id)');
        $this->addSql('ALTER TABLE sale_delivery_note ADD CONSTRAINT FK_3FA6B46A584598A3 FOREIGN KEY (operator_id) REFERENCES operator (id)');
        $this->addSql('CREATE INDEX IDX_3FA6B46A4EAB1A17 ON sale_delivery_note (sale_service_tariff_id)');
        $this->addSql('CREATE INDEX IDX_3FA6B46A545317D1 ON sale_delivery_note (vehicle_id)');
        $this->addSql('CREATE INDEX IDX_3FA6B46A7D15FA0C ON sale_delivery_note (secondary_vehicle_id)');
        $this->addSql('CREATE INDEX IDX_3FA6B46A584598A3 ON sale_delivery_note (operator_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sale_delivery_note DROP FOREIGN KEY FK_3FA6B46A4EAB1A17');
        $this->addSql('ALTER TABLE sale_delivery_note DROP FOREIGN KEY FK_3FA6B46A545317D1');
        $this->addSql('ALTER TABLE sale_delivery_note DROP FOREIGN KEY FK_3FA6B46A7D15FA0C');
        $this->addSql('ALTER TABLE sale_delivery_note DROP FOREIGN KEY FK_3FA6B46A584598A3');
        $this->addSql('DROP INDEX IDX_3FA6B46A4EAB1A17 ON sale_delivery_note');
        $this->addSql('DROP INDEX IDX_3FA6B46A545317D1 ON sale_delivery_note');
        $this->addSql('DROP INDEX IDX_3FA6B46A7D15FA0C ON sale_delivery_note');
        $this->addSql('DROP INDEX IDX_3FA6B46A584598A3 ON sale_delivery_note');
        $this->addSql('ALTER TABLE sale_delivery_note DROP sale_service_tariff_id, DROP vehicle_id, DROP secondary_vehicle_id, DROP operator_id');
    }
}
