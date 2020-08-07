<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200806173356 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Creates three first entities: User, PhotoShoot, PhotoPackage';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('CREATE TABLE photo_package (id UUID NOT NULL, name VARCHAR(255) NOT NULL, quantity INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN photo_package.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE photo_shoot (id UUID NOT NULL, customer_id UUID NOT NULL, expiration DATE DEFAULT NULL, status VARCHAR(30) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_CA15FB099395C3F3 ON photo_shoot (customer_id)');
        $this->addSql('COMMENT ON COLUMN photo_shoot.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN photo_shoot.customer_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE "user" (id UUID NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('COMMENT ON COLUMN "user".id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE photo_shoot ADD CONSTRAINT FK_CA15FB099395C3F3 FOREIGN KEY (customer_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE photo_shoot DROP CONSTRAINT FK_CA15FB099395C3F3');
        $this->addSql('DROP TABLE photo_package');
        $this->addSql('DROP TABLE photo_shoot');
        $this->addSql('DROP TABLE "user"');
    }
}
