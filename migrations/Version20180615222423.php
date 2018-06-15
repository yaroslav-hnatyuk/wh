<?php

namespace Bisaga\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180615222423 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $dish_group = $schema->getTable('dish_group');
        $dish_group->addColumn('is_lunch', 'boolean');

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $dish_group = $schema->getTable('dish_group');
        $dish_group->dropColumn('is_lunch');
    }
}
