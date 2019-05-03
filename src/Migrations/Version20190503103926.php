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
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190503103926 extends AbstractMigration
{
    /**
     * @param Schema $schema
     *
     * @throws DBALException
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE data.te_programmation DROP edc_stopped');
        $this->addSql('ALTER TABLE data.te_programmation DROP egr_stopped');
        $this->addSql('ALTER TABLE data.te_programmation DROP ethanol_done');
        $this->addSql('ALTER TABLE data.te_programmation DROP fap_stopped');
        $this->addSql('ALTER TABLE data.te_programmation DROP stage_one_done');
        $this->addSql('ALTER TABLE data.te_programmation DROP created_at');
        $this->addSql('ALTER TABLE data.te_programmation DROP delivered_at');
        $this->addSql('ALTER TABLE data.te_programmation RENAME COLUMN reader_tool TO reader');
    }

    public function getDescription(): string
    {
        return '';
    }

    /**
     * @param Schema $schema
     *
     * @throws DBALException
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE data.te_programmation ADD edc_stopped BOOLEAN DEFAULT \'false\' NOT NULL');
        $this->addSql('ALTER TABLE data.te_programmation ADD egr_stopped BOOLEAN DEFAULT \'false\' NOT NULL');
        $this->addSql('ALTER TABLE data.te_programmation ADD ethanol_done BOOLEAN DEFAULT \'false\' NOT NULL');
        $this->addSql('ALTER TABLE data.te_programmation ADD fap_stopped BOOLEAN DEFAULT \'false\' NOT NULL');
        $this->addSql('ALTER TABLE data.te_programmation ADD stage_one_done BOOLEAN DEFAULT \'false\' NOT NULL');
        $this->addSql('ALTER TABLE data.te_programmation ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE data.te_programmation ADD delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE data.te_programmation RENAME COLUMN reader TO reader_tool');
    }
}
