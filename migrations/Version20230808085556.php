<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230808085556 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE pokemon_type_user (pokemon_type_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_6B75F967A926F002 (pokemon_type_id), INDEX IDX_6B75F967A76ED395 (user_id), PRIMARY KEY(pokemon_type_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE pokemon_type_user ADD CONSTRAINT FK_6B75F967A926F002 FOREIGN KEY (pokemon_type_id) REFERENCES pokemon_type (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE pokemon_type_user ADD CONSTRAINT FK_6B75F967A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pokemon_type_user DROP FOREIGN KEY FK_6B75F967A926F002');
        $this->addSql('ALTER TABLE pokemon_type_user DROP FOREIGN KEY FK_6B75F967A76ED395');
        $this->addSql('DROP TABLE pokemon_type_user');
    }
}
