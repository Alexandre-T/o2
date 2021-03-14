<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210314110121 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Adding new parameters';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $welcomeFr = serialize('Bienvenue dans le File-Service');
        $welcomeEn = serialize('Welcome in our File-Service');
        $this->addSql("INSERT INTO `ts_settings` (`id`, `code`, `value`, `updatable`) VALUES (NULL, 'welcome-fr', '$welcomeFr', '0'); ");
        $this->addSql("INSERT INTO `ts_settings` (`id`, `code`, `value`, `updatable`) VALUES (NULL, 'welcome-en', '$welcomeEn', '0'); ");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("DELETE FROM `ts_settings` WHERE `ts_settings`.`code` = 'welcome-fr'");
        $this->addSql("DELETE FROM `ts_settings` WHERE `ts_settings`.`code` = 'welcome-en'");
    }
}
