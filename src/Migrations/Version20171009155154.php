<?php

namespace DoctrineMigrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171009155154 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE operator CHANGE shoe_size shoe_size VARCHAR(255) DEFAULT NULL, CHANGE jerseyt_size jerseyt_size VARCHAR(255) DEFAULT NULL, CHANGE jacket_size jacket_size VARCHAR(255) DEFAULT NULL, CHANGE t_shirt_size t_shirt_size VARCHAR(255) DEFAULT NULL, CHANGE pant_size pant_size VARCHAR(255) DEFAULT NULL, CHANGE working_dress_size working_dress_size VARCHAR(255) DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE operator CHANGE shoe_size shoe_size INT DEFAULT NULL, CHANGE jerseyt_size jerseyt_size INT DEFAULT NULL, CHANGE jacket_size jacket_size INT DEFAULT NULL, CHANGE t_shirt_size t_shirt_size INT DEFAULT NULL, CHANGE pant_size pant_size INT DEFAULT NULL, CHANGE working_dress_size working_dress_size INT DEFAULT NULL');
    }
}
