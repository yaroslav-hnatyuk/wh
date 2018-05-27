<?php

namespace Bisaga\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20180526170352 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $company = $this->createCompany($schema);
        $office = $this->createOffice($schema);
        $office->addForeignKeyConstraint(
            $company, array('company_id'), array('id'), 
            array('onUpdate' => 'CASCADE', 'onDelete' => 'CASCADE')
        );

        $user = $this->createUser($schema);
        $user->addForeignKeyConstraint(
            $office, array('office_id'), array('id'), 
            array('onUpdate'=>'CASCADE', 'onDelete' => 'CASCADE')
        );

        $dishGroup = $this->createDishGroup($schema);
        $dish = $this->createDish($schema);
        $dish->addForeignKeyConstraint(
            $dishGroup, array('dish_group_id'), array('id'),
            array('onUpdate'=>'CASCADE', 'onDelete' => 'CASCADE')
        );

        $menuDish = $this->createMenuDish($schema);
        $menuDish->addForeignKeyConstraint(
            $dish, array('dish_id'), array('id'),
            array('onUpdate'=>'CASCADE', 'onDelete' => 'CASCADE')
        );

        $order = $this->createOrder($schema);
        $order->addForeignKeyConstraint(
            $user, array('user_id'), array('id'),
            array('onUpdate'=>'CASCADE', 'onDelete' => 'CASCADE')
        );
        $order->addForeignKeyConstraint(
            $menuDish, array('menu_dish_id'), array('id'),
            array('onUpdate'=>'CASCADE', 'onDelete' => 'CASCADE')
        );

        $rating = $this->createRating($schema);
        $rating->addForeignKeyConstraint(
            $user, array('user_id'), array('id'),
            array('onUpdate'=>'CASCADE', 'onDelete' => 'CASCADE')
        );
        $rating->addForeignKeyConstraint(
            $dish, array('dish_id'), array('id'),
            array('onUpdate'=>'CASCADE', 'onDelete' => 'CASCADE')
        );

        $feedback = $this->createFeedback($schema);
        $feedback->addForeignKeyConstraint(
            $user, array('user_id'), array('id'),
            array('onUpdate'=>'CASCADE', 'onDelete' => 'CASCADE')
        );
        $feedback->addForeignKeyConstraint(
            $dish, array('dish_id'), array('id'),
            array('onUpdate'=>'CASCADE', 'onDelete' => 'CASCADE')
        );
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('feedback');
        $schema->dropTable('rating');
        $schema->dropTable('order');
        $schema->dropTable('menu_dish');
        $schema->dropTable('dish');
        $schema->dropTable('dish_group');
        $schema->dropTable('user');
        $schema->dropTable('office');
        $schema->dropTable('company');
    }

    private function createCompany(Schema $schema) 
    {
        $company = $schema->createTable('company');
        $company->addColumn('id', 'integer', ['unsigned' => true, 'autoincrement' => true]);
        $company->addColumn('name', 'string', ['length' => 255]);
        $company->setPrimaryKey(['id']);

        return $company;
    }

    private function createOffice(Schema $schema) 
    {
        $office = $schema->createTable('office');
        $office->addColumn('id', 'integer', ['unsigned' => true, 'autoincrement' => true]);
        $office->addColumn('address', 'string', ['length' => 1000]);
        $office->addColumn('company_id', 'integer', ['unsigned'=>true]);
        $office->setPrimaryKey(['id']);

        return $office;
    }

    private function createUser(Schema $schema)
    {
        $user = $schema->createTable('user');
        $user->addColumn('id', 'integer', ['unsigned' => true, 'autoincrement' => true]);
        $user->addColumn('email', 'string', ['length' => 255]);
        $user->addColumn('role', 'string', ['length' => 45]);
        $user->addColumn('first_name', 'string', ['length' => 45]);
        $user->addColumn('last_name', 'string', ['length' => 45]);
        $user->addColumn('phone', 'string', ['length' => 45]);
        $user->addColumn('office_id', 'integer', ['unsigned'=>true, 'notnull' => false]);
        $user->setPrimaryKey(['id']);

        return $user;
    }

    private function createDishGroup(Schema $schema)
    {
        $dishGroup = $schema->createTable('dish_group');
        $dishGroup->addColumn('id', 'integer', ['unsigned' => true, 'autoincrement' => true]);
        $dishGroup->addColumn('name', 'string', ['length' => 255]);
        $dishGroup->setPrimaryKey(['id']);

        return $dishGroup;
    }

    private function createDish(Schema $schema)
    {
        $dish = $schema->createTable('dish');
        $dish->addColumn('id', 'integer', ['unsigned' => true, 'autoincrement' => true]);
        $dish->addColumn('name', 'string', ['length' => 500]);
        $dish->addColumn('description', 'string', ['length' => 2000]);
        $dish->addColumn('ingredients', 'string', ['length' => 2000]);
        $dish->addColumn('weight', 'decimal', ['precision' => 8, 'scale' => 2]);
        $dish->addColumn('price', 'decimal', ['precision' => 8, 'scale' => 2]);
        $dish->addColumn('dish_group_id', 'integer', ['unsigned' => true]);
        $dish->setPrimaryKey(['id']);  

        return $dish;
    }

    private function createMenuDish(Schema $schema)
    {
        $menuDish = $schema->createTable('menu_dish');
        $menuDish->addColumn('id', 'integer', ['unsigned' => true, 'autoincrement' => true]);
        $menuDish->addColumn('start', 'date', ['notnull' => true]);
        $menuDish->addColumn('end', 'date', ['notnull' => true]);
        $menuDish->addColumn('dish_id', 'integer', ['unsigned' => true]);
        $menuDish->setPrimaryKey(['id']);

        return $menuDish;
    }

    private function createOrder(Schema $schema)
    {
        $order = $schema->createTable('order');
        $order->addColumn('id', 'integer', ['unsigned' => true, 'autoincrement' => true]);
        $order->addColumn('day', 'date', ['notnull' => true]);
        $order->addColumn('count', 'integer', ['unsigned' => true]);
        $order->addColumn('menu_dish_id', 'integer', ['unsigned' => true]);
        $order->addColumn('user_id', 'integer', ['unsigned' => true]);
        $order->setPrimaryKey(['id']);

        return $order;
    }

    private function createRating(Schema $schema)
    {
        $rating = $schema->createTable('rating');
        $rating->addColumn('id', 'integer', ['unsigned' => true, 'autoincrement' => true]);
        $rating->addColumn('mark', 'integer', ['unsigned' => true]);
        $rating->addColumn('dish_id', 'integer', ['unsigned' => true]);
        $rating->addColumn('user_id', 'integer', ['unsigned' => true]);
        $rating->setPrimaryKey(['id']);

        return $rating;
    }

    private function createFeedback(Schema $schema)
    {
        $feedback = $schema->createTable('feedback');
        $feedback->addColumn('id', 'integer', ['unsigned' => true, 'autoincrement' => true]);
        $feedback->addColumn('text', 'string', ['length' => 3000]);
        $feedback->addColumn('created', 'datetime', ['notnull' => true]);
        $feedback->addColumn('dish_id', 'integer', ['unsigned' => true]);
        $feedback->addColumn('user_id', 'integer', ['unsigned' => true]);
        $feedback->setPrimaryKey(['id']);

        return $feedback;
    }
}
