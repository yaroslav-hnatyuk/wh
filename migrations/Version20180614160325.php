<?php

namespace Bisaga\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20180614160325 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $office = $schema->getTable('office');
        $office->addColumn('uid', 'string', ['length' => 1000]);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $office = $schema->getTable('office');
        $office->dropColumn('uid');
    }
}
