<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251010091301 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE app_user (id SERIAL NOT NULL, team_vilain_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, codename VARCHAR(20) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, uuid VARCHAR(36) NOT NULL, is_verified BOOLEAN NOT NULL, starter_pokemon VARCHAR(50) DEFAULT NULL, starter_pokemon_id INT DEFAULT NULL, has_done_first_theft BOOLEAN DEFAULT false NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_88BDF3E9E7927C74 ON app_user (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_88BDF3E9FB685056 ON app_user (codename)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_88BDF3E9D17F50A6 ON app_user (uuid)');
        $this->addSql('CREATE INDEX IDX_88BDF3E9447478C6 ON app_user (team_vilain_id)');
        $this->addSql('CREATE TABLE channel (id SERIAL NOT NULL, slug VARCHAR(50) NOT NULL, name VARCHAR(100) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A2F98E47989D9B62 ON channel (slug)');
        $this->addSql('CREATE TABLE chat (id SERIAL NOT NULL, user_id INT NOT NULL, channel_id INT NOT NULL, message TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_659DF2AAA76ED395 ON chat (user_id)');
        $this->addSql('CREATE INDEX IDX_659DF2AA72F5A1AA ON chat (channel_id)');
        $this->addSql('CREATE TABLE pokemon (id SERIAL NOT NULL, name VARCHAR(50) NOT NULL, api_id VARCHAR(50) NOT NULL, sprite_front VARCHAR(255) DEFAULT NULL, sprite_back VARCHAR(255) DEFAULT NULL, sprite_front_shiny VARCHAR(255) DEFAULT NULL, sprite_back_shiny VARCHAR(255) DEFAULT NULL, official_artwork VARCHAR(255) DEFAULT NULL, types JSON DEFAULT NULL, base_experience INT DEFAULT NULL, height INT DEFAULT NULL, weight INT DEFAULT NULL, hp INT DEFAULT NULL, attack INT DEFAULT NULL, defense INT DEFAULT NULL, special_attack INT DEFAULT NULL, special_defense INT DEFAULT NULL, speed INT DEFAULT NULL, status VARCHAR(20) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_62DC90F354963938 ON pokemon (api_id)');
        $this->addSql('CREATE TABLE sbire (id SERIAL NOT NULL, user_id INT NOT NULL, picture VARCHAR(100) NOT NULL, color VARCHAR(7) NOT NULL, accessory VARCHAR(20) NOT NULL, power SMALLINT NOT NULL, defense SMALLINT NOT NULL, speed SMALLINT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8EA82C86A76ED395 ON sbire (user_id)');
        $this->addSql('CREATE TABLE team_vilain (id SERIAL NOT NULL, name VARCHAR(50) NOT NULL, region VARCHAR(100) DEFAULT NULL, credo VARCHAR(255) NOT NULL, color_code VARCHAR(7) DEFAULT NULL, code VARCHAR(20) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A85068875E237E06 ON team_vilain (name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A850688777153098 ON team_vilain (code)');
        $this->addSql('ALTER TABLE app_user ADD CONSTRAINT FK_88BDF3E9447478C6 FOREIGN KEY (team_vilain_id) REFERENCES team_vilain (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE chat ADD CONSTRAINT FK_659DF2AAA76ED395 FOREIGN KEY (user_id) REFERENCES app_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE chat ADD CONSTRAINT FK_659DF2AA72F5A1AA FOREIGN KEY (channel_id) REFERENCES channel (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE sbire ADD CONSTRAINT FK_8EA82C86A76ED395 FOREIGN KEY (user_id) REFERENCES app_user (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE app_user DROP CONSTRAINT FK_88BDF3E9447478C6');
        $this->addSql('ALTER TABLE chat DROP CONSTRAINT FK_659DF2AAA76ED395');
        $this->addSql('ALTER TABLE chat DROP CONSTRAINT FK_659DF2AA72F5A1AA');
        $this->addSql('ALTER TABLE sbire DROP CONSTRAINT FK_8EA82C86A76ED395');
        $this->addSql('DROP TABLE app_user');
        $this->addSql('DROP TABLE channel');
        $this->addSql('DROP TABLE chat');
        $this->addSql('DROP TABLE pokemon');
        $this->addSql('DROP TABLE sbire');
        $this->addSql('DROP TABLE team_vilain');
    }
}
