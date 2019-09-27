<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190927193519 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE days_of_week_aware_config_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE days_of_week_aware_config (id INT NOT NULL, address_id INT DEFAULT NULL, restaurant_id INT DEFAULT NULL, days_of_week VARCHAR(255) NOT NULL, opening_hours JSON DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_CC5C08FF5B7AF75 ON days_of_week_aware_config (address_id)');
        $this->addSql('CREATE INDEX IDX_CC5C08FB1E7706E ON days_of_week_aware_config (restaurant_id)');
        $this->addSql('COMMENT ON COLUMN days_of_week_aware_config.opening_hours IS \'(DC2Type:json_array)\'');
        $this->addSql('ALTER TABLE days_of_week_aware_config ADD CONSTRAINT FK_CC5C08FF5B7AF75 FOREIGN KEY (address_id) REFERENCES address (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE days_of_week_aware_config ADD CONSTRAINT FK_CC5C08FB1E7706E FOREIGN KEY (restaurant_id) REFERENCES restaurant (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE days_of_week_aware_config_id_seq CASCADE');
        $this->addSql('DROP TABLE days_of_week_aware_config');
        $this->addSql('COMMENT ON COLUMN zone.polygon IS \'(DC2Type:geojson)(DC2Type:geojson)\'');
        $this->addSql('ALTER TABLE api_user ADD invitation_code VARCHAR(180) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX uniq_ac64a0baba14fccc ON api_user (invitation_code)');
    }
}
