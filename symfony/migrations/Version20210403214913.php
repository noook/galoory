<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210403214913 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX idx_ca15fb099395c3f3');
        $this->addSql('CREATE TABLE selected_picture (id UUID NOT NULL, photoshoot_id UUID NOT NULL, filename VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_FE9F31172191CB92 ON selected_picture (photoshoot_id)');
        $this->addSql('COMMENT ON COLUMN selected_picture.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN selected_picture.photoshoot_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE selected_picture ADD CONSTRAINT FK_FE9F31172191CB92 FOREIGN KEY (photoshoot_id) REFERENCES photo_shoot (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE photo_shoot ADD date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE photo_shoot ADD quantity INT DEFAULT NULL');
        $this->addSql('UPDATE photo_shoot AS Shoot SET quantity = Package.quantity FROM photo_package AS Package WHERE Shoot.package_id = Package.id');
        $this->addSql('ALTER TABLE photo_shoot ALTER COLUMN quantity SET NOT NULL');
        $this->addSql('ALTER TABLE photo_shoot ADD comment TEXT default \'\' NOT NULL');
        $this->addSql('ALTER TABLE photo_shoot DROP package_id');
        $this->addSql('ALTER TABLE photo_shoot DROP expiration');
        $this->addSql('DROP TABLE photo_package');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CA15FB099395C3F3 ON photo_shoot (customer_id)');
        $this->addSql('ALTER TABLE "user" DROP lastname');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE TABLE photo_package (id UUID NOT NULL, name VARCHAR(255) NOT NULL, quantity INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN photo_package.id IS \'(DC2Type:uuid)\'');
        $this->addSql('DROP TABLE selected_picture');
        $this->addSql('ALTER TABLE "user" ADD lastname VARCHAR(100) NOT NULL');
        $this->addSql('DROP INDEX UNIQ_CA15FB099395C3F3');
        $this->addSql('ALTER TABLE photo_shoot ADD package_id UUID NOT NULL');
        $this->addSql('ALTER TABLE photo_shoot ADD expiration DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE photo_shoot DROP date');
        $this->addSql('ALTER TABLE photo_shoot DROP quantity');
        $this->addSql('ALTER TABLE photo_shoot DROP comment');
        $this->addSql('COMMENT ON COLUMN photo_shoot.package_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE INDEX idx_ca15fb099395c3f3 ON photo_shoot (customer_id)');
    }
}
