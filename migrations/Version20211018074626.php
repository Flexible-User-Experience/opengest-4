<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211018074626 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE payslip (id INT AUTO_INCREMENT NOT NULL, operator_id INT DEFAULT NULL, `from` DATETIME NOT NULL, `to` DATETIME NOT NULL, year INT NOT NULL, expenses DOUBLE PRECISION NOT NULL, social_security_cost DOUBLE PRECISION NOT NULL, extra_pay DOUBLE PRECISION NOT NULL, other_costs DOUBLE PRECISION NOT NULL, total_amount DOUBLE PRECISION NOT NULL, enabled TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_9A13CDF0584598A3 (operator_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE payslip ADD CONSTRAINT FK_9A13CDF0584598A3 FOREIGN KEY (operator_id) REFERENCES operator (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE payslip');
    }
}
