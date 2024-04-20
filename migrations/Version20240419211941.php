<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240419211941 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE client (id SERIAL NOT NULL, code UUID DEFAULT uuid_generate_v4() NOT NULL, first_name VARCHAR(50) NOT NULL, last_name VARCHAR(50) NOT NULL, email VARCHAR(180) DEFAULT NULL, country_code VARCHAR(5) NOT NULL, phone_number VARCHAR(20) NOT NULL, password VARCHAR(255) NOT NULL, awaiting_for_delivery BOOLEAN NOT NULL, id_verified BOOLEAN NOT NULL, active BOOLEAN NOT NULL, profile_img TEXT DEFAULT NULL, created_at TIMESTAMP(0) WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN client.code IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE new_api_user (id SERIAL NOT NULL, code VARCHAR(225) NOT NULL, role VARCHAR(225) NOT NULL, mail VARCHAR(225) NOT NULL, username VARCHAR(225) NOT NULL, password VARCHAR(255) NOT NULL, roles VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE pack (id SERIAL NOT NULL, transporter_id INT NOT NULL, code UUID DEFAULT uuid_generate_v4() NOT NULL, price NUMERIC(6, 3) NOT NULL, created_at TIMESTAMP(0) WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, expiration_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_97DE5E234F335C8B ON pack (transporter_id)');
        $this->addSql('COMMENT ON COLUMN pack.code IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE package (id SERIAL NOT NULL, sender_id INT NOT NULL, receiver_id INT NOT NULL, trip_id INT DEFAULT NULL, code UUID DEFAULT uuid_generate_v4() NOT NULL, description VARCHAR(300) DEFAULT NULL, weight DOUBLE PRECISION NOT NULL, dimensions JSON NOT NULL, transportation_charge NUMERIC(6, 3) DEFAULT NULL, status VARCHAR(255) NOT NULL, img TEXT DEFAULT NULL, created_at TIMESTAMP(0) WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_DE686795F624B39D ON package (sender_id)');
        $this->addSql('CREATE INDEX IDX_DE686795CD53EDB6 ON package (receiver_id)');
        $this->addSql('CREATE INDEX IDX_DE686795A5BC2E0E ON package (trip_id)');
        $this->addSql('COMMENT ON COLUMN package.code IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE token_api_user (id SERIAL NOT NULL, name VARCHAR(50) NOT NULL, token VARCHAR(400) NOT NULL, roles JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D83B7D395F37A13B ON token_api_user (token)');
        $this->addSql('CREATE TABLE transporter (id SERIAL NOT NULL, code UUID DEFAULT uuid_generate_v4() NOT NULL, first_name VARCHAR(50) NOT NULL, last_name VARCHAR(50) NOT NULL, address VARCHAR(255) NOT NULL, email VARCHAR(180) DEFAULT NULL, country_code VARCHAR(5) NOT NULL, phone_number VARCHAR(20) NOT NULL, password VARCHAR(255) NOT NULL, id_verified BOOLEAN NOT NULL, active BOOLEAN NOT NULL, profile_img TEXT DEFAULT NULL, created_at TIMESTAMP(0) WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN transporter.code IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE trip (id SERIAL NOT NULL, transporter_id INT NOT NULL, code UUID DEFAULT uuid_generate_v4() NOT NULL, pick_uplocation VARCHAR(300) NOT NULL, delivery_location VARCHAR(300) NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, status VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7656F53B4F335C8B ON trip (transporter_id)');
        $this->addSql('COMMENT ON COLUMN trip.code IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE vehicle (id SERIAL NOT NULL, transporter_id INT NOT NULL, code UUID DEFAULT uuid_generate_v4() NOT NULL, model VARCHAR(100) NOT NULL, registration_nbr VARCHAR(100) NOT NULL, created_at TIMESTAMP(0) WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_1B80E4864F335C8B ON vehicle (transporter_id)');
        $this->addSql('COMMENT ON COLUMN vehicle.code IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE wording (id SERIAL NOT NULL, domain_id INT DEFAULT NULL, code VARCHAR(100) NOT NULL, label VARCHAR(100) NOT NULL, created_at TIMESTAMP(0) WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_15F91DD2115F0EE5 ON wording (domain_id)');
        $this->addSql('CREATE TABLE wording_domain (id SERIAL NOT NULL, code VARCHAR(100) NOT NULL, label VARCHAR(100) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE wording_translation (id SERIAL NOT NULL, wording_id INT DEFAULT NULL, content VARCHAR(200) NOT NULL, language VARCHAR(2) NOT NULL, created_at TIMESTAMP(0) WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_353F7543D34102DF ON wording_translation (wording_id)');
        $this->addSql('ALTER TABLE pack ADD CONSTRAINT FK_97DE5E234F335C8B FOREIGN KEY (transporter_id) REFERENCES transporter (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE package ADD CONSTRAINT FK_DE686795F624B39D FOREIGN KEY (sender_id) REFERENCES client (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE package ADD CONSTRAINT FK_DE686795CD53EDB6 FOREIGN KEY (receiver_id) REFERENCES client (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE package ADD CONSTRAINT FK_DE686795A5BC2E0E FOREIGN KEY (trip_id) REFERENCES trip (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE trip ADD CONSTRAINT FK_7656F53B4F335C8B FOREIGN KEY (transporter_id) REFERENCES transporter (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE vehicle ADD CONSTRAINT FK_1B80E4864F335C8B FOREIGN KEY (transporter_id) REFERENCES transporter (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE wording ADD CONSTRAINT FK_15F91DD2115F0EE5 FOREIGN KEY (domain_id) REFERENCES wording_domain (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE wording_translation ADD CONSTRAINT FK_353F7543D34102DF FOREIGN KEY (wording_id) REFERENCES wording (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE pack DROP CONSTRAINT FK_97DE5E234F335C8B');
        $this->addSql('ALTER TABLE package DROP CONSTRAINT FK_DE686795F624B39D');
        $this->addSql('ALTER TABLE package DROP CONSTRAINT FK_DE686795CD53EDB6');
        $this->addSql('ALTER TABLE package DROP CONSTRAINT FK_DE686795A5BC2E0E');
        $this->addSql('ALTER TABLE trip DROP CONSTRAINT FK_7656F53B4F335C8B');
        $this->addSql('ALTER TABLE vehicle DROP CONSTRAINT FK_1B80E4864F335C8B');
        $this->addSql('ALTER TABLE wording DROP CONSTRAINT FK_15F91DD2115F0EE5');
        $this->addSql('ALTER TABLE wording_translation DROP CONSTRAINT FK_353F7543D34102DF');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE new_api_user');
        $this->addSql('DROP TABLE pack');
        $this->addSql('DROP TABLE package');
        $this->addSql('DROP TABLE token_api_user');
        $this->addSql('DROP TABLE transporter');
        $this->addSql('DROP TABLE trip');
        $this->addSql('DROP TABLE vehicle');
        $this->addSql('DROP TABLE wording');
        $this->addSql('DROP TABLE wording_domain');
        $this->addSql('DROP TABLE wording_translation');
    }
}
