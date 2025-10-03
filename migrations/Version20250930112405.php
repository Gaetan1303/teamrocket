<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250930112405 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE pokemon (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(50) NOT NULL, api_id VARCHAR(50) NOT NULL, sprite_front VARCHAR(255) DEFAULT NULL, types CLOB DEFAULT NULL --(DC2Type:json)
        , base_experience INTEGER DEFAULT NULL, height INTEGER DEFAULT NULL, weight INTEGER DEFAULT NULL, hp INTEGER DEFAULT NULL, attack INTEGER DEFAULT NULL, defense INTEGER DEFAULT NULL, special_attack INTEGER DEFAULT NULL, special_defense INTEGER DEFAULT NULL, speed INTEGER DEFAULT NULL, status VARCHAR(20) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_62DC90F354963938 ON pokemon (api_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE pokemon');
    }
}
