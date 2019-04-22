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
 * Bill type and order uuid.
 */
final class Version20190422073724 extends AbstractMigration
{
    /**
     * Drop bill type and order uuid.
     *
     * @param Schema $schema
     *
     * @throws DBALException
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE data.te_order DROP uuid');
        $this->addSql('ALTER TABLE data.te_bill DROP per_type');
    }

    /**
     * Description getter.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return 'Bill type and order uuid';
    }

    /**
     * Create bill type and order uuid.
     *
     * @param Schema $schema
     *
     * @throws DBALException
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE data.te_bill ADD per_type BOOLEAN NOT NULL DEFAULT false');
        $this->addSql('ALTER TABLE data.te_bill ALTER per_type DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN data.te_bill.per_type IS \'Morale or physic\'');
        $this->addSql('ALTER TABLE data.te_order ADD uuid UUID NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX uk_order_uuid ON data.te_order (uuid)');
    }
}
