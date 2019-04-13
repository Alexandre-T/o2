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
 * Doctrine order and status order tables.
 */
final class Version20190413122350 extends AbstractMigration
{
    /**
     * Description getter.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return 'Manipulate order and status order tables.';
    }

    /**
     * Create order and status order tables.
     *
     * @param Schema $schema schema is not used
     *
     * @throws DBALException when error occurred
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE data.te_order (identifier SERIAL NOT NULL, customer_id INT NOT NULL, status_order_id INT NOT NULL, credits SMALLINT NOT NULL, number INT NOT NULL, price NUMERIC(7, 2) NOT NULL, payment_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, status_credit BOOLEAN DEFAULT \'false\' NOT NULL, vat NUMERIC(7, 2) NOT NULL, per_given VARCHAR(32) DEFAULT NULL, per_name VARCHAR(32) DEFAULT NULL, per_society VARCHAR(64) DEFAULT NULL, per_vat VARCHAR(32) DEFAULT NULL, pad_complement VARCHAR(32) DEFAULT NULL, pad_country VARCHAR(2) NOT NULL, pad_locality VARCHAR(32) NOT NULL, pad_code VARCHAR(5) NOT NULL, pad_street VARCHAR(32) NOT NULL, PRIMARY KEY(identifier))');
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
        $this->addSql('CREATE TABLE data.tr_status_order (id SERIAL NOT NULL, canceled BOOLEAN DEFAULT \'false\' NOT NULL, code VARCHAR(8) NOT NULL, paid BOOLEAN DEFAULT \'false\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX uk_status_order_code ON data.tr_status_order (code)');
        $this->addSql('ALTER TABLE data.te_order ADD CONSTRAINT FK_ORDER_CUSTOMER FOREIGN KEY (customer_id) REFERENCES data.ts_user (usr_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE data.te_order ADD CONSTRAINT FK_ORDER_STATUS_ORDER FOREIGN KEY (status_order_id) REFERENCES data.tr_status_order (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE data.ts_user RENAME COLUMN usr_type TO per_type');
        $this->addSql('ALTER TABLE data.ts_user RENAME COLUMN usr_given TO per_given');
        $this->addSql('ALTER TABLE data.ts_user RENAME COLUMN usr_name TO per_name');
        $this->addSql('ALTER TABLE data.ts_user RENAME COLUMN usr_society TO per_society');
        $this->addSql('ALTER TABLE data.ts_user RENAME COLUMN usr_tva TO per_vat');
        $this->addSql('COMMENT ON COLUMN data.ts_user.per_type IS \'Morale or physic\'');
        $this->addSql('COMMENT ON COLUMN data.ts_user.per_given IS \'Given name\'');
        $this->addSql('COMMENT ON COLUMN data.ts_user.per_name IS \'Name\'');
        $this->addSql('COMMENT ON COLUMN data.ts_user.per_society IS \'Society name\'');
    }

    /**
     * Drop order and status order tables.
     *
     * @param Schema $schema schema is not used
     *
     * @throws DBALException when error occured
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE data.te_order DROP CONSTRAINT FK_ORDER_STATUS_ORDER');
        $this->addSql('DROP TABLE data.te_order');
        $this->addSql('DROP TABLE data.tr_status_order');
        $this->addSql('ALTER TABLE data.ts_user RENAME COLUMN per_type TO usr_type');
        $this->addSql('ALTER TABLE data.ts_user RENAME COLUMN per_given TO usr_given');
        $this->addSql('ALTER TABLE data.ts_user RENAME COLUMN per_name TO usr_name');
        $this->addSql('ALTER TABLE data.ts_user RENAME COLUMN per_society TO usr_society');
        $this->addSql('ALTER TABLE data.ts_user RENAME COLUMN per_vat TO usr_tva');
        $this->addSql('COMMENT ON COLUMN data.ts_user.usr_given IS \'User given name\'');
        $this->addSql('COMMENT ON COLUMN data.ts_user.usr_name IS \'User name\'');
        $this->addSql('COMMENT ON COLUMN data.ts_user.usr_society IS \'User society\'');
        $this->addSql('COMMENT ON COLUMN data.ts_user.usr_type IS \'User mail\'');
    }
}
