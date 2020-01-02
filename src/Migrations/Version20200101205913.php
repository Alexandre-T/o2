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
 * Adding OLSX.
 */
final class Version20200101205913 extends AbstractMigration
{
    /**
     * Downgrade Schema.
     *
     * @param Schema $schema it is not used
     *
     * @throws DBALException when an error occurred
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ts_user DROP olsx_identifier, DROP olsx_status');
    }

    /**
     * Description getter.
     */
    public function getDescription(): string
    {
        return 'Olsx support';
    }

    /**
     * Upgrade schema.
     *
     * @param Schema $schema it is not used
     *
     * @throws DBALException when an error occurred
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ts_user ADD olsx_identifier INT DEFAULT NULL, ADD olsx_status SMALLINT DEFAULT 0 NOT NULL');
    }
}
