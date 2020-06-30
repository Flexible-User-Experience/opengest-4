<?php

namespace DoctrineMigrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180821080305 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE partner_contact (id INT AUTO_INCREMENT NOT NULL, partner_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, care VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, mobile VARCHAR(255) DEFAULT NULL, fax VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, notes TEXT DEFAULT NULL, enabled TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_8B8885259393F8FE (partner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE partner_contact ADD CONSTRAINT FK_8B8885259393F8FE FOREIGN KEY (partner_id) REFERENCES partner (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE partner_contact');
    }
}
