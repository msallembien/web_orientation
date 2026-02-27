<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260227100319 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE race ADD id_map_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE race ADD CONSTRAINT FK_DA6FBBAF75F2EA89 FOREIGN KEY (id_map_id) REFERENCES map (id)');
        $this->addSql('CREATE INDEX IDX_DA6FBBAF75F2EA89 ON race (id_map_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE race DROP FOREIGN KEY FK_DA6FBBAF75F2EA89');
        $this->addSql('DROP INDEX IDX_DA6FBBAF75F2EA89 ON race');
        $this->addSql('ALTER TABLE race DROP id_map_id');
    }
}
