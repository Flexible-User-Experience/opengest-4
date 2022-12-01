<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221201074627 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE document (id INT AUTO_INCREMENT NOT NULL, operator_id INT DEFAULT NULL, vehicle_id INT DEFAULT NULL, enterprise_id INT DEFAULT NULL, description VARCHAR(255) NOT NULL, file VARCHAR(255) DEFAULT NULL, enabled TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_D8698A76584598A3 (operator_id), INDEX IDX_D8698A76545317D1 (vehicle_id), INDEX IDX_D8698A76A97D1AC3 (enterprise_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A76584598A3 FOREIGN KEY (operator_id) REFERENCES operator (id)');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A76545317D1 FOREIGN KEY (vehicle_id) REFERENCES vehicle (id)');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A76A97D1AC3 FOREIGN KEY (enterprise_id) REFERENCES enterprise (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE document DROP FOREIGN KEY FK_D8698A76584598A3');
        $this->addSql('ALTER TABLE document DROP FOREIGN KEY FK_D8698A76545317D1');
        $this->addSql('ALTER TABLE document DROP FOREIGN KEY FK_D8698A76A97D1AC3');
        $this->addSql('DROP TABLE document');
    }
}
