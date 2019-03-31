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

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration for user entity.
 */
final class Version20190331134444 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Migration for user entity';
    }

    /**
     * Create user table.
     *
     * @param Schema $schema
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA data');
        $this->addSql('CREATE TABLE data.ts_user (usr_id SERIAL NOT NULL, usr_credit INT NOT NULL, usr_given VARCHAR(32) DEFAULT NULL, usr_mail VARCHAR(255) NOT NULL, usr_name VARCHAR(32) DEFAULT NULL, usr_password VARCHAR(128) NOT NULL, usr_roles TEXT NOT NULL, usr_society VARCHAR(64) DEFAULT NULL, usr_type BOOLEAN NOT NULL, PRIMARY KEY(usr_id))');
        $this->addSql('CREATE UNIQUE INDEX uk_user_mail ON data.ts_user (usr_mail)');
        $this->addSql('COMMENT ON COLUMN data.ts_user.usr_id IS \'User identifier\'');
        $this->addSql('COMMENT ON COLUMN data.ts_user.usr_credit IS \'User credits\'');
        $this->addSql('COMMENT ON COLUMN data.ts_user.usr_given IS \'User given name\'');
        $this->addSql('COMMENT ON COLUMN data.ts_user.usr_mail IS \'User mail\'');
        $this->addSql('COMMENT ON COLUMN data.ts_user.usr_name IS \'User name\'');
        $this->addSql('COMMENT ON COLUMN data.ts_user.usr_password IS \'Encrypted password\'');
        $this->addSql('COMMENT ON COLUMN data.ts_user.usr_roles IS \'User roles(DC2Type:json_array)\'');
        $this->addSql('COMMENT ON COLUMN data.ts_user.usr_society IS \'User society\'');
        $this->addSql('COMMENT ON COLUMN data.ts_user.usr_type IS \'User mail\'');
    }

    /**
     * Drop user table.
     *
     * @param Schema $schema
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE data.ts_user');
        $this->addSql('DROP SCHEMA data');
    }
}
