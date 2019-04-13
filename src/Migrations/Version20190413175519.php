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
final class Version20190413175519 extends AbstractMigration
{
    /**
     * Description getter.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return 'Manipulate article tables';
    }

    /**
     * Create article tables.
     *
     * @param Schema $schema
     *
     * @throws DBALException
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE data.tr_article (id SERIAL NOT NULL, code VARCHAR(8) NOT NULL, cost NUMERIC(6, 2) NOT NULL, credit INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX uk_article_code ON data.tr_article (code)');
        $this->addSql('COMMENT ON COLUMN data.tr_article.id IS \'Article identifier\'');
        $this->addSql('COMMENT ON COLUMN data.tr_article.code IS \'Article unique code\'');
        $this->addSql('COMMENT ON COLUMN data.tr_article.cost IS \'Article cost\'');
        $this->addSql('COMMENT ON COLUMN data.tr_article.credit IS \'Credit gained when buying article\'');
        $this->addSql('CREATE TABLE data.tj_ordered_article (article_id INT NOT NULL, order_id INT NOT NULL, quantity SMALLINT NOT NULL, unit_cost NUMERIC(6, 2) NOT NULL, PRIMARY KEY(article_id, order_id))');
        $this->addSql('CREATE INDEX ndx_ordered_article_order ON data.tj_ordered_article (order_id)');
        $this->addSql('CREATE INDEX ndx_ordered_article_full ON data.tj_ordered_article (order_id, article_id)');
        $this->addSql('CREATE INDEX ndx_ordered_article_article ON data.tj_ordered_article (article_id)');
        $this->addSql('COMMENT ON COLUMN data.tj_ordered_article.article_id IS \'Article identifier\'');
        $this->addSql('ALTER TABLE data.te_order RENAME COLUMN identifier TO id');
        $this->addSql('ALTER TABLE data.tj_ordered_article ADD CONSTRAINT FK_ORDERED_ARTICLE_ARTICLE FOREIGN KEY (article_id) REFERENCES data.tr_article (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE data.tj_ordered_article ADD CONSTRAINT FK_ORDERED_ARTICLE_ORDER FOREIGN KEY (order_id) REFERENCES data.te_order (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    /**
     * Drop article tables.
     *
     * @param Schema $schema
     *
     * @throws DBALException
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE data.tj_ordered_article DROP CONSTRAINT FK_ORDERED_ARTICLE_ARTICLE');
        $this->addSql('DROP TABLE data.tr_article');
        $this->addSql('DROP TABLE data.tj_ordered_article');
        $this->addSql('ALTER TABLE data.te_order RENAME COLUMN id TO identifier');
    }
}
