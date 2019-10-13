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
final class Version20191012111553 extends AbstractMigration
{
    /**
     * Downgrade database.
     *
     * @param Schema $schema Schema is not use
     *
     * @throws DBALException
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840D8D9F6D38');
        $this->addSql('DROP INDEX ndx_order ON payment');
        $this->addSql('ALTER TABLE te_order ADD payment_id INT DEFAULT NULL');
        $this->addSQL('UPDATE te_order set payment_id= (SELECT payment.id as payment_id FROM payment where payment.order_id = te_order.id ORDER BY order_id DESC LIMIT 1) WHERE payment_id is NULL');
        $this->addSql('ALTER TABLE payment DROP order_id');
        $this->addSql('ALTER TABLE te_order ADD CONSTRAINT FK_5A65FDE64C3A3BB FOREIGN KEY (payment_id) REFERENCES payment (id)');
    }

    /**
     * Description getter.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return 'Change relation between order and payment';
    }

    /**
     * Upgrade database.
     *
     * @param Schema $schema Schema is not use
     *
     * @throws DBALException
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE te_order DROP FOREIGN KEY FK_5A65FDE64C3A3BB');
        $this->addSql('DROP INDEX UNIQ_5A65FDE64C3A3BB ON te_order');
        $this->addSql('ALTER TABLE payment ADD order_id INT DEFAULT NULL');
        $this->addSQL('UPDATE payment set order_id= (SELECT te_order.id as order_id FROM te_order where te_order.payment_id = payment.id ORDER BY payment_id DESC LIMIT 1) WHERE order_id is NULL');
        $this->addSql('ALTER TABLE te_order DROP payment_id');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D8D9F6D38 FOREIGN KEY (order_id) REFERENCES te_order (id)');
        $this->addSql('CREATE INDEX ndx_order ON payment (order_id)');
    }
}
