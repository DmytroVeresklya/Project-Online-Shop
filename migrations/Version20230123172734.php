<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230123172734 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'create refresh_token';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE refresh_token_id INCREMENT BY 1 MINVALUE 100 START 100');
        $this->addSql('CREATE TABLE refresh_token (id INT NOT NULL, user_id INT NOT NULL, refresh_token VARCHAR(255) DEFAULT NULL, username VARCHAR(255) DEFAULT NULL, valid TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, modified_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_9BACE7E1A76ED395 ON refresh_token (user_id)');
        $this->addSql('ALTER TABLE refresh_token ADD CONSTRAINT FK_9BACE7E1A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE refresh_token_id CASCADE');
        $this->addSql('ALTER TABLE refresh_token DROP CONSTRAINT FK_9BACE7E1A76ED395');
        $this->addSql('DROP TABLE refresh_token');
    }
}
