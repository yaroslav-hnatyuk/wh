<?php

namespace Bisaga\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20180614003818 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $dishesTable = $schema->getTable('dish');
        $dishesTable->addColumn('calories', 'integer', ['unsigned' => true]);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $dishesTable = $schema->getTable('dish');
        $dishesTable->dropColumn('calories');
    }
}
