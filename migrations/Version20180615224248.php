<?php

namespace Bisaga\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20180615224248 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("INSERT INTO dish_group (`name`, `order`, `is_lunch`) VALUES ('Супи', 1, 1)");
        $this->addSql("INSERT INTO dish_group (`name`, `order`, `is_lunch`) VALUES ('Салати', 2, 1)");
        $this->addSql("INSERT INTO dish_group (`name`, `order`, `is_lunch`) VALUES ('Основні страви', 3, 1)");
        $this->addSql("INSERT INTO dish_group (`name`, `order`, `is_lunch`) VALUES ('Десерти', 4, 1)");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql("DELETE FROM dish_group WHERE `name`='Супи'");
        $this->addSql("DELETE FROM dish_group WHERE `name`='Салати'");
        $this->addSql("DELETE FROM dish_group WHERE `name`='Основні страви'");
        $this->addSql("DELETE FROM dish_group WHERE `name`='Десерти'");
    }
}
