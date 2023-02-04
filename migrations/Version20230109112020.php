<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230109112020 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '
            add [slug, image, made_in, crated_at, modified_at, active, search_queries], unique index for slug to product
        ';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE product ADD slug VARCHAR(127) NOT NULL');
        $this->addSql('ALTER TABLE product ADD image VARCHAR(127) DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD made_in VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE product ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE product ADD modified_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE product ADD active BOOLEAN DEFAULT false NOT NULL');
        $this->addSql('ALTER TABLE product ADD search_queries TEXT DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN product.search_queries IS \'(DC2Type:simple_array)\'');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D34A04AD989D9B62 ON product (slug)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX UNIQ_D34A04AD989D9B62');
        $this->addSql('ALTER TABLE product DROP slug');
        $this->addSql('ALTER TABLE product DROP image');
        $this->addSql('ALTER TABLE product DROP made_in');
        $this->addSql('ALTER TABLE product DROP created_at');
        $this->addSql('ALTER TABLE product DROP modified_at');
        $this->addSql('ALTER TABLE product DROP active');
        $this->addSql('ALTER TABLE product DROP search_queries');
    }
}
