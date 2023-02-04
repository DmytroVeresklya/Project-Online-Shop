<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230120145718 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'create subscriber';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE subscriber_id INCREMENT BY 1 MINVALUE 100 START 100');
        $this->addSql('CREATE TABLE subscriber (id INT NOT NULL, email VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, modified_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN subscriber.created_at IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE subscriber_id CASCADE');
        $this->addSql('DROP TABLE subscriber');
    }
}
