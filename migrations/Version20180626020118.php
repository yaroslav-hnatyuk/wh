<?php

namespace Bisaga\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180626020118 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql(
            "INSERT INTO `user` (`email`, `role`, `first_name`, `last_name`, `phone`, `is_feedback_active`, `pass`, `salt`, `is_active`) 
            VALUES ('sa@walnut.house', 'admin', 'ADMIN', 'ADMIN', 'n/a', '0', '$2y$10$8tnPNHTytU7peMWyXIlLAOY46Jtnj9znUDbH7WBu3zfPxXWZ261sK', '708cb89a1c06923a9b21cf18757d2e560f6aea6dd17ee314cff81dd569597f4d', '1')"
        );
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {}
}
