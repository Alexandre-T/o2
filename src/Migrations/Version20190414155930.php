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
 * Payment instruction and order join.
 */
final class Version20190414155930 extends AbstractMigration
{
    /**
     * Remove links.
     *
     * @param Schema $schema
     *
     * @throws DBALException
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE data.te_order DROP CONSTRAINT fk_order_payment_instruction');
        $this->addSql('ALTER TABLE data.te_order DROP payment_instruction_id');
        $this->addSql('ALTER TABLE data.te_order DROP amount');
    }

    /**
     * Description getter.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return 'Payment instruction and order join';
    }

    /**
     * Create links.
     *
     * @param Schema $schema
     *
     * @throws DBALException
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE data.te_order ADD payment_instruction_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE data.te_order ADD amount NUMERIC(10, 5) NOT NULL DEFAULT 0');
        $this->addSql('ALTER TABLE data.te_order ALTER amount DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN data.te_order.amount IS \'Amount TTC\'');
        $this->addSql('ALTER TABLE data.te_order ADD CONSTRAINT fk_order_payment_instruction FOREIGN KEY (payment_instruction_id) REFERENCES payment_instructions (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX uk_order_payment_instruction ON data.te_order (payment_instruction_id)');
    }
}
