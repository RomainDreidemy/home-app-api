<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210308194902 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE shopping_item ADD shopping_list_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE shopping_item ADD CONSTRAINT FK_6612795F23245BF9 FOREIGN KEY (shopping_list_id) REFERENCES shopping_list (id)');
        $this->addSql('CREATE INDEX IDX_6612795F23245BF9 ON shopping_item (shopping_list_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE shopping_item DROP FOREIGN KEY FK_6612795F23245BF9');
        $this->addSql('DROP INDEX IDX_6612795F23245BF9 ON shopping_item');
        $this->addSql('ALTER TABLE shopping_item DROP shopping_list_id');
    }
}
