<?php

namespace Bisaga\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20180811182432 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $orderGroup = $schema->getTable('order_group');
        $orderGroup->addColumn('dessert', 'boolean', ['default' => false]);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $orderGroup = $schema->getTable('order_group');
        $orderGroup->dropColumn('dessert');
    }
}
