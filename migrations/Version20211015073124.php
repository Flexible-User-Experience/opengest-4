<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211015073124 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE payslip_operator_default_line (id INT AUTO_INCREMENT NOT NULL, operator_id INT DEFAULT NULL, payslip_line_concept_id INT DEFAULT NULL, units DOUBLE PRECISION NOT NULL, price_unit DOUBLE PRECISION NOT NULL, amount DOUBLE PRECISION NOT NULL, enabled TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_3C465780584598A3 (operator_id), INDEX IDX_3C465780F5618732 (payslip_line_concept_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE payslip_operator_default_line ADD CONSTRAINT FK_3C465780584598A3 FOREIGN KEY (operator_id) REFERENCES operator (id)');
        $this->addSql('ALTER TABLE payslip_operator_default_line ADD CONSTRAINT FK_3C465780F5618732 FOREIGN KEY (payslip_line_concept_id) REFERENCES payslip_line_concept (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE payslip_operator_default_line');
    }
}