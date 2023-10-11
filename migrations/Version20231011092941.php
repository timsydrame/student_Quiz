<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231011092941 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE candidate_response (id INT AUTO_INCREMENT NOT NULL, question_id INT NOT NULL, enoncer VARCHAR(1000) NOT NULL, image_response VARCHAR(255) DEFAULT NULL, iscorrect TINYINT(1) NOT NULL, INDEX IDX_9F2549281E27F6BF (question_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE candidate_response ADD CONSTRAINT FK_9F2549281E27F6BF FOREIGN KEY (question_id) REFERENCES question (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE candidate_response DROP FOREIGN KEY FK_9F2549281E27F6BF');
        $this->addSql('DROP TABLE candidate_response');
    }
}
