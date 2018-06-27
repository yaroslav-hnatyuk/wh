<?php

namespace Bisaga\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20180624021605 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("
            CREATE TRIGGER `dish_group_BEFORE_UPDATE` BEFORE UPDATE ON `dish_group` FOR EACH ROW
            BEGIN
                DECLARE nameLength INT UNSIGNED;
                SET nameLength = (SELECT LENGTH(NEW.name));
                IF (nameLength = 0) THEN
                    SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'Dish group name should not be empty.';
                END IF;
            END;
        ");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql("DROP TRIGGER IF EXISTS `wh`.`dish_group_BEFORE_UPDATE`;");
    }
}
