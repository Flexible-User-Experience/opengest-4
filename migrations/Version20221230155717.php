<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221230155717 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE operator_cheking_ppe (id INT AUTO_INCREMENT NOT NULL, operator_id INT DEFAULT NULL, type_id INT DEFAULT NULL, begin DATE NOT NULL, end DATE NOT NULL, enabled TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_80F7424F584598A3 (operator_id), INDEX IDX_80F7424FC54C8C93 (type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE operator_cheking_training (id INT AUTO_INCREMENT NOT NULL, operator_id INT DEFAULT NULL, type_id INT DEFAULT NULL, begin DATE NOT NULL, end DATE NOT NULL, enabled TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_EA5DF99584598A3 (operator_id), INDEX IDX_EA5DF99C54C8C93 (type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE operator_cheking_ppe ADD CONSTRAINT FK_80F7424F584598A3 FOREIGN KEY (operator_id) REFERENCES operator (id)');
        $this->addSql('ALTER TABLE operator_cheking_ppe ADD CONSTRAINT FK_80F7424FC54C8C93 FOREIGN KEY (type_id) REFERENCES operator_checking_type (id)');
        $this->addSql('ALTER TABLE operator_cheking_training ADD CONSTRAINT FK_EA5DF99584598A3 FOREIGN KEY (operator_id) REFERENCES operator (id)');
        $this->addSql('ALTER TABLE operator_cheking_training ADD CONSTRAINT FK_EA5DF99C54C8C93 FOREIGN KEY (type_id) REFERENCES operator_checking_type (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE operator_cheking_ppe DROP FOREIGN KEY FK_80F7424F584598A3');
        $this->addSql('ALTER TABLE operator_cheking_ppe DROP FOREIGN KEY FK_80F7424FC54C8C93');
        $this->addSql('ALTER TABLE operator_cheking_training DROP FOREIGN KEY FK_EA5DF99584598A3');
        $this->addSql('ALTER TABLE operator_cheking_training DROP FOREIGN KEY FK_EA5DF99C54C8C93');
        $this->addSql('DROP TABLE operator_cheking_ppe');
        $this->addSql('DROP TABLE operator_cheking_training');
    }
}
