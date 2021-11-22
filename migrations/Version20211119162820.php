<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211119162820 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE vehicle_consumption ADD vehicle_fuel_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE vehicle_consumption ADD CONSTRAINT FK_A2C4508F88B4CB18 FOREIGN KEY (vehicle_fuel_id) REFERENCES vehicle_fuel (id)');
        $this->addSql('CREATE INDEX IDX_A2C4508F88B4CB18 ON vehicle_consumption (vehicle_fuel_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE vehicle_consumption DROP FOREIGN KEY FK_A2C4508F88B4CB18');
        $this->addSql('DROP INDEX IDX_A2C4508F88B4CB18 ON vehicle_consumption');
        $this->addSql('ALTER TABLE vehicle_consumption DROP vehicle_fuel_id');
    }
}
