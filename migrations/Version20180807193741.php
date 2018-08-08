<?php

namespace Bisaga\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180807193741 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $orderGroup = $schema->createTable('order_group');
        $orderGroup->addColumn('id', 'integer', ['unsigned' => true, 'autoincrement' => true]);
        $orderGroup->addColumn('day', 'date', ['notnull' => true]);
        $orderGroup->addColumn('count', 'integer', ['unsigned' => true]);
        $orderGroup->addColumn('group_id', 'integer', ['unsigned' => true]);
        $orderGroup->addColumn('user_id', 'integer', ['unsigned' => true]);
        $orderGroup->setPrimaryKey(['id']);

        $user = $schema->getTable('user');
        $orderGroup->addForeignKeyConstraint(
            $user, array('user_id'), array('id'),
            array('onUpdate'=>'CASCADE', 'onDelete' => 'CASCADE')
        );

        $group = $schema->getTable('dish_group');
        $orderGroup->addForeignKeyConstraint(
            $group, array('group_id'), array('id'),
            array('onUpdate'=>'CASCADE', 'onDelete' => 'RESTRICT')
        );
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('order_group');
    }
}
