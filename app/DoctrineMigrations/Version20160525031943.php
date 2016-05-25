<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160525031943 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE issue_activity ADD changed_field VARCHAR(255) DEFAULT NULL, ADD old_value VARCHAR(255) DEFAULT NULL, ADD new_value VARCHAR(255) DEFAULT NULL, DROP body');
        $this->addSql('ALTER TABLE issue_activity CHANGE type type ENUM(\'issue_added\',\'issue_changed\',\'comment_added\') DEFAULT NULL COMMENT \'(DC2Type:resolution_type)\'');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE issue_activity ADD body LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, DROP changed_field, DROP old_value, DROP new_value');
    }
}
