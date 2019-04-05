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
 * Tva number and telephone user columns.
 */
final class Version20190406164540 extends AbstractMigration
{
    /**
     * Description getter.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return 'Tva number and telephone user columns';
    }

    /**
     * Create columns.
     *
     * @param Schema $schema the new schema
     *
     * @throws DBALException throws when something went wrong
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE data.ts_user ADD tva_number VARCHAR(32) DEFAULT NULL');
        $this->addSql('ALTER TABLE data.ts_user ADD telephone VARCHAR(21) DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN data.ts_user.pad_complement IS \'Complement\'');
    }

    /**
     * Drop columns.
     *
     * @param Schema $schema the new schema
     *
     * @throws DBALException throws when something went wrong
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE data.ts_user DROP tva_number');
        $this->addSql('ALTER TABLE data.ts_user DROP telephone');
        $this->addSql('COMMENT ON COLUMN data.ts_user.pad_complement IS \'Address complement\'');
    }
}
