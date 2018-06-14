<?php

namespace Bisaga\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20180614015121 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $user = $schema->getTable('user');
        $user->addColumn('ipn', 'string', ['length' => 255, 'nullable' => true]);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $user = $schema->getTable('user');
        $user->dropColumn('ipn');
    }
}
