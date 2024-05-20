<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Software Limited 2023-2024.
 *
 * Use of this software is governed by the Functional Source License, Version 1.1, Apache 2.0 Future License included in the LICENSE.md file and at https://github.com/BillaBear/billabear/blob/main/LICENSE.
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240517145124 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE slack_notification (id UUID NOT NULL, slack_webhook_id UUID DEFAULT NULL, event VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, is_deleted BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_4FFC6C91B2A257EF ON slack_notification (slack_webhook_id)');
        $this->addSql('COMMENT ON COLUMN slack_notification.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN slack_notification.slack_webhook_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE slack_notification ADD CONSTRAINT FK_4FFC6C91B2A257EF FOREIGN KEY (slack_webhook_id) REFERENCES slack_webhook (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE slack_notification DROP CONSTRAINT FK_4FFC6C91B2A257EF');
        $this->addSql('DROP TABLE slack_notification');
    }
}
