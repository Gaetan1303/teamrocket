<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251008091902 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout et population de la colonne code sur team_vilain.';
    }

    public function up(Schema $schema): void
    {
        // 1. Ajouter la colonne 'code', en la rendant temporairement NULLABLE si nécessaire.
        // On suppose ici que la base de données est PostgreSQL (vu l'erreur 23502).
        $this->addSql('ALTER TABLE team_vilain ADD code VARCHAR(20) DEFAULT NULL');

        // 2. Mettre à jour les enregistrements existants avec une valeur unique non-nulle.
        // La concaténation de la chaîne 'TEMP' et de l'id garantit l'unicité.
        $this->addSql("UPDATE team_vilain SET code = 'TEMP' || id WHERE code IS NULL");

        // 3. Rendre la colonne 'code' NOT NULL après l'avoir remplie.
        $this->addSql('ALTER TABLE team_vilain ALTER COLUMN code SET NOT NULL');

        // 4. Ajouter l\'index unique.
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A850688777153098 ON team_vilain (code)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX UNIQ_A850688777153098');
        $this->addSql('ALTER TABLE team_vilain DROP code');
    }
}