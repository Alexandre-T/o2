<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191029210907 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE te_askedvat (id INT AUTO_INCREMENT NOT NULL, customer_id INT UNSIGNED NOT NULL COMMENT \'User identifier\', accountant_id INT UNSIGNED DEFAULT NULL COMMENT \'User identifier\', vat NUMERIC(4, 2) NOT NULL, status SMALLINT NOT NULL, code VARCHAR(63) DEFAULT NULL, created_at DATETIME NOT NULL, INDEX ndx_askedvat_customer (customer_id), INDEX ndx_askedvat_accountant (accountant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB COMMENT = \'Store customers asking new VAT profile and accountant decisions\' ');
        $this->addSql('ALTER TABLE te_askedvat ADD CONSTRAINT FK_82CF274C9395C3F3 FOREIGN KEY (customer_id) REFERENCES ts_user (usr_id)');
        $this->addSql('ALTER TABLE te_askedvat ADD CONSTRAINT FK_82CF274C9582AA74 FOREIGN KEY (accountant_id) REFERENCES ts_user (usr_id)');
        $this->addSql('ALTER TABLE ts_user ADD vat NUMERIC(4, 2) NULL');
        $this->addSql('ALTER TABLE ts_user ADD bill_indication VARCHAR(63) DEFAULT NULL');
        $this->addSql('UPDATE ts_user SET vat=\'20.00\' WHERE vat IS NULL');
        $this->addSql('ALTER TABLE ts_user MODIFY vat NUMERIC(4, 2) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE te_askedvat');
        $this->addSql('ALTER TABLE ts_user DROP vat');
        $this->addSql('ALTER TABLE ts_user DROP bill_indication');
    }
}
