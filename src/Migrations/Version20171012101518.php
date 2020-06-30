<?php

namespace DoctrineMigrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171012101518 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE vehicle ADD enterprise_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE vehicle ADD CONSTRAINT FK_1B80E486A97D1AC3 FOREIGN KEY (enterprise_id) REFERENCES enterprise (id)');
        $this->addSql('CREATE INDEX IDX_1B80E486A97D1AC3 ON vehicle (enterprise_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE vehicle DROP FOREIGN KEY FK_1B80E486A97D1AC3');
        $this->addSql('DROP INDEX IDX_1B80E486A97D1AC3 ON vehicle');
        $this->addSql('ALTER TABLE vehicle DROP enterprise_id');
    }
}
