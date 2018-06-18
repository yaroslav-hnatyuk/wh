<?php

namespace Bisaga\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20180618172431 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE dish MODIFY `weight` INT(10) UNSIGNED");
        $this->addSql("ALTER TABLE dish MODIFY `price` INT(10) UNSIGNED");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema){}
}
