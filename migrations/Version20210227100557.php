<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210227100557 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE shopping_list ADD home_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE shopping_list ADD CONSTRAINT FK_3DC1A45928CDC89C FOREIGN KEY (home_id) REFERENCES home (id)');
        $this->addSql('CREATE INDEX IDX_3DC1A45928CDC89C ON shopping_list (home_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE shopping_list DROP FOREIGN KEY FK_3DC1A45928CDC89C');
        $this->addSql('DROP INDEX IDX_3DC1A45928CDC89C ON shopping_list');
        $this->addSql('ALTER TABLE shopping_list DROP home_id');
    }
}
