<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211021135649 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE payslip (id INT AUTO_INCREMENT NOT NULL, operator_id INT DEFAULT NULL, from_date DATETIME NOT NULL, to_date DATETIME NOT NULL, year INT NOT NULL, expenses DOUBLE PRECISION NOT NULL, social_security_cost DOUBLE PRECISION NOT NULL, extra_pay DOUBLE PRECISION NOT NULL, other_costs DOUBLE PRECISION NOT NULL, total_amount DOUBLE PRECISION NOT NULL, enabled TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_9A13CDF0584598A3 (operator_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payslip_line (id INT AUTO_INCREMENT NOT NULL, payslip_id INT DEFAULT NULL, payslip_line_concept_id INT DEFAULT NULL, units INT NOT NULL, price_unit DOUBLE PRECISION NOT NULL, amount DOUBLE PRECISION NOT NULL, enabled TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_D5ECDDD2296F5EA7 (payslip_id), INDEX IDX_D5ECDDD2F5618732 (payslip_line_concept_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payslip_line_concept (id INT AUTO_INCREMENT NOT NULL, description VARCHAR(255) NOT NULL, enabled TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payslip_operator_default_line (id INT AUTO_INCREMENT NOT NULL, operator_id INT DEFAULT NULL, payslip_line_concept_id INT NOT NULL, units DOUBLE PRECISION NOT NULL, price_unit DOUBLE PRECISION NOT NULL, amount DOUBLE PRECISION NOT NULL, enabled TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_3C465780584598A3 (operator_id), INDEX IDX_3C465780F5618732 (payslip_line_concept_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE payslip ADD CONSTRAINT FK_9A13CDF0584598A3 FOREIGN KEY (operator_id) REFERENCES operator (id)');
        $this->addSql('ALTER TABLE payslip_line ADD CONSTRAINT FK_D5ECDDD2296F5EA7 FOREIGN KEY (payslip_id) REFERENCES payslip (id)');
        $this->addSql('ALTER TABLE payslip_line ADD CONSTRAINT FK_D5ECDDD2F5618732 FOREIGN KEY (payslip_line_concept_id) REFERENCES payslip_line_concept (id)');
        $this->addSql('ALTER TABLE payslip_operator_default_line ADD CONSTRAINT FK_3C465780584598A3 FOREIGN KEY (operator_id) REFERENCES operator (id)');
        $this->addSql('ALTER TABLE payslip_operator_default_line ADD CONSTRAINT FK_3C465780F5618732 FOREIGN KEY (payslip_line_concept_id) REFERENCES payslip_line_concept (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE payslip_line DROP FOREIGN KEY FK_D5ECDDD2296F5EA7');
        $this->addSql('ALTER TABLE payslip_line DROP FOREIGN KEY FK_D5ECDDD2F5618732');
        $this->addSql('ALTER TABLE payslip_operator_default_line DROP FOREIGN KEY FK_3C465780F5618732');
        $this->addSql('DROP TABLE payslip');
        $this->addSql('DROP TABLE payslip_line');
        $this->addSql('DROP TABLE payslip_line_concept');
        $this->addSql('DROP TABLE payslip_operator_default_line');
    }
}
