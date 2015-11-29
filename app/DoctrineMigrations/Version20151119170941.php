<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151119170941 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE fos_user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, username_canonical VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, email_canonical VARCHAR(255) NOT NULL, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, locked TINYINT(1) NOT NULL, expired TINYINT(1) NOT NULL, expires_at DATETIME DEFAULT NULL, confirmation_token VARCHAR(255) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', credentials_expired TINYINT(1) NOT NULL, credentials_expire_at DATETIME DEFAULT NULL, deleted SMALLINT DEFAULT 0 NOT NULL, name VARCHAR(50) NOT NULL, surname VARCHAR(50) NOT NULL, UNIQUE INDEX UNIQ_957A647992FC23A8 (username_canonical), UNIQUE INDEX UNIQ_957A6479A0D96FBF (email_canonical), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE host_order (id INT AUTO_INCREMENT NOT NULL, users_id INT DEFAULT NULL, supplier_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, close_date DATETIME NOT NULL, state_id SMALLINT NOT NULL, deleted SMALLINT NOT NULL, order_token VARCHAR(32) NOT NULL, UNIQUE INDEX UNIQ_564BDE6BDCA9D5C6 (order_token), INDEX IDX_564BDE6B67B3B43D (users_id), INDEX IDX_564BDE6B2ADD6D8C (supplier_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE host_order_state (id INT AUTO_INCREMENT NOT NULL, state VARCHAR(30) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_participants (id INT AUTO_INCREMENT NOT NULL, host_order_id INT DEFAULT NULL, users_email VARCHAR(255) NOT NULL, invite_token VARCHAR(255) NOT NULL, confirmed SMALLINT NOT NULL, deleted SMALLINT NOT NULL, INDEX IDX_75A9E4F8DC76912E (host_order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_order (id INT AUTO_INCREMENT NOT NULL, host_order_id INT DEFAULT NULL, users_id INT DEFAULT NULL, payed SMALLINT NOT NULL, deleted SMALLINT NOT NULL, INDEX IDX_17EB68C0DC76912E (host_order_id), INDEX IDX_17EB68C067B3B43D (users_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_order_details (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, host_order_id INT DEFAULT NULL, product_id INT DEFAULT NULL, quantity INT NOT NULL, INDEX IDX_6AD20F91A76ED395 (user_id), INDEX IDX_6AD20F91DC76912E (host_order_id), INDEX IDX_6AD20F914584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE host_order ADD CONSTRAINT FK_564BDE6B67B3B43D FOREIGN KEY (users_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE host_order ADD CONSTRAINT FK_564BDE6B2ADD6D8C FOREIGN KEY (supplier_id) REFERENCES supplier (id)');
        $this->addSql('ALTER TABLE order_participants ADD CONSTRAINT FK_75A9E4F8DC76912E FOREIGN KEY (host_order_id) REFERENCES host_order (id)');
        $this->addSql('ALTER TABLE user_order ADD CONSTRAINT FK_17EB68C0DC76912E FOREIGN KEY (host_order_id) REFERENCES host_order (id)');
        $this->addSql('ALTER TABLE user_order ADD CONSTRAINT FK_17EB68C067B3B43D FOREIGN KEY (users_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE user_order_details ADD CONSTRAINT FK_6AD20F91A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE user_order_details ADD CONSTRAINT FK_6AD20F91DC76912E FOREIGN KEY (host_order_id) REFERENCES host_order (id)');
        $this->addSql('ALTER TABLE user_order_details ADD CONSTRAINT FK_6AD20F914584665A FOREIGN KEY (product_id) REFERENCES product (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE host_order DROP FOREIGN KEY FK_564BDE6B67B3B43D');
        $this->addSql('ALTER TABLE user_order DROP FOREIGN KEY FK_17EB68C067B3B43D');
        $this->addSql('ALTER TABLE user_order_details DROP FOREIGN KEY FK_6AD20F91A76ED395');
        $this->addSql('ALTER TABLE order_participants DROP FOREIGN KEY FK_75A9E4F8DC76912E');
        $this->addSql('ALTER TABLE user_order DROP FOREIGN KEY FK_17EB68C0DC76912E');
        $this->addSql('ALTER TABLE user_order_details DROP FOREIGN KEY FK_6AD20F91DC76912E');
        $this->addSql('DROP TABLE fos_user');
        $this->addSql('DROP TABLE host_order');
        $this->addSql('DROP TABLE host_order_state');
        $this->addSql('DROP TABLE order_participants');
        $this->addSql('DROP TABLE user_order');
        $this->addSql('DROP TABLE user_order_details');
    }
}
