<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210908215502 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE article_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE category_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE project_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE project_category_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE tag_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE users_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE article (id INT NOT NULL, category_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, summary VARCHAR(255) NOT NULL, image VARCHAR(255) DEFAULT NULL, markdown TEXT DEFAULT NULL, html TEXT DEFAULT NULL, featured BOOLEAN NOT NULL, timestamp TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_23A0E66989D9B62 ON article (slug)');
        $this->addSql('CREATE INDEX IDX_23A0E6612469DE2 ON article (category_id)');
        $this->addSql('CREATE TABLE article_tag (article_id INT NOT NULL, tag_id INT NOT NULL, PRIMARY KEY(article_id, tag_id))');
        $this->addSql('CREATE INDEX IDX_919694F97294869C ON article_tag (article_id)');
        $this->addSql('CREATE INDEX IDX_919694F9BAD26311 ON article_tag (tag_id)');
        $this->addSql('CREATE TABLE category (id INT NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, timestamp TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_64C19C1989D9B62 ON category (slug)');
        $this->addSql('CREATE TABLE project (id INT NOT NULL, category_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, summary VARCHAR(255) NOT NULL, image VARCHAR(255) DEFAULT NULL, markdown TEXT DEFAULT NULL, html TEXT DEFAULT NULL, timestamp TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2FB3D0EE989D9B62 ON project (slug)');
        $this->addSql('CREATE INDEX IDX_2FB3D0EE12469DE2 ON project (category_id)');
        $this->addSql('CREATE TABLE project_category (id INT NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, timestamp TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3B02921A989D9B62 ON project_category (slug)');
        $this->addSql('CREATE TABLE tag (id INT NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, timestamp TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_389B783989D9B62 ON tag (slug)');
        $this->addSql('CREATE TABLE users (id INT NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, roles JSON NOT NULL, confirmation_status BOOLEAN NOT NULL, api_token VARCHAR(255) DEFAULT NULL, timestamp TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E6612469DE2 FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE article_tag ADD CONSTRAINT FK_919694F97294869C FOREIGN KEY (article_id) REFERENCES article (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE article_tag ADD CONSTRAINT FK_919694F9BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EE12469DE2 FOREIGN KEY (category_id) REFERENCES project_category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE article_tag DROP CONSTRAINT FK_919694F97294869C');
        $this->addSql('ALTER TABLE article DROP CONSTRAINT FK_23A0E6612469DE2');
        $this->addSql('ALTER TABLE project DROP CONSTRAINT FK_2FB3D0EE12469DE2');
        $this->addSql('ALTER TABLE article_tag DROP CONSTRAINT FK_919694F9BAD26311');
        $this->addSql('DROP SEQUENCE article_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE category_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE project_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE project_category_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE tag_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE users_id_seq CASCADE');
        $this->addSql('DROP TABLE article');
        $this->addSql('DROP TABLE article_tag');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE project');
        $this->addSql('DROP TABLE project_category');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE users');
    }
}
