<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250512093719 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE enterprise_group_bounty ADD transp DOUBLE PRECISION DEFAULT '0', ADD cp40 DOUBLE PRECISION DEFAULT '0', ADD cp_plus40 DOUBLE PRECISION DEFAULT '0', ADD crane40 DOUBLE PRECISION DEFAULT '0', ADD crane50 DOUBLE PRECISION DEFAULT '0', ADD crane60 DOUBLE PRECISION DEFAULT '0', ADD crane80 DOUBLE PRECISION DEFAULT '0', ADD crane100 DOUBLE PRECISION DEFAULT '0', ADD crane120 DOUBLE PRECISION DEFAULT '0', ADD crane200 DOUBLE PRECISION DEFAULT '0', ADD crane250300 DOUBLE PRECISION DEFAULT '0', ADD platform40 DOUBLE PRECISION DEFAULT '0', ADD platform50 DOUBLE PRECISION DEFAULT '0', ADD platform60 DOUBLE PRECISION DEFAULT '0', ADD platform70 DOUBLE PRECISION DEFAULT '0'
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE enterprise_group_bounty DROP transp, DROP cp40, DROP cp_plus40, DROP crane40, DROP crane50, DROP crane60, DROP crane80, DROP crane100, DROP crane120, DROP crane200, DROP crane250300, DROP platform40, DROP platform50, DROP platform60, DROP platform70
        SQL);
    }
}
