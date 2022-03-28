<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210707081300 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sale_delivery_note_line ADD sale_item_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sale_delivery_note_line ADD CONSTRAINT FK_48887922677190CC FOREIGN KEY (sale_item_id) REFERENCES sale_item (id)');
        $this->addSql('CREATE INDEX IDX_48887922677190CC ON sale_delivery_note_line (sale_item_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sale_delivery_note_line DROP FOREIGN KEY FK_48887922677190CC');
        $this->addSql('DROP INDEX IDX_48887922677190CC ON sale_delivery_note_line');
        $this->addSql('ALTER TABLE sale_delivery_note_line DROP sale_item_id');
    }
}
