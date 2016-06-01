<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160601070048 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE issue_activity CHANGE type type ENUM(\'issue_added\', \'issue_changed\', \'comment_added\', \'comment_changed\') NOT NULL COMMENT \'(DC2Type:activity_type)\'');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE issue_activity CHANGE type type ENUM(\'Fixed\', \'Wont fix\', \'Done\') DEFAULT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:resolution_type)\'');
    }
}
