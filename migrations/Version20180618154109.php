<?php

namespace Bisaga\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20180618154109 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE `rating` ADD CONSTRAINT uk_rating_user_dish UNIQUE (`user_id`, `dish_id`)");
        $this->addSql("ALTER TABLE `order` ADD CONSTRAINT uk_order_user_menu_day UNIQUE (`user_id`, `menu_dish_id`, `day`)");
        $this->addSql("ALTER TABLE `office` ADD CONSTRAINT uk_office_company_addr UNIQUE (`company_id`, `address`)");
        $this->addSql("ALTER TABLE `menu_dish` ADD CONSTRAINT uk_menu_dish_start_end UNIQUE (`dish_id`, `start`, `end`)");
        $this->addSql("ALTER TABLE `feedback` ADD CONSTRAINT uk_feedback_user_dish_created UNIQUE (`user_id`, `dish_id`, `created`)");
        $this->addSql("ALTER TABLE `dish_group` ADD CONSTRAINT uk_dish_group_name UNIQUE (`name`)");
        $this->addSql("ALTER TABLE `discount` ADD CONSTRAINT uk_discount_type UNIQUE (`type`)");
        $this->addSql("ALTER TABLE `company` ADD CONSTRAINT uk_company_name UNIQUE (`name`)");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql("ALTER TABLE `rating` DROP KEY uk_rating_user_dish");
        $this->addSql("ALTER TABLE `order` DROP KEY uk_order_user_menu_day");
        $this->addSql("ALTER TABLE `office` DROP KEY uk_office_company_addr");
        $this->addSql("ALTER TABLE `menu_dish` DROP KEY uk_menu_dish_start_end");
        $this->addSql("ALTER TABLE `feedback` DROP KEY uk_feedback_user_dish_created");
        $this->addSql("ALTER TABLE `dish_group` DROP KEY uk_dish_group_name");
        $this->addSql("ALTER TABLE `discount` DROP KEY uk_discount_type");
        $this->addSql("ALTER TABLE `company` DROP KEY uk_company_name");
    }
}
