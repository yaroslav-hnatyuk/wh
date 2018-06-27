<?php

namespace Bisaga\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180626221738 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("INSERT INTO settings (`name`, `value`) VALUES ('lunch_discount', 15)");
        $this->addSql("INSERT INTO settings (`name`, `value`) VALUES ('weekly_discount', 15)");
        $this->addSql("INSERT INTO settings (`name`, `value`) VALUES ('order_hour', 11)");

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql("DELETE FROM settings WHERE `name`='lunch_discount'");
        $this->addSql("DELETE FROM settings WHERE `name`='weekly_discount'");
        $this->addSql("DELETE FROM settings WHERE `name`='order_hour'");
    }
}
