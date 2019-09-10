<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190812163540 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE com_post (id INT AUTO_INCREMENT NOT NULL, post_id INT NOT NULL, username VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, date_publication DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE post ADD user_creator VARCHAR(255) NOT NULL, DROP image, DROP creat_by, CHANGE creat_at date_post DATETIME NOT NULL');
        $this->addSql('ALTER TABLE users ADD username VARCHAR(255) NOT NULL, ADD pwd VARCHAR(255) NOT NULL, ADD date_inscription DATETIME NOT NULL, DROP pseudo, DROP email, DROP mdp');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE com_post');
        $this->addSql('ALTER TABLE post ADD creat_by VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE user_creator image VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE date_post creat_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE users ADD pseudo VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, ADD email VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, ADD mdp VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, DROP username, DROP pwd, DROP date_inscription');
    }
}
