<?php

namespace DoctrineMigrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170927100032 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE operator_absence (id INT AUTO_INCREMENT NOT NULL, operator_id INT DEFAULT NULL, type_id INT DEFAULT NULL, begin DATE NOT NULL, end DATE NOT NULL, enabled TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_EB1F38AA584598A3 (operator_id), INDEX IDX_EB1F38AAC54C8C93 (type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE operator_absence_type (id INT AUTO_INCREMENT NOT NULL, enabled TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, name VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, UNIQUE INDEX UNIQ_7403BE215E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE operator_absence ADD CONSTRAINT FK_EB1F38AA584598A3 FOREIGN KEY (operator_id) REFERENCES operator (id)');
        $this->addSql('ALTER TABLE operator_absence ADD CONSTRAINT FK_EB1F38AAC54C8C93 FOREIGN KEY (type_id) REFERENCES operator_absence_type (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE operator_absence DROP FOREIGN KEY FK_EB1F38AAC54C8C93');
        $this->addSql('DROP TABLE operator_absence');
        $this->addSql('DROP TABLE operator_absence_type');
    }
}
