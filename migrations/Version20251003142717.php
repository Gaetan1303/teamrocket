<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251003142717 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE chat_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE user_id_seq CASCADE');
        $this->addSql('CREATE TABLE app_user (id SERIAL NOT NULL, team_vilain_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, codename VARCHAR(20) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, uuid VARCHAR(36) NOT NULL, is_verified BOOLEAN NOT NULL, starter_pokemon VARCHAR(50) DEFAULT NULL, starter_pokemon_id INT DEFAULT NULL, has_done_first_theft BOOLEAN DEFAULT false NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_88BDF3E9E7927C74 ON app_user (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_88BDF3E9FB685056 ON app_user (codename)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_88BDF3E9D17F50A6 ON app_user (uuid)');
        $this->addSql('CREATE INDEX IDX_88BDF3E9447478C6 ON app_user (team_vilain_id)');
        $this->addSql('ALTER TABLE app_user ADD CONSTRAINT FK_88BDF3E9447478C6 FOREIGN KEY (team_vilain_id) REFERENCES team_vilain (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE chat DROP CONSTRAINT fk_659df2aaa76ed395');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT fk_8d93d649447478c6');
        $this->addSql('DROP TABLE chat');
        $this->addSql('DROP TABLE "user"');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE chat_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE user_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE chat (id SERIAL NOT NULL, user_id INT NOT NULL, message TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_659df2aaa76ed395 ON chat (user_id)');
        $this->addSql('CREATE TABLE "user" (id SERIAL NOT NULL, team_vilain_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, codename VARCHAR(20) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, uuid VARCHAR(36) NOT NULL, is_verified BOOLEAN NOT NULL, starter_pokemon VARCHAR(50) DEFAULT NULL, starter_pokemon_id INT DEFAULT NULL, has_done_first_theft BOOLEAN DEFAULT false NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_8d93d649447478c6 ON "user" (team_vilain_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_8d93d649d17f50a6 ON "user" (uuid)');
        $this->addSql('CREATE UNIQUE INDEX uniq_8d93d649e7927c74 ON "user" (email)');
        $this->addSql('CREATE UNIQUE INDEX uniq_8d93d649fb685056 ON "user" (codename)');
        $this->addSql('ALTER TABLE chat ADD CONSTRAINT fk_659df2aaa76ed395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT fk_8d93d649447478c6 FOREIGN KEY (team_vilain_id) REFERENCES team_vilain (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE app_user DROP CONSTRAINT FK_88BDF3E9447478C6');
        $this->addSql('DROP TABLE app_user');
    }
}
