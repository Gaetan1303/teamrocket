<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250929144650 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__team_vilain AS SELECT id, name, region, credo, color_code FROM team_vilain');
        $this->addSql('DROP TABLE team_vilain');
        $this->addSql('CREATE TABLE team_vilain (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(50) NOT NULL, region VARCHAR(100) DEFAULT NULL, credo VARCHAR(255) NOT NULL, color_code VARCHAR(7) DEFAULT NULL)');
        $this->addSql('INSERT INTO team_vilain (id, name, region, credo, color_code) SELECT id, name, region, credo, color_code FROM __temp__team_vilain');
        $this->addSql('DROP TABLE __temp__team_vilain');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A85068875E237E06 ON team_vilain (name)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE team_vilain ADD COLUMN logo VARCHAR(255) DEFAULT NULL');
    }
}
