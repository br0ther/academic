<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160602085548 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE timezone (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, country VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, offset INT NOT NULL, is_active TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_3701B2975E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user ADD timezone_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6493FE997DE FOREIGN KEY (timezone_id) REFERENCES timezone (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_8D93D6493FE997DE ON user (timezone_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6493FE997DE');
        $this->addSql('DROP TABLE timezone');
        $this->addSql('DROP INDEX IDX_8D93D6493FE997DE ON `user`');
        $this->addSql('ALTER TABLE `user` DROP timezone_id');
    }
}
