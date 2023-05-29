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
final class Version20230529094026 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE payment_attempt (id UUID NOT NULL, customer_id UUID DEFAULT NULL, invoice_id UUID DEFAULT NULL, failure_reason VARCHAR(255) NOT NULL, amount INT NOT NULL, currency VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_1A50C8C9395C3F3 ON payment_attempt (customer_id)');
        $this->addSql('CREATE INDEX IDX_1A50C8C2989F1FD ON payment_attempt (invoice_id)');
        $this->addSql('COMMENT ON COLUMN payment_attempt.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN payment_attempt.customer_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN payment_attempt.invoice_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE payment_attempt_subscription (payment_attempt_id UUID NOT NULL, subscription_id UUID NOT NULL, PRIMARY KEY(payment_attempt_id, subscription_id))');
        $this->addSql('CREATE INDEX IDX_A9AFAA2384673FBE ON payment_attempt_subscription (payment_attempt_id)');
        $this->addSql('CREATE INDEX IDX_A9AFAA239A1887DC ON payment_attempt_subscription (subscription_id)');
        $this->addSql('COMMENT ON COLUMN payment_attempt_subscription.payment_attempt_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN payment_attempt_subscription.subscription_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE payment_attempt ADD CONSTRAINT FK_1A50C8C9395C3F3 FOREIGN KEY (customer_id) REFERENCES customers (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE payment_attempt ADD CONSTRAINT FK_1A50C8C2989F1FD FOREIGN KEY (invoice_id) REFERENCES invoice (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE payment_attempt_subscription ADD CONSTRAINT FK_A9AFAA2384673FBE FOREIGN KEY (payment_attempt_id) REFERENCES payment_attempt (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE payment_attempt_subscription ADD CONSTRAINT FK_A9AFAA239A1887DC FOREIGN KEY (subscription_id) REFERENCES subscription (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE payment_failure_process DROP CONSTRAINT fk_f939a8484c3a3bb');
        $this->addSql('DROP INDEX idx_f939a8484c3a3bb');
        $this->addSql('ALTER TABLE payment_failure_process RENAME COLUMN payment_id TO payment_attempt_id');
        $this->addSql('ALTER TABLE payment_failure_process ADD CONSTRAINT FK_F939A84884673FBE FOREIGN KEY (payment_attempt_id) REFERENCES payment_attempt (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_F939A84884673FBE ON payment_failure_process (payment_attempt_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE payment_failure_process DROP CONSTRAINT FK_F939A84884673FBE');
        $this->addSql('ALTER TABLE payment_attempt DROP CONSTRAINT FK_1A50C8C9395C3F3');
        $this->addSql('ALTER TABLE payment_attempt DROP CONSTRAINT FK_1A50C8C2989F1FD');
        $this->addSql('ALTER TABLE payment_attempt_subscription DROP CONSTRAINT FK_A9AFAA2384673FBE');
        $this->addSql('ALTER TABLE payment_attempt_subscription DROP CONSTRAINT FK_A9AFAA239A1887DC');
        $this->addSql('DROP TABLE payment_attempt');
        $this->addSql('DROP TABLE payment_attempt_subscription');
        $this->addSql('DROP INDEX IDX_F939A84884673FBE');
        $this->addSql('ALTER TABLE payment_failure_process RENAME COLUMN payment_attempt_id TO payment_id');
        $this->addSql('ALTER TABLE payment_failure_process ADD CONSTRAINT fk_f939a8484c3a3bb FOREIGN KEY (payment_id) REFERENCES payment (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_f939a8484c3a3bb ON payment_failure_process (payment_id)');
    }
}
