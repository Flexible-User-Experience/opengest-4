<?php

namespace DoctrineMigrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180801141323 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE enterprise_transfer_account DROP INDEX UNIQ_D3DD4C47A97D1AC3, ADD INDEX IDX_D3DD4C47A97D1AC3 (enterprise_id)');
        $this->addSql('ALTER TABLE enterprise DROP FOREIGN KEY FK_B1B36A0341A66153');
        $this->addSql('DROP INDEX UNIQ_B1B36A0341A66153 ON enterprise');
        $this->addSql('ALTER TABLE enterprise DROP transfer_account_id');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE enterprise ADD transfer_account_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE enterprise ADD CONSTRAINT FK_B1B36A0341A66153 FOREIGN KEY (transfer_account_id) REFERENCES enterprise_transfer_account (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B1B36A0341A66153 ON enterprise (transfer_account_id)');
        $this->addSql('ALTER TABLE enterprise_transfer_account DROP INDEX IDX_D3DD4C47A97D1AC3, ADD UNIQUE INDEX UNIQ_D3DD4C47A97D1AC3 (enterprise_id)');
    }
}
