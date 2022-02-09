<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220209091950 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sale_invoice ADD collection_document_type_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sale_invoice ADD CONSTRAINT FK_E57BD5D61328F0D1 FOREIGN KEY (collection_document_type_id) REFERENCES collection_document_type (id)');
        $this->addSql('CREATE INDEX IDX_E57BD5D61328F0D1 ON sale_invoice (collection_document_type_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sale_invoice DROP FOREIGN KEY FK_E57BD5D61328F0D1');
        $this->addSql('DROP INDEX IDX_E57BD5D61328F0D1 ON sale_invoice');
        $this->addSql('ALTER TABLE sale_invoice DROP collection_document_type_id');
    }
}
