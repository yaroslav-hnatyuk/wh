<?php

namespace Bisaga\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20180624155024 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("
            CREATE TRIGGER `order_BEFORE_INSERT` BEFORE INSERT ON `order` FOR EACH ROW
            BEGIN
                DECLARE currentDate datetime;
                SET currentDate = DATE_FORMAT(NOW(),'%Y-%m-%d');
                IF (NEW.day < currentDate) THEN
                    SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'Order day in the past.';
                END IF;
            END;
        ");

        $this->addSql("
            CREATE TRIGGER `order_BEFORE_UPDATE` BEFORE UPDATE ON `order` FOR EACH ROW
            BEGIN
                DECLARE currentDate datetime;
                SET currentDate = DATE_FORMAT(NOW(),'%Y-%m-%d');
                IF (NEW.day < currentDate) THEN
                    SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'Order day in the past.';
                END IF;
            END;
        ");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql("DROP TRIGGER IF EXISTS `order_BEFORE_INSERT`;");
        $this->addSql("DROP TRIGGER IF EXISTS `order_BEFORE_UPDATE`;");
    }
}
