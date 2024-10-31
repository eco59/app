<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241029144756 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event CHANGE nombre_de_joueur nombre_de_joueur INT NOT NULL, CHANGE image image LONGTEXT DEFAULT NULL, CHANGE status status VARCHAR(20) DEFAULT \'pending\' NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event CHANGE nombre_de_joueur nombre_de_joueur NUMERIC(10, 0) NOT NULL, CHANGE image image LONGTEXT NOT NULL, CHANGE status status VARCHAR(20) NOT NULL');
    }
}
