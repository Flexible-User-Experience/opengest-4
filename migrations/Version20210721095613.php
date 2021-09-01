<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210721095613 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE operator_work_register (id INT AUTO_INCREMENT NOT NULL, operator_id INT DEFAULT NULL, sale_delivery_note_id INT DEFAULT NULL, date DATE NOT NULL, start TIME DEFAULT NULL, finish TIME DEFAULT NULL, units DOUBLE PRECISION NOT NULL, price_unit DOUBLE PRECISION NOT NULL, amount DOUBLE PRECISION NOT NULL, description VARCHAR(255) DEFAULT NULL, enabled TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_E4A80088584598A3 (operator_id), INDEX IDX_E4A800889C1DE698 (sale_delivery_note_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE operator_work_register ADD CONSTRAINT FK_E4A80088584598A3 FOREIGN KEY (operator_id) REFERENCES operator (id)');
        $this->addSql('ALTER TABLE operator_work_register ADD CONSTRAINT FK_E4A800889C1DE698 FOREIGN KEY (sale_delivery_note_id) REFERENCES sale_delivery_note (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE operator_work_register');
    }
}
