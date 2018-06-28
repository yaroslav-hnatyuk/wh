<?php

namespace Bisaga\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180627230822 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $feedback = $schema->createTable('reminder');
        $feedback->addColumn('id', 'integer', ['unsigned' => true, 'autoincrement' => true]);
        $feedback->addColumn('text', 'string', ['length' => 5000]);
        $feedback->addColumn('created', 'datetime', ['notnull' => true]);
        $feedback->setPrimaryKey(['id']);

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('reminder');
    }
}
