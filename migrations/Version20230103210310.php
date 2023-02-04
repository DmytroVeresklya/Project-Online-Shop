<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230103210310 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add sequence strategy';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE product_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE product_category_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE product_id INCREMENT BY 1 MINVALUE 100 START 100');
        $this->addSql('CREATE SEQUENCE product_category_id INCREMENT BY 1 MINVALUE 100 START 100');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE product_id CASCADE');
        $this->addSql('DROP SEQUENCE product_category_id CASCADE');
        $this->addSql('CREATE SEQUENCE product_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE product_category_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
    }
}
