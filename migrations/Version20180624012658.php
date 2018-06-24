<?php

namespace Bisaga\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180624012658 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("
            CREATE TRIGGER `dish_BEFORE_INSERT` BEFORE INSERT ON `dish` FOR EACH ROW
            BEGIN
                
                DECLARE nameLength INT UNSIGNED;
                DECLARE descriptionLength INT UNSIGNED;
                DECLARE ingredientsLength INT UNSIGNED;
                
                SET nameLength = (SELECT LENGTH(NEW.name));
                IF (nameLength = 0) THEN
                    SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'Dish name should not be empty.';
                END IF;
                
                SET descriptionLength = (SELECT LENGTH(NEW.description));
                IF (descriptionLength = 0) THEN
                    SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'Dish description should not be empty.';
                END IF;
                
                SET ingredientsLength = (SELECT LENGTH(NEW.ingredients));
                IF (ingredientsLength = 0) THEN
                    SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'Dish ingredients should not be empty.';
                END IF;
                
                IF (NEW.weight <= 0) THEN
                    SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'Weight should be > 0.';
                END IF;
                
                IF (NEW.price <= 0) THEN
                    SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'Price should be > 0.';
                END IF;
                
                IF (NEW.calories <= 0) THEN
                    SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'Calories should be > 0.';
                END IF;
                
            END
        ");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql("DROP TRIGGER IF EXISTS `dish_BEFORE_INSERT`");
    }
}
