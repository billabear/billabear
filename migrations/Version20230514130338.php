<?php

declare(strict_types=1);

/*
 * Copyright Iain Cambridge 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: TBD ( 3 years after 1.0.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230514130338 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subscription DROP CONSTRAINT fk_a3c664d38eec86f7');
        $this->addSql('CREATE TABLE payment_card (id UUID NOT NULL, customer_id UUID DEFAULT NULL, provider VARCHAR(255) DEFAULT NULL, stored_customer_reference VARCHAR(255) DEFAULT NULL, stored_payment_reference VARCHAR(255) DEFAULT NULL, default_payment_option BOOLEAN NOT NULL, brand VARCHAR(255) DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, last_four VARCHAR(255) DEFAULT NULL, expiry_month VARCHAR(255) DEFAULT NULL, expiry_year VARCHAR(255) DEFAULT NULL, is_deleted BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_37970FA79395C3F3 ON payment_card (customer_id)');
        $this->addSql('COMMENT ON COLUMN payment_card.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN payment_card.customer_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE payment_card ADD CONSTRAINT FK_37970FA79395C3F3 FOREIGN KEY (customer_id) REFERENCES customers (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE payment_method DROP CONSTRAINT fk_7b61a1f69395c3f3');
        $this->addSql('DROP TABLE payment_method');
        $this->addSql('ALTER TABLE subscription DROP CONSTRAINT FK_A3C664D38EEC86F7');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D38EEC86F7 FOREIGN KEY (payment_details_id) REFERENCES payment_card (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE subscription DROP CONSTRAINT FK_A3C664D38EEC86F7');
        $this->addSql('CREATE TABLE payment_method (id UUID NOT NULL, customer_id UUID DEFAULT NULL, provider VARCHAR(255) DEFAULT NULL, stored_customer_reference VARCHAR(255) DEFAULT NULL, stored_payment_reference VARCHAR(255) DEFAULT NULL, default_payment_option BOOLEAN NOT NULL, brand VARCHAR(255) DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, last_four VARCHAR(255) DEFAULT NULL, expiry_month VARCHAR(255) DEFAULT NULL, expiry_year VARCHAR(255) DEFAULT NULL, is_deleted BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_7b61a1f69395c3f3 ON payment_method (customer_id)');
        $this->addSql('COMMENT ON COLUMN payment_method.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN payment_method.customer_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE payment_method ADD CONSTRAINT fk_7b61a1f69395c3f3 FOREIGN KEY (customer_id) REFERENCES customers (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE payment_card DROP CONSTRAINT FK_37970FA79395C3F3');
        $this->addSql('DROP TABLE payment_card');
        $this->addSql('ALTER TABLE subscription DROP CONSTRAINT fk_a3c664d38eec86f7');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT fk_a3c664d38eec86f7 FOREIGN KEY (payment_details_id) REFERENCES payment_method (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
