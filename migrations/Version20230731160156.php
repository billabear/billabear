<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2023.
 *
 * Use of this software is governed by the Business Source License included in the LICENSE file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 *
 * Change Date: DD.MM.2026 ( 3 years after 1.1.0 release )
 *
 * On the date above, in accordance with the Business Source License, use of this software will be governed by the open source license specified in the LICENSE file.
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230731160156 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE webhook_event (id UUID NOT NULL, type VARCHAR(255) NOT NULL, payload TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN webhook_event.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE webhook_event_response (id UUID NOT NULL, event_id UUID DEFAULT NULL, endpoint_id UUID DEFAULT NULL, status_code INT NOT NULL, body TEXT NOT NULL, processing_time DOUBLE PRECISION NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_FB4F885B71F7E88B ON webhook_event_response (event_id)');
        $this->addSql('CREATE INDEX IDX_FB4F885B21AF7E36 ON webhook_event_response (endpoint_id)');
        $this->addSql('COMMENT ON COLUMN webhook_event_response.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN webhook_event_response.event_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN webhook_event_response.endpoint_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE webhook_event_response ADD CONSTRAINT FK_FB4F885B71F7E88B FOREIGN KEY (event_id) REFERENCES webhook_event (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE webhook_event_response ADD CONSTRAINT FK_FB4F885B21AF7E36 FOREIGN KEY (endpoint_id) REFERENCES webhook_endpoint (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE payment ADD invoice_id UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN payment.invoice_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D2989F1FD FOREIGN KEY (invoice_id) REFERENCES invoice (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_6D28840D2989F1FD ON payment (invoice_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE webhook_event_response DROP CONSTRAINT FK_FB4F885B71F7E88B');
        $this->addSql('ALTER TABLE webhook_event_response DROP CONSTRAINT FK_FB4F885B21AF7E36');
        $this->addSql('DROP TABLE webhook_event');
        $this->addSql('DROP TABLE webhook_event_response');
        $this->addSql('ALTER TABLE payment DROP CONSTRAINT FK_6D28840D2989F1FD');
        $this->addSql('DROP INDEX IDX_6D28840D2989F1FD');
        $this->addSql('ALTER TABLE payment DROP invoice_id');
    }
}
