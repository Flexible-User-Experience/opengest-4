<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211214170952 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_CDEABF3F5E237E06 ON admin_group');
        $this->addSql('ALTER TABLE admin_group DROP name, DROP roles');
        $this->addSql('ALTER TABLE partner ADD collection_document_type_id INT DEFAULT NULL, ADD invoice_email VARCHAR(255) DEFAULT NULL, ADD accounting_account INT DEFAULT NULL, ADD collection_term1 INT DEFAULT NULL, ADD collection_term2 INT DEFAULT NULL, ADD collection_term3 INT DEFAULT NULL, ADD pay_day1 INT DEFAULT NULL, ADD pay_day2 INT DEFAULT NULL, ADD pay_day3 INT DEFAULT NULL, ADD invoice_copies_number INT DEFAULT NULL');
        $this->addSql('ALTER TABLE partner ADD CONSTRAINT FK_312B3E161328F0D1 FOREIGN KEY (collection_document_type_id) REFERENCES collection_document_type (id)');
        $this->addSql('CREATE INDEX IDX_312B3E161328F0D1 ON partner (collection_document_type_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE admin_group ADD name VARCHAR(180) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, ADD roles LONGTEXT CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci` COMMENT \'(DC2Type:array)\'');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CDEABF3F5E237E06 ON admin_group (name)');
        $this->addSql('ALTER TABLE partner DROP FOREIGN KEY FK_312B3E161328F0D1');
        $this->addSql('DROP INDEX IDX_312B3E161328F0D1 ON partner');
        $this->addSql('ALTER TABLE partner DROP collection_document_type_id, DROP invoice_email, DROP accounting_account, DROP collection_term1, DROP collection_term2, DROP collection_term3, DROP pay_day1, DROP pay_day2, DROP pay_day3, DROP invoice_copies_number');
    }
}
