<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230806175057 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pokemon_pokemon_type DROP FOREIGN KEY FK_F1F052B32FE71C3E');
        $this->addSql('ALTER TABLE pokemon_pokemon_type DROP FOREIGN KEY FK_F1F052B3A926F002');
        $this->addSql('DROP TABLE pokemon_pokemon_type');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE pokemon_pokemon_type (pokemon_id INT NOT NULL, pokemon_type_id INT NOT NULL, INDEX IDX_F1F052B32FE71C3E (pokemon_id), INDEX IDX_F1F052B3A926F002 (pokemon_type_id), PRIMARY KEY(pokemon_id, pokemon_type_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE pokemon_pokemon_type ADD CONSTRAINT FK_F1F052B32FE71C3E FOREIGN KEY (pokemon_id) REFERENCES pokemon (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE pokemon_pokemon_type ADD CONSTRAINT FK_F1F052B3A926F002 FOREIGN KEY (pokemon_type_id) REFERENCES pokemon_type (id) ON UPDATE NO ACTION ON DELETE CASCADE');
    }
}
