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
 * Bill table.
 */
final class Version20190417120615 extends AbstractMigration
{
    /**
     * Drop bill table.
     *
     * @param Schema $schema
     *
     * @throws DBALException
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE data.te_bill');
        $this->addSql('ALTER TABLE data.te_order ADD number INT DEFAULT NULL');
        $this->addSql('ALTER TABLE data.te_order ADD payment_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE data.te_order ADD per_given VARCHAR(32) DEFAULT NULL');
        $this->addSql('ALTER TABLE data.te_order ADD per_name VARCHAR(32) DEFAULT NULL');
        $this->addSql('ALTER TABLE data.te_order ADD per_society VARCHAR(64) DEFAULT NULL');
        $this->addSql('ALTER TABLE data.te_order ADD per_vat VARCHAR(32) DEFAULT NULL');
        $this->addSql('ALTER TABLE data.te_order ADD pad_complement VARCHAR(32) DEFAULT NULL');
        $this->addSql("ALTER TABLE data.te_order ADD pad_country VARCHAR(2) DEFAULT 'FR' NOT NULL");
        $this->addSql('ALTER TABLE data.te_order ADD pad_locality VARCHAR(32) DEFAULT \'\' NOT NULL');
        $this->addSql('ALTER TABLE data.te_order ADD pad_code VARCHAR(5) DEFAULT \'\' NOT NULL');
        $this->addSql('ALTER TABLE data.te_order ADD pad_street VARCHAR(32) DEFAULT \'\' NOT NULL');
        $this->addSql('ALTER TABLE data.te_order ADD amount NUMERIC(10, 5) DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE data.te_order ALTER pad_country DROP DEFAULT');
        $this->addSql('ALTER TABLE data.te_order ALTER pad_locality DROP DEFAULT');
        $this->addSql('ALTER TABLE data.te_order ALTER pad_code DROP DEFAULT');
        $this->addSql('ALTER TABLE data.te_order ALTER pad_street DROP DEFAULT');
        $this->addSql('ALTER TABLE data.te_order ALTER amount DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN data.te_order.per_given IS \'Given name\'');
        $this->addSql('COMMENT ON COLUMN data.te_order.per_name IS \'Name\'');
        $this->addSql('COMMENT ON COLUMN data.te_order.per_society IS \'Society name\'');
        $this->addSql('COMMENT ON COLUMN data.te_order.per_vat IS \'VAT number\'');
        $this->addSql('COMMENT ON COLUMN data.te_order.pad_complement IS \'Complement\'');
        $this->addSql('COMMENT ON COLUMN data.te_order.pad_country IS \'Country alpha2 code\'');
        $this->addSql('COMMENT ON COLUMN data.te_order.pad_locality IS \'Locality\'');
        $this->addSql('COMMENT ON COLUMN data.te_order.pad_code IS \'Postal code\'');
        $this->addSql('COMMENT ON COLUMN data.te_order.pad_street IS \'Street address\'');
        $this->addSql('COMMENT ON COLUMN data.te_order.amount IS \'Amount TTC\'');
    }

    /**
     * Description getter.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return 'Bill table';
    }

    /**
     * Create bill table.
     *
     * @param Schema $schema
     *
     * @throws DBALException
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE data.te_bill (id SERIAL NOT NULL, customer_id INT NOT NULL, order_id INT NOT NULL, canceled_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, number INT NOT NULL, paid_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, per_given VARCHAR(32) DEFAULT NULL, per_name VARCHAR(32) DEFAULT NULL, per_society VARCHAR(64) DEFAULT NULL, usr_phone VARCHAR(21) DEFAULT NULL, per_vat VARCHAR(32) DEFAULT NULL, pad_complement VARCHAR(32) DEFAULT NULL, pad_country VARCHAR(2) NOT NULL, pad_locality VARCHAR(32) NOT NULL, pad_code VARCHAR(5) NOT NULL, pad_street VARCHAR(32) NOT NULL, price NUMERIC(7, 2) NOT NULL, vat NUMERIC(7, 2) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX ndx_bill_customer ON data.te_bill (customer_id)');
        $this->addSql('CREATE INDEX ndx_bill_order ON data.te_bill (order_id)');
        $this->addSql('CREATE UNIQUE INDEX uk_bill_number ON data.te_bill (number)');
        $this->addSql('COMMENT ON COLUMN data.te_bill.customer_id IS \'User identifier\'');
        $this->addSql('COMMENT ON COLUMN data.te_bill.per_given IS \'Given name\'');
        $this->addSql('COMMENT ON COLUMN data.te_bill.per_name IS \'Name\'');
        $this->addSql('COMMENT ON COLUMN data.te_bill.per_society IS \'Society name\'');
        $this->addSql('COMMENT ON COLUMN data.te_bill.usr_phone IS \'User phone\'');
        $this->addSql('COMMENT ON COLUMN data.te_bill.per_vat IS \'VAT number\'');
        $this->addSql('COMMENT ON COLUMN data.te_bill.pad_complement IS \'Complement\'');
        $this->addSql('COMMENT ON COLUMN data.te_bill.pad_country IS \'Country alpha2 code\'');
        $this->addSql('COMMENT ON COLUMN data.te_bill.pad_locality IS \'Locality\'');
        $this->addSql('COMMENT ON COLUMN data.te_bill.pad_code IS \'Postal code\'');
        $this->addSql('COMMENT ON COLUMN data.te_bill.pad_street IS \'Street address\'');
        $this->addSql('ALTER TABLE data.te_bill ADD CONSTRAINT FK_BILL_CUSTOMER FOREIGN KEY (customer_id) REFERENCES data.ts_user (usr_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE data.te_bill ADD CONSTRAINT FK_BILL_ORDER FOREIGN KEY (order_id) REFERENCES data.te_order (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE data.te_order DROP number');
        $this->addSql('ALTER TABLE data.te_order DROP payment_at');
        $this->addSql('ALTER TABLE data.te_order DROP per_given');
        $this->addSql('ALTER TABLE data.te_order DROP per_name');
        $this->addSql('ALTER TABLE data.te_order DROP per_society');
        $this->addSql('ALTER TABLE data.te_order DROP per_vat');
        $this->addSql('ALTER TABLE data.te_order DROP pad_complement');
        $this->addSql('ALTER TABLE data.te_order DROP pad_country');
        $this->addSql('ALTER TABLE data.te_order DROP pad_locality');
        $this->addSql('ALTER TABLE data.te_order DROP pad_code');
        $this->addSql('ALTER TABLE data.te_order DROP pad_street');
        $this->addSql('ALTER TABLE data.te_order DROP amount');
    }
}
