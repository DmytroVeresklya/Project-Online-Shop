<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230123181941 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add roles to user';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER INDEX idx_9bace7e1a76ed395 RENAME TO IDX_C74F2195A76ED395');
        $this->addSql('ALTER TABLE "user" ADD roles TEXT DEFAULT \'ROLE_USER\' NOT NULL');
        $this->addSql('COMMENT ON COLUMN "user".roles IS \'(DC2Type:simple_array)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER INDEX idx_c74f2195a76ed395 RENAME TO idx_9bace7e1a76ed395');
        $this->addSql('ALTER TABLE "user" DROP roles');
    }
}
