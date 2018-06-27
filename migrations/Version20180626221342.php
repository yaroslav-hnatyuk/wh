<?php

namespace Bisaga\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180626221342 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $schema->dropTable('discount');
        $settings = $schema->createTable('settings');
        $settings->addColumn('id', 'integer', ['unsigned' => true, 'autoincrement' => true]);
        $settings->addColumn('name', 'string', ['length' => 255]);
        $settings->addColumn('value', 'string', ['length' => 255]);
        $settings->setPrimaryKey(['id']);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('settings');
    }
}
