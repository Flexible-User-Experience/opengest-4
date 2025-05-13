<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250513082649 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("UPDATE opengest4.operator_work_register owr SET owr.description = replace(owr.description, 'Hora extra', 'Hora nocturna') where id > 0;");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("UPDATE opengest4.operator_work_register owr SET owr.description = replace(owr.description, 'Hora nocturna', 'Hora extra') where id > 0;");
    }
}
