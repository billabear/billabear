<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 24.08.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230909161022 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE checkout (id UUID NOT NULL, customer_id UUID DEFAULT NULL, created_by_id UUID DEFAULT NULL, name VARCHAR(255) NOT NULL, permanent BOOLEAN NOT NULL, slug VARCHAR(255) NOT NULL, currency VARCHAR(255) NOT NULL, success_redirect VARCHAR(255) DEFAULT NULL, cancel_redirect VARCHAR(255) DEFAULT NULL, amount_due INT DEFAULT NULL, total INT DEFAULT NULL, sub_total INT DEFAULT NULL, tax_total INT DEFAULT NULL, expires_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_AF382D4E9395C3F3 ON checkout (customer_id)');
        $this->addSql('CREATE INDEX IDX_AF382D4EB03A8386 ON checkout (created_by_id)');
        $this->addSql('COMMENT ON COLUMN checkout.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN checkout.customer_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN checkout.created_by_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE checkout_line (id UUID NOT NULL, checkout_id UUID DEFAULT NULL, subscription_plan_id UUID DEFAULT NULL, price_id UUID DEFAULT NULL, seat_number INT DEFAULT NULL, currency VARCHAR(255) NOT NULL, total INT DEFAULT NULL, sub_total INT DEFAULT NULL, tax_total INT DEFAULT NULL, tax_percentage DOUBLE PRECISION DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, tax_type VARCHAR(255) DEFAULT NULL, include_tax BOOLEAN NOT NULL, tax_country VARCHAR(255) DEFAULT NULL, reverse_charge BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_3A4D4128146D8724 ON checkout_line (checkout_id)');
        $this->addSql('CREATE INDEX IDX_3A4D41289B8CE200 ON checkout_line (subscription_plan_id)');
        $this->addSql('CREATE INDEX IDX_3A4D4128D614C7E7 ON checkout_line (price_id)');
        $this->addSql('COMMENT ON COLUMN checkout_line.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN checkout_line.checkout_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN checkout_line.subscription_plan_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN checkout_line.price_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE checkout ADD CONSTRAINT FK_AF382D4E9395C3F3 FOREIGN KEY (customer_id) REFERENCES customers (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE checkout ADD CONSTRAINT FK_AF382D4EB03A8386 FOREIGN KEY (created_by_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE checkout_line ADD CONSTRAINT FK_3A4D4128146D8724 FOREIGN KEY (checkout_id) REFERENCES checkout (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE checkout_line ADD CONSTRAINT FK_3A4D41289B8CE200 FOREIGN KEY (subscription_plan_id) REFERENCES subscription_plan (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE checkout_line ADD CONSTRAINT FK_3A4D4128D614C7E7 FOREIGN KEY (price_id) REFERENCES price (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE checkout DROP CONSTRAINT FK_AF382D4E9395C3F3');
        $this->addSql('ALTER TABLE checkout DROP CONSTRAINT FK_AF382D4EB03A8386');
        $this->addSql('ALTER TABLE checkout_line DROP CONSTRAINT FK_3A4D4128146D8724');
        $this->addSql('ALTER TABLE checkout_line DROP CONSTRAINT FK_3A4D41289B8CE200');
        $this->addSql('ALTER TABLE checkout_line DROP CONSTRAINT FK_3A4D4128D614C7E7');
        $this->addSql('DROP TABLE checkout');
        $this->addSql('DROP TABLE checkout_line');
    }
}
