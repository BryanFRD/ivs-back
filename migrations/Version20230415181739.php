<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230415181739 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE building DROP INDEX UNIQ_E16F61D432C8A3DE, ADD INDEX IDX_E16F61D432C8A3DE (organization_id)');
        $this->addSql('ALTER TABLE room DROP INDEX UNIQ_729F519B4D2A7E12, ADD INDEX IDX_729F519B4D2A7E12 (building_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE building DROP INDEX IDX_E16F61D432C8A3DE, ADD UNIQUE INDEX UNIQ_E16F61D432C8A3DE (organization_id)');
        $this->addSql('ALTER TABLE room DROP INDEX IDX_729F519B4D2A7E12, ADD UNIQUE INDEX UNIQ_729F519B4D2A7E12 (building_id)');
    }
}
