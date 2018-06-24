<?php

namespace Bisaga\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20180624025503 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("
            CREATE TRIGGER `company_BEFORE_UPDATE` BEFORE UPDATE ON `company` FOR EACH ROW
            BEGIN
                DECLARE nameLength INT UNSIGNED;
                SET nameLength = (SELECT LENGTH(NEW.name));
                IF (nameLength = 0) THEN
                    SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'Company name should not be empty.';
                END IF;
            END;
        ");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql("DROP TRIGGER IF EXISTS `company_BEFORE_UPDATE`;");
    }
}
