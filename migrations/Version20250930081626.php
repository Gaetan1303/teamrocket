<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250930081626 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sbire ADD COLUMN starter_pokemon VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE sbire ADD COLUMN starter_pokemon_id INTEGER DEFAULT NULL');
        $this->addSql('ALTER TABLE sbire ADD COLUMN has_done_first_theft BOOLEAN DEFAULT 0 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__sbire AS SELECT id, team_vilain_id, email, codename, roles, password, uuid, is_verified FROM sbire');
        $this->addSql('DROP TABLE sbire');
        $this->addSql('CREATE TABLE sbire (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, team_vilain_id INTEGER DEFAULT NULL, email VARCHAR(180) NOT NULL, codename VARCHAR(20) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) NOT NULL, uuid VARCHAR(36) NOT NULL, is_verified BOOLEAN NOT NULL, CONSTRAINT FK_8EA82C86447478C6 FOREIGN KEY (team_vilain_id) REFERENCES team_vilain (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO sbire (id, team_vilain_id, email, codename, roles, password, uuid, is_verified) SELECT id, team_vilain_id, email, codename, roles, password, uuid, is_verified FROM __temp__sbire');
        $this->addSql('DROP TABLE __temp__sbire');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8EA82C86E7927C74 ON sbire (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8EA82C86FB685056 ON sbire (codename)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8EA82C86D17F50A6 ON sbire (uuid)');
        $this->addSql('CREATE INDEX IDX_8EA82C86447478C6 ON sbire (team_vilain_id)');
    }
}
