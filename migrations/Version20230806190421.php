<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230806190421 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE pokemon (id INT NOT NULL, evolves_to_id INT DEFAULT NULL, name VARCHAR(100) NOT NULL, color VARCHAR(50) NOT NULL, INDEX IDX_62DC90F312D554B7 (evolves_to_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pokemon_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pokemon_type_pokemon (pokemon_type_id INT NOT NULL, pokemon_id INT NOT NULL, INDEX IDX_B7A84DFA926F002 (pokemon_type_id), INDEX IDX_B7A84DF2FE71C3E (pokemon_id), PRIMARY KEY(pokemon_type_id, pokemon_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE pokemon ADD CONSTRAINT FK_62DC90F312D554B7 FOREIGN KEY (evolves_to_id) REFERENCES pokemon (id)');
        $this->addSql('ALTER TABLE pokemon_type_pokemon ADD CONSTRAINT FK_B7A84DFA926F002 FOREIGN KEY (pokemon_type_id) REFERENCES pokemon_type (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE pokemon_type_pokemon ADD CONSTRAINT FK_B7A84DF2FE71C3E FOREIGN KEY (pokemon_id) REFERENCES pokemon (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pokemon DROP FOREIGN KEY FK_62DC90F312D554B7');
        $this->addSql('ALTER TABLE pokemon_type_pokemon DROP FOREIGN KEY FK_B7A84DFA926F002');
        $this->addSql('ALTER TABLE pokemon_type_pokemon DROP FOREIGN KEY FK_B7A84DF2FE71C3E');
        $this->addSql('DROP TABLE pokemon');
        $this->addSql('DROP TABLE pokemon_type');
        $this->addSql('DROP TABLE pokemon_type_pokemon');
    }
}
