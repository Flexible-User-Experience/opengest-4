<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211119170122 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE vehicle ADD technical_datasheet1 VARCHAR(255) DEFAULT NULL, ADD technical_datasheet2 VARCHAR(255) DEFAULT NULL, ADD load_table VARCHAR(255) DEFAULT NULL, ADD reach_diagram VARCHAR(255) DEFAULT NULL, ADD traffic_certificate VARCHAR(255) DEFAULT NULL, ADD dimensions VARCHAR(255) DEFAULT NULL, ADD transport_card VARCHAR(255) DEFAULT NULL, ADD traffic_insurance VARCHAR(255) DEFAULT NULL, ADD itv VARCHAR(255) DEFAULT NULL, ADD itc VARCHAR(255) DEFAULT NULL, ADD cedeclaration VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE vehicle DROP technical_datasheet1, DROP technical_datasheet2, DROP load_table, DROP reach_diagram, DROP traffic_certificate, DROP dimensions, DROP transport_card, DROP traffic_insurance, DROP itv, DROP itc, DROP cedeclaration');
    }
}
