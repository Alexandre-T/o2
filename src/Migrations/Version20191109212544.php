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
final class Version20191109212544 extends AbstractMigration
{
    /**
     * Downgrade Schema.
     *
     * @param Schema $schema the schema is not used
     *
     * @throws DBALException when an SQL error happened
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE tr_article ADD vat NUMERIC(7, 2) NULL');
        $this->addSql('UPDATE tr_article SET vat = price * 0.2 where vat is null');
        $this->addSql('ALTER TABLE tr_article MODIFY vat NUMERIC(7, 2) NOT NULL');
    }

    /**
     * Description getter.
     */
    public function getDescription(): string
    {
        return '';
    }

    /**
     * Upgrade Schema.
     *
     * @param Schema $schema the schema is not used
     *
     * @throws DBALException when an SQL error happened
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE tr_article DROP vat');
    }
}
