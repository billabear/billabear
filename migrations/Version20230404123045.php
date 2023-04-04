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
final class Version20230404123045 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE subscription (id UUID NOT NULL, price_id UUID DEFAULT NULL, subscription_plan_id UUID DEFAULT NULL, customer_id UUID DEFAULT NULL, plan_name VARCHAR(255) DEFAULT NULL, payment_schedule VARCHAR(255) DEFAULT NULL, seats INT DEFAULT NULL, active BOOLEAN DEFAULT NULL, status VARCHAR(255) DEFAULT NULL, amount INT DEFAULT NULL, currency VARCHAR(255) DEFAULT NULL, main_external_reference VARCHAR(255) DEFAULT NULL, child_external_reference VARCHAR(255) DEFAULT NULL, started_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, valid_until TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, ended_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A3C664D3D614C7E7 ON subscription (price_id)');
        $this->addSql('CREATE INDEX IDX_A3C664D39B8CE200 ON subscription (subscription_plan_id)');
        $this->addSql('CREATE INDEX IDX_A3C664D39395C3F3 ON subscription (customer_id)');
        $this->addSql('COMMENT ON COLUMN subscription.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN subscription.price_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN subscription.subscription_plan_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN subscription.customer_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D3D614C7E7 FOREIGN KEY (price_id) REFERENCES price (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D39B8CE200 FOREIGN KEY (subscription_plan_id) REFERENCES subscription_plan (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D39395C3F3 FOREIGN KEY (customer_id) REFERENCES customers (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE customers DROP subscription_plan_name');
        $this->addSql('ALTER TABLE customers DROP subscription_payment_schedule');
        $this->addSql('ALTER TABLE customers DROP subscription_active');
        $this->addSql('ALTER TABLE customers DROP subscription_status');
        $this->addSql('ALTER TABLE customers DROP subscription_amount');
        $this->addSql('ALTER TABLE customers DROP subscription_currency');
        $this->addSql('ALTER TABLE customers DROP subscription_started_at');
        $this->addSql('ALTER TABLE customers DROP subscription_valid_until');
        $this->addSql('ALTER TABLE customers DROP subscription_updated_at');
        $this->addSql('ALTER TABLE customers DROP subscription_ended_at');
        $this->addSql('ALTER TABLE customers DROP subscription_seats');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE subscription DROP CONSTRAINT FK_A3C664D3D614C7E7');
        $this->addSql('ALTER TABLE subscription DROP CONSTRAINT FK_A3C664D39B8CE200');
        $this->addSql('ALTER TABLE subscription DROP CONSTRAINT FK_A3C664D39395C3F3');
        $this->addSql('DROP TABLE subscription');
        $this->addSql('ALTER TABLE customers ADD subscription_plan_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE customers ADD subscription_payment_schedule VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE customers ADD subscription_active BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE customers ADD subscription_status VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE customers ADD subscription_amount INT DEFAULT NULL');
        $this->addSql('ALTER TABLE customers ADD subscription_currency VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE customers ADD subscription_started_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE customers ADD subscription_valid_until TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE customers ADD subscription_updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE customers ADD subscription_ended_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE customers ADD subscription_seats INT DEFAULT NULL');
    }
}
