<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220811073159 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE purchase_invoice ADD enterprise_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE purchase_invoice ADD CONSTRAINT FK_8BBFDD3DA97D1AC3 FOREIGN KEY (enterprise_id) REFERENCES enterprise (id)');
        $this->addSql('CREATE INDEX IDX_8BBFDD3DA97D1AC3 ON purchase_invoice (enterprise_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE purchase_invoice DROP FOREIGN KEY FK_8BBFDD3DA97D1AC3');
        $this->addSql('DROP INDEX IDX_8BBFDD3DA97D1AC3 ON purchase_invoice');
        $this->addSql('ALTER TABLE purchase_invoice DROP enterprise_id');
    }
}
