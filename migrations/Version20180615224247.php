<?php

namespace Bisaga\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20180615224247 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $dish_group = $schema->getTable('dish_group');
        $dish_group->addColumn('`order`', 'integer', ['unsigned' => true]);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $dish_group = $schema->getTable('dish_group');
        $dish_group->dropColumn('`order`');
    }
}
