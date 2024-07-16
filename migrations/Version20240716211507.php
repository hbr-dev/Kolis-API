<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240716211507 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE package DROP CONSTRAINT fk_de686795cd53edb6');
        $this->addSql('DROP INDEX idx_de686795cd53edb6');
        $this->addSql('ALTER TABLE package ADD receiver_first_name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE package ADD receiver_last_name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE package ADD receiver_phone_number VARCHAR(12) NOT NULL');
        $this->addSql('ALTER TABLE package ADD receiver_email VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE package DROP receiver_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE package DROP receiver_first_name');
        $this->addSql('ALTER TABLE package DROP receiver_last_name');
        $this->addSql('ALTER TABLE package DROP receiver_phone_number');
        $this->addSql('ALTER TABLE package DROP receiver_email');
    }
}
