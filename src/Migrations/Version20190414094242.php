<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Script initialization
 */
final class Version20190414094242 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Script initialization';
    }

    /**
     * Script initialization.
     *
     * @param Schema $schema
     * @throws DBALException
     */
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE ext_log_entries_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE data.tr_article (id SERIAL NOT NULL, code VARCHAR(8) NOT NULL, cost NUMERIC(6, 2) NOT NULL, credit INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX uk_article_code ON data.tr_article (code)');
        $this->addSql('COMMENT ON COLUMN data.tr_article.id IS \'Article identifier\'');
        $this->addSql('COMMENT ON COLUMN data.tr_article.code IS \'Article unique code\'');
        $this->addSql('COMMENT ON COLUMN data.tr_article.cost IS \'Article cost\'');
        $this->addSql('COMMENT ON COLUMN data.tr_article.credit IS \'Credit gained when buying article\'');
        $this->addSql('CREATE TABLE data.te_order (id SERIAL NOT NULL, customer_id INT NOT NULL, status_order_id INT NOT NULL, credits SMALLINT NOT NULL, number INT DEFAULT NULL, price NUMERIC(7, 2) NOT NULL, payment_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, status_credit BOOLEAN DEFAULT \'false\' NOT NULL, vat NUMERIC(7, 2) NOT NULL, per_given VARCHAR(32) DEFAULT NULL, per_name VARCHAR(32) DEFAULT NULL, per_society VARCHAR(64) DEFAULT NULL, per_vat VARCHAR(32) DEFAULT NULL, pad_complement VARCHAR(32) DEFAULT NULL, pad_country VARCHAR(2) NOT NULL, pad_locality VARCHAR(32) NOT NULL, pad_code VARCHAR(5) NOT NULL, pad_street VARCHAR(32) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX ndx_user ON data.te_order (customer_id)');
        $this->addSql('CREATE INDEX ndx_status_order ON data.te_order (status_order_id)');
        $this->addSql('CREATE UNIQUE INDEX uk_order_number ON data.te_order (number)');
        $this->addSql('COMMENT ON COLUMN data.te_order.customer_id IS \'User identifier\'');
        $this->addSql('COMMENT ON COLUMN data.te_order.per_given IS \'Given name\'');
        $this->addSql('COMMENT ON COLUMN data.te_order.per_name IS \'Name\'');
        $this->addSql('COMMENT ON COLUMN data.te_order.per_society IS \'Society name\'');
        $this->addSql('COMMENT ON COLUMN data.te_order.per_vat IS \'VAT number\'');
        $this->addSql('COMMENT ON COLUMN data.te_order.pad_complement IS \'Complement\'');
        $this->addSql('COMMENT ON COLUMN data.te_order.pad_country IS \'Country alpha2 code\'');
        $this->addSql('COMMENT ON COLUMN data.te_order.pad_locality IS \'Locality\'');
        $this->addSql('COMMENT ON COLUMN data.te_order.pad_code IS \'Postal code\'');
        $this->addSql('COMMENT ON COLUMN data.te_order.pad_street IS \'Street address\'');
        $this->addSql('CREATE TABLE data.tj_ordered_article (article_id INT NOT NULL, order_id INT NOT NULL, quantity SMALLINT NOT NULL, unit_cost NUMERIC(6, 2) NOT NULL, PRIMARY KEY(article_id, order_id))');
        $this->addSql('CREATE INDEX ndx_ordered_article_order ON data.tj_ordered_article (order_id)');
        $this->addSql('CREATE INDEX ndx_ordered_article_full ON data.tj_ordered_article (order_id, article_id)');
        $this->addSql('CREATE INDEX ndx_ordered_article_article ON data.tj_ordered_article (article_id)');
        $this->addSql('COMMENT ON COLUMN data.tj_ordered_article.article_id IS \'Article identifier\'');
        $this->addSql('CREATE TABLE data.tr_status_order (id SERIAL NOT NULL, canceled BOOLEAN DEFAULT \'false\' NOT NULL, code VARCHAR(8) NOT NULL, paid BOOLEAN DEFAULT \'false\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX uk_status_order_code ON data.tr_status_order (code)');
        $this->addSql('CREATE TABLE data.ts_user (usr_id SERIAL NOT NULL, usr_credit INT NOT NULL, usr_mail VARCHAR(255) NOT NULL, usr_password VARCHAR(128) NOT NULL, resetting_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, resetting_token VARCHAR(255) DEFAULT NULL, usr_roles JSON NOT NULL, usr_phone VARCHAR(21) DEFAULT NULL, usr_tos BOOLEAN NOT NULL, per_type BOOLEAN NOT NULL, pad_complement VARCHAR(32) DEFAULT NULL, pad_country VARCHAR(2) NOT NULL, pad_locality VARCHAR(32) NOT NULL, pad_code VARCHAR(5) NOT NULL, pad_street VARCHAR(32) NOT NULL, per_given VARCHAR(32) DEFAULT NULL, per_name VARCHAR(32) DEFAULT NULL, per_society VARCHAR(64) DEFAULT NULL, per_vat VARCHAR(32) DEFAULT NULL, PRIMARY KEY(usr_id))');
        $this->addSql('CREATE UNIQUE INDEX uk_user_mail ON data.ts_user (usr_mail)');
        $this->addSql('COMMENT ON COLUMN data.ts_user.usr_id IS \'User identifier\'');
        $this->addSql('COMMENT ON COLUMN data.ts_user.usr_credit IS \'User credits\'');
        $this->addSql('COMMENT ON COLUMN data.ts_user.usr_mail IS \'User mail\'');
        $this->addSql('COMMENT ON COLUMN data.ts_user.usr_password IS \'Encrypted password\'');
        $this->addSql('COMMENT ON COLUMN data.ts_user.resetting_at IS \'reset password timestamp\'');
        $this->addSql('COMMENT ON COLUMN data.ts_user.resetting_token IS \'token to reset password\'');
        $this->addSql('COMMENT ON COLUMN data.ts_user.usr_roles IS \'User roles(DC2Type:json_array)\'');
        $this->addSql('COMMENT ON COLUMN data.ts_user.usr_phone IS \'User phone\'');
        $this->addSql('COMMENT ON COLUMN data.ts_user.usr_tos IS \'TOS accepted\'');
        $this->addSql('COMMENT ON COLUMN data.ts_user.per_type IS \'Morale or physic\'');
        $this->addSql('COMMENT ON COLUMN data.ts_user.pad_complement IS \'Complement\'');
        $this->addSql('COMMENT ON COLUMN data.ts_user.pad_country IS \'Country alpha2 code\'');
        $this->addSql('COMMENT ON COLUMN data.ts_user.pad_locality IS \'Locality\'');
        $this->addSql('COMMENT ON COLUMN data.ts_user.pad_code IS \'Postal code\'');
        $this->addSql('COMMENT ON COLUMN data.ts_user.pad_street IS \'Street address\'');
        $this->addSql('COMMENT ON COLUMN data.ts_user.per_given IS \'Given name\'');
        $this->addSql('COMMENT ON COLUMN data.ts_user.per_name IS \'Name\'');
        $this->addSql('COMMENT ON COLUMN data.ts_user.per_society IS \'Society name\'');
        $this->addSql('COMMENT ON COLUMN data.ts_user.per_vat IS \'VAT number\'');
        $this->addSql('CREATE TABLE ext_log_entries (id INT NOT NULL, action VARCHAR(8) NOT NULL, logged_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, object_id VARCHAR(64) DEFAULT NULL, object_class VARCHAR(255) NOT NULL, version INT NOT NULL, data TEXT DEFAULT NULL, username VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX log_class_lookup_idx ON ext_log_entries (object_class)');
        $this->addSql('CREATE INDEX log_date_lookup_idx ON ext_log_entries (logged_at)');
        $this->addSql('CREATE INDEX log_user_lookup_idx ON ext_log_entries (username)');
        $this->addSql('CREATE INDEX log_version_lookup_idx ON ext_log_entries (object_id, object_class, version)');
        $this->addSql('COMMENT ON COLUMN ext_log_entries.data IS \'(DC2Type:array)\'');
        $this->addSql('ALTER TABLE data.te_order ADD CONSTRAINT fk_order_user FOREIGN KEY (customer_id) REFERENCES data.ts_user (usr_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE data.te_order ADD CONSTRAINT fk_order_status_order FOREIGN KEY (status_order_id) REFERENCES data.tr_status_order (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE data.tj_ordered_article ADD CONSTRAINT fk_ordered_article_article FOREIGN KEY (article_id) REFERENCES data.tr_article (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE data.tj_ordered_article ADD CONSTRAINT fk_ordered_article_order FOREIGN KEY (order_id) REFERENCES data.te_order (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    /**
     * Script removing all table.
     *
     * @param Schema $schema
     * @throws DBALException
     */
    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE data.tj_ordered_article DROP CONSTRAINT fk_ordered_article_article');
        $this->addSql('ALTER TABLE data.tj_ordered_article DROP CONSTRAINT fk_ordered_article_order');
        $this->addSql('ALTER TABLE data.te_order DROP CONSTRAINT fk_order_status_order');
        $this->addSql('ALTER TABLE data.te_order DROP CONSTRAINT fk_order_user');
        $this->addSql('DROP SEQUENCE ext_log_entries_id_seq CASCADE');
        $this->addSql('DROP TABLE data.tr_article');
        $this->addSql('DROP TABLE data.te_order');
        $this->addSql('DROP TABLE data.tj_ordered_article');
        $this->addSql('DROP TABLE data.tr_status_order');
        $this->addSql('DROP TABLE data.ts_user');
        $this->addSql('DROP TABLE ext_log_entries');
    }
}
