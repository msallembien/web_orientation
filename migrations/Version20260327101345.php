<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260327101345 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE scan_log ADD id_runner_id INT DEFAULT NULL, ADD id_beacon_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE scan_log ADD CONSTRAINT FK_FB19A492BC4A09B4 FOREIGN KEY (id_runner_id) REFERENCES runner (id)');
        $this->addSql('ALTER TABLE scan_log ADD CONSTRAINT FK_FB19A4927698E95F FOREIGN KEY (id_beacon_id) REFERENCES beacon (id)');
        $this->addSql('CREATE INDEX IDX_FB19A492BC4A09B4 ON scan_log (id_runner_id)');
        $this->addSql('CREATE INDEX IDX_FB19A4927698E95F ON scan_log (id_beacon_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE scan_log DROP FOREIGN KEY FK_FB19A492BC4A09B4');
        $this->addSql('ALTER TABLE scan_log DROP FOREIGN KEY FK_FB19A4927698E95F');
        $this->addSql('DROP INDEX IDX_FB19A492BC4A09B4 ON scan_log');
        $this->addSql('DROP INDEX IDX_FB19A4927698E95F ON scan_log');
        $this->addSql('ALTER TABLE scan_log DROP id_runner_id, DROP id_beacon_id');
    }
}
