<?php

namespace Bisaga\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20180619000654 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $user = $schema->getTable('user');
        $user->addColumn('is_active', 'boolean', ['default' => true]);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $user = $schema->getTable('user');
        $user->dropColumn('is_active');
    }
}
