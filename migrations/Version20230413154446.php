<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230413154446 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE building ADD organization_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:ulid)\'');
        $this->addSql('ALTER TABLE building ADD CONSTRAINT FK_E16F61D432C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E16F61D432C8A3DE ON building (organization_id)');
        $this->addSql('ALTER TABLE room ADD building_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:ulid)\'');
        $this->addSql('ALTER TABLE room ADD CONSTRAINT FK_729F519B4D2A7E12 FOREIGN KEY (building_id) REFERENCES building (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_729F519B4D2A7E12 ON room (building_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE building DROP FOREIGN KEY FK_E16F61D432C8A3DE');
        $this->addSql('DROP INDEX UNIQ_E16F61D432C8A3DE ON building');
        $this->addSql('ALTER TABLE building DROP organization_id');
        $this->addSql('ALTER TABLE room DROP FOREIGN KEY FK_729F519B4D2A7E12');
        $this->addSql('DROP INDEX UNIQ_729F519B4D2A7E12 ON room');
        $this->addSql('ALTER TABLE room DROP building_id');
    }
}
