<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251014075310 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sbire ADD has_done_first_theft BOOLEAN DEFAULT false NOT NULL');
        $this->addSql('ALTER TABLE sbire ADD starter_pokemon_id INT DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8EA82C8665FF0F22 ON sbire (starter_pokemon_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX UNIQ_8EA82C8665FF0F22');
        $this->addSql('ALTER TABLE sbire DROP has_done_first_theft');
        $this->addSql('ALTER TABLE sbire DROP starter_pokemon_id');
    }
}
