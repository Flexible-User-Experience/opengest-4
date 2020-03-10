<?php

namespace App\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170811110124 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE enterprises_users (enterprise_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_5BB9084A97D1AC3 (enterprise_id), INDEX IDX_5BB9084A76ED395 (user_id), PRIMARY KEY(enterprise_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE enterprises_users ADD CONSTRAINT FK_5BB9084A97D1AC3 FOREIGN KEY (enterprise_id) REFERENCES enterprise (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE enterprises_users ADD CONSTRAINT FK_5BB9084A76ED395 FOREIGN KEY (user_id) REFERENCES admin_user (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE enterprises_users');
    }
}
