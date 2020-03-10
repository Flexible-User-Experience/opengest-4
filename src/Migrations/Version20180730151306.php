<?php

namespace DoctrineMigrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180730151306 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE enterprise_transfer_account (id INT AUTO_INCREMENT NOT NULL, enterprise_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, iban VARCHAR(255) DEFAULT NULL, swift VARCHAR(255) DEFAULT NULL, bank_code VARCHAR(255) DEFAULT NULL, office_number VARCHAR(255) DEFAULT NULL, control_digit VARCHAR(255) DEFAULT NULL, account_number VARCHAR(255) DEFAULT NULL, enabled TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_D3DD4C47A97D1AC3 (enterprise_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE enterprise_transfer_account ADD CONSTRAINT FK_D3DD4C47A97D1AC3 FOREIGN KEY (enterprise_id) REFERENCES enterprise (id)');
        $this->addSql('ALTER TABLE enterprise ADD transfer_account_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE enterprise ADD CONSTRAINT FK_B1B36A0341A66153 FOREIGN KEY (transfer_account_id) REFERENCES enterprise_transfer_account (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B1B36A0341A66153 ON enterprise (transfer_account_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE enterprise DROP FOREIGN KEY FK_B1B36A0341A66153');
        $this->addSql('DROP TABLE enterprise_transfer_account');
        $this->addSql('DROP INDEX UNIQ_B1B36A0341A66153 ON enterprise');
        $this->addSql('ALTER TABLE enterprise DROP transfer_account_id');
    }
}
