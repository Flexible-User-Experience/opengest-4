<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211217171858 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE fos_user_user_group');
//        $this->addSql('DROP TABLE migration_versions');
        $this->addSql('ALTER TABLE sale_invoice ADD delivery_address_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sale_invoice ADD CONSTRAINT FK_E57BD5D6EBF23851 FOREIGN KEY (delivery_address_id) REFERENCES partner_delivery_address (id)');
        $this->addSql('CREATE INDEX IDX_E57BD5D6EBF23851 ON sale_invoice (delivery_address_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE fos_user_user_group (user_id INT NOT NULL, group_id INT NOT NULL, INDEX IDX_B3C77447A76ED395 (user_id), INDEX IDX_B3C77447FE54D947 (group_id), PRIMARY KEY(user_id, group_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
//        $this->addSql('CREATE TABLE migration_versions (version VARCHAR(14) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, executed_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(version)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE fos_user_user_group ADD CONSTRAINT FK_B3C77447A76ED395 FOREIGN KEY (user_id) REFERENCES admin_user (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE fos_user_user_group ADD CONSTRAINT FK_B3C77447FE54D947 FOREIGN KEY (group_id) REFERENCES admin_group (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sale_invoice DROP FOREIGN KEY FK_E57BD5D6EBF23851');
        $this->addSql('DROP INDEX IDX_E57BD5D6EBF23851 ON sale_invoice');
        $this->addSql('ALTER TABLE sale_invoice DROP delivery_address_id');
    }
}
