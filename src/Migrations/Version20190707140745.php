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
final class Version20190707140745 extends AbstractMigration
{
    /**
     * Downgrade database.
     *
     * @param Schema $schema the schema is not used
     *
     * @throws DBALException
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE tj_ordered_article DROP FOREIGN KEY FK_42F235907294869C');
        $this->addSql('ALTER TABLE te_programmation DROP FOREIGN KEY FK_B621F6A910E8F220');
        $this->addSql('ALTER TABLE te_programmation DROP FOREIGN KEY FK_B621F6A9154BD79B');
        $this->addSql('ALTER TABLE te_bill DROP FOREIGN KEY FK_3C542B4A8D9F6D38');
        $this->addSql('ALTER TABLE tj_ordered_article DROP FOREIGN KEY FK_42F235908D9F6D38');
        $this->addSql('ALTER TABLE te_order DROP FOREIGN KEY FK_5A65FDE64C3A3BB');
        $this->addSql('ALTER TABLE te_bill DROP FOREIGN KEY FK_3C542B4A9395C3F3');
        $this->addSql('ALTER TABLE te_order DROP FOREIGN KEY FK_5A65FDE69395C3F3');
        $this->addSql('ALTER TABLE te_programmation DROP FOREIGN KEY FK_B621F6A99395C3F3');
        $this->addSql('DROP TABLE tr_article');
        $this->addSql('DROP TABLE te_bill');
        $this->addSql('DROP TABLE te_file');
        $this->addSql('DROP TABLE te_order');
        $this->addSql('DROP TABLE tj_ordered_article');
        $this->addSql('DROP TABLE payment');
        $this->addSql('DROP TABLE payment_token');
        $this->addSql('DROP TABLE te_programmation');
        $this->addSql('DROP TABLE ts_settings');
        $this->addSql('DROP TABLE ts_user');
        $this->addSql('DROP TABLE ext_log_entries');
    }

    /**
     * Description getter.
     */
    public function getDescription(): string
    {
        return 'Database first build';
    }

