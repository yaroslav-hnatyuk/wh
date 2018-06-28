<?php

namespace Bisaga\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180628003257 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $user = $schema->getTable('user');
        $user->addColumn('reminders', 'integer', ['unsigned' => true, 'default' => 0]);
        $user->addColumn('feedback_count', 'integer', ['unsigned' => true, 'default' => 0]);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $user = $schema->getTable('user');
        $user->dropColumn('reminders');
        $user->dropColumn('feedback_count');
    }
}
