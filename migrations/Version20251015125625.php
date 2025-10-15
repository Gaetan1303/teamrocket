<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251015125625 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX uniq_8ea82c8665ff0f22');
        $this->addSql('ALTER TABLE sbire ADD CONSTRAINT FK_8EA82C8665FF0F22 FOREIGN KEY (starter_pokemon_id) REFERENCES pokemon (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_8EA82C8665FF0F22 ON sbire (starter_pokemon_id)');
        $this->addSql('ALTER TABLE team_vilain ADD logo VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE sbire DROP CONSTRAINT FK_8EA82C8665FF0F22');
        $this->addSql('DROP INDEX IDX_8EA82C8665FF0F22');
        $this->addSql('CREATE UNIQUE INDEX uniq_8ea82c8665ff0f22 ON sbire (starter_pokemon_id)');
        $this->addSql('ALTER TABLE team_vilain DROP logo');
    }
}
