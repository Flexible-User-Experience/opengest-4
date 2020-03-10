<?php

namespace App\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180727085433 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE operator ADD enterprise_group_bounty_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE operator ADD CONSTRAINT FK_D7A6A781D4C833C FOREIGN KEY (enterprise_group_bounty_id) REFERENCES enterprise_group_bounty (id)');
        $this->addSql('CREATE INDEX IDX_D7A6A781D4C833C ON operator (enterprise_group_bounty_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE operator DROP FOREIGN KEY FK_D7A6A781D4C833C');
        $this->addSql('DROP INDEX IDX_D7A6A781D4C833C ON operator');
        $this->addSql('ALTER TABLE operator DROP enterprise_group_bounty_id');
    }
}
