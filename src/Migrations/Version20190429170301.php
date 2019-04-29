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

use Doctrine\DBAL\DBALException as DBALExceptionAlias;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190429170301 extends AbstractMigration
{
    /**
     * @param Schema $schema
     *
     * @throws DBALExceptionAlias
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE data.te_programmation DROP CONSTRAINT FK_D695C7B7154BD79B');
        $this->addSql('ALTER TABLE data.te_programmation DROP CONSTRAINT FK_D695C7B710E8F220');
        $this->addSql('DROP TABLE data.te_file');
        $this->addSql('DROP TABLE data.te_programmation');
    }

    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE data.te_file (id SERIAL NOT NULL, path VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, mime_type VARCHAR(255) NOT NULL, size NUMERIC(10, 0) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE data.te_programmation (id SERIAL NOT NULL, original_file_id INT NOT NULL, final_file_id INT DEFAULT NULL, customer_id INT NOT NULL, comment TEXT DEFAULT NULL, cylinder_capacity NUMERIC(7, 5) NOT NULL, e85 BOOLEAN NOT NULL, edc15off BOOLEAN NOT NULL, egr_off BOOLEAN NOT NULL, fap_off BOOLEAN NOT NULL, gear BOOLEAN NOT NULL, make VARCHAR(16) NOT NULL, model VARCHAR(16) NOT NULL, odb SMALLINT NOT NULL, odometer INT NOT NULL, power INT NOT NULL, protocol VARCHAR(32) DEFAULT NULL, read SMALLINT NOT NULL, reader VARCHAR(12) NOT NULL, serial VARCHAR(25) DEFAULT NULL, stage_one BOOLEAN NOT NULL, version VARCHAR(16) NOT NULL, year SMALLINT NOT NULL, credit SMALLINT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX ndx_customer ON data.te_programmation (customer_id)');
        $this->addSql('CREATE UNIQUE INDEX uk_programmation_original_file ON data.te_programmation (original_file_id)');
        $this->addSql('CREATE UNIQUE INDEX uk_programmation_final_file ON data.te_programmation (final_file_id)');
        $this->addSql('COMMENT ON COLUMN data.te_programmation.customer_id IS \'User identifier\'');
        $this->addSql('ALTER TABLE data.te_programmation ADD CONSTRAINT FK_D695C7B7154BD79B FOREIGN KEY (original_file_id) REFERENCES data.te_file (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE data.te_programmation ADD CONSTRAINT FK_D695C7B710E8F220 FOREIGN KEY (final_file_id) REFERENCES data.te_file (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE data.te_programmation ADD CONSTRAINT FK_D695C7B79395C3F3 FOREIGN KEY (customer_id) REFERENCES data.ts_user (usr_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
