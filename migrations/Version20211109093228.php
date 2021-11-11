<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211109093228 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE vehicle_manteinance (id INT AUTO_INCREMENT NOT NULL, vehicle_id INT DEFAULT NULL, vehicle_maintenance_task_id INT DEFAULT NULL, date DATETIME NOT NULL, needs_check TINYINT(1) NOT NULL, enabled TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, description TEXT DEFAULT NULL, INDEX IDX_9ECE92B4545317D1 (vehicle_id), INDEX IDX_9ECE92B473AB9475 (vehicle_maintenance_task_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE vehicle_manteinance ADD CONSTRAINT FK_9ECE92B4545317D1 FOREIGN KEY (vehicle_id) REFERENCES vehicle (id)');
        $this->addSql('ALTER TABLE vehicle_manteinance ADD CONSTRAINT FK_9ECE92B473AB9475 FOREIGN KEY (vehicle_maintenance_task_id) REFERENCES vehicle_manteinance_task (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE vehicle_manteinance');
    }
}
