<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260227100958 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE beacon ADD id_map_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE beacon ADD CONSTRAINT FK_244829E775F2EA89 FOREIGN KEY (id_map_id) REFERENCES map (id)');
        $this->addSql('CREATE INDEX IDX_244829E775F2EA89 ON beacon (id_map_id)');
        $this->addSql('ALTER TABLE runner ADD id_race_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE runner ADD CONSTRAINT FK_F92B8B3EB0C47D7D FOREIGN KEY (id_race_id) REFERENCES race (id)');
        $this->addSql('CREATE INDEX IDX_F92B8B3EB0C47D7D ON runner (id_race_id)');
        $this->addSql('ALTER TABLE user ADD idestablishments_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649B15EAA7A FOREIGN KEY (idestablishments_id) REFERENCES establishments (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649B15EAA7A ON user (idestablishments_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE beacon DROP FOREIGN KEY FK_244829E775F2EA89');
        $this->addSql('DROP INDEX IDX_244829E775F2EA89 ON beacon');
        $this->addSql('ALTER TABLE beacon DROP id_map_id');
        $this->addSql('ALTER TABLE runner DROP FOREIGN KEY FK_F92B8B3EB0C47D7D');
        $this->addSql('DROP INDEX IDX_F92B8B3EB0C47D7D ON runner');
        $this->addSql('ALTER TABLE runner DROP id_race_id');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649B15EAA7A');
        $this->addSql('DROP INDEX IDX_8D93D649B15EAA7A ON user');
        $this->addSql('ALTER TABLE user DROP idestablishments_id');
    }
}
