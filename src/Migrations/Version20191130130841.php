<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191130130841 extends AbstractMigration
{
    /**
     * Adding gear and cat.
     *
     * @return string
     */
    public function getDescription() : string
    {
        return 'Gear and cat';
    }

    /**
     * Upgrade schema.
     *
     * @param Schema $schema
     *
     * @throws DBALException
     */
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE te_programmation ADD cat_off TINYINT(1) NOT NULL DEFAULT 0, ADD cat_stopped TINYINT(1) NOT NULL DEFAULT 0, ADD gear TINYINT(1) NOT NULL DEFAULT 0, ADD gear_done TINYINT(1) NOT NULL DEFAULT 0');
    }

    /**
     * Downgrade schema.
     *
     * @param Schema $schema the schema is not used
     *
     * @throws DBALException when an error occurred
     */
    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE te_programmation DROP cat_off, DROP cat_stopped, DROP gear, DROP gear_done');
    }
}
