<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240714110214 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        if ($schema->getTable('trip')->hasColumn('route')) {
            // If the route column exists, drop it first
            $this->addSql('ALTER TABLE trip DROP COLUMN route');
        }
        
        $this->addSql('ALTER TABLE trip ADD COLUMN route VARCHAR(300) NOT NULL DEFAULT \'tunisia-sousse-sahloul|french-Nice|marseille, toulon, cannes\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE trip ALTER route DROP NOT NULL');
    }
}
