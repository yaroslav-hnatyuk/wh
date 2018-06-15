<?php

namespace Bisaga\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20180615204542 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $discount = $schema->createTable('discount');
        $discount->addColumn('id', 'integer', ['unsigned' => true, 'autoincrement' => true]);
        $discount->addColumn('type', 'string', ['length' => 255]);
        $discount->addColumn('percent', 'integer', ['unsigned' => true]);
        $discount->setPrimaryKey(['id']);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('discount');
    }
}
