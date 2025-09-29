<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250929084648 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE sbire (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, team_vilain_id INTEGER DEFAULT NULL, email VARCHAR(180) NOT NULL, codename VARCHAR(20) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) NOT NULL, uuid VARCHAR(36) NOT NULL, CONSTRAINT FK_8EA82C86447478C6 FOREIGN KEY (team_vilain_id) REFERENCES team_vilain (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8EA82C86E7927C74 ON sbire (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8EA82C86FB685056 ON sbire (codename)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8EA82C86D17F50A6 ON sbire (uuid)');
        $this->addSql('CREATE INDEX IDX_8EA82C86447478C6 ON sbire (team_vilain_id)');
        $this->addSql('CREATE TABLE team_vilain (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(50) NOT NULL, region VARCHAR(100) DEFAULT NULL, credo VARCHAR(255) NOT NULL, color_code VARCHAR(7) DEFAULT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A85068875E237E06 ON team_vilain (name)');
        $this->addSql('CREATE TABLE messenger_messages (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, body CLOB NOT NULL, headers CLOB NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , available_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , delivered_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        )');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE sbire');
        $this->addSql('DROP TABLE team_vilain');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
