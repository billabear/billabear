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
final class Version20230407183714 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE payment_subscription (payment_id UUID NOT NULL, subscription_id UUID NOT NULL, PRIMARY KEY(payment_id, subscription_id))');
        $this->addSql('CREATE INDEX IDX_C536D3C94C3A3BB ON payment_subscription (payment_id)');
        $this->addSql('CREATE INDEX IDX_C536D3C99A1887DC ON payment_subscription (subscription_id)');
        $this->addSql('COMMENT ON COLUMN payment_subscription.payment_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN payment_subscription.subscription_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE refund (id UUID NOT NULL, payment_id UUID DEFAULT NULL, customer_id UUID DEFAULT NULL, billing_admin_id UUID DEFAULT NULL, amount INT NOT NULL, currency VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, external_reference VARCHAR(255) NOT NULL, reason VARCHAR(255) DEFAULT NULL, started_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5B2C14584C3A3BB ON refund (payment_id)');
        $this->addSql('CREATE INDEX IDX_5B2C14589395C3F3 ON refund (customer_id)');
        $this->addSql('CREATE INDEX IDX_5B2C14587CF7EBEC ON refund (billing_admin_id)');
        $this->addSql('COMMENT ON COLUMN refund.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN refund.payment_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN refund.customer_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN refund.billing_admin_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE payment_subscription ADD CONSTRAINT FK_C536D3C94C3A3BB FOREIGN KEY (payment_id) REFERENCES payment-refund (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE payment_subscription ADD CONSTRAINT FK_C536D3C99A1887DC FOREIGN KEY (subscription_id) REFERENCES subscription (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE refund ADD CONSTRAINT FK_5B2C14584C3A3BB FOREIGN KEY (payment_id) REFERENCES payment-refund (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE refund ADD CONSTRAINT FK_5B2C14589395C3F3 FOREIGN KEY (customer_id) REFERENCES customers (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE refund ADD CONSTRAINT FK_5B2C14587CF7EBEC FOREIGN KEY (billing_admin_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subscription ADD payment_external_reference VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE payment_subscription DROP CONSTRAINT FK_C536D3C94C3A3BB');
        $this->addSql('ALTER TABLE payment_subscription DROP CONSTRAINT FK_C536D3C99A1887DC');
        $this->addSql('ALTER TABLE refund DROP CONSTRAINT FK_5B2C14584C3A3BB');
        $this->addSql('ALTER TABLE refund DROP CONSTRAINT FK_5B2C14589395C3F3');
        $this->addSql('ALTER TABLE refund DROP CONSTRAINT FK_5B2C14587CF7EBEC');
        $this->addSql('DROP TABLE payment_subscription');
        $this->addSql('DROP TABLE refund');
        $this->addSql('ALTER TABLE subscription DROP payment_external_reference');
    }
}
