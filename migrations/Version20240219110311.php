<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240219110311 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE operator_work_register DROP FOREIGN KEY FK_E4A800889C1DE698');
        $this->addSql('ALTER TABLE operator_work_register ADD CONSTRAINT FK_E4A800889C1DE698 FOREIGN KEY (sale_delivery_note_id) REFERENCES sale_delivery_note (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE operator_work_register DROP FOREIGN KEY FK_E4A800889C1DE698');
        $this->addSql('ALTER TABLE operator_work_register ADD CONSTRAINT FK_E4A800889C1DE698 FOREIGN KEY (sale_delivery_note_id) REFERENCES sale_delivery_note (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
