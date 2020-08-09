<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200809204611 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Links a photoshoot to a photo package, and removed the photoshoot on package removal';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE photo_shoot ADD package_id UUID NOT NULL');
        $this->addSql('COMMENT ON COLUMN photo_shoot.package_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE photo_shoot ADD CONSTRAINT FK_CA15FB09F44CABFF FOREIGN KEY (package_id) REFERENCES photo_package (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_CA15FB09F44CABFF ON photo_shoot (package_id)');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE photo_shoot DROP CONSTRAINT FK_CA15FB09F44CABFF');
        $this->addSql('DROP INDEX IDX_CA15FB09F44CABFF');
        $this->addSql('ALTER TABLE photo_shoot DROP package_id');
    }
}
