<?php

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
     * Description getter.
     *
     * @return string
     */
    public function getDescription() : string
    {
        return 'Original file can be deleted';
    }

    /**
     * Upgrade Database.
     *
     * @param Schema $schema
     *
     * @throws DBALException when an error occurred
     */
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE te_programmation CHANGE original_file_id original_file_id INT DEFAULT NULL');
    }

    /**
     * Downgrade Database.
     *
     * @param Schema $schema
     *
     * @throws DBALException when an error occurred
     */
    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        //$this->addSql('UPDATE te_programmation SET original_file_id= (SELECT min(id) FROM te_file) WHERE original_file_id is null');
        $this->addSql('ALTER TABLE te_programmation CHANGE original_file_id original_file_id INT NOT NULL');
    }
}
