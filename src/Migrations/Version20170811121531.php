<?php

namespace App\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170811121531 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE admin_user ADD default_enterprise_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE admin_user ADD CONSTRAINT FK_AD8A54A99F5C86D0 FOREIGN KEY (default_enterprise_id) REFERENCES enterprise (id)');
        $this->addSql('CREATE INDEX IDX_AD8A54A99F5C86D0 ON admin_user (default_enterprise_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE admin_user DROP FOREIGN KEY FK_AD8A54A99F5C86D0');
        $this->addSql('DROP INDEX IDX_AD8A54A99F5C86D0 ON admin_user');
        $this->addSql('ALTER TABLE admin_user DROP default_enterprise_id');
    }
}
