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
final class Version20191208165256 extends AbstractMigration
{
    /**
     * Downgrade Database.
     *
     * @param Schema $schema schema is not used
     *
     * @throws DBALException when an error occurred
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        //$this->addSql('UPDATE te_programmation SET original_file_id= (SELECT min(id) FROM te_file) WHERE original_file_id is null');
        $this->addSql('ALTER TABLE te_programmation CHANGE original_file_id original_file_id INT NOT NULL');
    }

    /**
     * Description getter.
     */
    public function getDescription(): string
    {
        return 'Original file can be deleted';
    }

    /**
     * Upgrade Database.
     *
     * @param Schema $schema schema is not used
     *
     * @throws DBALException when an error occurred
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE te_programmation CHANGE original_file_id original_file_id INT DEFAULT NULL');
    }
}
