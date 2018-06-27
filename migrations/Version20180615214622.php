<?php

namespace Bisaga\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20180615214622 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("INSERT INTO discount (`type`, `percent`) VALUES ('lunch', 0)");
        $this->addSql("INSERT INTO discount (`type`, `percent`) VALUES ('week', 0)");
        $this->addSql("INSERT INTO discount (`type`, `percent`) VALUES ('2weeks', 0)");

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql("DELETE FROM discount WHERE `type`='lunch'");
        $this->addSql("DELETE FROM discount WHERE `type`='week'");
        $this->addSql("DELETE FROM discount WHERE `type`='2weeks'");
    }
}
