<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: 24.08.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE.md file.
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230906123352 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Version 1.1.2';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE subscription_seat_modification (id UUID NOT NULL, subscription_id UUID DEFAULT NULL, type VARCHAR(255) NOT NULL, change_value INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E532907B9A1887DC ON subscription_seat_modification (subscription_id)');
        $this->addSql('COMMENT ON COLUMN subscription_seat_modification.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN subscription_seat_modification.subscription_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE subscription_seat_modification ADD CONSTRAINT FK_E532907B9A1887DC FOREIGN KEY (subscription_id) REFERENCES subscription (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE invoice_process ALTER due_at DROP NOT NULL');
        $this->addSql('ALTER TABLE quote_line ADD seat_number INT DEFAULT NULL');

        $this->addSql('CREATE TABLE checkout (id UUID NOT NULL, customer_id UUID DEFAULT NULL, brand_settings_id UUID DEFAULT NULL, created_by_id UUID DEFAULT NULL, name VARCHAR(255) NOT NULL, permanent BOOLEAN NOT NULL, slug VARCHAR(255) NOT NULL, currency VARCHAR(255) NOT NULL, success_redirect VARCHAR(255) DEFAULT NULL, cancel_redirect VARCHAR(255) DEFAULT NULL, amount_due INT DEFAULT NULL, total INT DEFAULT NULL, sub_total INT DEFAULT NULL, tax_total INT DEFAULT NULL, expires_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, valid BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_AF382D4E9395C3F3 ON checkout (customer_id)');
        $this->addSql('CREATE INDEX IDX_AF382D4E38C5B87D ON checkout (brand_settings_id)');
        $this->addSql('CREATE INDEX IDX_AF382D4EB03A8386 ON checkout (created_by_id)');
        $this->addSql('COMMENT ON COLUMN checkout.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN checkout.customer_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN checkout.brand_settings_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN checkout.created_by_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE checkout_line (id UUID NOT NULL, checkout_id UUID DEFAULT NULL, subscription_plan_id UUID DEFAULT NULL, price_id UUID DEFAULT NULL, seat_number INT DEFAULT NULL, currency VARCHAR(255) NOT NULL, total INT DEFAULT NULL, sub_total INT DEFAULT NULL, tax_total INT DEFAULT NULL, tax_percentage DOUBLE PRECISION DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, tax_type VARCHAR(255) DEFAULT NULL, include_tax BOOLEAN NOT NULL, tax_country VARCHAR(255) DEFAULT NULL, reverse_charge BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_3A4D4128146D8724 ON checkout_line (checkout_id)');
        $this->addSql('CREATE INDEX IDX_3A4D41289B8CE200 ON checkout_line (subscription_plan_id)');
        $this->addSql('CREATE INDEX IDX_3A4D4128D614C7E7 ON checkout_line (price_id)');
        $this->addSql('COMMENT ON COLUMN checkout_line.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN checkout_line.checkout_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN checkout_line.subscription_plan_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN checkout_line.price_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE checkout_session (id UUID NOT NULL, customer_id UUID DEFAULT NULL, checkout_id UUID DEFAULT NULL, currency VARCHAR(255) NOT NULL, amount_due INT DEFAULT NULL, total INT DEFAULT NULL, sub_total INT DEFAULT NULL, tax_total INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, paid BOOLEAN NOT NULL, paid_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_2C2F31AD9395C3F3 ON checkout_session (customer_id)');
        $this->addSql('CREATE INDEX IDX_2C2F31AD146D8724 ON checkout_session (checkout_id)');
        $this->addSql('COMMENT ON COLUMN checkout_session.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN checkout_session.customer_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN checkout_session.checkout_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE checkout_session_subscription (checkout_session_id UUID NOT NULL, subscription_id UUID NOT NULL, PRIMARY KEY(checkout_session_id, subscription_id))');
        $this->addSql('CREATE INDEX IDX_622CEAC2DA6D31C4 ON checkout_session_subscription (checkout_session_id)');
        $this->addSql('CREATE INDEX IDX_622CEAC29A1887DC ON checkout_session_subscription (subscription_id)');
        $this->addSql('COMMENT ON COLUMN checkout_session_subscription.checkout_session_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN checkout_session_subscription.subscription_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE checkout_session_line (id UUID NOT NULL, checkout_session_id UUID DEFAULT NULL, subscription_plan_id UUID DEFAULT NULL, price_id UUID DEFAULT NULL, seat_number INT DEFAULT NULL, currency VARCHAR(255) NOT NULL, total INT DEFAULT NULL, sub_total INT DEFAULT NULL, tax_total INT DEFAULT NULL, tax_percentage DOUBLE PRECISION DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, tax_type VARCHAR(255) DEFAULT NULL, include_tax BOOLEAN NOT NULL, tax_country VARCHAR(255) DEFAULT NULL, reverse_charge BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_46316A7DDA6D31C4 ON checkout_session_line (checkout_session_id)');
        $this->addSql('CREATE INDEX IDX_46316A7D9B8CE200 ON checkout_session_line (subscription_plan_id)');
        $this->addSql('CREATE INDEX IDX_46316A7DD614C7E7 ON checkout_session_line (price_id)');
        $this->addSql('COMMENT ON COLUMN checkout_session_line.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN checkout_session_line.checkout_session_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN checkout_session_line.subscription_plan_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN checkout_session_line.price_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE checkout ADD CONSTRAINT FK_AF382D4E9395C3F3 FOREIGN KEY (customer_id) REFERENCES customers (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE checkout ADD CONSTRAINT FK_AF382D4E38C5B87D FOREIGN KEY (brand_settings_id) REFERENCES brand_settings (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE checkout ADD CONSTRAINT FK_AF382D4EB03A8386 FOREIGN KEY (created_by_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE checkout_line ADD CONSTRAINT FK_3A4D4128146D8724 FOREIGN KEY (checkout_id) REFERENCES checkout (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE checkout_line ADD CONSTRAINT FK_3A4D41289B8CE200 FOREIGN KEY (subscription_plan_id) REFERENCES subscription_plan (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE checkout_line ADD CONSTRAINT FK_3A4D4128D614C7E7 FOREIGN KEY (price_id) REFERENCES price (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE checkout_session ADD CONSTRAINT FK_2C2F31AD9395C3F3 FOREIGN KEY (customer_id) REFERENCES customers (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE checkout_session ADD CONSTRAINT FK_2C2F31AD146D8724 FOREIGN KEY (checkout_id) REFERENCES checkout (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE checkout_session_subscription ADD CONSTRAINT FK_622CEAC2DA6D31C4 FOREIGN KEY (checkout_session_id) REFERENCES checkout_session (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE checkout_session_subscription ADD CONSTRAINT FK_622CEAC29A1887DC FOREIGN KEY (subscription_id) REFERENCES subscription (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE checkout_session_line ADD CONSTRAINT FK_46316A7DDA6D31C4 FOREIGN KEY (checkout_session_id) REFERENCES checkout_session (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE checkout_session_line ADD CONSTRAINT FK_46316A7D9B8CE200 FOREIGN KEY (subscription_plan_id) REFERENCES subscription_plan (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE checkout_session_line ADD CONSTRAINT FK_46316A7DD614C7E7 FOREIGN KEY (price_id) REFERENCES price (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE subscription_seat_modification DROP CONSTRAINT FK_E532907B9A1887DC');
        $this->addSql('DROP TABLE subscription_seat_modification');
        $this->addSql('ALTER TABLE invoice_process ALTER due_at SET NOT NULL');
        $this->addSql('ALTER TABLE quote_line DROP seat_number');

        $this->addSql('ALTER TABLE checkout DROP CONSTRAINT FK_AF382D4E9395C3F3');
        $this->addSql('ALTER TABLE checkout DROP CONSTRAINT FK_AF382D4E38C5B87D');
        $this->addSql('ALTER TABLE checkout DROP CONSTRAINT FK_AF382D4EB03A8386');
        $this->addSql('ALTER TABLE checkout_line DROP CONSTRAINT FK_3A4D4128146D8724');
        $this->addSql('ALTER TABLE checkout_line DROP CONSTRAINT FK_3A4D41289B8CE200');
        $this->addSql('ALTER TABLE checkout_line DROP CONSTRAINT FK_3A4D4128D614C7E7');
        $this->addSql('ALTER TABLE checkout_session DROP CONSTRAINT FK_2C2F31AD9395C3F3');
        $this->addSql('ALTER TABLE checkout_session DROP CONSTRAINT FK_2C2F31AD146D8724');
        $this->addSql('ALTER TABLE checkout_session_subscription DROP CONSTRAINT FK_622CEAC2DA6D31C4');
        $this->addSql('ALTER TABLE checkout_session_subscription DROP CONSTRAINT FK_622CEAC29A1887DC');
        $this->addSql('ALTER TABLE checkout_session_line DROP CONSTRAINT FK_46316A7DDA6D31C4');
        $this->addSql('ALTER TABLE checkout_session_line DROP CONSTRAINT FK_46316A7D9B8CE200');
        $this->addSql('ALTER TABLE checkout_session_line DROP CONSTRAINT FK_46316A7DD614C7E7');
        $this->addSql('DROP TABLE checkout');
        $this->addSql('DROP TABLE checkout_line');
        $this->addSql('DROP TABLE checkout_session');
        $this->addSql('DROP TABLE checkout_session_subscription');
        $this->addSql('DROP TABLE checkout_session_line');
    }
}
