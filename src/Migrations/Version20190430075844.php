<?php
/**
 * This file is part of the O2 Application.
 *
 * PHP version 7.1|7.2|7.3|7.4
 *
 * (c) Alexandre Tranchant <alexandre.tranchant@gmail.com>
 *
 * @author    Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @copyright 2019 Alexandre Tranchant
 * @license   Cecill-B http://www.cecill.info/licences/Licence_CeCILL-B_V1-fr.txt
 */

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190430075844 extends AbstractMigration
{
    /**
     * @param Schema $schema
     * @throws DBALException
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE data.tj_ordered_article DROP CONSTRAINT FK_C76D6AC17294869C');
        $this->addSql('ALTER TABLE data.te_programmation DROP CONSTRAINT FK_D695C7B710E8F220');
        $this->addSql('ALTER TABLE data.te_programmation DROP CONSTRAINT FK_D695C7B7154BD79B');
        $this->addSql('ALTER TABLE data.te_bill DROP CONSTRAINT FK_38A5ECB58D9F6D38');
        $this->addSql('ALTER TABLE data.tj_ordered_article DROP CONSTRAINT FK_C76D6AC18D9F6D38');
        $this->addSql('ALTER TABLE data.te_bill DROP CONSTRAINT FK_38A5ECB59395C3F3');
        $this->addSql('ALTER TABLE data.te_order DROP CONSTRAINT FK_7763E3AC9395C3F3');
        $this->addSql('ALTER TABLE data.te_programmation DROP CONSTRAINT FK_D695C7B79395C3F3');
        $this->addSql('ALTER TABLE financial_transactions DROP CONSTRAINT FK_1353F2D9CE062FF9');
        $this->addSql('ALTER TABLE credits DROP CONSTRAINT FK_4117D17E4C3A3BB');
        $this->addSql('ALTER TABLE financial_transactions DROP CONSTRAINT FK_1353F2D94C3A3BB');
        $this->addSql('ALTER TABLE data.te_order DROP CONSTRAINT FK_7763E3AC8789B572');
        $this->addSql('ALTER TABLE credits DROP CONSTRAINT FK_4117D17E8789B572');
        $this->addSql('ALTER TABLE payments DROP CONSTRAINT FK_65D29B328789B572');
        $this->addSql('DROP SEQUENCE ext_log_entries_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE credits_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE financial_transactions_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE payments_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE payment_instructions_id_seq CASCADE');
        $this->addSql('DROP TABLE data.tr_article');
        $this->addSql('DROP TABLE data.te_bill');
        $this->addSql('DROP TABLE data.te_file');
        $this->addSql('DROP TABLE data.te_order');
        $this->addSql('DROP TABLE data.tj_ordered_article');
        $this->addSql('DROP TABLE data.te_programmation');
        $this->addSql('DROP TABLE data.ts_settings');
        $this->addSql('DROP TABLE data.ts_user');
        $this->addSql('DROP TABLE ext_log_entries');
        $this->addSql('DROP TABLE credits');
        $this->addSql('DROP TABLE financial_transactions');
        $this->addSql('DROP TABLE payments');
        $this->addSql('DROP TABLE payment_instructions');
    }

    public function getDescription(): string
    {
        return '';
    }

    /**
     * @param Schema $schema
     * @throws DBALException
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA data');
        $this->addSql('CREATE SEQUENCE ext_log_entries_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE credits_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE financial_transactions_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE payments_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE payment_instructions_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE data.tr_article (id SERIAL NOT NULL, code VARCHAR(8) NOT NULL, credit INT NOT NULL, price NUMERIC(7, 2) NOT NULL, vat NUMERIC(7, 2) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX uk_article_code ON data.tr_article (code)');
        $this->addSql('COMMENT ON COLUMN data.tr_article.id IS \'Article identifier\'');
        $this->addSql('COMMENT ON COLUMN data.tr_article.code IS \'Article unique code\'');
        $this->addSql('COMMENT ON COLUMN data.tr_article.credit IS \'Credit gained when buying article\'');
        $this->addSql('CREATE TABLE data.te_bill (id SERIAL NOT NULL, customer_id INT NOT NULL, order_id INT NOT NULL, canceled_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, number INT NOT NULL, paid_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, price NUMERIC(7, 2) NOT NULL, vat NUMERIC(7, 2) NOT NULL, per_given VARCHAR(32) DEFAULT NULL, per_name VARCHAR(32) DEFAULT NULL, per_society VARCHAR(64) DEFAULT NULL, usr_phone VARCHAR(21) DEFAULT NULL, per_type BOOLEAN NOT NULL, per_vat VARCHAR(32) DEFAULT NULL, pad_complement VARCHAR(32) DEFAULT NULL, pad_country VARCHAR(2) NOT NULL, pad_locality VARCHAR(32) NOT NULL, pad_code VARCHAR(5) NOT NULL, pad_street VARCHAR(32) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX ndx_bill_customer ON data.te_bill (customer_id)');
        $this->addSql('CREATE INDEX ndx_bill_order ON data.te_bill (order_id)');
        $this->addSql('CREATE UNIQUE INDEX uk_bill_number ON data.te_bill (number)');
        $this->addSql('COMMENT ON COLUMN data.te_bill.customer_id IS \'User identifier\'');
        $this->addSql('COMMENT ON COLUMN data.te_bill.per_given IS \'Given name\'');
        $this->addSql('COMMENT ON COLUMN data.te_bill.per_name IS \'Name\'');
        $this->addSql('COMMENT ON COLUMN data.te_bill.per_society IS \'Society name\'');
        $this->addSql('COMMENT ON COLUMN data.te_bill.usr_phone IS \'User phone\'');
        $this->addSql('COMMENT ON COLUMN data.te_bill.per_type IS \'Morale or physic\'');
        $this->addSql('COMMENT ON COLUMN data.te_bill.per_vat IS \'VAT number\'');
        $this->addSql('COMMENT ON COLUMN data.te_bill.pad_complement IS \'Complement\'');
        $this->addSql('COMMENT ON COLUMN data.te_bill.pad_country IS \'Country alpha2 code\'');
        $this->addSql('COMMENT ON COLUMN data.te_bill.pad_locality IS \'Locality\'');
        $this->addSql('COMMENT ON COLUMN data.te_bill.pad_code IS \'Postal code\'');
        $this->addSql('COMMENT ON COLUMN data.te_bill.pad_street IS \'Street address\'');
        $this->addSql('CREATE TABLE data.te_file (id SERIAL NOT NULL, mime_type VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, path VARCHAR(255) NOT NULL, size NUMERIC(10, 0) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE data.te_order (id SERIAL NOT NULL, customer_id INT NOT NULL, payment_instruction_id INT DEFAULT NULL, credits SMALLINT NOT NULL, payer_id VARCHAR(32) DEFAULT NULL, price NUMERIC(7, 2) NOT NULL, status_credit BOOLEAN DEFAULT \'false\' NOT NULL, status_order SMALLINT DEFAULT 1 NOT NULL, token VARCHAR(32) DEFAULT NULL, uuid VARCHAR(23) NOT NULL, vat NUMERIC(7, 2) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX ndx_user ON data.te_order (customer_id)');
        $this->addSql('CREATE INDEX ndx_user_status ON data.te_order (customer_id, status_order)');
        $this->addSql('CREATE INDEX ndx_status_order ON data.te_order (status_order)');
        $this->addSql('CREATE UNIQUE INDEX uk_order_payment_instruction ON data.te_order (payment_instruction_id)');
        $this->addSql('CREATE UNIQUE INDEX uk_order_uuid ON data.te_order (uuid)');
        $this->addSql('COMMENT ON COLUMN data.te_order.customer_id IS \'User identifier\'');
        $this->addSql('CREATE TABLE data.tj_ordered_article (article_id INT NOT NULL, order_id INT NOT NULL, price NUMERIC(7, 2) NOT NULL, quantity SMALLINT NOT NULL, vat NUMERIC(7, 2) NOT NULL, PRIMARY KEY(article_id, order_id))');
        $this->addSql('CREATE INDEX ndx_ordered_article_order ON data.tj_ordered_article (order_id)');
        $this->addSql('CREATE INDEX ndx_ordered_article_full ON data.tj_ordered_article (order_id, article_id)');
        $this->addSql('CREATE INDEX ndx_ordered_article_article ON data.tj_ordered_article (article_id)');
        $this->addSql('COMMENT ON COLUMN data.tj_ordered_article.article_id IS \'Article identifier\'');
        $this->addSql('CREATE TABLE data.te_programmation (id SERIAL NOT NULL, customer_id INT NOT NULL, final_file_id INT DEFAULT NULL, original_file_id INT NOT NULL, comment TEXT DEFAULT NULL, credit SMALLINT NOT NULL, cylinder_capacity NUMERIC(7, 5) NOT NULL, edc_off BOOLEAN DEFAULT \'false\' NOT NULL, egr_off BOOLEAN DEFAULT \'false\' NOT NULL, ethanol BOOLEAN DEFAULT \'false\' NOT NULL, fap_off BOOLEAN DEFAULT \'false\' NOT NULL, gear_automatic BOOLEAN DEFAULT \'false\' NOT NULL, make VARCHAR(16) NOT NULL, model VARCHAR(16) NOT NULL, odb SMALLINT NOT NULL, odometer INT NOT NULL, power INT NOT NULL, protocol VARCHAR(32) DEFAULT NULL, read SMALLINT NOT NULL, reader VARCHAR(12) NOT NULL, serial VARCHAR(25) DEFAULT NULL, stage_one BOOLEAN NOT NULL, version VARCHAR(16) NOT NULL, year SMALLINT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX ndx_customer ON data.te_programmation (customer_id)');
        $this->addSql('CREATE UNIQUE INDEX uk_programmation_original_file ON data.te_programmation (original_file_id)');
        $this->addSql('CREATE UNIQUE INDEX uk_programmation_final_file ON data.te_programmation (final_file_id)');
        $this->addSql('COMMENT ON COLUMN data.te_programmation.customer_id IS \'User identifier\'');
        $this->addSql('CREATE TABLE data.ts_settings (id SERIAL NOT NULL, code VARCHAR(32) NOT NULL, value TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX uk_settings_code ON data.ts_settings (code)');
        $this->addSql('CREATE TABLE data.ts_user (usr_id SERIAL NOT NULL, usr_credit INT NOT NULL, usr_mail VARCHAR(255) NOT NULL, usr_password VARCHAR(128) NOT NULL, resetting_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, resetting_token VARCHAR(255) DEFAULT NULL, usr_roles JSON NOT NULL, usr_tos BOOLEAN NOT NULL, per_given VARCHAR(32) DEFAULT NULL, per_name VARCHAR(32) DEFAULT NULL, per_society VARCHAR(64) DEFAULT NULL, usr_phone VARCHAR(21) DEFAULT NULL, per_type BOOLEAN NOT NULL, per_vat VARCHAR(32) DEFAULT NULL, pad_complement VARCHAR(32) DEFAULT NULL, pad_country VARCHAR(2) NOT NULL, pad_locality VARCHAR(32) NOT NULL, pad_code VARCHAR(5) NOT NULL, pad_street VARCHAR(32) NOT NULL, PRIMARY KEY(usr_id))');
        $this->addSql('CREATE UNIQUE INDEX uk_user_mail ON data.ts_user (usr_mail)');
        $this->addSql('COMMENT ON COLUMN data.ts_user.usr_id IS \'User identifier\'');
        $this->addSql('COMMENT ON COLUMN data.ts_user.usr_credit IS \'User credits\'');
        $this->addSql('COMMENT ON COLUMN data.ts_user.usr_mail IS \'User mail\'');
        $this->addSql('COMMENT ON COLUMN data.ts_user.usr_password IS \'Encrypted password\'');
        $this->addSql('COMMENT ON COLUMN data.ts_user.resetting_at IS \'reset password timestamp\'');
        $this->addSql('COMMENT ON COLUMN data.ts_user.resetting_token IS \'token to reset password\'');
        $this->addSql('COMMENT ON COLUMN data.ts_user.usr_roles IS \'User roles(DC2Type:json_array)\'');
        $this->addSql('COMMENT ON COLUMN data.ts_user.usr_tos IS \'TOS accepted\'');
        $this->addSql('COMMENT ON COLUMN data.ts_user.per_given IS \'Given name\'');
        $this->addSql('COMMENT ON COLUMN data.ts_user.per_name IS \'Name\'');
        $this->addSql('COMMENT ON COLUMN data.ts_user.per_society IS \'Society name\'');
        $this->addSql('COMMENT ON COLUMN data.ts_user.usr_phone IS \'User phone\'');
        $this->addSql('COMMENT ON COLUMN data.ts_user.per_type IS \'Morale or physic\'');
        $this->addSql('COMMENT ON COLUMN data.ts_user.per_vat IS \'VAT number\'');
        $this->addSql('COMMENT ON COLUMN data.ts_user.pad_complement IS \'Complement\'');
        $this->addSql('COMMENT ON COLUMN data.ts_user.pad_country IS \'Country alpha2 code\'');
        $this->addSql('COMMENT ON COLUMN data.ts_user.pad_locality IS \'Locality\'');
        $this->addSql('COMMENT ON COLUMN data.ts_user.pad_code IS \'Postal code\'');
        $this->addSql('COMMENT ON COLUMN data.ts_user.pad_street IS \'Street address\'');
        $this->addSql('CREATE TABLE ext_log_entries (id INT NOT NULL, action VARCHAR(8) NOT NULL, logged_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, object_id VARCHAR(64) DEFAULT NULL, object_class VARCHAR(255) NOT NULL, version INT NOT NULL, data TEXT DEFAULT NULL, username VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX log_class_lookup_idx ON ext_log_entries (object_class)');
        $this->addSql('CREATE INDEX log_date_lookup_idx ON ext_log_entries (logged_at)');
        $this->addSql('CREATE INDEX log_user_lookup_idx ON ext_log_entries (username)');
        $this->addSql('CREATE INDEX log_version_lookup_idx ON ext_log_entries (object_id, object_class, version)');
        $this->addSql('COMMENT ON COLUMN ext_log_entries.data IS \'(DC2Type:array)\'');
        $this->addSql('CREATE TABLE credits (id INT NOT NULL, payment_instruction_id INT NOT NULL, payment_id INT DEFAULT NULL, attention_required BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, credited_amount NUMERIC(10, 5) NOT NULL, crediting_amount NUMERIC(10, 5) NOT NULL, reversing_amount NUMERIC(10, 5) NOT NULL, state SMALLINT NOT NULL, target_amount NUMERIC(10, 5) NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_4117D17E8789B572 ON credits (payment_instruction_id)');
        $this->addSql('CREATE INDEX IDX_4117D17E4C3A3BB ON credits (payment_id)');
        $this->addSql('CREATE TABLE financial_transactions (id INT NOT NULL, credit_id INT DEFAULT NULL, payment_id INT DEFAULT NULL, extended_data TEXT DEFAULT NULL, processed_amount NUMERIC(10, 5) NOT NULL, reason_code VARCHAR(100) DEFAULT NULL, reference_number VARCHAR(100) DEFAULT NULL, requested_amount NUMERIC(10, 5) NOT NULL, response_code VARCHAR(100) DEFAULT NULL, state SMALLINT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, tracking_id VARCHAR(100) DEFAULT NULL, transaction_type SMALLINT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_1353F2D9CE062FF9 ON financial_transactions (credit_id)');
        $this->addSql('CREATE INDEX IDX_1353F2D94C3A3BB ON financial_transactions (payment_id)');
        $this->addSql('COMMENT ON COLUMN financial_transactions.extended_data IS \'(DC2Type:extended_payment_data)\'');
        $this->addSql('CREATE TABLE payments (id INT NOT NULL, payment_instruction_id INT NOT NULL, approved_amount NUMERIC(10, 5) NOT NULL, approving_amount NUMERIC(10, 5) NOT NULL, credited_amount NUMERIC(10, 5) NOT NULL, crediting_amount NUMERIC(10, 5) NOT NULL, deposited_amount NUMERIC(10, 5) NOT NULL, depositing_amount NUMERIC(10, 5) NOT NULL, expiration_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, reversing_approved_amount NUMERIC(10, 5) NOT NULL, reversing_credited_amount NUMERIC(10, 5) NOT NULL, reversing_deposited_amount NUMERIC(10, 5) NOT NULL, state SMALLINT NOT NULL, target_amount NUMERIC(10, 5) NOT NULL, attention_required BOOLEAN NOT NULL, expired BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_65D29B328789B572 ON payments (payment_instruction_id)');
        $this->addSql('CREATE TABLE payment_instructions (id INT NOT NULL, amount NUMERIC(10, 5) NOT NULL, approved_amount NUMERIC(10, 5) NOT NULL, approving_amount NUMERIC(10, 5) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, credited_amount NUMERIC(10, 5) NOT NULL, crediting_amount NUMERIC(10, 5) NOT NULL, currency VARCHAR(3) NOT NULL, deposited_amount NUMERIC(10, 5) NOT NULL, depositing_amount NUMERIC(10, 5) NOT NULL, extended_data TEXT NOT NULL, payment_system_name VARCHAR(100) NOT NULL, reversing_approved_amount NUMERIC(10, 5) NOT NULL, reversing_credited_amount NUMERIC(10, 5) NOT NULL, reversing_deposited_amount NUMERIC(10, 5) NOT NULL, state SMALLINT NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN payment_instructions.extended_data IS \'(DC2Type:extended_payment_data)\'');
        $this->addSql('ALTER TABLE data.te_bill ADD CONSTRAINT FK_38A5ECB59395C3F3 FOREIGN KEY (customer_id) REFERENCES data.ts_user (usr_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE data.te_bill ADD CONSTRAINT FK_38A5ECB58D9F6D38 FOREIGN KEY (order_id) REFERENCES data.te_order (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE data.te_order ADD CONSTRAINT FK_7763E3AC9395C3F3 FOREIGN KEY (customer_id) REFERENCES data.ts_user (usr_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE data.te_order ADD CONSTRAINT FK_7763E3AC8789B572 FOREIGN KEY (payment_instruction_id) REFERENCES payment_instructions (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE data.tj_ordered_article ADD CONSTRAINT FK_C76D6AC17294869C FOREIGN KEY (article_id) REFERENCES data.tr_article (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE data.tj_ordered_article ADD CONSTRAINT FK_C76D6AC18D9F6D38 FOREIGN KEY (order_id) REFERENCES data.te_order (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE data.te_programmation ADD CONSTRAINT FK_D695C7B79395C3F3 FOREIGN KEY (customer_id) REFERENCES data.ts_user (usr_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE data.te_programmation ADD CONSTRAINT FK_D695C7B710E8F220 FOREIGN KEY (final_file_id) REFERENCES data.te_file (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE data.te_programmation ADD CONSTRAINT FK_D695C7B7154BD79B FOREIGN KEY (original_file_id) REFERENCES data.te_file (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE credits ADD CONSTRAINT FK_4117D17E8789B572 FOREIGN KEY (payment_instruction_id) REFERENCES payment_instructions (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE credits ADD CONSTRAINT FK_4117D17E4C3A3BB FOREIGN KEY (payment_id) REFERENCES payments (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE financial_transactions ADD CONSTRAINT FK_1353F2D9CE062FF9 FOREIGN KEY (credit_id) REFERENCES credits (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE financial_transactions ADD CONSTRAINT FK_1353F2D94C3A3BB FOREIGN KEY (payment_id) REFERENCES payments (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE payments ADD CONSTRAINT FK_65D29B328789B572 FOREIGN KEY (payment_instruction_id) REFERENCES payment_instructions (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
