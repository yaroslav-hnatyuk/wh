<?php

namespace Bisaga\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180624135759 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("
            CREATE TRIGGER `user_BEFORE_INSERT` BEFORE INSERT ON `user` FOR EACH ROW
            BEGIN
                DECLARE emailLength INT UNSIGNED;
                DECLARE passLength INT UNSIGNED;
                DECLARE saltlLength INT UNSIGNED;
                DECLARE firstNamelLength INT UNSIGNED;
                DECLARE lastNamelLength INT UNSIGNED;

                SET emailLength = (SELECT LENGTH(NEW.email));
                SET passLength = (SELECT LENGTH(NEW.pass));
                SET saltlLength = (SELECT LENGTH(NEW.salt));
                SET firstNamelLength = (SELECT LENGTH(NEW.first_name));
                SET lastNamelLength = (SELECT LENGTH(NEW.last_name));


                IF (emailLength < 3) THEN
                    SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'User email should contain at least 4 letters.';
                END IF;

                IF (NEW.role != 'user' AND NEW.role != 'manager' AND NEW.role != 'admin') THEN
                    SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'User role is not correct.';
                END IF;

                IF (passLength = 0) THEN
                    SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'Password should not be empty.';
                END IF;
                
                IF (saltlLength = 0) THEN
                    SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'Salt should not be empty.';
                END IF;

                IF (firstNamelLength = 0) THEN
                    SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'First name should not be empty.';
                END IF;

                IF (lastNamelLength = 0) THEN
                    SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'Last name should not be empty.';
                END IF;

            END;
        ");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql("DROP TRIGGER IF EXISTS `user_BEFORE_INSERT`;");
    }
}
