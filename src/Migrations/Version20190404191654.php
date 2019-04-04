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
 * Postal address trait.
 */
final class Version20190404191654 extends AbstractMigration
{
    /**
     * Description.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return 'Postal address trait';
    }

    /**
     * Create postal address columns.
     *
     * @param Schema $schema
     *
     * @throws DBALException
     */
    public function up(Schema $schema): void
    {
        $this->abortIf(
            'postgresql' !== $this->connection->getDatabasePlatform()->getName(),
            'Migration can only be executed safely on \'postgresql\'.'
        );

        $this->addSql('ALTER TABLE data.ts_user ADD pad_complement VARCHAR(32) DEFAULT NULL');
        $this->addSql("ALTER TABLE data.ts_user ADD pad_country VARCHAR(2) NOT NULL DEFAULT 'FR' ");
        $this->addSql("ALTER TABLE data.ts_user ADD pad_locality VARCHAR(32) NOT NULL DEFAULT ''");
        $this->addSql("ALTER TABLE data.ts_user ADD pad_code VARCHAR(5) NOT NULL DEFAULT ''");
        $this->addSql("ALTER TABLE data.ts_user ADD pad_street VARCHAR(32) NOT NULL DEFAULT ''");
        $this->addSql('ALTER TABLE data.ts_user ALTER COLUMN pad_country DROP DEFAULT');
        $this->addSql('ALTER TABLE data.ts_user ALTER COLUMN pad_locality DROP DEFAULT');
        $this->addSql('ALTER TABLE data.ts_user ALTER COLUMN pad_code DROP DEFAULT');
        $this->addSql('ALTER TABLE data.ts_user ALTER COLUMN pad_street DROP DEFAULT');
        $this->addSql("COMMENT ON COLUMN data.ts_user.pad_complement IS 'Address complement'");
        $this->addSql("COMMENT ON COLUMN data.ts_user.pad_country IS 'Country alpha2 code'");
        $this->addSql("COMMENT ON COLUMN data.ts_user.pad_locality IS 'Locality'");
        $this->addSql("COMMENT ON COLUMN data.ts_user.pad_code IS 'Postal code'");
        $this->addSql("COMMENT ON COLUMN data.ts_user.pad_street IS 'Street address'");
    }

    /**
     * Drop postal address columns.
     *
     * @param Schema $schema
     *
     * @throws DBALException
     */
    public function down(Schema $schema): void
    {
        $this->abortIf(
            'postgresql' !== $this->connection->getDatabasePlatform()->getName(),
            'Migration can only be executed safely on \'postgresql\'.'
        );

        $this->addSql('ALTER TABLE data.ts_user DROP pad_complement');
        $this->addSql('ALTER TABLE data.ts_user DROP pad_country');
        $this->addSql('ALTER TABLE data.ts_user DROP pad_locality');
        $this->addSql('ALTER TABLE data.ts_user DROP pad_code');
        $this->addSql('ALTER TABLE data.ts_user DROP pad_street');
    }
}
