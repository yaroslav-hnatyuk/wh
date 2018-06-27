<?php

namespace Bisaga\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20180624025846 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("
            CREATE TRIGGER `office_BEFORE_UPDATE` BEFORE UPDATE ON `office` FOR EACH ROW
            BEGIN
                DECLARE addressLength INT UNSIGNED;
                SET addressLength = (SELECT LENGTH(NEW.address));
                IF (addressLength = 0) THEN
                    SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'Office address should not be empty.';
                END IF;
            END;
        ");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql("DROP TRIGGER IF EXISTS `office_BEFORE_UPDATE`;");
    }
}
