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
 * Price trait implementation.
 */
final class Version20190418120912 extends AbstractMigration
{
    /**
     * Price trait drop.
     *
     * @param Schema $schema
     *
     * @throws DBALException
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE data.tr_article ADD cost NUMERIC(6, 2) DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE data.tr_article ALTER cost DROP DEFAULT');
        $this->addSql('UPDATE data.tr_article SET cost = price');
        $this->addSql('ALTER TABLE data.tr_article DROP price');
        $this->addSql('ALTER TABLE data.tr_article DROP vat');
        $this->addSql('COMMENT ON COLUMN data.tr_article.cost IS \'Article cost\'');
    }

    /**
     * Description getter.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return 'Price trait implementation';
    }

    /**
     * Price trait creation.
     *
     * @param Schema $schema
     *
     * @throws DBALException
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE data.tr_article ADD price NUMERIC(7, 2) DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE data.tr_article ADD vat NUMERIC(7, 2) DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE data.tr_article ALTER price DROP DEFAULT');
        $this->addSql('ALTER TABLE data.tr_article ALTER vat DROP DEFAULT');
        $this->addSql('UPDATE data.tr_article SET price = cost, vat = cost * 0.2');

        $this->addSql('ALTER TABLE data.tr_article DROP cost');
    }
}
