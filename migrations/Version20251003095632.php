<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251003095632 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE chat (id SERIAL NOT NULL, user_id INT NOT NULL, message TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_659DF2AAA76ED395 ON chat (user_id)');
        $this->addSql('CREATE TABLE pokemon (id SERIAL NOT NULL, name VARCHAR(50) NOT NULL, api_id VARCHAR(50) NOT NULL, sprite_front VARCHAR(255) DEFAULT NULL, types JSON DEFAULT NULL, base_experience INT DEFAULT NULL, height INT DEFAULT NULL, weight INT DEFAULT NULL, hp INT DEFAULT NULL, attack INT DEFAULT NULL, defense INT DEFAULT NULL, special_attack INT DEFAULT NULL, special_defense INT DEFAULT NULL, speed INT DEFAULT NULL, status VARCHAR(20) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_62DC90F354963938 ON pokemon (api_id)');
        $this->addSql('CREATE TABLE team_vilain (id SERIAL NOT NULL, name VARCHAR(50) NOT NULL, region VARCHAR(100) DEFAULT NULL, credo VARCHAR(255) NOT NULL, color_code VARCHAR(7) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A85068875E237E06 ON team_vilain (name)');
        $this->addSql('CREATE TABLE "user" (id SERIAL NOT NULL, team_vilain_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, codename VARCHAR(20) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, uuid VARCHAR(36) NOT NULL, is_verified BOOLEAN NOT NULL, starter_pokemon VARCHAR(50) DEFAULT NULL, starter_pokemon_id INT DEFAULT NULL, has_done_first_theft BOOLEAN DEFAULT false NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649FB685056 ON "user" (codename)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649D17F50A6 ON "user" (uuid)');
        $this->addSql('CREATE INDEX IDX_8D93D649447478C6 ON "user" (team_vilain_id)');
        $this->addSql('ALTER TABLE chat ADD CONSTRAINT FK_659DF2AAA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT FK_8D93D649447478C6 FOREIGN KEY (team_vilain_id) REFERENCES team_vilain (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE chat DROP CONSTRAINT FK_659DF2AAA76ED395');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT FK_8D93D649447478C6');
        $this->addSql('DROP TABLE chat');
        $this->addSql('DROP TABLE pokemon');
        $this->addSql('DROP TABLE team_vilain');
        $this->addSql('DROP TABLE "user"');
    }
}