    /**
     * Upgrade database.
     *
     * @param Schema $schema the schema is not used
     *
     * @throws DBALException
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE tr_article (id INT AUTO_INCREMENT NOT NULL COMMENT \'Article identifier\', code VARCHAR(8) NOT NULL COMMENT \'Article unique code\', credit INT NOT NULL COMMENT \'Credit gained when buying article\', price NUMERIC(7, 2) NOT NULL, vat NUMERIC(7, 2) NOT NULL, UNIQUE INDEX uk_article_code (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB COMMENT = \'Article resource table\' ');
        $this->addSql('CREATE TABLE te_bill (id INT AUTO_INCREMENT NOT NULL, customer_id INT UNSIGNED NOT NULL COMMENT \'User identifier\', order_id INT NOT NULL, canceled_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, number INT NOT NULL, paid_at DATETIME DEFAULT NULL, price NUMERIC(7, 2) NOT NULL, vat NUMERIC(7, 2) NOT NULL, per_given VARCHAR(32) DEFAULT NULL COMMENT \'Given name\', per_name VARCHAR(32) DEFAULT NULL COMMENT \'Name\', per_society VARCHAR(64) DEFAULT NULL COMMENT \'Society name\', usr_phone VARCHAR(21) DEFAULT NULL COMMENT \'User phone\', per_type TINYINT(1) NOT NULL COMMENT \'Morale or physic\', per_vat VARCHAR(32) DEFAULT NULL COMMENT \'VAT number\', pad_complement VARCHAR(32) DEFAULT NULL COMMENT \'Complement\', pad_country VARCHAR(2) NOT NULL COMMENT \'Country alpha2 code\', pad_locality VARCHAR(32) NOT NULL COMMENT \'Locality\', pad_code VARCHAR(5) NOT NULL COMMENT \'Postal code\', pad_street VARCHAR(32) NOT NULL COMMENT \'Street address\', INDEX ndx_bill_customer (customer_id), INDEX ndx_bill_order (order_id), UNIQUE INDEX uk_bill_number (number), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB COMMENT = \'bill data table\' ');
        $this->addSql('CREATE TABLE te_file (id INT AUTO_INCREMENT NOT NULL, mime_type VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, original_name VARCHAR(255) NOT NULL, size NUMERIC(10, 0) NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE te_order (id INT AUTO_INCREMENT NOT NULL, customer_id INT UNSIGNED NOT NULL COMMENT \'User identifier\', payment_id INT DEFAULT NULL, credits SMALLINT NOT NULL, payer_id VARCHAR(32) DEFAULT NULL, price NUMERIC(7, 2) NOT NULL, status_credit TINYINT(1) DEFAULT \'0\' NOT NULL, status_order SMALLINT DEFAULT 1 NOT NULL, token VARCHAR(32) DEFAULT NULL, uuid VARCHAR(23) NOT NULL, vat NUMERIC(7, 2) NOT NULL, UNIQUE INDEX UNIQ_5A65FDE64C3A3BB (payment_id), INDEX ndx_user (customer_id), INDEX ndx_user_status (customer_id, status_order), INDEX ndx_status_order (status_order), UNIQUE INDEX uk_order_uuid (uuid), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDb COMMENT = \'order data table\' ');
        $this->addSql('CREATE TABLE tj_ordered_article (article_id INT NOT NULL COMMENT \'Article identifier\', order_id INT NOT NULL, price NUMERIC(7, 2) NOT NULL, quantity SMALLINT NOT NULL, vat NUMERIC(7, 2) NOT NULL, INDEX ndx_ordered_article_order (order_id), INDEX ndx_ordered_article_full (order_id, article_id), INDEX ndx_ordered_article_article (article_id), PRIMARY KEY(article_id, order_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB COMMENT = \'Ordered articles\' ');
        $this->addSql('CREATE TABLE payment (id INT AUTO_INCREMENT NOT NULL, number VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, client_email VARCHAR(255) DEFAULT NULL, client_id VARCHAR(255) DEFAULT NULL, total_amount INT DEFAULT NULL, currency_code VARCHAR(255) DEFAULT NULL, details JSON NOT NULL COMMENT \'(DC2Type:json_array)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment_token (hash VARCHAR(255) NOT NULL, details LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:object)\', after_url LONGTEXT DEFAULT NULL, target_url LONGTEXT NOT NULL, gateway_name VARCHAR(255) NOT NULL, PRIMARY KEY(hash)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE te_programmation (id INT AUTO_INCREMENT NOT NULL, customer_id INT UNSIGNED NOT NULL COMMENT \'User identifier\', final_file_id INT DEFAULT NULL, original_file_id INT NOT NULL, comment LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, credit SMALLINT NOT NULL, cylinder_capacity NUMERIC(7, 5) NOT NULL, delivered_at DATETIME DEFAULT NULL, edc_off TINYINT(1) DEFAULT \'0\' NOT NULL, edc_stopped TINYINT(1) DEFAULT \'0\' NOT NULL, egr_off TINYINT(1) DEFAULT \'0\' NOT NULL, egr_stopped TINYINT(1) DEFAULT \'0\' NOT NULL, ethanol TINYINT(1) DEFAULT \'0\' NOT NULL, ethanol_done TINYINT(1) DEFAULT \'0\' NOT NULL, fap_off TINYINT(1) DEFAULT \'0\' NOT NULL, fap_stopped TINYINT(1) DEFAULT \'0\' NOT NULL, gear_automatic TINYINT(1) DEFAULT \'0\' NOT NULL, make VARCHAR(16) NOT NULL, model VARCHAR(16) NOT NULL, odb SMALLINT NOT NULL, odometer INT NOT NULL, power INT NOT NULL, protocol VARCHAR(32) DEFAULT NULL, readed SMALLINT NOT NULL, reader_tool VARCHAR(12) NOT NULL, response LONGTEXT DEFAULT NULL, serial VARCHAR(25) DEFAULT NULL, stage_one TINYINT(1) NOT NULL, stage_one_done TINYINT(1) DEFAULT \'0\' NOT NULL, version VARCHAR(16) NOT NULL, year SMALLINT NOT NULL, INDEX ndx_customer (customer_id), UNIQUE INDEX uk_programmation_original_file (original_file_id), UNIQUE INDEX uk_programmation_final_file (final_file_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ts_settings (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(32) NOT NULL, value LONGTEXT NOT NULL, UNIQUE INDEX uk_settings_code (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB COMMENT = \'Settings application\' ');
        $this->addSql('CREATE TABLE ts_user (usr_id INT UNSIGNED AUTO_INCREMENT NOT NULL COMMENT \'User identifier\', usr_credit INT UNSIGNED NOT NULL COMMENT \'User credits\', usr_mail VARCHAR(255) NOT NULL COMMENT \'User mail\', usr_password VARCHAR(128) NOT NULL COMMENT \'Encrypted password\', resetting_at DATETIME DEFAULT NULL COMMENT \'reset password timestamp\', resetting_token VARCHAR(255) DEFAULT NULL COMMENT \'token to reset password\', usr_roles JSON NOT NULL COMMENT \'User roles(DC2Type:json_array)\', usr_tos TINYINT(1) NOT NULL COMMENT \'TOS accepted\', per_given VARCHAR(32) DEFAULT NULL COMMENT \'Given name\', per_name VARCHAR(32) DEFAULT NULL COMMENT \'Name\', per_society VARCHAR(64) DEFAULT NULL COMMENT \'Society name\', usr_phone VARCHAR(21) DEFAULT NULL COMMENT \'User phone\', per_type TINYINT(1) NOT NULL COMMENT \'Morale or physic\', per_vat VARCHAR(32) DEFAULT NULL COMMENT \'VAT number\', pad_complement VARCHAR(32) DEFAULT NULL COMMENT \'Complement\', pad_country VARCHAR(2) NOT NULL COMMENT \'Country alpha2 code\', pad_locality VARCHAR(32) NOT NULL COMMENT \'Locality\', pad_code VARCHAR(5) NOT NULL COMMENT \'Postal code\', pad_street VARCHAR(32) NOT NULL COMMENT \'Street address\', UNIQUE INDEX uk_user_mail (usr_mail), PRIMARY KEY(usr_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB COMMENT = \'Users data table\' ');
        $this->addSql('CREATE TABLE ext_log_entries (id INT AUTO_INCREMENT NOT NULL, action VARCHAR(8) NOT NULL, logged_at DATETIME NOT NULL, object_id VARCHAR(64) DEFAULT NULL, object_class VARCHAR(255) NOT NULL, version INT NOT NULL, data LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', username VARCHAR(255) DEFAULT NULL, INDEX log_class_lookup_idx (object_class), INDEX log_date_lookup_idx (logged_at), INDEX log_user_lookup_idx (username), INDEX log_version_lookup_idx (object_id, object_class, version), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB ROW_FORMAT = DYNAMIC');
        $this->addSql('ALTER TABLE te_bill ADD CONSTRAINT FK_3C542B4A9395C3F3 FOREIGN KEY (customer_id) REFERENCES ts_user (usr_id)');
        $this->addSql('ALTER TABLE te_bill ADD CONSTRAINT FK_3C542B4A8D9F6D38 FOREIGN KEY (order_id) REFERENCES te_order (id)');
        $this->addSql('ALTER TABLE te_order ADD CONSTRAINT FK_5A65FDE69395C3F3 FOREIGN KEY (customer_id) REFERENCES ts_user (usr_id)');
        $this->addSql('ALTER TABLE te_order ADD CONSTRAINT FK_5A65FDE64C3A3BB FOREIGN KEY (payment_id) REFERENCES payment (id)');
        $this->addSql('ALTER TABLE tj_ordered_article ADD CONSTRAINT FK_42F235907294869C FOREIGN KEY (article_id) REFERENCES tr_article (id)');
        $this->addSql('ALTER TABLE tj_ordered_article ADD CONSTRAINT FK_42F235908D9F6D38 FOREIGN KEY (order_id) REFERENCES te_order (id)');
        $this->addSql('ALTER TABLE te_programmation ADD CONSTRAINT FK_B621F6A99395C3F3 FOREIGN KEY (customer_id) REFERENCES ts_user (usr_id)');
        $this->addSql('ALTER TABLE te_programmation ADD CONSTRAINT FK_B621F6A910E8F220 FOREIGN KEY (final_file_id) REFERENCES te_file (id)');
        $this->addSql('ALTER TABLE te_programmation ADD CONSTRAINT FK_B621F6A9154BD79B FOREIGN KEY (original_file_id) REFERENCES te_file (id)');
    }
}
