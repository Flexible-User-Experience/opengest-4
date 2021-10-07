<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210914094904 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE operator_work_register_header (id INT AUTO_INCREMENT NOT NULL, operator_id INT DEFAULT NULL, date DATE NOT NULL, enabled TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_35F2E9EF584598A3 (operator_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE operator_work_register_header ADD CONSTRAINT FK_35F2E9EF584598A3 FOREIGN KEY (operator_id) REFERENCES operator (id)');
        $this->addSql('ALTER TABLE operator_work_register DROP FOREIGN KEY FK_E4A80088584598A3');
        $this->addSql('DROP INDEX IDX_E4A80088584598A3 ON operator_work_register');
        $this->addSql('ALTER TABLE operator_work_register DROP date, CHANGE operator_id operator_work_register_header_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE operator_work_register ADD CONSTRAINT FK_E4A8008818F0890D FOREIGN KEY (operator_work_register_header_id) REFERENCES operator_work_register_header (id)');
        $this->addSql('CREATE INDEX IDX_E4A8008818F0890D ON operator_work_register (operator_work_register_header_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE operator_work_register DROP FOREIGN KEY FK_E4A8008818F0890D');
        $this->addSql('DROP TABLE operator_work_register_header');
        $this->addSql('DROP INDEX IDX_E4A8008818F0890D ON operator_work_register');
        $this->addSql('ALTER TABLE operator_work_register ADD date DATE NOT NULL, CHANGE operator_work_register_header_id operator_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE operator_work_register ADD CONSTRAINT FK_E4A80088584598A3 FOREIGN KEY (operator_id) REFERENCES operator (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_E4A80088584598A3 ON operator_work_register (operator_id)');
    }
}
