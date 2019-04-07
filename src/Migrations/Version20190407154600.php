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
 * Columns to reset user password.
 */
final class Version20190407154600 extends AbstractMigration
{
    /**
     * Description getter.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return 'Columns to reset user password';
    }

    /**
     * Create columns to reset user password.
     *
     * @param Schema $schema
     *
     * @throws DBALException
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE data.ts_user ADD resetting_token VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE data.ts_user ADD resetting_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN data.ts_user.resetting_token IS \'token to reset password\'');
        $this->addSql('COMMENT ON COLUMN data.ts_user.resetting_at IS \'reset password timestamp\'');
    }

    /**
     * Drop columns to reset password.
     *
     * @param Schema $schema
     *
     * @throws DBALException
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE data.ts_user DROP resetting_token');
        $this->addSql('ALTER TABLE data.ts_user DROP resetting_at');
    }
}
