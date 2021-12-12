<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211104160556 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE vehicle ADD vehicle_category_id INT DEFAULT NULL, DROP category');
        $this->addSql('ALTER TABLE vehicle ADD CONSTRAINT FK_1B80E4869C7DE094 FOREIGN KEY (vehicle_category_id) REFERENCES vehicle_category (id)');
        $this->addSql('CREATE INDEX IDX_1B80E4869C7DE094 ON vehicle (vehicle_category_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE vehicle DROP FOREIGN KEY FK_1B80E4869C7DE094');
        $this->addSql('DROP INDEX IDX_1B80E4869C7DE094 ON vehicle');
        $this->addSql('ALTER TABLE vehicle ADD category VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, DROP vehicle_category_id');
    }
}
