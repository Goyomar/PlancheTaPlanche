<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220601093708 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE skateboard ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE skateboard ADD CONSTRAINT FK_E2E95F3AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_E2E95F3AA76ED395 ON skateboard (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE skateboard DROP FOREIGN KEY FK_E2E95F3AA76ED395');
        $this->addSql('DROP INDEX IDX_E2E95F3AA76ED395 ON skateboard');
        $this->addSql('ALTER TABLE skateboard DROP user_id');
    }
}
