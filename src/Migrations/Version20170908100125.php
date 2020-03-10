<?php

namespace App\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170908100125 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE operator_cheking (id INT AUTO_INCREMENT NOT NULL, operator_id INT DEFAULT NULL, type_id INT DEFAULT NULL, begin DATE NOT NULL, end DATE NOT NULL, enabled TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_8C8B1610584598A3 (operator_id), INDEX IDX_8C8B1610C54C8C93 (type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE operator_checking_type (id INT AUTO_INCREMENT NOT NULL, enabled TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, name VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, UNIQUE INDEX UNIQ_2F97D9B15E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE operator_cheking ADD CONSTRAINT FK_8C8B1610584598A3 FOREIGN KEY (operator_id) REFERENCES operator (id)');
        $this->addSql('ALTER TABLE operator_cheking ADD CONSTRAINT FK_8C8B1610C54C8C93 FOREIGN KEY (type_id) REFERENCES operator_checking_type (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE operator_cheking DROP FOREIGN KEY FK_8C8B1610C54C8C93');
        $this->addSql('DROP TABLE operator_cheking');
        $this->addSql('DROP TABLE operator_checking_type');
    }
}
