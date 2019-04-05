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

        $this->addSql('ALTER TABLE data.ts_user ADD usr_phone VARCHAR(21) DEFAULT NULL');
        $this->addSql('ALTER TABLE data.ts_user ADD usr_tos BOOLEAN NOT NULL DEFAULT TRUE');
        $this->addSql('ALTER TABLE data.ts_user ADD usr_tva VARCHAR(32) DEFAULT NULL');
        $this->addSql('ALTER TABLE data.ts_user ALTER usr_tos DROP DEFAULT ');
        $this->addSql('COMMENT ON COLUMN data.ts_user.pad_complement IS \'Complement\'');
        $this->addSql('COMMENT ON COLUMN data.ts_user.usr_phone IS \'User phone\'');
        $this->addSql('COMMENT ON COLUMN data.ts_user.usr_tos IS \'TOS accepted\'');
        $this->addSql('COMMENT ON COLUMN data.ts_user.usr_tva IS \'VAT number\'');
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

        $this->addSql('ALTER TABLE data.ts_user DROP usr_phone');
        $this->addSql('ALTER TABLE data.ts_user DROP usr_tos');
        $this->addSql('ALTER TABLE data.ts_user DROP usr_tva');
        $this->addSql('COMMENT ON COLUMN data.ts_user.pad_complement IS \'Address complement\'');
    }
}
