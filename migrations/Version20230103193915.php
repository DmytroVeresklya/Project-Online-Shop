<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20230103193915 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'create product_category / add product_category_id create foreign key productCategory->products';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE product_category_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE product_category (id INT NOT NULL, title VARCHAR(127) NOT NULL, slug VARCHAR(127) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE product ADD product_category_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADBE6903FD FOREIGN KEY (product_category_id) REFERENCES product_category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_D34A04ADBE6903FD ON product (product_category_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE product DROP CONSTRAINT FK_D34A04ADBE6903FD');
        $this->addSql('DROP SEQUENCE product_category_id_seq CASCADE');
        $this->addSql('DROP TABLE product_category');
        $this->addSql('DROP INDEX IDX_D34A04ADBE6903FD');
        $this->addSql('ALTER TABLE product DROP product_category_id');
    }
}
