<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240608154818 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE trip ADD pick_uplat DOUBLE PRECISION DEFAULT NULL AFTER pick_uplocation');
        $this->addSql('ALTER TABLE trip ADD pick_uplong DOUBLE PRECISION DEFAULT NULL AFTER pick_uplocation');
        $this->addSql('ALTER TABLE trip ADD delivery_lat DOUBLE PRECISION DEFAULT NULL AFTER delivery_location');
        $this->addSql('ALTER TABLE trip ADD delivery_long DOUBLE PRECISION DEFAULT NULL AFTER delivery_location');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE trip DROP pick_uplat');
        $this->addSql('ALTER TABLE trip DROP pick_uplong');
        $this->addSql('ALTER TABLE trip DROP delivery_lat');
        $this->addSql('ALTER TABLE trip DROP delivery_long');
    }
}
