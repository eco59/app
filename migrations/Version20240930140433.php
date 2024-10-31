<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240930140433 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE event (id INT AUTO_INCREMENT NOT NULL, pseudo_id INT NOT NULL, titre VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, nombre_de_joueur NUMERIC(10, 0) NOT NULL, autoriser TINYINT(1) NOT NULL, date_heure_debut DATETIME NOT NULL, date_heure_fin DATETIME NOT NULL, INDEX IDX_3BAE0AA720E394C2 (pseudo_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE favori (id INT AUTO_INCREMENT NOT NULL, id_pseudo_id INT NOT NULL, id_event_id INT NOT NULL, INDEX IDX_EF85A2CCA0D628E5 (id_pseudo_id), INDEX IDX_EF85A2CC212C041E (id_event_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, pseudo VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_PSEUDO (pseudo), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA720E394C2 FOREIGN KEY (pseudo_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE favori ADD CONSTRAINT FK_EF85A2CCA0D628E5 FOREIGN KEY (id_pseudo_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE favori ADD CONSTRAINT FK_EF85A2CC212C041E FOREIGN KEY (id_event_id) REFERENCES event (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA720E394C2');
        $this->addSql('ALTER TABLE favori DROP FOREIGN KEY FK_EF85A2CCA0D628E5');
        $this->addSql('ALTER TABLE favori DROP FOREIGN KEY FK_EF85A2CC212C041E');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE favori');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
