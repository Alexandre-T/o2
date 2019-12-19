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
final class Version20190830170921 extends AbstractMigration
{
    /**
     * Down schema.
     *
     * @param Schema $schema the schema
     *
     * @throws DBALException on database error
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("UPDATE te_programmation set cylinder_capacity = '0.00000'");
        $this->addSql('ALTER TABLE te_programmation CHANGE cylinder_capacity cylinder_capacity NUMERIC(7, 5) NOT NULL');
    }

    /**
     * Description getter.
     */
    public function getDescription(): string
    {
        return 'Alter cylinder_capacity.';
    }

    /**
     * Up schema.
     *
     * @param Schema $schema the schema
     *
     * @throws DBALException on database error
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE te_programmation CHANGE cylinder_capacity cylinder_capacity VARCHAR(16) NOT NULL');
    }
}
