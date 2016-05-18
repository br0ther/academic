<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160518032145 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, author_id INT DEFAULT NULL, issue_id INT NOT NULL, `label` LONGTEXT NOT NULL, created DATETIME NOT NULL, updated DATETIME NOT NULL, INDEX IDX_9474526CF675F31B (author_id), INDEX IDX_9474526C5E7AA58C (issue_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE issue (id INT AUTO_INCREMENT NOT NULL, reporter_id INT DEFAULT NULL, assignee_id INT DEFAULT NULL, parent_id INT DEFAULT NULL, project_id INT DEFAULT NULL, summary VARCHAR(255) NOT NULL, code VARCHAR(10) NOT NULL, description LONGTEXT DEFAULT NULL, type ENUM(\'Bug\', \'Subtask\', \'Task\', \'Story\') NOT NULL COMMENT \'(DC2Type:issue_type)\', priority ENUM(\'Major\', \'Blocker\', \'Critical\', \'Minor\', \'Trivial\') NOT NULL COMMENT \'(DC2Type:priority_type)\', status ENUM(\'Open\', \'In progress\', \'Resolved\', \'Closed\') NOT NULL COMMENT \'(DC2Type:status_type)\', resolution ENUM(\'fixed\', \'wont_fix\', \'done\') NOT NULL COMMENT \'(DC2Type:resolution_type)\', created DATETIME NOT NULL, updated DATETIME NOT NULL, UNIQUE INDEX UNIQ_12AD233ECE286663 (summary), UNIQUE INDEX UNIQ_12AD233E77153098 (code), INDEX IDX_12AD233EE1CFE6F5 (reporter_id), INDEX IDX_12AD233E59EC7D60 (assignee_id), INDEX IDX_12AD233E727ACA70 (parent_id), INDEX IDX_12AD233E166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE issue_collaborators (issue_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_93B721895E7AA58C (issue_id), INDEX IDX_93B72189A76ED395 (user_id), PRIMARY KEY(issue_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE issue_activity (id INT AUTO_INCREMENT NOT NULL, issue_id INT DEFAULT NULL, comment_id INT DEFAULT NULL, user_id INT DEFAULT NULL, type ENUM(\'issue_added\', \'issue_status_changed\', \'comment_added\') NOT NULL COMMENT \'(DC2Type:activity_type)\', body LONGTEXT DEFAULT NULL, INDEX IDX_7BDC23F15E7AA58C (issue_id), INDEX IDX_7BDC23F1F8697D13 (comment_id), INDEX IDX_7BDC23F1A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project (id INT AUTO_INCREMENT NOT NULL, `label` VARCHAR(255) NOT NULL, code VARCHAR(10) NOT NULL, summary LONGTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_2FB3D0EEEA750E8 (`label`), UNIQUE INDEX UNIQ_2FB3D0EE77153098 (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project_users (project_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_7D6AC77166D1F9C (project_id), INDEX IDX_7D6AC77A76ED395 (user_id), PRIMARY KEY(project_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CF675F31B FOREIGN KEY (author_id) REFERENCES `user` (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C5E7AA58C FOREIGN KEY (issue_id) REFERENCES issue (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE issue ADD CONSTRAINT FK_12AD233EE1CFE6F5 FOREIGN KEY (reporter_id) REFERENCES `user` (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE issue ADD CONSTRAINT FK_12AD233E59EC7D60 FOREIGN KEY (assignee_id) REFERENCES `user` (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE issue ADD CONSTRAINT FK_12AD233E727ACA70 FOREIGN KEY (parent_id) REFERENCES issue (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE issue ADD CONSTRAINT FK_12AD233E166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE issue_collaborators ADD CONSTRAINT FK_93B721895E7AA58C FOREIGN KEY (issue_id) REFERENCES issue (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE issue_collaborators ADD CONSTRAINT FK_93B72189A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE issue_activity ADD CONSTRAINT FK_7BDC23F15E7AA58C FOREIGN KEY (issue_id) REFERENCES issue (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE issue_activity ADD CONSTRAINT FK_7BDC23F1F8697D13 FOREIGN KEY (comment_id) REFERENCES comment (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE issue_activity ADD CONSTRAINT FK_7BDC23F1A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE project_users ADD CONSTRAINT FK_7D6AC77166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE project_users ADD CONSTRAINT FK_7D6AC77A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE issue_activity DROP FOREIGN KEY FK_7BDC23F1F8697D13');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C5E7AA58C');
        $this->addSql('ALTER TABLE issue DROP FOREIGN KEY FK_12AD233E727ACA70');
        $this->addSql('ALTER TABLE issue_collaborators DROP FOREIGN KEY FK_93B721895E7AA58C');
        $this->addSql('ALTER TABLE issue_activity DROP FOREIGN KEY FK_7BDC23F15E7AA58C');
        $this->addSql('ALTER TABLE issue DROP FOREIGN KEY FK_12AD233E166D1F9C');
        $this->addSql('ALTER TABLE project_users DROP FOREIGN KEY FK_7D6AC77166D1F9C');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE issue');
        $this->addSql('DROP TABLE issue_collaborators');
        $this->addSql('DROP TABLE issue_activity');
        $this->addSql('DROP TABLE project');
        $this->addSql('DROP TABLE project_users');
    }
}
